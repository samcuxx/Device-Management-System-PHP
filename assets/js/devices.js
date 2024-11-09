$(document).ready(function () {
  // Print functionality
  $("#printButton").click(function () {
    window.print();
  });

  // Search functionality
  document.getElementById("searchInput").addEventListener("keyup", function () {
    const searchText = this.value.toLowerCase();
    const table = document.getElementById("devicesTable");
    const rows = table.getElementsByTagName("tr");

    for (let i = 1; i < rows.length; i++) {
      const row = rows[i];
      const cells = row.getElementsByTagName("td");
      let found = false;

      for (let j = 0; j < cells.length; j++) {
        const cellText = cells[j].textContent || cells[j].innerText;
        if (cellText.toLowerCase().indexOf(searchText) > -1) {
          found = true;
          break;
        }
      }

      row.style.display = found ? "" : "none";
    }
  });

  // Delete device functionality
  let deleteId = null;
  const deleteModal = new bootstrap.Modal(
    document.getElementById("deleteModal")
  );

  document.addEventListener("click", function (e) {
    if (e.target.closest(".delete-device")) {
      e.preventDefault();
      const button = e.target.closest(".delete-device");
      deleteId = button.dataset.id;
      deleteModal.show();
    }
  });

  document
    .getElementById("confirmDelete")
    .addEventListener("click", function () {
      if (deleteId) {
        fetch("../api/delete_device.php", {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
          },
          body: JSON.stringify({ id: deleteId }),
        })
          .then((response) => response.json())
          .then((data) => {
            if (data.success) {
              const row = document.querySelector(`tr[data-id="${deleteId}"]`);
              if (row) {
                row.remove();
              }
              deleteModal.hide();
              alert("Device deleted successfully");
            } else {
              alert(
                "Error deleting device: " + (data.message || "Unknown error")
              );
            }
          })
          .catch((error) => {
            console.error("Error:", error);
            alert("Error deleting device");
          });
      }
    });
});
