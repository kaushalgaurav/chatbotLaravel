import { StrictMode } from 'react'
import React from 'react'
import { createRoot } from 'react-dom/client'
// import './index.css'
import App from '../app.jsx'
// import "reactflow/dist/style.css";  

createRoot(document.getElementById('root')).render(
  <StrictMode>
    <App />
  </StrictMode>,
)

