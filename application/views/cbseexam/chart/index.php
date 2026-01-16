<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>

<style>
    .chart-wrapper {
  border: 1px solid blue;
  height: 300px;
  width: 600px;
}

p {
  margin-top: 15px;
}
</style>


<div class="row">
    <div class="col-md-12">
      <div class="chart-wrapper">
      <canvas  id="myChart"></canvas>
      </div>
     
    </div>
  </div>



<script>

const ctx = document.getElementById('myChart').getContext('2d');
const myChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: ['Red', 'Blue', 'Yellow', 'Green', 'Purple', 'Orange'],
        datasets: [{
          label: "Africa",
          backgroundColor: "#3e95cd",
          data: [133,221,783,2478]
        }, {
          label: "Europe",
          backgroundColor: "#8e5ea2",
          data: [408,547,675,734]
        }]
    },
    options: {
        scales: {
            y: {
                beginAtZero: true
            }
        },
        plugins: {
            title: {
                display: true,
                text: 'Custom Chart Title'
            }
        }
    }
});




// const labels =  ['Red', 'Blue', 'Yellow', 'Green', 'Purple', 'Orange', 'Brown'];
// const data = {
//   labels: labels,
//   datasets: [  {
//           label: "Africa",
//           backgroundColor: "#3e95cd",
//           data: [133,221,783,2478]
//         }, {
//           label: "Europe",
//           backgroundColor: "#8e5ea2",
//           data: [408,547,675,734]
//         }]
// };

// const config = {
//   type: 'bar',
//   data: data,
//   options: {
//         responsive: true,
//         maintainAspectRatio: false,
//         scales: {
//             yAxes: [{
//                 ticks: {
//                     beginAtZero:true
//                 }
//             }]
//         }
//     }
// };



// const ctx = document.getElementById('myChart').getContext('2d');

// const myChart = new Chart(ctx, config);
// myChart.height = 150;
</script>