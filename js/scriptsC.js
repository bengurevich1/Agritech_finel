
let timeRangeDropdown = document.getElementById('timeRangeDropdown');
    let dropdownItems = document.querySelectorAll(".dropdown-item");
    let myChart;
    let myChart2;

    dropdownItems.forEach(function(item) {
      item.addEventListener("click", function() {
        let selectedText = this.textContent;
        let selectedTimeRange = this.getAttribute("data-time-range");
        timeRangeDropdown.textContent = selectedText;
        updateCharts(selectedTimeRange);
      });
    });

    function updateCharts(timeRange) {
      // Fetch data from JSON file based on the selected time range
      let dataURL = 'chartData.json';

      // Fetch data for both charts
      fetch(dataURL)
        .then(response => response.json())
        .then(data => {
          var chart1Data = data.chart1[timeRange];
          myChart.data.datasets = chart1Data.datasets;
          myChart.data.labels = chart1Data.labels;

          var chart2Data = data.chart2[timeRange];
          myChart2.data.datasets = chart2Data.datasets;
          myChart2.data.labels = chart2Data.labels;

          // Update both charts
          myChart.update();
          myChart2.update();
        });
    }

    let ctx = document.getElementById('myChart').getContext('2d');
    let data = {
      labels: ['2017', '2018', '2019', '2020', '2021', '2022', '2023'],
      datasets: [
        {
          label: 'Total water use',
          data: [65, 68, 65, 61, 70, 74, 67],
          fill: false,
          borderColor: 'rgb(75, 192, 192)',
          tension: 0.1
        },
        {
          label: 'Total pest',
          data: [50, 55, 56, 75, 80, 75, 65],
          fill: false,
          borderColor: 'rgb(255, 99, 132)',
          tension: 0.1
        }
      ]
    };

    let options = {
      responsive: true,
      scales: {
        y: {
          beginAtZero: true
        }
      }
    };

    myChart = new Chart(ctx, {
      type: 'line',
      data: data,
      options: options
    });

    let ctr = document.getElementById("myChart2").getContext('2d');
    let data2 = {
      labels: ["2017", "2018", "2019", "2020", "2021", "2022", "2023"],
      datasets: [
        {
          label: 'Savings',
          data: [105, 124, 78, 91, 62, 56,70],
          backgroundColor: ['rgba(255, 99, 132, 0.2)',
            'rgba(54, 162, 235, 0.2)',
            'rgba(255, 206, 86, 0.2)',
            'rgba(75, 192, 192, 0.2)',
            'rgba(153, 102, 255, 0.2)',
            'rgba(255, 159, 64, 0.2)',
            "rgba(255, 159, 64, 0.2)"
          ],
          borderColor: [
            'rgba(255, 99, 132, 1)',
            'rgba(54, 162, 235, 1)',
            'rgba(255, 206, 86, 1)',
            'rgba(75, 192, 192, 1)',
            'rgba(153, 102, 255, 1)',
            'rgba(255, 159, 64, 1)',
            "rgba(255, 159, 64, 0.2)"
          ],
          borderWidth: 1
        }
      ]
    };

    let options2 = {
      responsive: true,
      scales: {
        y: {
          beginAtZero: true
        }
      }
    };

    myChart2 = new Chart(ctr, {
      type: 'bar',
      data: data2,
      options: options2
    });

    $(document).ready(function() {
      $('#editProfilePic').on('click', function(event) {
      event.preventDefault(); // Prevent the link's default action
      $('#editModalProfile').modal('show'); // Show the modal with the specified ID
      });
  });