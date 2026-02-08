import { IconChevronRight, IconStar } from './Icons'

export default function StoreCard({ store, variant = 'default' }) {
  if (variant === 'compact') {
    return (
      <a
        href={store.store_url}
        className="group flex items-center gap-4 p-4 bg-white rounded-2xl border border-gray-100 hover:border-primary-200 hover:shadow-lg transition-all duration-300"
      >
        <div className="w-14 h-14 rounded-xl bg-gray-100 flex items-center justify-center overflow-hidden flex-shrink-0 ring-2 ring-gray-50 group-hover:ring-primary-100 transition-all">
          {store.logo_url ? (
            <img src={store.logo_url} alt={store.nombre} className="w-full h-full object-cover" loading="lazy" />
          ) : (
            <span className="text-xl font-bold text-primary-600">{store.nombre?.[0]}</span>
          )}
        </div>
        <div className="flex-1 min-w-0">
          <h3 className="font-semibold text-gray-900 group-hover:text-primary-600 transition-colors truncate">
            {store.nombre}
          </h3>
          {store.descripcion && (
            <p className="text-sm text-gray-500 truncate mt-0.5">{store.descripcion}</p>
          )}
        </div>
        <IconChevronRight className="w-4 h-4 text-gray-400 group-hover:text-primary-500 group-hover:translate-x-0.5 transition-all flex-shrink-0" />
      </a>
    )
  }

  return (
    <a
      href={store.store_url}
      className="group bg-white rounded-2xl shadow-sm hover:shadow-xl border border-gray-100 hover:border-primary-100 overflow-hidden transition-all duration-300"
    >
      {/* Banner */}
      <div className="relative h-28 bg-gradient-to-br from-primary-50 to-primary-100 overflow-hidden">
        <div className="absolute inset-0 bg-gradient-to-br from-primary-500/5 to-transparent" />
        {store.is_featured && (
          <span className="absolute top-3 right-3 inline-flex items-center gap-1 px-2.5 py-1 bg-amber-400 text-amber-900 text-xs font-bold rounded-full shadow-sm">
            <IconStar className="w-3 h-3" />
            Destacada
          </span>
        )}
        <div className="absolute -bottom-7 left-5">
          <div className="w-14 h-14 rounded-xl bg-white shadow-md flex items-center justify-center overflow-hidden ring-4 ring-white">
            {store.logo_url ? (
              <img src={store.logo_url} alt={store.nombre} className="w-full h-full object-cover" loading="lazy" />
            ) : (
              <span className="text-xl font-bold text-primary-600">{store.nombre?.[0]}</span>
            )}
          </div>
        </div>
      </div>

      {/* Info */}
      <div className="p-5 pt-10">
        <h3 className="font-bold text-gray-900 text-lg group-hover:text-primary-600 transition-colors">
          {store.nombre}
        </h3>
        {store.descripcion && (
          <p className="text-gray-500 text-sm mt-1.5 line-clamp-2 leading-relaxed">{store.descripcion}</p>
        )}

        {store.tags?.length > 0 && (
          <div className="flex flex-wrap gap-1.5 mt-3">
            {store.tags.slice(0, 3).map(tag => (
              <span key={tag} className="px-2.5 py-0.5 bg-gray-100 text-gray-600 text-xs font-medium rounded-full">
                {tag}
              </span>
            ))}
          </div>
        )}

        <div className="mt-4 pt-4 border-t border-gray-100 flex items-center justify-between">
          <span className="text-primary-600 font-medium text-sm inline-flex items-center gap-1 group-hover:gap-2 transition-all">
            Visitar tienda
            <IconChevronRight className="w-3.5 h-3.5" />
          </span>
        </div>
      </div>
    </a>
  )
}
