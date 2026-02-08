export default function LoadingSpinner({ size = 'default', light = false }) {
  const sizeClasses = {
    small: 'w-5 h-5 border-2',
    default: 'w-8 h-8 border-[3px]',
    large: 'w-12 h-12 border-4',
  }[size]

  return (
    <div className="flex items-center justify-center p-8" role="status" aria-label="Cargando">
      <div 
        className={`${sizeClasses} rounded-full animate-spin ${
          light 
            ? 'border-white/30 border-t-white' 
            : 'border-primary-100 border-t-primary-600'
        }`}
      />
      <span className="sr-only">Cargando...</span>
    </div>
  )
}

export function LoadingSkeleton({ className = '', variant = 'default' }) {
  const variants = {
    default: 'h-4 rounded',
    card: 'h-48 rounded-2xl',
    avatar: 'w-12 h-12 rounded-full',
    title: 'h-6 w-3/4 rounded',
    text: 'h-4 rounded',
    image: 'aspect-square rounded-2xl',
  }

  return (
    <div 
      className={`bg-gray-200 animate-shimmer ${variants[variant]} ${className}`}
      aria-hidden="true"
    />
  )
}

export function ProductCardSkeleton() {
  return (
    <div className="card-premium overflow-hidden" aria-hidden="true">
      <LoadingSkeleton variant="image" />
      <div className="p-4 space-y-3">
        <div className="flex items-center gap-2">
          <LoadingSkeleton variant="avatar" className="w-6 h-6" />
          <LoadingSkeleton className="h-3 w-20" />
        </div>
        <LoadingSkeleton variant="title" />
        <LoadingSkeleton className="h-3 w-full" />
        <div className="pt-3 border-t border-gray-100">
          <LoadingSkeleton className="h-6 w-16" />
        </div>
      </div>
    </div>
  )
}

export function StoreCardSkeleton() {
  return (
    <div className="card-premium overflow-hidden" aria-hidden="true">
      <LoadingSkeleton className="h-32" />
      <div className="p-5 pt-12 space-y-3">
        <LoadingSkeleton variant="title" />
        <LoadingSkeleton className="h-3 w-full" />
        <LoadingSkeleton className="h-3 w-3/4" />
        <div className="pt-4 border-t border-gray-100 flex gap-2">
          <LoadingSkeleton className="h-6 w-6 rounded-lg" />
          <LoadingSkeleton className="h-6 w-6 rounded-lg" />
          <LoadingSkeleton className="h-6 w-6 rounded-lg" />
        </div>
      </div>
    </div>
  )
}
