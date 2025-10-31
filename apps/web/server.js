import express from 'express';
import { createProxyMiddleware } from 'http-proxy-middleware';
import path from 'path';
import { fileURLToPath } from 'url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

const app = express();
const PORT = 3000;

// Proxy /api requests to backend (deve vir antes do static)
app.use('/api', (req, res, next) => {
  console.log(`[PROXY] ${req.method} ${req.originalUrl}`);
  next();
}, createProxyMiddleware({
  target: 'http://127.0.0.1:8888',
  changeOrigin: true,
  secure: false,
  onProxyReq: (proxyReq, req) => {
    console.log(`[PROXY-REQ] encaminhando para backend: ${proxyReq.path}`);
  },
  onError: (err, req, res) => {
    console.error('[PROXY-ERROR]', err);
    res.status(500).send('Proxy error');
  }
}));

// Serve static files from dist
app.use(express.static(path.join(__dirname, 'dist')));

// Fallback to index.html for SPA (usando regex para evitar erro de path-to-regexp)
app.get(/^(?!\/api).*/, (req, res) => {
  res.sendFile(path.join(__dirname, 'dist', 'index.html'));
});

app.listen(PORT, () => {
  console.log(`Frontend running on http://localhost:${PORT}`);
});
