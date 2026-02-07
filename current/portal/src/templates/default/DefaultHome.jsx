import { useState, useEffect } from 'react'
import { Link } from 'react-router-dom'
import { useStores, usePromotions, useFlyer } from '../../hooks/useApi'
import SectionHeader from '../../components/shared/SectionHeader'
import StoreCard from '../../components/shared/StoreCard'
import ProductCard from '../../components/shared/ProductCard'
import LoadingSpinner from '../../components/shared/LoadingSpinner'
import { IconArrowRight, IconCheck, IconShield, IconTruck, IconStore } from '../../components/shared/Icons'

export default function DefaultHome({ config }) {
  const { stores, loading: storesLoading } = useStores()
  const { promotions } = usePromotions()
  const { flyer } = useFlyer()
  const [currentSlide, setCurrentSlide] = useState(0)

  useEffect(() => {
    if (flyer.products?.length > 6) {
      const interval = setInterval(() => {
        setCurrentSlide(prev => (prev + 1) % Math.ceil(flyer.products.length / 6))
      }, 5000)
      return () => clearInterval(interval)
    }
  }, [flyer.products?.length])

  const accentColor = flyer.accent_color

  return (
    <div className="overflow-x-hidden">
      {/* Hero */}
      <section className="relative bg-gradient-to-br from-primary-600 via-primary-700 to-primary-800 text-white overflow-hidden">
        <div className="absolute inset-0">
          <div className="absolute top-0 right-0 w-[500px] h-[500px] bg-white/5 rounded-full -translate-y-1/2 translate-x-1/3 blur-3xl" />
          <div className="absolute bottom-0 left-0 w-[400px] h-[400px] bg-white/5 rounded-full translate-y-1/2 -translate-x-1/3 blur-3xl" />
        </div>
        <div className="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 lg:py-32">
          <div className="max-w-2xl">
            <h1 className="text-4xl sm:text-5xl lg:text-6xl font-bold tracking-tight leading-[1.1] animate-fade-in">
              {config?.hero?.title || 'Compra directo del mercado de abastos'}
            </h1>
            <p className="mt-5 text-lg lg:text-xl text-primary-100 leading-relaxed max-w-lg animate-fade-in" style={{ animationDelay: '100ms' }}>
              {config?.hero?.subtitle || 'Los mejores precios, la mejor calidad, directo a tu negocio o domicilio'}
            </p>
            <div className="mt-8 flex flex-col sm:flex-row gap-3 animate-fade-in" style={{ animationDelay: '200ms' }}>
              <Link
                to="/tiendas"
                className="inline-flex items-center justify-center gap-2 px-7 py-3.5 bg-white text-primary-700 rounded-xl font-semibold text-base hover:bg-gray-50 transition-all shadow-lg hover:shadow-xl"
              >
                {config?.hero?.cta_text || 'Explorar tiendas'}
                <IconArrowRight className="w-4 h-4" />
              </Link>
              <a
                href="#como-funciona"
                className="inline-flex items-center justify-center px-7 py-3.5 border-2 border-white/25 text-white rounded-xl font-semibold text-base hover:bg-white/10 transition-all"
              >
                Como funciona
              </a>
            </div>
          </div>
        </div>
      </section>

      {/* Featured Products */}
      {flyer.enabled && flyer.products?.length > 0 && (
        <section
          className="relative py-16 lg:py-20 overflow-hidden"
          style={accentColor
            ? { background: `linear-gradient(135deg, ${accentColor} 0%, ${accentColor}dd 100%)` }
            : { background: 'linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%)' }
          }
        >
          <div className="absolute inset-0 overflow-hidden">
            <div className="absolute -top-32 -right-32 w-64 h-64 rounded-full opacity-10" style={{ background: accentColor || '#e2e8f0' }} />
            <div className="absolute -bottom-32 -left-32 w-80 h-80 rounded-full opacity-10" style={{ background: accentColor || '#e2e8f0' }} />
          </div>

          <div className="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <SectionHeader
              title={flyer.title || 'Productos destacados'}
              subtitle={flyer.subtitle}
              linkTo="/productos"
              align="center"
              light={!!accentColor}
            />

            <div className="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
              {flyer.products.slice(currentSlide * 6, currentSlide * 6 + 6).map((product, i) => (
                <ProductCard
                  key={product.id}
                  product={product}
                  showPrice={config?.settings?.show_prices !== false}
                  index={i}
                />
              ))}
            </div>

            {flyer.products.length > 6 && (
              <div className="flex justify-center gap-2 mt-8" role="tablist" aria-label="Paginas de productos">
                {Array.from({ length: Math.ceil(flyer.products.length / 6) }).map((_, i) => (
                  <button
                    key={i}
                    onClick={() => setCurrentSlide(i)}
                    role="tab"
                    aria-selected={currentSlide === i}
                    aria-label={`Pagina ${i + 1}`}
                    className={`h-2.5 rounded-full transition-all duration-300 ${
                      currentSlide === i
                        ? `w-8 ${accentColor ? 'bg-white' : 'bg-primary-600'}`
                        : `w-2.5 ${accentColor ? 'bg-white/40 hover:bg-white/60' : 'bg-gray-300 hover:bg-gray-400'}`
                    }`}
                  />
                ))}
              </div>
            )}
          </div>
        </section>
      )}

      {/* Promotions */}
      {promotions.length > 0 && (
        <section className="py-16 lg:py-20 bg-white">
          <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <SectionHeader
              title="Ofertas destacadas"
              subtitle="Las mejores promociones de nuestros proveedores"
              linkTo="/promos"
            />
            <div className="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
              {promotions.slice(0, 6).map((promo) => (
                <a
                  key={promo.id}
                  href={promo.target_url}
                  className="group bg-white rounded-2xl shadow-sm hover:shadow-xl border border-gray-100 hover:border-primary-100 overflow-hidden transition-all duration-300"
                >
                  <div className="relative h-44 bg-gray-100 overflow-hidden">
                    {(promo.hero_image || promo.producto?.display_image) && (
                      <img
                        src={promo.hero_image || promo.producto?.display_image}
                        alt={promo.title}
                        className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                        loading="lazy"
                      />
                    )}
                    {promo.badge_text && (
                      <span className="absolute top-3 left-3 px-3 py-1 bg-amber-500 text-white text-xs font-bold rounded-full shadow-sm">
                        {promo.badge_text}
                      </span>
                    )}
                    {promo.discount_percent && (
                      <span className="absolute top-3 right-3 px-3 py-1 bg-red-500 text-white text-xs font-bold rounded-full shadow-sm">
                        -{promo.discount_percent}%
                      </span>
                    )}
                  </div>
                  <div className="p-4">
                    {promo.store && (
                      <p className="text-xs text-primary-600 font-semibold mb-1">{promo.store.nombre}</p>
                    )}
                    <h3 className="font-bold text-gray-900 group-hover:text-primary-600 transition-colors">{promo.title}</h3>
                    {config?.settings?.show_prices && promo.promo_price && (
                      <div className="flex items-baseline gap-2 mt-2">
                        <span className="text-xl font-bold text-primary-600">${promo.promo_price}</span>
                        {promo.original_price && (
                          <span className="text-sm text-gray-400 line-through">${promo.original_price}</span>
                        )}
                      </div>
                    )}
                  </div>
                </a>
              ))}
            </div>
          </div>
        </section>
      )}

      {/* How it Works */}
      <section id="como-funciona" className="py-16 lg:py-20 bg-gray-50">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <SectionHeader
            title="Como funciona"
            subtitle="Comprar en el mercado de abastos nunca fue tan facil"
            align="center"
          />
          <div className="grid md:grid-cols-3 gap-8 max-w-4xl mx-auto">
            {[
              { icon: IconStore, title: 'Elige tu proveedor', desc: 'Explora nuestro directorio de proveedores verificados del mercado' },
              { icon: IconCheck, title: 'Agrega al carrito', desc: 'Selecciona los productos que necesitas y la cantidad deseada' },
              { icon: IconTruck, title: 'Recibe tu pedido', desc: 'Recoge en tienda o recibe a domicilio el mismo dia' },
            ].map((step, i) => (
              <div key={i} className="text-center">
                <div className="w-16 h-16 bg-primary-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                  <step.icon className="w-7 h-7 text-primary-600" />
                </div>
                <h3 className="text-lg font-bold text-gray-900 mb-2">{step.title}</h3>
                <p className="text-gray-600 text-sm leading-relaxed">{step.desc}</p>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* Featured Stores */}
      {stores.length > 0 && (
        <section className="py-16 lg:py-20 bg-white">
          <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <SectionHeader
              title="Tiendas destacadas"
              subtitle="Proveedores verificados del mercado"
              linkTo="/tiendas"
            />
            <div className="grid sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
              {stores.slice(0, 8).map((store) => (
                <StoreCard key={store.id} store={store} />
              ))}
            </div>
          </div>
        </section>
      )}

      {/* Benefits */}
      <section className="py-16 lg:py-20 bg-gray-50">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <SectionHeader title="Por que elegirnos" align="center" />
          <div className="grid sm:grid-cols-2 lg:grid-cols-4 gap-6 max-w-5xl mx-auto">
            {[
              { icon: IconCheck, title: 'Precios de mayoreo', desc: 'Precios directos del mercado de abastos' },
              { icon: IconShield, title: 'Compra segura', desc: 'Multiples metodos de pago verificados' },
              { icon: IconTruck, title: 'Productos frescos', desc: 'Directo del proveedor a tu puerta' },
              { icon: IconStore, title: 'Soporte directo', desc: 'Comunicacion con proveedores por WhatsApp' },
            ].map((item, i) => (
              <div key={i} className="flex gap-4 items-start p-4">
                <div className="w-10 h-10 bg-primary-100 rounded-xl flex items-center justify-center flex-shrink-0">
                  <item.icon className="w-5 h-5 text-primary-600" />
                </div>
                <div>
                  <h3 className="font-semibold text-gray-900">{item.title}</h3>
                  <p className="text-sm text-gray-600 mt-1 leading-relaxed">{item.desc}</p>
                </div>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* FAQ */}
      <section id="faq" className="py-16 lg:py-20 bg-white">
        <div className="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
          <SectionHeader title="Preguntas frecuentes" align="center" />
          <div className="space-y-3">
            {[
              { q: 'Como puedo comprar?', a: 'Puedes comprar sin registrarte. Explora las tiendas, agrega productos al carrito y completa tu pedido.' },
              { q: 'Que metodos de pago aceptan?', a: 'Efectivo al recibir, transferencia bancaria y MercadoPago (tarjetas).' },
              { q: 'Hacen entregas a domicilio?', a: 'Depende de cada proveedor. Algunos ofrecen entrega a domicilio y otros solo recoleccion en tienda.' },
              { q: 'Como puedo ser proveedor?', a: 'Contactanos por WhatsApp y te ayudamos a configurar tu tienda en la plataforma.' },
            ].map((faq, i) => (
              <details key={i} className="group bg-gray-50 rounded-xl border border-gray-200 overflow-hidden">
                <summary className="p-4 cursor-pointer font-medium text-gray-900 flex justify-between items-center text-sm hover:bg-gray-100 transition-colors list-none [&::-webkit-details-marker]:hidden">
                  {faq.q}
                  <svg className="w-4 h-4 text-gray-400 group-open:rotate-180 transition-transform flex-shrink-0 ml-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 9l-7 7-7-7" />
                  </svg>
                </summary>
                <p className="px-4 pb-4 text-gray-600 text-sm leading-relaxed">{faq.a}</p>
              </details>
            ))}
          </div>
        </div>
      </section>

      {/* CTA */}
      <section className="py-20 bg-gradient-to-br from-primary-600 to-primary-800">
        <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
          <h2 className="text-3xl lg:text-4xl font-bold text-white tracking-tight">Listo para comprar?</h2>
          <p className="mt-4 text-lg text-primary-100">Explora las tiendas y encuentra lo que necesitas</p>
          <Link
            to="/tiendas"
            className="inline-flex items-center gap-2 mt-8 px-8 py-4 bg-white text-primary-700 rounded-xl font-bold text-base hover:bg-gray-50 transition-all shadow-lg hover:shadow-xl"
          >
            Ver tiendas disponibles
            <IconArrowRight className="w-4 h-4" />
          </Link>
        </div>
      </section>
    </div>
  )
}
