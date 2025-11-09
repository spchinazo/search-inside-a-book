
import React from 'react';
import { Bar, Pie, Doughnut, Radar, Line } from 'react-chartjs-2';
import {
  Chart as ChartJS,
  CategoryScale,
  LinearScale,
  BarElement,
  PointElement,
  LineElement,
  ArcElement,
  RadialLinearScale,
  Title,
  Tooltip,
  Legend
} from 'chart.js';

ChartJS.register(
  CategoryScale,
  LinearScale,
  BarElement,
  PointElement,
  LineElement,
  ArcElement,
  RadialLinearScale,
  Title,
  Tooltip,
  Legend
);

export default function Dashboard() {
  // Datos simulados de términos más buscados
  // 1. Términos más buscados (Barra)
  const techLabels = ['JavaScript', 'React', 'Laravel', 'API', 'Node', 'PHP', 'Frontend'];
  const techValues = [18, 14, 10, 8, 7, 5, 3];
  const techColors = [
    'rgba(54, 162, 235, 0.7)',
    'rgba(255, 99, 132, 0.7)',
    'rgba(255, 206, 86, 0.7)',
    'rgba(75, 192, 192, 0.7)',
    'rgba(153, 102, 255, 0.7)',
    'rgba(255, 159, 64, 0.7)',
    'rgba(99, 255, 132, 0.7)'
  ];
  const dataTech = {
    labels: techLabels,
    datasets: [
      {
  label: 'Búsquedas',
        data: techValues,
        backgroundColor: techColors,
        borderColor: techColors.map(c => c.replace('0.7', '1')),
        borderWidth: 1,
      },
    ],
  };

  // 2. Búsquedas por horario (Línea)
  const hourLabels = ['00h', '03h', '06h', '09h', '12h', '15h', '18h', '21h'];
  const hourValues = [2, 4, 8, 15, 20, 18, 10, 5];
  const dataHour = {
    labels: hourLabels,
    datasets: [
      {
  label: 'Búsquedas por horario',
        data: hourValues,
        fill: true,
        borderColor: 'rgba(54, 162, 235, 1)',
        backgroundColor: 'rgba(54, 162, 235, 0.2)',
        tension: 0.4,
      },
    ],
  };

  // 3. Porcentaje por categoría (Pie)
  const catLabels = ['Frontend', 'Backend', 'DevOps', 'Mobile'];
  const catValues = [40, 35, 15, 10];
  const catColors = [
    'rgba(255, 206, 86, 0.7)',
    'rgba(54, 162, 235, 0.7)',
    'rgba(255, 99, 132, 0.7)',
    'rgba(153, 102, 255, 0.7)'
  ];
  const dataCat = {
    labels: catLabels,
    datasets: [
      {
  label: 'Categorías',
        data: catValues,
        backgroundColor: catColors,
        borderColor: catColors.map(c => c.replace('0.7', '1')),
        borderWidth: 1,
      },
    ],
  };

  // 4. Tasa de éxito de las búsquedas (Doughnut)
  const dataSuccess = {
    labels: ['Com resultado', 'Sem resultado'],
    datasets: [
      {
  label: 'Tasa de éxito',
        data: [85, 15],
        backgroundColor: ['rgba(75, 192, 192, 0.7)', 'rgba(255, 99, 132, 0.7)'],
        borderColor: ['rgba(75, 192, 192, 1)', 'rgba(255, 99, 132, 1)'],
        borderWidth: 1,
      },
    ],
  };

  // 5. Distribución por día de la semana (Radar)
  const weekLabels = ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb'];
  const weekValues = [10, 15, 18, 20, 17, 12, 8];
  const dataWeek = {
    labels: weekLabels,
    datasets: [
      {
    label: 'Búsquedas por día',
        data: weekValues,
        backgroundColor: 'rgba(255, 159, 64, 0.3)',
        borderColor: 'rgba(255, 159, 64, 1)',
        borderWidth: 2,
        pointBackgroundColor: 'rgba(255, 159, 64, 1)',
      },
    ],
  };

  const options = {
    responsive: true,
    plugins: {
      legend: { display: false },
      title: {
        display: true,
  text: 'Términos más buscados',
        font: { size: 18 }
      },
    },
    scales: {
      y: {
        beginAtZero: true,
        ticks: { stepSize: 1 }
      }
    }
  };

  return (
    <div className="pt-4" style={{ width: '100%', minHeight: '100vh', margin: 0, padding: 0 }}>
      <div style={{ width: '100%', margin: 0, padding: 0 }}>
        <div style={{ width: '100%' }}>
          <div className="card card-primary card-outline mb-4 w-100" style={{ margin: 0, padding: 0 }}>
            <div className="card-body">
              <h2 className="mb-4">Dashboard</h2>
              <p>Bienvenido al panel de administración de <b>Search inside a book</b> (React).</p>
              <ul>
                <li>Utilice el menú para acceder a la búsqueda en el libro.</li>
                <li>Este dashboard muestra diferentes métricas relevantes para o sistema.</li>
              </ul>
            </div>
          </div>
          <div className="row">
            {/* Barras: términos más buscados */}
            <div className="col-12 mb-4">
              <div className="card card-info card-outline w-100" style={{ margin: 0, padding: 0 }}>
                <div className="card-body" style={{ padding: 0 }}>
                  <div style={{ width: '100%', height: 320 }}>
                    <Bar data={dataTech} options={{...options, plugins: { ...options.plugins, title: { ...options.plugins.title, text: 'Términos más buscados (Barra)' } }, maintainAspectRatio: false}} />
                  </div>
                </div>
              </div>
            </div>
            {/* Línea: búsquedas por horario */}
            <div className="col-12 mb-4">
              <div className="card card-success card-outline w-100" style={{ margin: 0, padding: 0 }}>
                <div className="card-body" style={{ padding: 0 }}>
                  <div style={{ width: '100%', height: 320 }}>
                    <Line data={dataHour} options={{...options, plugins: { ...options.plugins, title: { ...options.plugins.title, text: 'Búsquedas por horario (Línea)' } }, maintainAspectRatio: false, scales: { y: { beginAtZero: true } }}} />
                  </div>
                </div>
              </div>
            </div>
            {/* Pie: porcentaje por categoría */}
            <div className="col-12 mb-4">
              <div className="card card-warning card-outline w-100" style={{ margin: 0, padding: 0 }}>
                <div className="card-body" style={{ padding: 0 }}>
                  <div style={{ width: '100%', height: 320 }}>
                    <Pie data={dataCat} options={{...options, plugins: { ...options.plugins, title: { ...options.plugins.title, text: 'Porcentaje por categoría (Pie)' } }, maintainAspectRatio: false}} />
                  </div>
                </div>
              </div>
            </div>
            {/* Doughnut: tasa de éxito de las búsquedas */}
            <div className="col-12 mb-4">
              <div className="card card-danger card-outline w-100" style={{ margin: 0, padding: 0 }}>
                <div className="card-body" style={{ padding: 0 }}>
                  <div style={{ width: '100%', height: 320 }}>
                    <Doughnut data={dataSuccess} options={{...options, plugins: { ...options.plugins, title: { ...options.plugins.title, text: 'Tasa de éxito de las búsquedas (Doughnut)' } }, maintainAspectRatio: false}} />
                  </div>
                </div>
              </div>
            </div>
            {/* Radar: distribución por día de la semana */}
            <div className="col-12 mb-4">
              <div className="card card-secondary card-outline w-100" style={{ margin: 0, padding: 0 }}>
                <div className="card-body" style={{ padding: 0 }}>
                  <div style={{ width: '100%', height: 320 }}>
                    <Radar data={dataWeek} options={{...options, plugins: { ...options.plugins, title: { ...options.plugins.title, text: 'Distribución por día de la semana (Radar)' } }, maintainAspectRatio: false, scales: { r: { beginAtZero: true } }}} />
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}
