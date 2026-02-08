import { Link } from 'react-router-dom'
import { IconArrowRight } from './Icons'

export default function SectionHeader({ title, subtitle, linkTo, linkText = 'Ver todo', align = 'left', light = false }) {
  const titleColor = light ? 'text-white' : 'text-gray-900'
  const subtitleColor = light ? 'text-white/70' : 'text-gray-500'
  const linkColor = light ? 'text-white hover:text-white/80' : 'text-primary-600 hover:text-primary-700'

  if (align === 'center') {
    return (
      <div className="text-center mb-10">
        <h2 className={`text-3xl lg:text-4xl font-bold ${titleColor} tracking-tight`}>{title}</h2>
        {subtitle && <p className={`mt-3 text-lg ${subtitleColor} max-w-2xl mx-auto`}>{subtitle}</p>}
        {linkTo && (
          <Link to={linkTo} className={`inline-flex items-center gap-2 mt-4 font-medium ${linkColor} transition-colors`}>
            {linkText}
            <IconArrowRight className="w-4 h-4" />
          </Link>
        )}
      </div>
    )
  }

  return (
    <div className="flex items-end justify-between mb-8 gap-4">
      <div>
        <h2 className={`text-2xl lg:text-3xl font-bold ${titleColor} tracking-tight`}>{title}</h2>
        {subtitle && <p className={`mt-1.5 text-base ${subtitleColor}`}>{subtitle}</p>}
      </div>
      {linkTo && (
        <Link to={linkTo} className={`hidden sm:inline-flex items-center gap-1.5 font-medium text-sm ${linkColor} transition-colors flex-shrink-0`}>
          {linkText}
          <IconArrowRight className="w-4 h-4" />
        </Link>
      )}
    </div>
  )
}
