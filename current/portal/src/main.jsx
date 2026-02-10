import React from 'react'
import ReactDOM from 'react-dom/client'
import { BrowserRouter } from 'react-router-dom'
import App from './App'
import './index.css'

// Get basename from URL - first path segment is the portal slug
function getBasename() {
  const path = window.location.pathname
  const parts = path.split('/').filter(Boolean)
  
  // If first segment looks like a portal slug (not 'portal'), use it as basename
  if (parts.length > 0 && parts[0] !== 'portal') {
    return '/' + parts[0]
  }
  
  // Fallback to /portal for legacy
  return '/portal'
}

const basename = getBasename()

ReactDOM.createRoot(document.getElementById('root')).render(
  <React.StrictMode>
    <BrowserRouter basename={basename}>
      <App />
    </BrowserRouter>
  </React.StrictMode>
)
