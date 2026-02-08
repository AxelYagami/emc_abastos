import { useState } from 'react'
import { Link, useLocation } from 'react-router-dom'
import { IconHome, IconStore, IconTag, IconSparkles, IconMenu, IconClose } from '../../components/shared/Icons'

const navItems = [
  { to: '/', label: 'Inicio', icon: IconHome },
  { to: '/tiendas', label: 'Tiendas', icon: IconStore },
  { to: '/promos', label: 'Promos', icon: IconTag },
]

export default function DefaultHeader({ config, onOpenAssistant }) {
  const [mobileMenuOpen, setMobileMenuOpen] = useState(false)
  const location = useLocation()

  const isActive = (path) => location.pathname === path

  return (
    <header className="bg-white/95 backdrop-blur-md border-b border-gray-200/80 sticky top-0 z-50 shadow-sm">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="flex justify-between items-center h-16">
          {/* Logo */}
          <Link to="/" className="flex items-center gap-3 group flex-shrink-0">
            <div className="w-9 h-9 bg-gradient-to-br from-primary-500 to-primary-700 rounded-xl flex items-center justify-center shadow-md group-hover:shadow-lg transition-shadow">
              <IconStore className="w-5 h-5 text-white" />
            </div>
            <span className="text-lg font-bold text-gray-900 hidden sm:block tracking-tight">
              {config?.portal_name || 'Central de Abastos'}
            </span>
          </Link>

          {/* Desktop Navigation */}
          <nav className="hidden lg:flex items-center gap-1" aria-label="Principal">
            {navItems.map((item) => (
              <Link
                key={item.to}
                to={item.to}
                className={`flex items-center gap-2 px-4 py-2 rounded-lg font-medium text-sm transition-all duration-200 ${
                  isActive(item.to)
                    ? 'bg-primary-50 text-primary-700'
                    : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100'
                }`}
                aria-current={isActive(item.to) ? 'page' : undefined}
              >
                <item.icon className="w-4 h-4" />
                <span>{item.label}</span>
              </Link>
            ))}
          </nav>

          {/* Right Actions */}
          <div className="flex items-center gap-2">
            {config?.settings?.ai_assistant_enabled !== false && (
              <button
                onClick={onOpenAssistant}
                className="flex items-center gap-2 px-3 py-2 text-primary-600 hover:bg-primary-50 rounded-lg font-medium text-sm transition-colors"
                aria-label="Abrir asistente IA"
              >
                <IconSparkles className="w-4 h-4" />
                <span className="hidden md:inline">Ayuda IA</span>
              </button>
            )}

            {/* Login button */}
            <a
              href="/login"
              className="hidden sm:flex items-center gap-2 px-4 py-2 bg-gray-900 text-white rounded-lg font-medium text-sm hover:bg-gray-800 transition-colors"
              data-testid="header-login-btn"
            >
              Ingreso
            </a>

            {/* Mobile menu */}
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

        {/* Mobile Navigation */}
        {mobileMenuOpen && (
          <nav className="lg:hidden py-3 border-t border-gray-200 animate-fade-in" aria-label="Menu movil">
            <div className="flex flex-col gap-1">
              {navItems.map((item) => (
                <Link
                  key={item.to}
                  to={item.to}
                  onClick={() => setMobileMenuOpen(false)}
                  className={`flex items-center gap-3 px-4 py-3 rounded-lg font-medium transition-colors ${
                    isActive(item.to)
                      ? 'bg-primary-50 text-primary-700'
                      : 'text-gray-700 hover:bg-gray-50'
                  }`}
                >
                  <item.icon className="w-5 h-5" />
                  <span>{item.label}</span>
                </Link>
              ))}
              <hr className="my-2 border-gray-200" />
              <a
                href="/login"
                className="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-gray-50 rounded-lg font-medium"
              >
                Ingreso
              </a>
            </div>
          </nav>
        )}
      </div>
    </header>
  )
}
