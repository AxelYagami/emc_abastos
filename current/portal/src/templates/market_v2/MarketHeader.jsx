import { useState } from 'react'
import { Link, useLocation } from 'react-router-dom'
import { IconHome, IconStore, IconProducts, IconTag, IconSparkles, IconMenu, IconClose, IconSearch } from '../../components/shared/Icons'

const navItems = [
  { to: '/', label: 'Inicio', icon: IconHome },
  { to: '/tiendas', label: 'Tiendas', icon: IconStore },
  { to: '/productos', label: 'Productos', icon: IconProducts },
  { to: '/promos', label: 'Ofertas', icon: IconTag },
]

export default function MarketHeader({ config, onOpenAssistant }) {
  const [mobileMenuOpen, setMobileMenuOpen] = useState(false)
  const location = useLocation()

  const isActive = (path) => location.pathname === path

  return (
    <header className="sticky top-0 z-50">
      {/* Top bar */}
      <div className="bg-gray-900 text-gray-400 text-xs">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between h-8">
          <span className="hidden sm:block">{config?.portal_tagline || 'Tu mercado de abastos en linea'}</span>
          <div className="flex items-center gap-4 ml-auto">
            {config?.settings?.ai_assistant_enabled !== false && (
              <button
                onClick={onOpenAssistant}
                className="flex items-center gap-1.5 hover:text-white transition-colors"
                aria-label="Asistente IA"
              >
                <IconSparkles className="w-3.5 h-3.5" />
                <span className="hidden sm:inline">Ayuda IA</span>
              </button>
            )}
          </div>
        </div>
      </div>

      {/* Main nav */}
      <div className="bg-white border-b border-gray-200 shadow-sm">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="flex items-center justify-between h-14">
            {/* Logo */}
            <Link to="/" className="flex items-center gap-2.5 group flex-shrink-0">
              <div className="w-8 h-8 bg-gradient-to-br from-primary-500 to-primary-700 rounded-lg flex items-center justify-center">
                <IconStore className="w-4.5 h-4.5 text-white" />
              </div>
              <span className="font-bold text-gray-900 text-base tracking-tight hidden sm:block">
                {config?.portal_name || 'Central de Abastos'}
              </span>
            </Link>

            {/* Desktop Nav */}
            <nav className="hidden lg:flex items-center" aria-label="Principal">
              {navItems.map((item) => (
                <Link
                  key={item.to}
                  to={item.to}
                  className={`relative px-4 py-4 text-sm font-medium transition-colors ${
                    isActive(item.to)
                      ? 'text-primary-600'
                      : 'text-gray-600 hover:text-gray-900'
                  }`}
                  aria-current={isActive(item.to) ? 'page' : undefined}
                >
                  {item.label}
                  {isActive(item.to) && (
                    <span className="absolute bottom-0 left-4 right-4 h-0.5 bg-primary-600 rounded-full" />
                  )}
                </Link>
              ))}
            </nav>

            {/* Right */}
            <div className="flex items-center gap-2">
              <Link
                to="/productos"
                className="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-colors lg:hidden"
                aria-label="Buscar"
              >
                <IconSearch className="w-5 h-5" />
              </Link>

              <button
                onClick={() => setMobileMenuOpen(!mobileMenuOpen)}
                className="lg:hidden p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors"
                aria-expanded={mobileMenuOpen}
                aria-label={mobileMenuOpen ? 'Cerrar menu' : 'Abrir menu'}
              >
                {mobileMenuOpen ? <IconClose /> : <IconMenu />}
              </button>
            </div>
          </div>
        </div>

        {/* Mobile */}
        {mobileMenuOpen && (
          <div className="lg:hidden border-t border-gray-200 bg-white animate-fade-in">
            <nav className="max-w-7xl mx-auto px-4 py-3" aria-label="Menu movil">
              {navItems.map((item) => (
                <Link
                  key={item.to}
                  to={item.to}
                  onClick={() => setMobileMenuOpen(false)}
                  className={`flex items-center gap-3 px-3 py-3 rounded-lg font-medium text-sm transition-colors ${
                    isActive(item.to)
                      ? 'bg-primary-50 text-primary-700'
                      : 'text-gray-700 hover:bg-gray-50'
                  }`}
                >
                  <item.icon className="w-5 h-5" />
                  {item.label}
                </Link>
              ))}
            </nav>
          </div>
        )}
      </div>
    </header>
  )
}
