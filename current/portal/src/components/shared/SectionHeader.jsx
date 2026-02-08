import { Link } from 'react-router-dom'
import { IconArrowRight } from './Icons'

export default function SectionHeader({ 
  title, 
  subtitle, 
  linkTo, 
  linkText = 'Ver todo', 
  align = 'left',
  light = false,
  size = 'default'
}) {
  const alignClass = {
    left: 'text-left',
    center: 'text-center mx-auto',
    right: 'text-right',
  }[align]

  const titleSize = size === 'large' 
    ? 'text-3xl lg:text-4xl' 
    : 'text-2xl lg:text-3xl'

  return (
    <div className={`mb-8 lg:mb-10 ${alignClass} ${align === 'center' ? 'max-w-2xl' : ''}`}>
      <div className={`flex items-end justify-between gap-4 ${align === 'center' ? 'flex-col items-center' : ''}`}>
        <div>
          <h2 className={`${titleSize} font-bold tracking-tight text-balance ${
            light ? 'text-white' : 'text-gray-900'
          }`}>
            {title}
          </h2>
          {subtitle && (
            <p className={`mt-2 text-base lg:text-lg leading-relaxed ${
              light ? 'text-white/80' : 'text-gray-500'
            }`}>
              {subtitle}
            </p>
          )}
        </div>
        {linkTo && (
          <Link
            to={linkTo}
            className={`inline-flex items-center gap-2 px-4 py-2 rounded-xl font-semibold text-sm transition-all btn-press ${
              light 
                ? 'bg-white/10 hover:bg-white/20 text-white border border-white/20' 
                : 'bg-gray-100 hover:bg-gray-200 text-gray-700'
            }`}
          >
            {linkText}
            <IconArrowRight className="w-4 h-4" />
          </Link>
        )}
      </div>
    </div>
  )
}
