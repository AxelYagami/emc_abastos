import { useState, useEffect } from 'react'

const API_BASE = import.meta.env.VITE_API_URL || '/api'

// Get portal slug from URL path: /portal-slug/tiendas -> portal-slug
function getPortalSlug() {
  const path = window.location.pathname
  const parts = path.split('/').filter(Boolean)
  
  // First segment is the portal slug (e.g., /portal2/tiendas -> portal2)
  if (parts.length > 0 && parts[0] !== 'portal') {
    return parts[0]
  }
  
  // Fallback: check query param
  const params = new URLSearchParams(window.location.search)
  if (params.get('portal')) {
    return params.get('portal')
  }
  
  return null
}

const PORTAL_SLUG = getPortalSlug()

function buildUrl(endpoint) {
  const url = `${API_BASE}${endpoint}`
  if (PORTAL_SLUG) {
    const separator = url.includes('?') ? '&' : '?'
    return `${url}${separator}portal=${PORTAL_SLUG}`
  }
  return url
}

export function usePortalConfig() {
  const [config, setConfig] = useState(null)
  const [loading, setLoading] = useState(true)

  useEffect(() => {
    fetch(buildUrl('/public/portal-config'))
      .then(res => res.json())
      .then(data => {
        if (data.success) setConfig(data.data)
      })
      .catch(console.error)
      .finally(() => setLoading(false))
  }, [])

  return { config, loading, portalSlug: PORTAL_SLUG }
}

export function useStores() {
  const [stores, setStores] = useState([])
  const [loading, setLoading] = useState(true)

  useEffect(() => {
    fetch(buildUrl('/public/stores'))
      .then(res => res.json())
      .then(data => {
        if (data.success) setStores(data.data)
      })
      .catch(console.error)
      .finally(() => setLoading(false))
  }, [])

  return { stores, loading }
}

export function usePromotions() {
  const [promotions, setPromotions] = useState([])
  const [loading, setLoading] = useState(true)

  useEffect(() => {
    fetch(buildUrl('/public/promotions'))
      .then(res => res.json())
      .then(data => {
        if (data.success) setPromotions(data.data)
      })
      .catch(console.error)
      .finally(() => setLoading(false))
  }, [])

  return { promotions, loading }
}

export function useFlyer() {
  const [flyer, setFlyer] = useState({ enabled: false, products: [] })
  const [loading, setLoading] = useState(true)

  useEffect(() => {
    fetch(buildUrl('/public/flyer'))
      .then(res => res.json())
      .then(data => {
        if (data.success) setFlyer(data.data)
      })
      .catch(console.error)
      .finally(() => setLoading(false))
  }, [])

  return { flyer, loading }
}

export function useProducts(params = {}) {
  const [products, setProducts] = useState([])
  const [loading, setLoading] = useState(true)
  const [meta, setMeta] = useState({})

  useEffect(() => {
    const queryString = new URLSearchParams(params).toString()
    const baseEndpoint = `/public/products${queryString ? '?' + queryString : ''}`
    fetch(buildUrl(baseEndpoint))
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          setProducts(data.data)
          setMeta(data.meta || {})
        }
      })
      .catch(console.error)
      .finally(() => setLoading(false))
  }, [JSON.stringify(params)])

  return { products, loading, meta }
}

// Export portal slug for use in components
export const currentPortalSlug = PORTAL_SLUG
