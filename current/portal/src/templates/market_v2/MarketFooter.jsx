import { Link } from 'react-router-dom'
import { IconMail, IconWhatsApp, IconStore } from '../../components/shared/Icons'

export default function MarketFooter({ config }) {
  const dev = config?.developer || {}

  return (
    <footer className="bg-gray-950 text-white">
      {/* Main */}
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14 lg:py-20">
        <div className="grid lg:grid-cols-12 gap-10">
          {/* Brand */}
          <div className="lg:col-span-5">
            <div className="flex items-center gap-2.5 mb-4">
              <div className="w-8 h-8 bg-gradient-to-br from-primary-500 to-primary-600 rounded-lg flex items-center justify-center">
                <IconStore className="w-4 h-4 text-white" />
              </div>
              <span className="font-bold text-lg tracking-tight">{config?.portal_name || 'Central de Abastos'}</span>
            </div>
            {config?.portal_description && (
              <p className="text-gray-400 text-sm leading-relaxed max-w-sm">{config.portal_description}</p>
            )}
          </div>

          {/* Nav */}
          <div className="lg:col-span-2">
            <h4 className="font-semibold text-xs uppercase tracking-wider text-gray-500 mb-4">Portal</h4>
            <ul className="space-y-2.5">
              <li><Link to="/" className="text-gray-400 hover:text-white text-sm transition-colors">Inicio</Link></li>
              <li><Link to="/tiendas" className="text-gray-400 hover:text-white text-sm transition-colors">Tiendas</Link></li>
              <li><Link to="/productos" className="text-gray-400 hover:text-white text-sm transition-colors">Productos</Link></li>
              <li><Link to="/promos" className="text-gray-400 hover:text-white text-sm transition-colors">Ofertas</Link></li>
            </ul>
          </div>

          {/* Info */}
          <div className="lg:col-span-2">
            <h4 className="font-semibold text-xs uppercase tracking-wider text-gray-500 mb-4">Informacion</h4>
            <ul className="space-y-2.5">
              <li><a href="#como-funciona" className="text-gray-400 hover:text-white text-sm transition-colors">Como funciona</a></li>
              <li><a href="#faq" className="text-gray-400 hover:text-white text-sm transition-colors">Preguntas frecuentes</a></li>
            </ul>
          </div>

          {/* Contact */}
          <div className="lg:col-span-3">
            <h4 className="font-semibold text-xs uppercase tracking-wider text-gray-500 mb-4">Contacto</h4>
            <ul className="space-y-3">
              {dev.email && (
                <li>
                  <a href={`mailto:${dev.email}`} className="text-gray-400 hover:text-white text-sm transition-colors flex items-center gap-2.5">
                    <IconMail className="w-4 h-4 flex-shrink-0 text-gray-500" />
                    {dev.email}
                  </a>
                </li>
              )}
              {dev.whatsapp && (
                <li>
                  <a href={`https://wa.me/52${dev.whatsapp}`} target="_blank" rel="noopener noreferrer"
                     className="inline-flex items-center gap-2 px-4 py-2 bg-green-600/20 text-green-400 rounded-lg text-sm font-medium hover:bg-green-600/30 transition-colors">
                    <IconWhatsApp className="w-4 h-4" />
                    WhatsApp
                  </a>
                </li>
              )}
            </ul>
          </div>
        </div>
      </div>

      {/* Bottom */}
      <div className="border-t border-gray-800/50">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 flex flex-col sm:flex-row justify-between items-center gap-3">
          <p className="text-gray-600 text-xs">
            &copy; {new Date().getFullYear()} {config?.portal_name}. Todos los derechos reservados.
          </p>
          {dev.name && (
            <p className="text-gray-600 text-xs">
              Desarrollado por{' '}
              <a href={dev.url} target="_blank" rel="noopener noreferrer" className="text-primary-400 hover:text-primary-300 transition-colors">
                {dev.name}
              </a>
            </p>
          )}
        </div>
      </div>
    </footer>
  )
}
