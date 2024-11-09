document.addEventListener("DOMContentLoaded", function () {
  // Status Distribution Chart
  const statusCtx = document.getElementById("statusChart").getContext("2d");
  const statusData = JSON.parse(
    document.getElementById("statusData").textContent
  );

  new Chart(statusCtx, {
    type: "doughnut",
    data: {
      labels: statusData.map((item) => item.status),
      datasets: [
        {
          data: statusData.map((item) => item.count),
          backgroundColor: [
            "#ffc107", // Pending
            "#17a2b8", // In Progress
            "#28a745", // Completed
          ],
        },
      ],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          position: "bottom",
        },
      },
    },
  });

  // Device Types Distribution Chart
  const typeCtx = document.getElementById("typeChart").getContext("2d");
  const typeData = JSON.parse(document.getElementById("typeData").textContent);

  new Chart(typeCtx, {
    type: "bar",
    data: {
      labels: typeData.map((item) => item.device_type),
      datasets: [
        {
          label: "Number of Devices",
          data: typeData.map((item) => item.count),
          backgroundColor: "#007bff",
        },
      ],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          display: false,
        },
      },
      scales: {
        y: {
          beginAtZero: true,
          ticks: {
            stepSize: 1,
          },
        },
      },
    },
  });
});

// Sidebar Toggle
document
  .querySelector(".sidebar-toggle")
  .addEventListener("click", function () {
    document.querySelector(".sidebar").classList.toggle("show");
  });
