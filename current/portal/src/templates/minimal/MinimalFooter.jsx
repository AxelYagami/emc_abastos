export default function MinimalFooter({ config }) {
  return (
    <footer className="border-t border-gray-100 py-8">
      <div className="max-w-6xl mx-auto px-4 sm:px-6 text-center">
        <p className="text-xs text-gray-400">
          © {new Date().getFullYear()} {config?.portal_name || 'Portal'}
          {config?.developer?.name && (
            <> · <a href={config.developer.url} className="hover:text-gray-600" target="_blank" rel="noopener noreferrer">{config.developer.name}</a></>
          )}
        </p>
      </div>
    </footer>
  )
}
