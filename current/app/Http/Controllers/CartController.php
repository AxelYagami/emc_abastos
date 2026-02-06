public function add(Request \)
{
    \ = session('cart', []);
    \ = \->input('producto_id');
    \ = \->input('qty', 1);

    \ = Producto::find(\);
    if (!\) {
        return response()->json(['error' => 'Producto no encontrado'], 404);
    }

    // Agregar al carrito (puedes adaptar según tu modelo de datos)
    \[\] = [
        'id' => \->id,
        'nombre' => \->nombre,
        'precio' => \->precio,
        'qty' => \,
    ];

    session(['cart' => \]);

    // Actualizar el resumen del carrito
    return response()->json([
        'count' => count(\),
        'total' => array_sum(array_map(fn(\3) => \3['precio'] * \3['qty'], \)),
    ]);
}
