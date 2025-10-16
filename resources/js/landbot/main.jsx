import { StrictMode } from 'react'
import React from 'react'
import { createRoot } from 'react-dom/client'
import App from '../app.jsx'
// resources/js/landbot/main.jsx (top of file)
import '../../scss/bootstrap.scss';
import '../../scss/icons.scss';
import '../../scss/app.scss';
import '../landbot/index.css';



createRoot(document.getElementById('root')).render(
  <StrictMode>
    <App />
  </StrictMode>,
)

