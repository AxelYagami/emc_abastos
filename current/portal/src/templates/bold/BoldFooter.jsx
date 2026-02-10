export default function BoldFooter({ config }) {
  return (
    <footer className="bg-gray-900 text-white py-12">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <p className="text-2xl font-black mb-2">{config?.portal_name || 'Portal'}</p>
        <p className="text-gray-400">Tu mercado digital favorito</p>
        <div className="mt-6 text-sm text-gray-500">
          © {new Date().getFullYear()}
          {config?.developer?.name && (
            <> · Hecho con ❤️ por <a href={config.developer.url} className="text-orange-400 hover:text-orange-300" target="_blank" rel="noopener noreferrer">{config.developer.name}</a></>
          )}
        </div>
      </div>
    </footer>
  )
}
