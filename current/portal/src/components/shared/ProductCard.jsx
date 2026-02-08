import { IconArrowRight } from './Icons'

export default function ProductCard({ product, showPrice = true, showStore = true, index = 0, featured = false }) {
  const storeUrl = product.store?.store_url || '#'
  const productUrl = product.store?.handle
    ? `/t/${product.store.handle}/producto/${product.id}`
    : storeUrl

  return (
    <a
      href={productUrl}
      className={`group card-premium overflow-hidden flex flex-col ${
        featured ? 'animate-flash' : ''
      }`}
      style={{ animationDelay: `${index * 60}ms` }}
      data-testid={`product-card-${product.id}`}
    >
      {/* Image */}
      <div className="relative aspect-square bg-gray-50 overflow-hidden img-container">
        <img
          src={product.display_image || product.imagen_url || '/images/producto-default.svg'}
          alt={product.nombre}
          className="w-full h-full object-cover"
          loading="lazy"
          onError={(e) => { e.target.src = '/images/producto-default.svg' }}
        />
        
        {/* Category badge */}
        {product.categoria && (
          <span className="absolute top-3 left-3 badge glass text-gray-800">
            {product.categoria.nombre}
          </span>
        )}
        
        {/* Promo badge */}
        {product.en_promocion && (
          <span className="absolute top-3 right-3 badge badge-promo">
            Oferta
          </span>
        )}
        
        {/* Hover overlay */}
        <div className="absolute inset-0 bg-gradient-to-t from-black/30 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end justify-center pb-4">
          <span className="inline-flex items-center gap-1.5 px-4 py-2 bg-white/95 backdrop-blur-sm text-gray-900 text-sm font-semibold rounded-full shadow-lg transform translate-y-2 group-hover:translate-y-0 transition-transform duration-300">
            Ver producto
            <IconArrowRight className="w-3.5 h-3.5" />
          </span>
        </div>
      </div>

      {/* Info */}
      <div className="p-4 flex flex-col flex-1">
        {showStore && product.store && (
          <div className="flex items-center gap-2 mb-2.5">
            <div className="w-6 h-6 rounded-lg bg-gray-100 overflow-hidden flex-shrink-0 ring-1 ring-gray-200">
              {product.store.logo_url ? (
                <img src={product.store.logo_url} alt="" className="w-full h-full object-cover" />
              ) : (
                <span className="w-full h-full flex items-center justify-center text-[10px] font-bold text-primary-600 bg-primary-50">
                  {product.store.nombre?.[0]}
                </span>
              )}
            </div>
            <span className="text-xs text-gray-500 font-medium truncate">{product.store.nombre}</span>
          </div>
        )}

        <h3 className="font-semibold text-gray-900 group-hover:text-primary-600 transition-colors line-clamp-2 leading-snug flex-1">
          {product.nombre}
        </h3>

        {showPrice && product.precio != null && (
          <div className="mt-3 pt-3 border-t border-gray-100 flex items-center justify-between">
            <p className="text-xl font-bold text-primary-600">
              ${parseFloat(product.precio).toFixed(2)}
            </p>
            <span className="text-xs text-gray-400 group-hover:text-primary-500 transition-colors">
              Ver mas
            </span>
          </div>
        )}
      </div>
    </a>
  )
}
