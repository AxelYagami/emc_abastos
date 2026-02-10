import { Link } from 'react-router-dom'
import { useStores, useFlyer } from '../../hooks/useApi'
import StoreCard from '../../components/shared/StoreCard'
import ProductCard from '../../components/shared/ProductCard'
import { IconArrowRight } from '../../components/shared/Icons'

export default function MinimalHome({ config }) {
  const { stores } = useStores()
  const { flyer } = useFlyer()

  return (
    <div className="bg-white min-h-screen">
      {/* Hero - Ultra minimal */}
      <section className="py-20 lg:py-32">
        <div className="max-w-4xl mx-auto px-4 sm:px-6 text-center">
          <h1 className="text-3xl sm:text-4xl lg:text-5xl font-light tracking-tight text-gray-900">
            {config?.hero?.title || 'Mercado digital'}
          </h1>
          <p className="mt-6 text-lg text-gray-400 font-light max-w-lg mx-auto">
            {config?.hero?.subtitle || 'Productos frescos, directo a ti'}
          </p>
          <Link to="/tiendas" className="inline-flex items-center gap-2 mt-10 text-sm text-gray-900 border-b border-gray-900 pb-1 hover:text-gray-600 hover:border-gray-600 transition">
            {config?.hero?.cta_text || 'Explorar'}
            <IconArrowRight className="w-3 h-3" />
          </Link>
        </div>
      </section>

      {/* Products - Clean grid */}
      {flyer.enabled && flyer.products?.length > 0 && (
        <section className="py-16 border-t border-gray-100">
          <div className="max-w-6xl mx-auto px-4 sm:px-6">
            <h2 className="text-xs uppercase tracking-widest text-gray-400 mb-8">{flyer.title || 'Destacados'}</h2>
            <div className="grid sm:grid-cols-2 lg:grid-cols-3 gap-8">
              {flyer.products.slice(0, 6).map((product, i) => (
                <ProductCard key={product.id} product={product} showPrice={config?.settings?.show_prices !== false} index={i} />
              ))}
            </div>
          </div>
        </section>
      )}

      {/* Stores */}
      {stores.length > 0 && (
        <section className="py-16 border-t border-gray-100">
          <div className="max-w-6xl mx-auto px-4 sm:px-6">
            <div className="flex justify-between items-center mb-8">
              <h2 className="text-xs uppercase tracking-widest text-gray-400">Tiendas</h2>
              <Link to="/tiendas" className="text-xs text-gray-400 hover:text-gray-900">Ver todas</Link>
            </div>
            <div className="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
              {stores.slice(0, 8).map((store) => (
                <StoreCard key={store.id} store={store} />
              ))}
            </div>
          </div>
        </section>
      )}
    </div>
  )
}
