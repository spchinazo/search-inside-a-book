function PageView({ page, onBack, error }) {
  if (error) {
    return (
      <div style={{ marginTop: 32 }}>
        <button onClick={onBack} style={{ marginBottom: 16, padding: '8px 16px', backgroundColor: '#007bff', color: 'white', border: 'none', borderRadius: '4px', cursor: 'pointer' }}>&larr; Volver a los resultados</button>
        <p className="text-danger" style={{ color: 'red', fontWeight: 'bold' }}>{error}</p>
      </div>
    );
  }
  if (!page) {
    return (
      <div style={{ marginTop: 32 }}>
        <button onClick={onBack} style={{ marginBottom: 16, padding: '8px 16px', backgroundColor: '#007bff', color: 'white', border: 'none', borderRadius: '4px', cursor: 'pointer' }}>&larr; Volver a los resultados</button>
        <p>Cargando página...</p>
      </div>
    );
  }
  
  const content = page.text_content || page.content || page.texto || page.conteudo || 'Contenido no disponible';
  const pageNum = page.page || page.pagina || page.numero || 'N/A';
  
  return (
    <div style={{ marginTop: 32 }}>
      <button onClick={onBack} style={{ marginBottom: 16, padding: '8px 16px', backgroundColor: '#007bff', color: 'white', border: 'none', borderRadius: '4px', cursor: 'pointer' }}>
        &larr; Volver a los resultados
      </button>
      <h2 style={{ marginBottom: 16 }}>Página {pageNum}</h2>
      <div style={{ 
        background: '#f8f9fa', 
        padding: 20, 
        borderRadius: 8, 
        border: '1px solid #dee2e6',
        whiteSpace: 'pre-wrap',
        fontFamily: 'Georgia, serif',
        fontSize: '16px',
        lineHeight: '1.6',
        maxHeight: '75vh',
        overflow: 'auto',
        minHeight: '400px',
        color: '#000000' // Fonte preta para garantir visibilidade
      }}>
        {content}
      </div>
    </div>
  );
}

export default PageView;
