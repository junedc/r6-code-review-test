import React from 'react'
import { createRoot } from 'react-dom/client'
import Forecast from './components/Forecast'

function App() {
  return (
    <div style={{ maxWidth: 920, margin: '40px auto', padding: 16 }}>
      <h1 style={{ marginBottom: 8 }}>5-Day Weather Forecast</h1>
      <p style={{ marginTop: 0, color: '#555' }}>Select a city to load forecast (Brisbane, Gold Coast, Sunshine Coast).</p>
      <Forecast />
    </div>
  )
}

const root = createRoot(document.getElementById('root'))
root.render(<App />)
