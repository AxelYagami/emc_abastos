export default function ProductCard({ product, showPrice = true, showStore = true, index = 0 }) {
  const storeUrl = product.store?.store_url || '#'
  const productUrl = product.store?.handle
    ? `/t/${product.store.handle}/producto/${product.id}`
    : storeUrl

  return (
    <a
      href={productUrl}
      className="group bg-white rounded-2xl shadow-sm hover:shadow-xl border border-gray-100 hover:border-primary-100 overflow-hidden transition-all duration-300"
      style={{ animationDelay: `${index * 60}ms` }}
    >
      {/* Image */}
      <div className="relative aspect-square bg-gray-100 overflow-hidden">
        <img
          src={product.display_image || product.imagen_url || '/images/producto-default.svg'}
          alt={product.nombre}
          className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
          loading="lazy"
          onError={(e) => { e.target.src = '/images/producto-default.svg' }}
        />
        {product.categoria && (
          <span className="absolute top-3 left-3 px-2.5 py-1 bg-white/90 backdrop-blur-sm text-gray-700 text-xs font-semibold rounded-full shadow-sm">
            {product.categoria.nombre}
          </span>
        )}
        <div className="absolute inset-0 bg-gradient-to-t from-black/10 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300" />
      </div>

      {/* Info */}
      <div className="p-4">
        {showStore && product.store && (
          <div className="flex items-center gap-2 mb-2">
            <div className="w-5 h-5 rounded-full bg-gray-100 overflow-hidden flex-shrink-0">
              {product.store.logo_url ? (
                <img src={product.store.logo_url} alt="" className="w-full h-full object-cover" />
              ) : (
                <span className="w-full h-full flex items-center justify-center text-[10px] font-bold text-gray-400">
                  {product.store.nombre?.[0]}
                </span>
              )}
            </div>
            <span className="text-xs text-gray-500 truncate">{product.store.nombre}</span>
          </div>
        )}

        <h3 className="font-semibold text-gray-900 group-hover:text-primary-600 transition-colors line-clamp-2 leading-snug min-h-[2.5rem]">
          {product.nombre}
        </h3>

        {showPrice && product.precio != null && (
          <p className="text-xl font-bold text-primary-600 mt-2">
            ${parseFloat(product.precio).toFixed(2)}
          </p>
        )}
      </div>
    </a>
  )
}
