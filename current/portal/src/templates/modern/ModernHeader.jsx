import { useState } from 'react'
import { Link, useLocation } from 'react-router-dom'
import { IconHome, IconStore, IconTag, IconSparkles, IconMenu, IconClose } from '../../components/shared/Icons'

const navItems = [
  { to: '/', label: 'Inicio', icon: IconHome },
  { to: '/tiendas', label: 'Tiendas', icon: IconStore },
  { to: '/promos', label: 'Promos', icon: IconTag },
]

export default function ModernHeader({ config, onOpenAssistant }) {
  const [mobileMenuOpen, setMobileMenuOpen] = useState(false)
  const location = useLocation()
  const isActive = (path) => location.pathname === path

  return (
    <header className="bg-gray-900 text-white sticky top-0 z-50">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="flex justify-between items-center h-16">
          <Link to="/" className="flex items-center gap-3">
            <div className="w-9 h-9 bg-emerald-500 rounded-lg flex items-center justify-center">
              <IconStore className="w-5 h-5 text-white" />
            </div>
            <span className="text-lg font-bold hidden sm:block">
              {config?.portal_name || 'Portal'}
            </span>
          </Link>

          <nav className="hidden lg:flex items-center gap-1">
            {navItems.map((item) => (
              <Link
                key={item.to}
                to={item.to}
                className={`flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium transition-all ${
                  isActive(item.to) ? 'bg-emerald-500 text-white' : 'text-gray-300 hover:text-white hover:bg-gray-800'
                }`}
              >
                <item.icon className="w-4 h-4" />
                <span>{item.label}</span>
              </Link>
            ))}
          </nav>

          <div className="flex items-center gap-2">
            {config?.settings?.ai_assistant_enabled !== false && (
              <button onClick={onOpenAssistant} className="flex items-center gap-2 px-3 py-2 text-emerald-400 hover:bg-gray-800 rounded-lg text-sm">
                <IconSparkles className="w-4 h-4" />
                <span className="hidden md:inline">IA</span>
              </button>
            )}
            <a href="/login" className="hidden sm:flex px-4 py-2 bg-emerald-500 text-white rounded-lg text-sm font-medium hover:bg-emerald-600">
              Ingreso
            </a>
            <button onClick={() => setMobileMenuOpen(!mobileMenuOpen)} className="lg:hidden p-2 text-gray-300 hover:text-white">
              {mobileMenuOpen ? <IconClose /> : <IconMenu />}
            </button>
          </div>
        </div>

        {mobileMenuOpen && (
          <nav className="lg:hidden py-3 border-t border-gray-800">
            {navItems.map((item) => (
              <Link key={item.to} to={item.to} onClick={() => setMobileMenuOpen(false)}
                className={`flex items-center gap-3 px-4 py-3 rounded-lg font-medium ${isActive(item.to) ? 'bg-emerald-500' : 'text-gray-300 hover:bg-gray-800'}`}>
                <item.icon className="w-5 h-5" />
                <span>{item.label}</span>
              </Link>
            ))}
          </nav>
        )}
      </div>
    </header>
  )
}
