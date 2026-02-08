import { IconChevronRight, IconStar, IconTruck, IconStore } from './Icons'

export default function StoreCard({ store, variant = 'default' }) {
  const hasDelivery = store.enable_delivery
  const hasPickup = store.enable_pickup

  if (variant === 'compact') {
    return (
      <a
        href={store.store_url}
        className="group card-premium flex items-center gap-4 p-4"
        data-testid={`store-card-compact-${store.id}`}
      >
        <div className="w-14 h-14 rounded-xl bg-gradient-to-br from-primary-50 to-primary-100 flex items-center justify-center overflow-hidden flex-shrink-0 ring-2 ring-white shadow-soft group-hover:shadow-soft-lg transition-all">
          {store.logo_url ? (
            <img src={store.logo_url} alt={store.nombre} className="w-full h-full object-cover" loading="lazy" />
          ) : (
            <IconStore className="w-6 h-6 text-primary-600" />
          )}
        </div>
        <div className="flex-1 min-w-0">
          <div className="flex items-center gap-2">
            <h3 className="font-semibold text-gray-900 group-hover:text-primary-600 transition-colors truncate">
              {store.nombre}
            </h3>
            {store.is_featured && (
              <IconStar className="w-4 h-4 text-amber-400 flex-shrink-0" />
            )}
          </div>
          {store.descripcion && (
            <p className="text-sm text-gray-500 truncate mt-0.5">{store.descripcion}</p>
          )}
        </div>
        <div className="flex items-center gap-2">
          {hasDelivery && (
            <span className="p-1.5 bg-primary-50 rounded-lg" title="Entrega a domicilio">
              <IconTruck className="w-4 h-4 text-primary-600" />
            </span>
          )}
          <IconChevronRight className="w-5 h-5 text-gray-300 group-hover:text-primary-500 group-hover:translate-x-1 transition-all flex-shrink-0" />
        </div>
      </a>
    )
  }

  return (
    <a
      href={store.store_url}
      className="group card-premium overflow-hidden flex flex-col"
      data-testid={`store-card-${store.id}`}
    >
      {/* Banner */}
      <div className="relative h-32 bg-gradient-to-br from-primary-500 via-primary-600 to-primary-700 overflow-hidden">
        {/* Pattern overlay */}
        <div className="absolute inset-0 opacity-10">
          <svg className="w-full h-full" viewBox="0 0 100 100" preserveAspectRatio="none">
            <defs>
              <pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse">
                <path d="M 10 0 L 0 0 0 10" fill="none" stroke="white" strokeWidth="0.5"/>
              </pattern>
            </defs>
            <rect width="100" height="100" fill="url(#grid)" />
          </svg>
        </div>
        
        {/* Featured badge */}
        {store.is_featured && (
          <span className="absolute top-3 right-3 badge badge-featured">
            <IconStar className="w-3 h-3" />
            Destacada
          </span>
        )}
        
        {/* Delivery badges */}
        <div className="absolute top-3 left-3 flex gap-1.5">
          {hasDelivery && (
            <span className="badge glass text-gray-800">
              <IconTruck className="w-3 h-3" />
              Envio
            </span>
          )}
        </div>
        
        {/* Logo */}
        <div className="absolute -bottom-8 left-5">
          <div className="w-16 h-16 rounded-2xl bg-white shadow-soft-lg flex items-center justify-center overflow-hidden ring-4 ring-white">
            {store.logo_url ? (
              <img src={store.logo_url} alt={store.nombre} className="w-full h-full object-cover" loading="lazy" />
            ) : (
              <IconStore className="w-7 h-7 text-primary-600" />
            )}
          </div>
        </div>
      </div>

      {/* Info */}
      <div className="p-5 pt-12 flex flex-col flex-1">
        <h3 className="font-bold text-gray-900 text-lg group-hover:text-primary-600 transition-colors">
          {store.nombre}
        </h3>
        {store.descripcion && (
          <p className="text-gray-500 text-sm mt-2 line-clamp-2 leading-relaxed flex-1">{store.descripcion}</p>
        )}

        {store.tags?.length > 0 && (
          <div className="flex flex-wrap gap-1.5 mt-4">
            {store.tags.slice(0, 3).map(tag => (
              <span key={tag} className="px-2.5 py-1 bg-gray-100 text-gray-600 text-xs font-medium rounded-lg">
                {tag}
              </span>
            ))}
          </div>
        )}

        <div className="mt-4 pt-4 border-t border-gray-100 flex items-center justify-between">
          <span className="text-primary-600 font-semibold text-sm inline-flex items-center gap-1.5 btn-press">
            Visitar tienda
            <IconChevronRight className="w-4 h-4 group-hover:translate-x-1 transition-transform" />
          </span>
          {store.hora_atencion_inicio && store.hora_atencion_fin && (
            <span className="text-xs text-gray-400">
              {store.hora_atencion_inicio} - {store.hora_atencion_fin}
            </span>
          )}
        </div>
      </div>
    </a>
  )
}
