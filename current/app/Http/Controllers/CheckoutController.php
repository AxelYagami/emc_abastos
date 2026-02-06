<?php

namespace App\Http\Controllers;

use App\Models\Orden;
use App\Models\OrdenItem;
use App\Models\Producto;
use App\Models\Cliente;
use App\Models\Empresa;
use App\Services\MercadoPagoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    public function show()
    {
        $cart = session('cart', []);
        if (empty($cart)) {
            return redirect()->route('cart.index')->withErrors(['cart' => 'Tu carrito está vacío.']);
        }

        $empresaId = session('empresa_id');
        $empresa = Empresa::find($empresaId);
        $hasMercadoPago = $empresa && $empresa->hasMercadoPago();

        return view('store.checkout', compact('hasMercadoPago', 'empresa'));
    }

    public function place(Request $request)
    {
        $request->validate([
            'comprador_nombre' => ['required', 'string', 'max:120'],
            'comprador_whatsapp' => ['required', 'string', 'max:40'],
            'comprador_email' => ['nullable', 'email', 'max:150'],
            'tipo_entrega' => ['nullable', 'in:pickup,delivery'],
            'metodo_pago' => ['nullable', 'in:efectivo,mercadopago'],
        ]);

        $empresaId = session('empresa_id');
        if (!$empresaId) {
            return redirect()->route('empresa.switch')->withErrors(['empresa' => 'Selecciona una empresa antes de comprar.']);
        }

        $cart = session('cart', []);
        if (empty($cart)) {
            return redirect()->route('cart.index')->withErrors(['cart' => 'Tu carrito está vacío.']);
        }

        $productos = Producto::whereIn('id', array_keys($cart))->get()->keyBy('id');

        foreach ($cart as $pid => $qty) {
            $p = $productos->get((int)$pid);
            if (!$p) return back()->withErrors(['cart' => 'Producto inválido.'])->withInput();
            if ((int)$p->empresa_id !== (int)$empresaId) {
                return back()->withErrors(['cart' => 'Tu carrito contiene productos de otra empresa.']);
            }
            if (isset($p->activo) && !$p->activo) {
                return back()->withErrors(['cart' => 'Tu carrito contiene productos inactivos.'])->withInput();
            }
            if ($qty < 1) return back()->withErrors(['cart' => 'Cantidad inválida.'])->withInput();
        }

        $orden = DB::transaction(function () use ($request, $empresaId, $cart, $productos) {
            // Upsert cliente
            $cliente = Cliente::upsertFromCheckout(
                $empresaId,
                $request->input('comprador_nombre'),
                $request->input('comprador_whatsapp'),
                $request->input('comprador_email')
            );

            // Generate unique folio
            do {
                $folio = 'EMC-' . strtoupper(Str::random(10));
            } while (Orden::where('folio', $folio)->exists());

            $subtotal = 0;
            foreach ($cart as $pid => $qty) {
                $p = $productos->get((int)$pid);
                $subtotal += ((float)$p->precio) * ((int)$qty);
            }

            $orden = Orden::create([
                'empresa_id' => $empresaId,
                'cliente_id' => $cliente->id,
                'usuario_id' => auth()->id(),
                'folio' => $folio,
                'status' => 'creada',
                'tipo_entrega' => $request->input('tipo_entrega', 'pickup'),
                'comprador_nombre' => $request->input('comprador_nombre'),
                'comprador_whatsapp' => $request->input('comprador_whatsapp'),
                'comprador_email' => $request->input('comprador_email'),
                'subtotal' => $subtotal,
                'descuento' => 0,
                'envio' => 0,
                'total' => $subtotal,
                'meta' => [
                    'metodo_pago_preferido' => $request->input('metodo_pago', 'efectivo'),
                ],
            ]);

            foreach ($cart as $pid => $qty) {
                $p = $productos->get((int)$pid);
                $precio = (float)$p->precio;
                $total = $precio * (int)$qty;

                OrdenItem::create([
                    'orden_id' => $orden->id,
                    'empresa_id' => $empresaId,
                    'producto_id' => $p->id,
                    'nombre_snapshot' => $p->nombre,
                    'nombre' => $p->nombre,
                    'precio_unitario' => $precio,
                    'precio' => $precio,
                    'cantidad' => (int)$qty,
                    'total' => $total,
                ]);
            }

            session()->forget('cart');
            session()->forget('cart_empresa_id');

            return $orden;
        });

        // If MercadoPago selected and configured, redirect to payment
        if ($request->input('metodo_pago') === 'mercadopago') {
            try {
                if (MercadoPagoService::isConfigured($empresaId)) {
                    $mpService = new MercadoPagoService($empresaId);
                    $preference = $mpService->createPreference(
                        $orden,
                        route('checkout.success', $orden->folio),
                        route('checkout.failure', $orden->folio),
                        route('checkout.pending', $orden->folio)
                    );

                    return redirect($preference['init_point']);
                }
            } catch (\Exception $e) {
                // Log error but continue to thanks page
                \Log::error('MercadoPago error', ['error' => $e->getMessage()]);
            }
        }

        return redirect()->route('store.thanks', $orden->folio);
    }

    public function success(string $folio)
    {
        $orden = Orden::where('folio', $folio)->firstOrFail();
        return view('store.thanks', [
            'orden' => $orden,
            'status' => 'success',
            'message' => 'Tu pago fue procesado correctamente.',
        ]);
    }

    public function failure(string $folio)
    {
        $orden = Orden::where('folio', $folio)->firstOrFail();
        return view('store.thanks', [
            'orden' => $orden,
            'status' => 'failure',
            'message' => 'El pago no pudo ser procesado. Puedes intentar de nuevo o pagar en efectivo.',
        ]);
    }

    public function pending(string $folio)
    {
        $orden = Orden::where('folio', $folio)->firstOrFail();
        return view('store.thanks', [
            'orden' => $orden,
            'status' => 'pending',
            'message' => 'Tu pago está pendiente de confirmación.',
        ]);
    }
}
