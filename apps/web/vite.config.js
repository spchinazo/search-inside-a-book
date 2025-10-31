import { defineConfig } from 'vite'
import react from '@vitejs/plugin-react'
import { VitePWA } from 'vite-plugin-pwa'

export default defineConfig({
  plugins: [
    react(),
    VitePWA({
      registerType: 'autoUpdate',
      manifest: {
        name: 'Buscar dentro del libro',
        short_name: 'BuscaLibro',
        description: 'Aplicación de búsqueda dentro de un libro',
        theme_color: '#222831',
        background_color: '#222831',
        display: 'standalone',
        start_url: '.',
        icons: [
          {
            src: 'icons/icon-192x192.png',
            sizes: '192x192',
            type: 'image/png',
          },
          {
            src: 'icons/icon-512x512.png',
            sizes: '512x512',
            type: 'image/png',
          },
        ],
        screenshots: [
          {
            src: 'screenshots/screenshot-1.png',
            sizes: '600x800',
            type: 'image/png',
            form_factor: 'wide'
          },
          {
            src: 'screenshots/screenshot-mobile.png',
            sizes: '430x932',
            type: 'image/png'
          }
        ],
      },
    })
  ],
  server: {
    proxy: {
      '/api': {
        target: 'http://localhost:8888',
        changeOrigin: true,
        secure: false
      }
    }
  }
});