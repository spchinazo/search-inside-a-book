import React, { useState } from 'react';
import { Link } from 'react-router-dom';
import './AdminLayout.css';


export default function AdminLayout({ children }) {
  const [sidebarOpen, setSidebarOpen] = useState(false);

  // Fecha sidebar ao clicar em overlay ou link
  const closeSidebar = () => setSidebarOpen(false);

  return (
    <div className="wrapper">
      {/* Botão menu mobile */}
      <button
        className="sidebar-toggle-btn d-md-none"
        aria-label="Abrir menu"
        onClick={() => setSidebarOpen(true)}
      >
        <span className="sidebar-toggle-icon">&#9776;</span>
      </button>

      {/* Overlay mobile */}
      {sidebarOpen && <div className="sidebar-overlay" onClick={closeSidebar}></div>}

      {/* Sidebar */}
      <aside className={`main-sidebar sidebar-dark-primary elevation-4${sidebarOpen ? ' open' : ''}`}>
        <Link to="/dashboard" className="brand-link d-flex align-items-center" onClick={closeSidebar}>
          <img src="/logo_publicala.png" alt="Logo" className="brand-image elevation-3" style={{ width: 140, height: 36, background: '#fff', borderRadius: 8, objectFit: 'contain' }} />
        </Link>
        <div className="sidebar mt-3">
          <ul className="nav nav-pills nav-sidebar flex-column" role="menu">
            <li className="nav-item">
              <Link to="/dashboard" className="nav-link" onClick={closeSidebar}>
                <i className="nav-icon fas fa-tachometer-alt"></i>
                <p className="ml-2 mb-0">Dashboard</p>
              </Link>
            </li>
            <li className="nav-item">
              <Link to="/" className="nav-link" onClick={closeSidebar}>
                <i className="nav-icon fas fa-search"></i>
                <p className="ml-2 mb-0">Buscar en el libro</p>
              </Link>
            </li>
          </ul>
        </div>
      </aside>

      {/* Content */}
      <div className="content-wrapper flex-grow-1 p-4">
        {children}
      </div>
    </div>
  );
}
