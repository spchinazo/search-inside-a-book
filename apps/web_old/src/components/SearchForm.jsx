import { useState } from 'react';

function SearchForm({ onSearch }) {
  const [query, setQuery] = useState('');

  const handleSubmit = (e) => {
    e.preventDefault();
    if (query.trim()) {
      onSearch(query);
    }
  };

  return (
    <form onSubmit={handleSubmit} style={{ marginBottom: 24 }}>
      <input
        type="text"
        value={query}
        onChange={e => setQuery(e.target.value)}
        placeholder="Escribe el término a buscar..."
        style={{ padding: 8, width: 300 }}
      />
      <button type="submit" style={{ marginLeft: 8, padding: '8px 16px' }}>
        Buscar
      </button>
    </form>
  );
}

export default SearchForm;
