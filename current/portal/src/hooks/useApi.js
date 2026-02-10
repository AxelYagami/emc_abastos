import { useState, useEffect } from 'react'

const API_BASE = import.meta.env.VITE_API_URL || '/api'

// Get portal slug from URL param, subdomain, or env
function getPortalSlug() {
  // 1. Check URL parameter ?portal=slug
  const params = new URLSearchParams(window.location.search)
  if (params.get('portal')) {
    return params.get('portal')
  }
  
  // 2. Check subdomain (portal1.example.com -> portal1)
  const hostname = window.location.hostname
  const parts = hostname.split('.')
  if (parts.length >= 3 && parts[0] !== 'www') {
    return parts[0]
  }
  
  // 3. Check env variable
  if (import.meta.env.VITE_PORTAL_SLUG) {
    return import.meta.env.VITE_PORTAL_SLUG
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

  return { config, loading }
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
    const baseUrl = `/public/products${queryString ? '?' + queryString : ''}`
    fetch(buildUrl(baseUrl.startsWith('/') ? baseUrl : '/' + baseUrl))
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
