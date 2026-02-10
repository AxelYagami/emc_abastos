import { useState } from 'react'
import { Link, useLocation } from 'react-router-dom'
import { IconHome, IconStore, IconTag, IconSparkles, IconMenu, IconClose } from '../../components/shared/Icons'

const navItems = [
  { to: '/', label: 'Inicio', icon: IconHome },
  { to: '/tiendas', label: 'Tiendas', icon: IconStore },
  { to: '/promos', label: 'Promos', icon: IconTag },
]

export default function BoldHeader({ config, onOpenAssistant }) {
  const [mobileMenuOpen, setMobileMenuOpen] = useState(false)
  const location = useLocation()
  const isActive = (path) => location.pathname === path

  return (
    <header className="bg-gradient-to-r from-orange-500 to-rose-500 text-white sticky top-0 z-50">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="flex justify-between items-center h-16">
          <Link to="/" className="flex items-center gap-3">
            <div className="w-10 h-10 bg-white/20 backdrop-blur rounded-full flex items-center justify-center">
              <IconStore className="w-5 h-5" />
            </div>
            <span className="text-lg font-black hidden sm:block">
              {config?.portal_name || 'Portal'}
            </span>
          </Link>

          <nav className="hidden lg:flex items-center gap-1">
            {navItems.map((item) => (
              <Link key={item.to} to={item.to}
                className={`flex items-center gap-2 px-4 py-2 rounded-full text-sm font-bold transition ${
                  isActive(item.to) ? 'bg-white text-orange-600' : 'text-white/90 hover:bg-white/20'
                }`}>
                <item.icon className="w-4 h-4" />
                <span>{item.label}</span>
              </Link>
            ))}
          </nav>

          <div className="flex items-center gap-2">
            {config?.settings?.ai_assistant_enabled !== false && (
              <button onClick={onOpenAssistant} className="flex items-center gap-2 px-3 py-2 bg-white/20 rounded-full text-sm font-bold">
                <IconSparkles className="w-4 h-4" />
                <span className="hidden md:inline">IA</span>
              </button>
            )}
            <a href="/login" className="hidden sm:flex px-5 py-2 bg-white text-orange-600 rounded-full text-sm font-black hover:bg-orange-50 transition">
              Entrar
            </a>
            <button onClick={() => setMobileMenuOpen(!mobileMenuOpen)} className="lg:hidden p-2">
              {mobileMenuOpen ? <IconClose /> : <IconMenu />}
            </button>
          </div>
        </div>

        {mobileMenuOpen && (
          <nav className="lg:hidden py-4 border-t border-white/20">
            {navItems.map((item) => (
              <Link key={item.to} to={item.to} onClick={() => setMobileMenuOpen(false)}
                className={`flex items-center gap-3 px-4 py-3 rounded-xl font-bold ${isActive(item.to) ? 'bg-white text-orange-600' : ''}`}>
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
