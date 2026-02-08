import { useState, useEffect, useMemo } from 'react'
import { Link } from 'react-router-dom'
import { useStores, usePromotions, useFlyer } from '../../hooks/useApi'
import SectionHeader from '../../components/shared/SectionHeader'
import StoreCard from '../../components/shared/StoreCard'
import ProductCard from '../../components/shared/ProductCard'
import LoadingSpinner, { ProductCardSkeleton, StoreCardSkeleton } from '../../components/shared/LoadingSpinner'
import { IconArrowRight, IconCheck, IconShield, IconTruck, IconStore, IconStar, IconWhatsApp } from '../../components/shared/Icons'

export default function DefaultHome({ config }) {
  const { stores, loading: storesLoading } = useStores()
  const { promotions } = usePromotions()
  const { flyer } = useFlyer()
  const [currentSlide, setCurrentSlide] = useState(0)

  // Featured stores filter
  const featuredStores = useMemo(() => 
    stores.filter(s => s.is_featured).slice(0, 8), 
    [stores]
  )

  // Auto-rotate products carousel
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
      {/* ═══════════════════════════════════════════════════════════════
          HERO SECTION - Premium gradient with animated shapes
          ═══════════════════════════════════════════════════════════════ */}
      <section className="relative bg-gradient-to-br from-primary-600 via-primary-700 to-primary-800 text-white overflow-hidden">
        {/* Animated background shapes */}
        <div className="absolute inset-0 overflow-hidden">
          <div className="absolute top-0 right-0 w-[600px] h-[600px] bg-white/5 rounded-full -translate-y-1/2 translate-x-1/4 blur-3xl animate-pulse-soft" />
          <div className="absolute bottom-0 left-0 w-[500px] h-[500px] bg-white/5 rounded-full translate-y-1/3 -translate-x-1/4 blur-3xl animate-pulse-soft" style={{ animationDelay: '1.5s' }} />
          <div className="absolute top-1/2 left-1/2 w-[300px] h-[300px] bg-primary-400/10 rounded-full -translate-x-1/2 -translate-y-1/2 blur-2xl" />
        </div>
        
        {/* Grid pattern subtle */}
        <div className="absolute inset-0 opacity-[0.03]" style={{
          backgroundImage: 'linear-gradient(rgba(255,255,255,.5) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,.5) 1px, transparent 1px)',
          backgroundSize: '48px 48px'
        }} />

        <div className="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 lg:py-32">
          <div className="max-w-2xl animate-fade-in">
            {/* Badge */}
            <div className="inline-flex items-center gap-2 px-4 py-2 bg-white/10 backdrop-blur-sm border border-white/20 rounded-full text-sm font-medium mb-6">
              <IconStar className="w-4 h-4 text-amber-300" />
              <span>Mercado de abastos digital</span>
            </div>
            
            <h1 className="text-4xl sm:text-5xl lg:text-6xl font-bold tracking-tight leading-[1.1] text-balance">
              {config?.hero?.title || 'Compra directo del mercado de abastos'}
            </h1>
            <p className="mt-6 text-lg lg:text-xl text-primary-100 leading-relaxed max-w-lg">
              {config?.hero?.subtitle || 'Los mejores precios, la mejor calidad, directo a tu negocio o domicilio'}
            </p>
            
            <div className="mt-8 flex flex-col sm:flex-row gap-3">
              <Link
                to="/tiendas"
                className="inline-flex items-center justify-center gap-2 px-7 py-4 bg-white text-primary-700 rounded-xl font-bold text-base hover:bg-gray-50 transition-all shadow-lg hover:shadow-xl btn-press"
                data-testid="hero-cta-primary"
              >
                {config?.hero?.cta_text || 'Explorar tiendas'}
                <IconArrowRight className="w-4 h-4" />
              </Link>
              <a
                href="#como-funciona"
                className="inline-flex items-center justify-center px-7 py-4 border-2 border-white/25 text-white rounded-xl font-semibold text-base hover:bg-white/10 transition-all btn-press"
              >
                Como funciona
              </a>
            </div>

            {/* Trust indicators */}
            <div className="mt-10 flex flex-wrap items-center gap-6 text-primary-200 text-sm">
              <div className="flex items-center gap-2">
                <IconShield className="w-5 h-5" />
                <span>Compra segura</span>
              </div>
              <div className="flex items-center gap-2">
                <IconTruck className="w-5 h-5" />
                <span>Envio rapido</span>
              </div>
              <div className="flex items-center gap-2">
                <IconCheck className="w-5 h-5" />
                <span>Proveedores verificados</span>
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* ═══════════════════════════════════════════════════════════════
          FEATURED PRODUCTS - With flash animation
          ═══════════════════════════════════════════════════════════════ */}
      {flyer.enabled && flyer.products?.length > 0 && (
        <section
          className="relative py-16 lg:py-24 overflow-hidden"
          style={accentColor
            ? { background: `linear-gradient(135deg, ${accentColor} 0%, ${accentColor}dd 100%)` }
            : { background: 'linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%)' }
          }
        >
          {/* Decorative blurs */}
          <div className="absolute inset-0 overflow-hidden pointer-events-none">
            <div className="absolute -top-32 -right-32 w-64 h-64 rounded-full opacity-20 blur-3xl" 
              style={{ background: accentColor || 'rgb(var(--color-primary))' }} />
            <div className="absolute -bottom-32 -left-32 w-80 h-80 rounded-full opacity-15 blur-3xl" 
              style={{ background: accentColor || 'rgb(var(--color-primary))' }} />
          </div>

          <div className="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <SectionHeader
              title={flyer.title || 'Productos destacados'}
              subtitle={flyer.subtitle}
              linkTo="/productos"
              align="center"
              light={!!accentColor}
              size="large"
            />

            <div className="grid sm:grid-cols-2 lg:grid-cols-3 gap-5 lg:gap-6 stagger-children">
              {flyer.products.slice(currentSlide * 6, currentSlide * 6 + 6).map((product, i) => (
                <ProductCard
                  key={product.id}
                  product={product}
                  showPrice={config?.settings?.show_prices !== false}
                  index={i}
                  featured={i === 0}
                />
              ))}
            </div>

            {/* Pagination dots */}
            {flyer.products.length > 6 && (
              <div className="flex justify-center gap-2 mt-10" role="tablist" aria-label="Paginas de productos">
                {Array.from({ length: Math.ceil(flyer.products.length / 6) }).map((_, i) => (
                  <button
                    key={i}
                    onClick={() => setCurrentSlide(i)}
                    role="tab"
                    aria-selected={currentSlide === i}
                    aria-label={`Pagina ${i + 1}`}
                    className={`h-2.5 rounded-full transition-all duration-300 btn-press ${
                      currentSlide === i
                        ? `w-10 ${accentColor ? 'bg-white' : 'bg-primary-600'}`
                        : `w-2.5 ${accentColor ? 'bg-white/40 hover:bg-white/60' : 'bg-gray-300 hover:bg-gray-400'}`
                    }`}
                  />
                ))}
              </div>
            )}
          </div>
        </section>
      )}

      {/* ═══════════════════════════════════════════════════════════════
          PROMOTIONS SECTION
          ═══════════════════════════════════════════════════════════════ */}
      {promotions.length > 0 && (
        <section className="py-16 lg:py-24 bg-white">
          <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <SectionHeader
              title="Ofertas destacadas"
              subtitle="Las mejores promociones de nuestros proveedores"
              linkTo="/promos"
            />
            <div className="grid sm:grid-cols-2 lg:grid-cols-3 gap-5 lg:gap-6 stagger-children">
              {promotions.slice(0, 6).map((promo, i) => (
                <a
                  key={promo.id}
                  href={promo.target_url}
                  className="group card-premium overflow-hidden"
                  data-testid={`promo-card-${promo.id}`}
                >
                  <div className="relative h-48 bg-gray-100 overflow-hidden img-container">
                    {(promo.hero_image || promo.producto?.display_image) && (
                      <img
                        src={promo.hero_image || promo.producto?.display_image}
                        alt={promo.title}
                        className="w-full h-full object-cover"
                        loading="lazy"
                      />
                    )}
                    <div className="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-transparent" />
                    
                    {/* Badges */}
                    <div className="absolute top-3 left-3 flex gap-2">
                      {promo.badge_text && (
                        <span className="badge badge-featured">{promo.badge_text}</span>
                      )}
                      {promo.discount_percent && (
                        <span className="badge badge-promo">-{promo.discount_percent}%</span>
                      )}
                    </div>
                    
                    {promo.store && (
                      <span className="absolute bottom-3 left-3 badge glass text-gray-800">
                        {promo.store.nombre}
                      </span>
                    )}
                  </div>
                  <div className="p-5">
                    <h3 className="font-bold text-gray-900 text-lg group-hover:text-primary-600 transition-colors">{promo.title}</h3>
                    {config?.settings?.show_prices && promo.promo_price && (
                      <div className="flex items-baseline gap-3 mt-3">
                        <span className="text-2xl font-bold text-primary-600">${promo.promo_price}</span>
                        {promo.original_price && (
                          <span className="text-base text-gray-400 line-through">${promo.original_price}</span>
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

      {/* ═══════════════════════════════════════════════════════════════
          HOW IT WORKS
          ═══════════════════════════════════════════════════════════════ */}
      <section id="como-funciona" className="py-16 lg:py-24 bg-gray-50">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <SectionHeader
            title="Como funciona"
            subtitle="Comprar en el mercado de abastos nunca fue tan facil"
            align="center"
            size="large"
          />
          <div className="grid md:grid-cols-3 gap-8 lg:gap-12 max-w-4xl mx-auto stagger-children">
            {[
              { icon: IconStore, step: '01', title: 'Elige tu proveedor', desc: 'Explora nuestro directorio de proveedores verificados del mercado de abastos' },
              { icon: IconCheck, step: '02', title: 'Agrega al carrito', desc: 'Selecciona los productos que necesitas y la cantidad deseada' },
              { icon: IconTruck, step: '03', title: 'Recibe tu pedido', desc: 'Recoge en tienda o recibe a domicilio el mismo dia' },
            ].map((item, i) => (
              <div key={i} className="text-center group">
                <div className="relative inline-flex mb-5">
                  <div className="w-20 h-20 bg-white rounded-2xl shadow-soft flex items-center justify-center group-hover:shadow-soft-lg group-hover:scale-105 transition-all">
                    <item.icon className="w-9 h-9 text-primary-600" />
                  </div>
                  <span className="absolute -top-2 -right-2 w-8 h-8 bg-primary-600 text-white text-xs font-bold rounded-full flex items-center justify-center shadow-md">
                    {item.step}
                  </span>
                </div>
                <h3 className="text-xl font-bold text-gray-900 mb-3">{item.title}</h3>
                <p className="text-gray-500 leading-relaxed">{item.desc}</p>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* ═══════════════════════════════════════════════════════════════
          FEATURED STORES
          ═══════════════════════════════════════════════════════════════ */}
      {stores.length > 0 && (
        <section className="py-16 lg:py-24 bg-white">
          <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <SectionHeader
              title="Tiendas destacadas"
              subtitle="Proveedores verificados del mercado"
              linkTo="/tiendas"
            />
            
            {storesLoading ? (
              <div className="grid sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5 lg:gap-6">
                {[...Array(4)].map((_, i) => <StoreCardSkeleton key={i} />)}
              </div>
            ) : (
              <div className="grid sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5 lg:gap-6 stagger-children">
                {stores.slice(0, 8).map((store) => (
                  <StoreCard key={store.id} store={store} />
                ))}
              </div>
            )}
          </div>
        </section>
      )}

      {/* ═══════════════════════════════════════════════════════════════
          BENEFITS / WHY US
          ═══════════════════════════════════════════════════════════════ */}
      <section className="py-16 lg:py-24 bg-gray-50">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <SectionHeader title="Por que elegirnos" align="center" />
          <div className="grid sm:grid-cols-2 lg:grid-cols-4 gap-6 max-w-5xl mx-auto stagger-children">
            {[
              { icon: IconCheck, title: 'Precios de mayoreo', desc: 'Precios directos del mercado de abastos sin intermediarios' },
              { icon: IconShield, title: 'Compra segura', desc: 'Multiples metodos de pago verificados y seguros' },
              { icon: IconTruck, title: 'Productos frescos', desc: 'Directo del proveedor a tu puerta, siempre fresco' },
              { icon: IconStore, title: 'Soporte directo', desc: 'Comunicacion directa con proveedores via WhatsApp' },
            ].map((item, i) => (
              <div key={i} className="flex gap-4 items-start p-5 bg-white rounded-2xl shadow-soft hover:shadow-soft-lg transition-shadow">
                <div className="w-12 h-12 bg-primary-100 rounded-xl flex items-center justify-center flex-shrink-0">
                  <item.icon className="w-6 h-6 text-primary-600" />
                </div>
                <div>
                  <h3 className="font-bold text-gray-900">{item.title}</h3>
                  <p className="text-sm text-gray-500 mt-1 leading-relaxed">{item.desc}</p>
                </div>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* ═══════════════════════════════════════════════════════════════
          FAQ SECTION
          ═══════════════════════════════════════════════════════════════ */}
      <section id="faq" className="py-16 lg:py-24 bg-white">
        <div className="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
          <SectionHeader title="Preguntas frecuentes" align="center" />
          <div className="space-y-3 stagger-children">
            {[
              { q: 'Como puedo comprar?', a: 'Puedes comprar sin registrarte. Explora las tiendas, agrega productos al carrito y completa tu pedido facilmente.' },
              { q: 'Que metodos de pago aceptan?', a: 'Efectivo al recibir, transferencia bancaria y MercadoPago (tarjetas de credito y debito).' },
              { q: 'Hacen entregas a domicilio?', a: 'Depende de cada proveedor. Algunos ofrecen entrega a domicilio y otros solo recoleccion en tienda.' },
              { q: 'Como puedo ser proveedor?', a: 'Contactanos por WhatsApp y te ayudamos a configurar tu tienda en la plataforma rapidamente.' },
            ].map((faq, i) => (
              <details key={i} className="group card-premium overflow-hidden">
                <summary className="p-5 cursor-pointer font-semibold text-gray-900 flex justify-between items-center hover:bg-gray-50 transition-colors list-none [&::-webkit-details-marker]:hidden">
                  {faq.q}
                  <svg className="w-5 h-5 text-gray-400 group-open:rotate-180 transition-transform flex-shrink-0 ml-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 9l-7 7-7-7" />
                  </svg>
                </summary>
                <p className="px-5 pb-5 text-gray-600 leading-relaxed">{faq.a}</p>
              </details>
            ))}
          </div>
        </div>
      </section>

      {/* ═══════════════════════════════════════════════════════════════
          CTA SECTION
          ═══════════════════════════════════════════════════════════════ */}
      <section className="relative py-20 lg:py-28 bg-gradient-to-br from-primary-600 via-primary-700 to-primary-800 overflow-hidden">
        {/* Background decoration */}
        <div className="absolute inset-0 overflow-hidden pointer-events-none">
          <div className="absolute top-1/2 left-1/2 w-[800px] h-[800px] bg-primary-500/20 rounded-full -translate-x-1/2 -translate-y-1/2 blur-[120px]" />
        </div>
        
        <div className="relative max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
          <h2 className="text-3xl lg:text-5xl font-bold text-white tracking-tight text-balance">
            Listo para comprar?
          </h2>
          <p className="mt-5 text-lg lg:text-xl text-primary-100 max-w-xl mx-auto">
            Explora las tiendas y encuentra lo que necesitas al mejor precio
          </p>
          <div className="mt-10 flex flex-col sm:flex-row gap-4 justify-center">
            <Link
              to="/tiendas"
              className="inline-flex items-center justify-center gap-2 px-8 py-4 bg-white text-primary-700 rounded-xl font-bold text-base hover:bg-gray-50 transition-all shadow-lg hover:shadow-xl btn-press"
              data-testid="cta-explore-stores"
            >
              Ver tiendas disponibles
              <IconArrowRight className="w-5 h-5" />
            </Link>
            {config?.developer?.whatsapp && (
              <a
                href={`https://wa.me/52${config.developer.whatsapp}?text=Hola, me interesa el portal de abastos`}
                target="_blank"
                rel="noopener noreferrer"
                className="inline-flex items-center justify-center gap-2 px-8 py-4 bg-white/10 backdrop-blur-sm border border-white/25 text-white rounded-xl font-semibold hover:bg-white/20 transition-all btn-press"
              >
                <IconWhatsApp className="w-5 h-5" />
                Contactar
              </a>
            )}
          </div>
        </div>
      </section>
    </div>
  )
}
