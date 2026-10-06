const lienzo = document.getElementById('grafica-gastos');

// Leemos los datos que PHP dejó en el atributo data-grafica
const datos = JSON.parse(lienzo.dataset.grafica);

new Chart(lienzo, {
  type: 'doughnut',
  data: {
    labels: datos.etiquetas,
    datasets: [
      {
        data: datos.valores,
        backgroundColor: [
          '#1e6f5c',
          '#e07a5f',
          '#3d5a80',
          '#f2cc8f',
          '#81b29a',
          '#a12622',
          '#98c1d9',
        ],
      },
    ],
  },
  options: {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
      legend: {
        position: 'bottom',
      },
      tooltip: {
        callbacks: {
          // Muestra el importe en formato español: "80,00 €"
          label: function (contexto) {
            const euros = contexto.parsed.toLocaleString('es-ES', {
              style: 'currency',
              currency: 'EUR',
            });
            return contexto.label + ': ' + euros;
          },
        },
      },
    },
  },
});
