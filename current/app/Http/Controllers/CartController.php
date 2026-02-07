<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * Display the cart page
     */
    public function index(Request $request)
    {
        $cart = session('cart', []);
        $items = [];
        $total = 0;

        foreach ($cart as $productId => $item) {
            $producto = Producto::find($productId);
            if ($producto) {
                $subtotal = $producto->precio * $item['qty'];
                $items[] = [
                    'id' => $producto->id,
                    'nombre' => $producto->nombre,
                    'precio' => $producto->precio,
                    'qty' => $item['qty'],
                    'subtotal' => $subtotal,
                    'imagen_url' => $producto->imagen_url,
                ];
                $total += $subtotal;
            }
        }

        return view('store.cart', compact('items', 'total'));
    }

    /**
     * Add item to cart (AJAX)
     */
    public function add(Request $request)
    {
        $cart = session('cart', []);
        $productId = $request->input('producto_id');
        $qty = max(1, (int) $request->input('qty', 1));

        $producto = Producto::find($productId);
        if (!$producto) {
            return response()->json(['error' => 'Producto no encontrado'], 404);
        }

        if (isset($cart[$productId])) {
            $cart[$productId]['qty'] += $qty;
        } else {
            $cart[$productId] = [
                'id' => $producto->id,
                'nombre' => $producto->nombre,
                'precio' => $producto->precio,
                'qty' => $qty,
            ];
        }

        session(['cart' => $cart]);

        return response()->json([
            'success' => true,
            'count' => array_sum(array_column($cart, 'qty')),
            'total' => array_sum(array_map(fn($item) => $item['precio'] * $item['qty'], $cart)),
            'message' => 'Producto agregado al carrito',
        ]);
    }

    /**
     * Update item quantity (AJAX)
     */
    public function update(Request $request)
    {
        $cart = session('cart', []);
        $productId = $request->input('producto_id');
        $qty = max(1, (int) $request->input('qty', 1));

        if (isset($cart[$productId])) {
            $cart[$productId]['qty'] = $qty;
            session(['cart' => $cart]);
        }

        return response()->json([
            'success' => true,
            'count' => array_sum(array_column($cart, 'qty')),
            'total' => array_sum(array_map(fn($item) => $item['precio'] * $item['qty'], $cart)),
        ]);
    }

    /**
     * Remove item from cart (AJAX)
     */
    public function remove(Request $request)
    {
        $cart = session('cart', []);
        $productId = $request->input('producto_id');

        if (isset($cart[$productId])) {
            unset($cart[$productId]);
            session(['cart' => $cart]);
        }

        return response()->json([
            'success' => true,
            'count' => array_sum(array_column($cart, 'qty')),
            'total' => array_sum(array_map(fn($item) => $item['precio'] * $item['qty'], $cart)),
        ]);
    }

    /**
     * Clear the cart (AJAX)
     */
    public function clear(Request $request)
    {
        session()->forget('cart');

        return response()->json([
            'success' => true,
            'count' => 0,
            'total' => 0,
        ]);
    }

    /**
     * Get cart summary (AJAX)
     */
    public function summary(Request $request)
    {
        $cart = session('cart', []);

        return response()->json([
            'count' => array_sum(array_column($cart, 'qty')),
            'total' => array_sum(array_map(fn($item) => $item['precio'] * $item['qty'], $cart)),
        ]);
    }
}
