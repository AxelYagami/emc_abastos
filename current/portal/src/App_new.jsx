import { useState, useEffect } from 'react'
import { Routes, Route } from 'react-router-dom'
import { usePortalConfig } from './hooks/useApi'

// Shared pages (same for all templates)
import Stores from './pages/Stores'
import Products from './pages/Products'
import Promos from './pages/Promos'
import AIAssistant from './components/AIAssistant'

// Default template
import DefaultHeader from './templates/default/DefaultHeader'
import DefaultFooter from './templates/default/DefaultFooter'
import DefaultHome from './templates/default/DefaultHome'

// Market V2 template
import MarketHeader from './templates/market_v2/MarketHeader'
import MarketFooter from './templates/market_v2/MarketFooter'
import MarketHome from './templates/market_v2/MarketHome'

// Template registry
const templates = {
  default: {
    Header: DefaultHeader,
    Footer: DefaultFooter,
    Home: DefaultHome,
  },
  market_v2: {
    Header: MarketHeader,
    Footer: MarketFooter,
    Home: MarketHome,
  },
}

export default function App() {
  const { config, loading } = usePortalConfig()
  const [assistantOpen, setAssistantOpen] = useState(false)

  // Set document title from config
  useEffect(() => {
    if (config?.portal_name) {
      document.title = config.portal_name
    }
    // Update meta description
    const metaDesc = document.querySelector('meta[name="description"]')
    if (metaDesc && config?.portal_description) {
      metaDesc.content = config.portal_description
    }
    // Update theme-color
    let metaTheme = document.querySelector('meta[name="theme-color"]')
    if (!metaTheme) {
      metaTheme = document.createElement('meta')
      metaTheme.name = 'theme-color'
      document.head.appendChild(metaTheme)
    }
    if (config?.theme?.primary_color) {
      metaTheme.content = config.theme.primary_color
    }
  }, [config])

  if (loading) {
    return (
      <div className="min-h-screen flex items-center justify-center bg-gray-50" role="status" aria-label="Cargando">
        <div className="w-10 h-10 border-[3px] border-gray-200 border-t-primary-600 rounded-full animate-spin" />
        <span className="sr-only">Cargando...</span>
      </div>
    )
  }

  // Resolve template (fallback to default)
  const templateKey = config?.active_template || 'default'
  const tmpl = templates[templateKey] || templates.default
  const { Header, Footer, Home } = tmpl

  return (
    <div className="min-h-screen flex flex-col bg-white">
      <Header config={config} onOpenAssistant={() => setAssistantOpen(true)} />

      <main className="flex-1">
        <Routes>
          <Route path="/" element={<Home config={config} />} />
          <Route path="/tiendas" element={<Stores config={config} />} />
          <Route path="/productos" element={<Products config={config} />} />
          <Route path="/promos" element={<Promos config={config} />} />
        </Routes>
      </main>

      <Footer config={config} />

      {/* AI Assistant */}
      {config?.settings?.ai_assistant_enabled !== false && (
        <AIAssistant
          isOpen={assistantOpen}
          onClose={() => setAssistantOpen(false)}
          config={config}
        />
      )}

      {/* Floating AI button (mobile) */}
      {config?.settings?.ai_assistant_enabled !== false && !assistantOpen && (
        <button
          onClick={() => setAssistantOpen(true)}
          className="fixed bottom-6 right-6 w-14 h-14 bg-primary-600 text-white rounded-full shadow-lg hover:bg-primary-700 transition-all hover:scale-110 flex items-center justify-center z-40 lg:hidden"
          aria-label="Abrir asistente IA"
        >
          <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
          </svg>
        </button>
      )}
    </div>
  )
}
