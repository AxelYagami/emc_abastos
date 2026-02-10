import { Link } from 'react-router-dom'
import { useStores, useFlyer } from '../../hooks/useApi'
import StoreCard from '../../components/shared/StoreCard'
import ProductCard from '../../components/shared/ProductCard'
import { IconArrowRight, IconStore, IconTruck, IconShield } from '../../components/shared/Icons'

export default function BoldHome({ config }) {
  const { stores } = useStores()
  const { flyer } = useFlyer()

  return (
    <div className="bg-orange-50 min-h-screen">
      {/* Hero - Vibrant gradient */}
      <section className="relative py-20 lg:py-28 overflow-hidden">
        <div className="absolute inset-0 bg-gradient-to-br from-orange-400 via-rose-500 to-purple-600" />
        <div className="absolute inset-0 bg-[url('data:image/svg+xml,...')] opacity-10" />
        <div className="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-white">
          <span className="inline-block px-5 py-2 bg-white/20 backdrop-blur rounded-full text-sm font-bold mb-6">
            🔥 Ofertas del dia
          </span>
          <h1 className="text-4xl sm:text-5xl lg:text-6xl font-black leading-tight">
            {config?.hero?.title || 'Tu mercado favorito'}
          </h1>
          <p className="mt-6 text-xl text-white/80 max-w-lg mx-auto font-medium">
            {config?.hero?.subtitle || 'Todo lo que necesitas en un solo lugar'}
          </p>
          <div className="mt-10">
            <Link to="/tiendas" className="inline-flex items-center gap-2 px-8 py-4 bg-white text-orange-600 rounded-full font-black text-lg hover:bg-orange-50 transition shadow-xl hover:shadow-2xl hover:scale-105">
              {config?.hero?.cta_text || 'Explorar ahora'}
              <IconArrowRight className="w-5 h-5" />
            </Link>
          </div>
        </div>
      </section>

      {/* Products */}
      {flyer.enabled && flyer.products?.length > 0 && (
        <section className="py-16 -mt-12 relative z-10">
          <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div className="bg-white rounded-3xl shadow-xl p-8">
              <h2 className="text-2xl font-black text-gray-900 mb-6">{flyer.title || '⭐ Lo mas vendido'}</h2>
              <div className="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                {flyer.products.slice(0, 6).map((product, i) => (
                  <ProductCard key={product.id} product={product} showPrice={config?.settings?.show_prices !== false} index={i} />
                ))}
              </div>
            </div>
          </div>
        </section>
      )}

      {/* Stores */}
      {stores.length > 0 && (
        <section className="py-16">
          <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div className="flex justify-between items-center mb-8">
              <h2 className="text-2xl font-black text-gray-900">🏪 Tiendas</h2>
              <Link to="/tiendas" className="text-orange-600 font-bold hover:underline">Ver todas →</Link>
            </div>
            <div className="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
              {stores.slice(0, 8).map((store) => (
                <StoreCard key={store.id} store={store} />
              ))}
            </div>
          </div>
        </section>
      )}

      {/* Features */}
      <section className="py-16 bg-gradient-to-r from-orange-500 to-rose-500 text-white">
        <div className="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="grid md:grid-cols-3 gap-8 text-center">
            {[
              { icon: IconStore, title: 'Tiendas verificadas', desc: 'Solo los mejores' },
              { icon: IconTruck, title: 'Envio express', desc: 'Mismo dia' },
              { icon: IconShield, title: '100% Seguro', desc: 'Garantizado' },
            ].map((item, i) => (
              <div key={i} className="p-6">
                <div className="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center mx-auto mb-4">
                  <item.icon className="w-8 h-8" />
                </div>
                <h3 className="font-black text-lg">{item.title}</h3>
                <p className="text-white/80 mt-1">{item.desc}</p>
              </div>
            ))}
          </div>
        </div>
      </section>
    </div>
  )
}
