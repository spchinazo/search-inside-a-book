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

  const handleSearch = async (query) => {
    setLoading(true);
    setError('');
    setSelectedPage(null);
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
      const data = await res.json();
      setSelectedPage(data);
    } catch (e) {
      setError('Error al cargar la página.');
    }
    setLoading(false);
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
              {error && <p className="text-danger">{error}</p>}
              {!selectedPage && <ResultsList results={results} onSelectPage={handleSelectPage} />}
              {selectedPage && <PageView page={selectedPage} onBack={() => setSelectedPage(null)} />}
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}

export default App;
