import { Link } from 'react-router-dom'
import { useStores, useFlyer } from '../../hooks/useApi'
import StoreCard from '../../components/shared/StoreCard'
import ProductCard from '../../components/shared/ProductCard'
import { IconArrowRight, IconStore, IconTruck, IconShield } from '../../components/shared/Icons'

export default function ModernHome({ config }) {
  const { stores } = useStores()
  const { flyer } = useFlyer()

  return (
    <div className="bg-gray-950 text-white min-h-screen">
      {/* Hero - Dark gradient */}
      <section className="relative py-24 lg:py-32 overflow-hidden">
        <div className="absolute inset-0 bg-gradient-to-br from-gray-900 via-gray-950 to-emerald-950" />
        <div className="absolute top-0 right-0 w-96 h-96 bg-emerald-500/10 rounded-full blur-3xl" />
        <div className="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="max-w-2xl">
            <span className="inline-block px-4 py-1.5 bg-emerald-500/20 text-emerald-400 rounded-full text-sm font-medium mb-6">
              Marketplace Digital
            </span>
            <h1 className="text-4xl sm:text-5xl lg:text-6xl font-bold leading-tight">
              {config?.hero?.title || 'El mercado en tu mano'}
            </h1>
            <p className="mt-6 text-lg text-gray-400 max-w-lg">
              {config?.hero?.subtitle || 'Productos frescos directo del proveedor'}
            </p>
            <div className="mt-8 flex flex-wrap gap-4">
              <Link to="/tiendas" className="inline-flex items-center gap-2 px-6 py-3 bg-emerald-500 text-white rounded-lg font-semibold hover:bg-emerald-600 transition">
                {config?.hero?.cta_text || 'Ver tiendas'}
                <IconArrowRight className="w-4 h-4" />
              </Link>
            </div>
          </div>
        </div>
      </section>

      {/* Products */}
      {flyer.enabled && flyer.products?.length > 0 && (
        <section className="py-16 bg-gray-900">
          <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 className="text-2xl font-bold mb-8">{flyer.title || 'Destacados'}</h2>
            <div className="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
              {flyer.products.slice(0, 6).map((product, i) => (
                <ProductCard key={product.id} product={product} showPrice={config?.settings?.show_prices !== false} index={i} />
              ))}
            </div>
          </div>
        </section>
      )}

      {/* Stores */}
      {stores.length > 0 && (
        <section className="py-16 bg-gray-950">
          <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div className="flex justify-between items-center mb-8">
              <h2 className="text-2xl font-bold">Tiendas</h2>
              <Link to="/tiendas" className="text-emerald-400 hover:text-emerald-300 text-sm font-medium">Ver todas</Link>
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
      <section className="py-16 bg-gray-900">
        <div className="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="grid md:grid-cols-3 gap-8">
            {[
              { icon: IconStore, title: 'Proveedores verificados', desc: 'Directorio de vendedores confiables' },
              { icon: IconTruck, title: 'Envio rapido', desc: 'Entrega el mismo dia' },
              { icon: IconShield, title: 'Pago seguro', desc: 'Multiples metodos de pago' },
            ].map((item, i) => (
              <div key={i} className="text-center p-6 bg-gray-800/50 rounded-2xl">
                <div className="w-14 h-14 bg-emerald-500/20 rounded-xl flex items-center justify-center mx-auto mb-4">
                  <item.icon className="w-7 h-7 text-emerald-400" />
                </div>
                <h3 className="font-semibold text-lg">{item.title}</h3>
                <p className="text-gray-400 text-sm mt-2">{item.desc}</p>
              </div>
            ))}
          </div>
        </div>
      </section>
    </div>
  )
}
