import { useState, useEffect } from 'react'
import { Link } from 'react-router-dom'
import { useStores, usePromotions, useFlyer } from '../../hooks/useApi'
import SectionHeader from '../../components/shared/SectionHeader'
import StoreCard from '../../components/shared/StoreCard'
import ProductCard from '../../components/shared/ProductCard'
import { IconArrowRight, IconCheck, IconShield, IconTruck, IconStore, IconStar, IconWhatsApp } from '../../components/shared/Icons'

export default function MarketHome({ config }) {
  const { stores } = useStores()
  const { promotions } = usePromotions()
  const { flyer } = useFlyer()
  const [currentSlide, setCurrentSlide] = useState(0)

  const featuredStores = stores.filter(s => s.is_featured)
  const accentColor = flyer.accent_color

  useEffect(() => {
    if (flyer.products?.length > 4) {
      const interval = setInterval(() => {
        setCurrentSlide(prev => (prev + 1) % Math.ceil(flyer.products.length / 4))
      }, 6000)
      return () => clearInterval(interval)
    }
  }, [flyer.products?.length])

  return (
    <div className="overflow-x-hidden">
      {/* Hero - Full-width image or gradient */}
      <section className="relative min-h-[480px] lg:min-h-[560px] flex items-center bg-gray-950 overflow-hidden">
        {/* Background pattern */}
        <div className="absolute inset-0">
          <div className="absolute inset-0 bg-gradient-to-br from-gray-900 via-gray-950 to-black" />
          <div className="absolute top-1/4 -right-20 w-[600px] h-[600px] bg-primary-600/10 rounded-full blur-[120px]" />
          <div className="absolute -bottom-20 -left-20 w-[400px] h-[400px] bg-primary-500/8 rounded-full blur-[100px]" />
          {/* Grid pattern overlay */}
          <div className="absolute inset-0 opacity-[0.03]" style={{
            backgroundImage: 'linear-gradient(rgba(255,255,255,.1) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,.1) 1px, transparent 1px)',
            backgroundSize: '60px 60px'
          }} />
        </div>

        <div className="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 lg:py-24 w-full">
          <div className="grid lg:grid-cols-2 gap-12 items-center">
            <div>
              <div className="inline-flex items-center gap-2 px-3.5 py-1.5 bg-primary-500/10 border border-primary-500/20 rounded-full text-primary-400 text-xs font-medium mb-6">
                <IconStar className="w-3.5 h-3.5" />
                Mercado digital verificado
              </div>
              <h1 className="text-4xl sm:text-5xl lg:text-[3.5rem] font-bold text-white tracking-tight leading-[1.1]">
                {config?.hero?.title || 'El mercado de abastos en tu mano'}
              </h1>
              <p className="mt-5 text-lg text-gray-400 leading-relaxed max-w-lg">
                {config?.hero?.subtitle || 'Conectamos proveedores del mercado con compradores. Precios de mayoreo, calidad garantizada.'}
              </p>
              <div className="mt-8 flex flex-col sm:flex-row gap-3">
                <Link
                  to="/tiendas"
                  className="inline-flex items-center justify-center gap-2 px-7 py-3.5 bg-primary-600 text-white rounded-xl font-semibold hover:bg-primary-500 transition-all shadow-lg shadow-primary-600/25"
                >
                  {config?.hero?.cta_text || 'Explorar tiendas'}
                  <IconArrowRight className="w-4 h-4" />
                </Link>
                <Link
                  to="/productos"
                  className="inline-flex items-center justify-center px-7 py-3.5 bg-white/5 border border-white/10 text-white rounded-xl font-semibold hover:bg-white/10 transition-all"
                >
                  Ver catalogo
                </Link>
              </div>
              {/* Trust badges */}
              <div className="mt-10 flex items-center gap-6 text-gray-500 text-sm">
                <div className="flex items-center gap-2">
                  <IconShield className="w-4 h-4 text-primary-500" />
                  <span>Compra segura</span>
                </div>
                <div className="flex items-center gap-2">
                  <IconTruck className="w-4 h-4 text-primary-500" />
                  <span>Entrega rapida</span>
                </div>
              </div>
            </div>

            {/* Right side: Featured stores mini-grid */}
            {featuredStores.length > 0 && (
              <div className="hidden lg:block">
                <div className="grid grid-cols-2 gap-3">
                  {featuredStores.slice(0, 4).map((store, i) => (
                    <a
                      key={store.id}
                      href={store.store_url}
                      className="group bg-white/5 backdrop-blur-sm border border-white/10 rounded-2xl p-5 hover:bg-white/10 hover:border-primary-500/30 transition-all duration-300"
                      style={{ animationDelay: `${i * 100}ms` }}
                    >
                      <div className="w-12 h-12 rounded-xl bg-white/10 flex items-center justify-center overflow-hidden mb-3">
                        {store.logo_url ? (
                          <img src={store.logo_url} alt={store.nombre} className="w-full h-full object-cover rounded-xl" loading="lazy" />
                        ) : (
                          <span className="text-xl font-bold text-primary-400">{store.nombre?.[0]}</span>
                        )}
                      </div>
                      <h3 className="font-semibold text-white text-sm group-hover:text-primary-400 transition-colors truncate">
                        {store.nombre}
                      </h3>
                      {store.descripcion && (
                        <p className="text-xs text-gray-500 mt-1 line-clamp-2">{store.descripcion}</p>
                      )}
                    </a>
                  ))}
                </div>
              </div>
            )}
          </div>
        </div>
      </section>

      {/* Stats bar */}
      <section className="bg-white border-b border-gray-100">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="grid grid-cols-2 lg:grid-cols-4 divide-x divide-gray-100">
            {[
              { value: `${stores.length}+`, label: 'Tiendas activas' },
              { value: 'Directo', label: 'Del proveedor' },
              { value: '24h', label: 'Entrega express' },
              { value: '100%', label: 'Verificados' },
            ].map((stat, i) => (
              <div key={i} className="py-5 lg:py-6 px-4 text-center">
                <div className="text-xl lg:text-2xl font-bold text-gray-900">{stat.value}</div>
                <div className="text-xs text-gray-500 mt-0.5">{stat.label}</div>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* Featured Products - Magazine layout */}
      {flyer.enabled && flyer.products?.length > 0 && (
        <section className="py-16 lg:py-20 bg-gray-50">
          <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <SectionHeader
              title={flyer.title || 'Seleccion del dia'}
              subtitle={flyer.subtitle || 'Productos destacados de nuestros proveedores'}
              linkTo="/productos"
            />
            <div className="grid grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-5">
              {flyer.products.slice(currentSlide * 4, currentSlide * 4 + 8).map((product, i) => (
                <ProductCard
                  key={product.id}
                  product={product}
                  showPrice={config?.settings?.show_prices !== false}
                  index={i}
                />
              ))}
            </div>

            {flyer.products.length > 8 && (
              <div className="flex justify-center gap-2 mt-8" role="tablist">
                {Array.from({ length: Math.ceil(flyer.products.length / 4) }).map((_, i) => (
                  <button
                    key={i}
                    onClick={() => setCurrentSlide(i)}
                    role="tab"
                    aria-selected={currentSlide === i}
                    aria-label={`Pagina ${i + 1}`}
                    className={`h-2 rounded-full transition-all duration-300 ${
                      currentSlide === i ? 'w-8 bg-primary-600' : 'w-2 bg-gray-300 hover:bg-gray-400'
                    }`}
                  />
                ))}
              </div>
            )}
          </div>
        </section>
      )}

      {/* Promotions - Horizontal scroll on mobile */}
      {promotions.length > 0 && (
        <section className="py-16 lg:py-20 bg-white">
          <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <SectionHeader
              title="Ofertas del mercado"
              subtitle="Aprovecha las promociones de nuestros proveedores"
              linkTo="/promos"
            />
            <div className="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
              {promotions.slice(0, 6).map((promo) => (
                <a
                  key={promo.id}
                  href={promo.target_url}
                  className="group relative bg-white rounded-2xl border border-gray-100 hover:border-primary-100 overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300"
                >
                  <div className="relative h-48 bg-gray-100 overflow-hidden">
                    {(promo.hero_image || promo.producto?.display_image) && (
                      <img
                        src={promo.hero_image || promo.producto?.display_image}
                        alt={promo.title}
                        className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                        loading="lazy"
                      />
                    )}
                    <div className="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent" />
                    <div className="absolute top-3 left-3 flex gap-2">
                      {promo.badge_text && (
                        <span className="px-2.5 py-1 bg-amber-500 text-white text-xs font-bold rounded-full">{promo.badge_text}</span>
                      )}
                      {promo.discount_percent && (
                        <span className="px-2.5 py-1 bg-red-500 text-white text-xs font-bold rounded-full">-{promo.discount_percent}%</span>
                      )}
                    </div>
                    {promo.store && (
                      <span className="absolute bottom-3 left-3 px-2.5 py-1 bg-white/90 backdrop-blur-sm text-gray-800 text-xs font-semibold rounded-full">
                        {promo.store.nombre}
                      </span>
                    )}
                  </div>
                  <div className="p-4">
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

      {/* Stores Directory */}
      {stores.length > 0 && (
        <section className="py-16 lg:py-20 bg-gray-50">
          <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <SectionHeader
              title="Directorio de tiendas"
              subtitle="Proveedores verificados del mercado de abastos"
              linkTo="/tiendas"
            />

            {/* Featured - cards */}
            {featuredStores.length > 0 && (
              <div className="grid sm:grid-cols-2 lg:grid-cols-3 gap-5 mb-8">
                {featuredStores.slice(0, 3).map(store => (
                  <StoreCard key={store.id} store={store} />
                ))}
              </div>
            )}

            {/* Rest - compact list */}
            {stores.filter(s => !s.is_featured).length > 0 && (
              <div className="grid sm:grid-cols-2 gap-3">
                {stores.filter(s => !s.is_featured).slice(0, 6).map(store => (
                  <StoreCard key={store.id} store={store} variant="compact" />
                ))}
              </div>
            )}

            <div className="text-center mt-10">
              <Link
                to="/tiendas"
                className="inline-flex items-center gap-2 px-6 py-3 bg-white border border-gray-200 rounded-xl font-medium text-sm text-gray-700 hover:border-primary-300 hover:text-primary-600 transition-all shadow-sm"
              >
                Ver todas las tiendas
                <IconArrowRight className="w-4 h-4" />
              </Link>
            </div>
          </div>
        </section>
      )}

      {/* How it works */}
      <section id="como-funciona" className="py-16 lg:py-20 bg-white">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <SectionHeader
            title="Como funciona"
            subtitle="Tres pasos simples para comprar en el mercado"
            align="center"
          />
          <div className="grid md:grid-cols-3 gap-8 max-w-4xl mx-auto">
            {[
              { step: '01', icon: IconStore, title: 'Elige tu proveedor', desc: 'Explora el directorio y encuentra al proveedor ideal para tu negocio' },
              { step: '02', icon: IconCheck, title: 'Agrega y ordena', desc: 'Selecciona productos, elige cantidad y completa tu pedido en minutos' },
              { step: '03', icon: IconTruck, title: 'Recibe rapido', desc: 'Recoge en tienda o recibe a domicilio el mismo dia' },
            ].map((item, i) => (
              <div key={i} className="relative text-center group">
                <div className="relative inline-flex">
                  <div className="w-16 h-16 bg-gray-100 group-hover:bg-primary-50 rounded-2xl flex items-center justify-center transition-colors">
                    <item.icon className="w-7 h-7 text-gray-600 group-hover:text-primary-600 transition-colors" />
                  </div>
                  <span className="absolute -top-2 -right-2 w-7 h-7 bg-primary-600 text-white text-xs font-bold rounded-full flex items-center justify-center shadow-sm">
                    {item.step}
                  </span>
                </div>
                <h3 className="text-lg font-bold text-gray-900 mt-4 mb-2">{item.title}</h3>
                <p className="text-gray-500 text-sm leading-relaxed">{item.desc}</p>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* FAQ */}
      <section id="faq" className="py-16 lg:py-20 bg-gray-50">
        <div className="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
          <SectionHeader title="Preguntas frecuentes" align="center" />
          <div className="space-y-3">
            {[
              { q: 'Necesito registrarme para comprar?', a: 'No es necesario. Puedes explorar y comprar como invitado en cualquier tienda del mercado.' },
              { q: 'Que metodos de pago aceptan?', a: 'Cada tienda maneja sus propios metodos. Generalmente aceptan efectivo, transferencia y MercadoPago.' },
              { q: 'Como funciona la entrega?', a: 'Depende de cada proveedor. Algunos ofrecen envio a domicilio y otros recoleccion en punto de venta.' },
              { q: 'Quiero vender en la plataforma', a: 'Contactanos por WhatsApp y te ayudamos a registrar tu negocio en el marketplace.' },
            ].map((faq, i) => (
              <details key={i} className="group bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm">
                <summary className="p-4 lg:p-5 cursor-pointer font-medium text-gray-900 flex justify-between items-center text-sm hover:bg-gray-50 transition-colors list-none [&::-webkit-details-marker]:hidden">
                  {faq.q}
                  <svg className="w-4 h-4 text-gray-400 group-open:rotate-180 transition-transform flex-shrink-0 ml-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 9l-7 7-7-7" />
                  </svg>
                </summary>
                <p className="px-4 pb-4 lg:px-5 lg:pb-5 text-gray-600 text-sm leading-relaxed">{faq.a}</p>
              </details>
            ))}
          </div>
        </div>
      </section>

      {/* CTA */}
      <section className="relative py-20 lg:py-24 bg-gray-950 overflow-hidden">
        <div className="absolute inset-0">
          <div className="absolute top-0 left-1/2 w-[600px] h-[600px] bg-primary-600/10 rounded-full -translate-x-1/2 -translate-y-1/2 blur-[120px]" />
        </div>
        <div className="relative max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
          <h2 className="text-3xl lg:text-4xl font-bold text-white tracking-tight">
            Listo para comprar directo del mercado?
          </h2>
          <p className="mt-4 text-lg text-gray-400 max-w-xl mx-auto">
            Explora proveedores verificados y encuentra los mejores precios de mayoreo
          </p>
          <div className="mt-8 flex flex-col sm:flex-row gap-3 justify-center">
            <Link
              to="/tiendas"
              className="inline-flex items-center justify-center gap-2 px-8 py-4 bg-primary-600 text-white rounded-xl font-bold text-base hover:bg-primary-500 transition-all shadow-lg shadow-primary-600/25"
            >
              Ver tiendas
              <IconArrowRight className="w-4 h-4" />
            </Link>
            {config?.developer?.whatsapp && (
              <a
                href={`https://wa.me/52${config.developer.whatsapp}?text=Hola, me interesa la plataforma`}
                target="_blank"
                rel="noopener noreferrer"
                className="inline-flex items-center justify-center gap-2 px-8 py-4 bg-white/5 border border-white/10 text-white rounded-xl font-semibold hover:bg-white/10 transition-all"
              >
                <IconWhatsApp className="w-4 h-4" />
                Contactar
              </a>
            )}
          </div>
        </div>
      </section>
    </div>
  )
}
