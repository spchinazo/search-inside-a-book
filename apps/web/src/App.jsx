import { useState } from 'react';
import './App.css';
import SearchForm from './components/SearchForm';
import ResultsList from './components/ResultsList';
import PageView from './components/PageView';


function App() {
  const [results, setResults] = useState([]);
  const [selectedPage, setSelectedPage] = useState(null);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState('');
  const [currentView, setCurrentView] = useState('search'); // 'search' or 'page'

  const handleSearch = async (query) => {
    setLoading(true);
    setError('');
    setSelectedPage(null);
    setCurrentView('search');
    try {
      const res = await fetch(`/api/search?query=${encodeURIComponent(query)}`);
      const data = await res.json();
      setResults(data.resultados || []);
    } catch (e) {
      setError('Error al buscar resultados.');
    }
    setLoading(false);
  };

  const handleSelectPage = async (pageNumber) => {
    setLoading(true);
    setError('');
    try {
      const res = await fetch(`/api/page/${pageNumber}`);
      if (!res.ok) {
        if (res.status === 404) {
          setError('Página no encontrada.');
        } else {
          setError('Error al cargar la página.');
        }
        setCurrentView('page');
        setLoading(false);
        return;
      }
      const data = await res.json();
      setSelectedPage(data);
      setCurrentView('page');
      setLoading(false);
    } catch (e) {
      setError('Error al cargar la página.');
      setCurrentView('page');
      setLoading(false);
    }
  };

  const handleBackToResults = () => {
    setCurrentView('search');
    setSelectedPage(null);
    setError('');
  };

  return (
    <div className="container-fluid pt-4">
      <div className="row justify-content-center">
        <div className="col-lg-10 col-xl-8">
          <div className="card card-primary card-outline">
            <div className="card-body">
              <h2 className="mb-4">Buscar dentro del libro</h2>
              <SearchForm onSearch={handleSearch} />
              {loading && <p>Cargando...</p>}
              {currentView === 'search' && <ResultsList results={results} onSelectPage={handleSelectPage} />}
              {currentView === 'page' && (
                <div style={{ 
                  position: 'fixed', 
                  top: 0, 
                  left: 0, 
                  width: '100vw',
                  height: '100vh',
                  backgroundColor: 'white', 
                  zIndex: 999999, 
                  padding: '20px', 
                  overflow: 'auto',
                  boxSizing: 'border-box'
                }}>
                  <PageView page={selectedPage} onBack={handleBackToResults} error={error} />
                </div>
              )}
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}

export default App;
