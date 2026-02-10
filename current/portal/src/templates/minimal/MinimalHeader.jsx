import { useState } from 'react'
import { Link, useLocation } from 'react-router-dom'
import { IconHome, IconStore, IconTag, IconSparkles, IconMenu, IconClose } from '../../components/shared/Icons'

const navItems = [
  { to: '/', label: 'Inicio' },
  { to: '/tiendas', label: 'Tiendas' },
  { to: '/promos', label: 'Promos' },
]

export default function MinimalHeader({ config, onOpenAssistant }) {
  const [mobileMenuOpen, setMobileMenuOpen] = useState(false)
  const location = useLocation()
  const isActive = (path) => location.pathname === path

  return (
    <header className="bg-white border-b border-gray-100 sticky top-0 z-50">
      <div className="max-w-6xl mx-auto px-4 sm:px-6">
        <div className="flex justify-between items-center h-14">
          <Link to="/" className="text-lg font-light tracking-wide text-gray-900">
            {config?.portal_name || 'Portal'}
          </Link>

          <nav className="hidden md:flex items-center gap-8">
            {navItems.map((item) => (
              <Link key={item.to} to={item.to}
                className={`text-sm tracking-wide transition ${isActive(item.to) ? 'text-gray-900' : 'text-gray-400 hover:text-gray-600'}`}>
                {item.label}
              </Link>
            ))}
          </nav>

          <div className="flex items-center gap-3">
            {config?.settings?.ai_assistant_enabled !== false && (
              <button onClick={onOpenAssistant} className="text-gray-400 hover:text-gray-600">
                <IconSparkles className="w-4 h-4" />
              </button>
            )}
            <a href="/login" className="hidden sm:block text-sm text-gray-500 hover:text-gray-900">Ingreso</a>
            <button onClick={() => setMobileMenuOpen(!mobileMenuOpen)} className="md:hidden p-1 text-gray-400">
              {mobileMenuOpen ? <IconClose className="w-5 h-5" /> : <IconMenu className="w-5 h-5" />}
            </button>
          </div>
        </div>

        {mobileMenuOpen && (
          <nav className="md:hidden py-4 border-t border-gray-100">
            {navItems.map((item) => (
              <Link key={item.to} to={item.to} onClick={() => setMobileMenuOpen(false)}
                className={`block py-2 text-sm ${isActive(item.to) ? 'text-gray-900' : 'text-gray-400'}`}>
                {item.label}
              </Link>
            ))}
          </nav>
        )}
      </div>
    </header>
  )
}
