function PageView({ page, onBack }) {
  if (!page) return null;
  return (
    <div style={{ marginTop: 32 }}>
      <button onClick={onBack} style={{ marginBottom: 16 }}>&larr; Volver a los resultados</button>
      <h2>Página {page.page}</h2>
      <pre style={{ background: '#f8f8f8', padding: 16, borderRadius: 4, whiteSpace: 'pre-wrap' }}>
        {page.text_content}
      </pre>
    </div>
  );
}

export default PageView;
