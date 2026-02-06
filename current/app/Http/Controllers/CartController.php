<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $cart = session('cart', []);
        $items = [];
        $total = 0;

        if (!empty($cart)) {
            $productos = Producto::whereIn('id', array_keys($cart))->get()->keyBy('id');
            foreach ($cart as $pid => $qty) {
                $p = $productos->get((int)$pid);
                if (!$p) continue;
                $items[] = ['producto' => $p, 'qty' => (int)$qty];
                $total += ((float)$p->precio) * (int)$qty;
            }
        }

        return view('store.cart', compact('items', 'total'));
    }

    public function add(Request $request)
    {
        $data = $request->validate([
            'producto_id' => ['required', 'integer'],
            'qty' => ['nullable', 'integer', 'min:1', 'max:999'],
        ]);

        $empresaId = session('empresa_id');
        if (!$empresaId) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Selecciona una empresa antes de agregar productos.',
                ], 400);
            }
            return redirect()->route('empresa.switch')->withErrors(['empresa' => 'Selecciona una empresa antes de agregar productos.']);
        }

        $p = Producto::find($data['producto_id']);
        if (!$p) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Producto no encontrado.',
                ], 404);
            }
            return back()->withErrors(['producto_id' => 'Producto no encontrado.']);
        }

        // Validate empresa + activo
        if ((int)$p->empresa_id !== (int)$empresaId) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Este producto pertenece a otra empresa.',
                ], 400);
            }
            return back()->withErrors(['producto_id' => 'Este producto pertenece a otra empresa.']);
        }

        if (isset($p->activo) && !$p->activo) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Este producto está inactivo.',
                ], 400);
            }
            return back()->withErrors(['producto_id' => 'Este producto está inactivo.']);
        }

        $qty = (int)($data['qty'] ?? 1);

        $cartEmpresa = session('cart_empresa_id');
        if ($cartEmpresa && (int)$cartEmpresa !== (int)$empresaId) {
            session()->forget('cart');
            session()->forget('cart_empresa_id');
        }

        $cart = session('cart', []);
        $cart[$p->id] = ($cart[$p->id] ?? 0) + $qty;

        session(['cart' => $cart, 'cart_empresa_id' => $empresaId]);

        // Calculate cart totals for response
        $cartCount = array_sum($cart);
        $cartTotal = $this->calculateCartTotal($cart);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Producto agregado al carrito',
                'product_name' => $p->nombre,
                'cart_count' => $cartCount,
                'cart_total' => $cartTotal,
                'cart_total_formatted' => '$' . number_format($cartTotal, 2),
            ]);
        }

        return redirect()->route('cart.index');
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'producto_id' => ['required', 'integer'],
            'qty' => ['required', 'integer', 'min:0', 'max:999'],
        ]);

        $cart = session('cart', []);

        if ($data['qty'] <= 0) {
            unset($cart[$data['producto_id']]);
        } else {
            $cart[$data['producto_id']] = (int)$data['qty'];
        }

        session(['cart' => $cart]);

        if ($request->expectsJson()) {
            $cartCount = array_sum($cart);
            $cartTotal = $this->calculateCartTotal($cart);

            return response()->json([
                'success' => true,
                'cart_count' => $cartCount,
                'cart_total' => $cartTotal,
                'cart_total_formatted' => '$' . number_format($cartTotal, 2),
            ]);
        }

        return redirect()->route('cart.index');
    }

    public function remove(Request $request)
    {
        $data = $request->validate([
            'producto_id' => ['required', 'integer'],
        ]);

        $cart = session('cart', []);
        unset($cart[$data['producto_id']]);
        session(['cart' => $cart]);

        if ($request->expectsJson()) {
            $cartCount = array_sum($cart);
            $cartTotal = $this->calculateCartTotal($cart);

            return response()->json([
                'success' => true,
                'cart_count' => $cartCount,
                'cart_total' => $cartTotal,
                'cart_total_formatted' => '$' . number_format($cartTotal, 2),
            ]);
        }

        return redirect()->route('cart.index');
    }

    public function clear(Request $request)
    {
        session()->forget('cart');
        session()->forget('cart_empresa_id');

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'cart_count' => 0,
                'cart_total' => 0,
                'cart_total_formatted' => '$0.00',
            ]);
        }

        return redirect()->route('cart.index');
    }

    public function summary()
    {
        $cart = session('cart', []);
        $cartCount = array_sum($cart);
        $cartTotal = $this->calculateCartTotal($cart);

        return response()->json([
            'cart_count' => $cartCount,
            'cart_total' => $cartTotal,
            'cart_total_formatted' => '$' . number_format($cartTotal, 2),
        ]);
    }

    private function calculateCartTotal(array $cart): float
    {
        if (empty($cart)) {
            return 0;
        }

        $productos = Producto::whereIn('id', array_keys($cart))->get()->keyBy('id');
        $total = 0;

        foreach ($cart as $pid => $qty) {
            $p = $productos->get((int)$pid);
            if ($p) {
                $total += ((float)$p->precio) * (int)$qty;
            }
        }

        return $total;
    }
}
