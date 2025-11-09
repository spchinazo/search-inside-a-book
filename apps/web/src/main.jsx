import { StrictMode } from 'react';
import { createRoot } from 'react-dom/client';
import './index.css';
import App from './App.jsx';
import Dashboard from './components/Dashboard.jsx';
import AdminLayout from './components/AdminLayout.jsx';
import { BrowserRouter, Routes, Route, Link } from 'react-router-dom';

createRoot(document.getElementById('root')).render(
  <StrictMode>
    <BrowserRouter>
      <Routes>
        <Route path="/dashboard" element={<AdminLayout><Dashboard /></AdminLayout>} />
        <Route path="/" element={<AdminLayout><App /></AdminLayout>} />
      </Routes>
    </BrowserRouter>
  </StrictMode>,
);
