export default function ModernFooter({ config }) {
  return (
    <footer className="bg-gray-900 border-t border-gray-800 py-12">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="flex flex-col md:flex-row justify-between items-center gap-4">
          <p className="text-gray-400 text-sm">
            © {new Date().getFullYear()} {config?.portal_name || 'Portal'}
          </p>
          {config?.developer?.name && (
            <p className="text-gray-500 text-sm">
              Desarrollado por{' '}
              <a href={config.developer.url} className="text-emerald-400 hover:text-emerald-300" target="_blank" rel="noopener noreferrer">
                {config.developer.name}
              </a>
            </p>
          )}
        </div>
      </div>
    </footer>
  )
}
