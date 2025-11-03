function ResultsList({ results, onSelectPage }) {
  if (!results || results.length === 0) {
    return <p>No se encontraron resultados.</p>;
  }
  return (
    <div style={{ marginTop: 24 }}>
  <h2>Resultados</h2>
      <ul style={{ listStyle: 'none', padding: 0 }}>
        {results.map((item, idx) => (
          <li key={idx} style={{ marginBottom: 16, borderBottom: '1px solid #eee', paddingBottom: 8 }}>
            <div>
              <strong>Página:</strong> {item.pagina || item.page}
            </div>
            <div dangerouslySetInnerHTML={{ __html: item.contexto || item.snippet }} />
            <button onClick={() => onSelectPage(item.pagina || item.page)} style={{ marginTop: 4 }}>
              Ver página completa
            </button>
          </li>
        ))}
      </ul>
    </div>
  );
}

export default ResultsList;
