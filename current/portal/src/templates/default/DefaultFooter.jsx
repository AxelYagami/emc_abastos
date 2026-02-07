import { Link } from 'react-router-dom'
import { IconMail, IconWhatsApp } from '../../components/shared/Icons'

export default function DefaultFooter({ config }) {
  const dev = config?.developer || {}

  return (
    <footer className="bg-gray-900 text-white" id="contacto">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-16">
        <div className="grid md:grid-cols-4 gap-8 lg:gap-12">
          {/* Brand */}
          <div className="md:col-span-2">
            <h3 className="text-xl font-bold mb-3 tracking-tight">{config?.portal_name || 'Central de Abastos'}</h3>
            {config?.portal_description && (
              <p className="text-gray-400 leading-relaxed max-w-md">{config.portal_description}</p>
            )}
          </div>

          {/* Links */}
          <div>
            <h4 className="font-semibold mb-4 text-gray-300 uppercase text-xs tracking-wider">Navegacion</h4>
            <ul className="space-y-2.5">
              <li><Link to="/" className="text-gray-400 hover:text-white transition-colors text-sm">Inicio</Link></li>
              <li><Link to="/tiendas" className="text-gray-400 hover:text-white transition-colors text-sm">Tiendas</Link></li>
              <li><Link to="/productos" className="text-gray-400 hover:text-white transition-colors text-sm">Productos</Link></li>
              <li><Link to="/promos" className="text-gray-400 hover:text-white transition-colors text-sm">Promos</Link></li>
            </ul>
          </div>

          {/* Contact */}
          <div>
            <h4 className="font-semibold mb-4 text-gray-300 uppercase text-xs tracking-wider">Contacto</h4>
            <ul className="space-y-2.5">
              {dev.email && (
                <li>
                  <a href={`mailto:${dev.email}`} className="text-gray-400 hover:text-white transition-colors text-sm flex items-center gap-2">
                    <IconMail className="w-4 h-4 flex-shrink-0" />
                    {dev.email}
                  </a>
                </li>
              )}
              {dev.whatsapp && (
                <li>
                  <a href={`https://wa.me/52${dev.whatsapp}`} target="_blank" rel="noopener noreferrer" className="text-gray-400 hover:text-white transition-colors text-sm flex items-center gap-2">
                    <IconWhatsApp className="w-4 h-4 flex-shrink-0" />
                    WhatsApp
                  </a>
                </li>
              )}
            </ul>
          </div>
        </div>

        <div className="border-t border-gray-800 mt-10 pt-8 flex flex-col sm:flex-row justify-between items-center gap-4">
          <p className="text-gray-500 text-sm">
            &copy; {new Date().getFullYear()} {config?.portal_name}. Todos los derechos reservados.
          </p>
          {dev.name && (
            <p className="text-gray-500 text-sm">
              Desarrollado por{' '}
              <a href={dev.url} target="_blank" rel="noopener noreferrer" className="text-primary-400 hover:text-primary-300 transition-colors font-medium">
                {dev.name}
              </a>
            </p>
          )}
        </div>
      </div>
    </footer>
  )
}
