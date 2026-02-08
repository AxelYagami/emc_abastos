export default function LoadingSpinner() {
  return (
    <div className="min-h-[60vh] flex items-center justify-center" role="status" aria-label="Cargando">
      <div className="flex flex-col items-center gap-4">
        <div className="w-10 h-10 border-[3px] border-gray-200 border-t-primary-600 rounded-full animate-spin" />
        <span className="sr-only">Cargando...</span>
      </div>
    </div>
  )
}
