$(document).ready(function () {
  // Initialize DataTable
  $("#employeesTable").DataTable({
    order: [[0, "desc"]],
    pageLength: 10,
    responsive: true,
  });

  // Add Employee Form Submission
  $("#addEmployeeForm").submit(function (e) {
    e.preventDefault();

    // Validate password
    const password = $("#password").val();
    if (password.length < 8) {
      alert("Password must be at least 8 characters long");
      return;
    }

    // Submit form via AJAX
    $.ajax({
      url: "../api/add_employee.php",
      type: "POST",
      data: $(this).serialize(),
      dataType: "json",
      success: function (response) {
        if (response.success) {
          alert(response.message);
          $("#addEmployeeModal").modal("hide");
          location.reload(); // Reload page to show new employee
        } else {
          alert(response.message || "Error adding employee");
        }
      },
      error: function (xhr, status, error) {
        console.error(xhr.responseText);
        alert("Error adding employee: " + error);
      },
    });
  });

  // Edit Employee
  $(".edit-employee").click(function () {
    const id = $(this).data("id");

    // Fetch employee data
    $.ajax({
      url: "../api/get_employee.php",
      type: "GET",
      data: { id: id },
      dataType: "json",
      success: function (response) {
        if (response.success) {
          const employee = response.data;
          const modalBody = $("#editEmployeeModal .modal-body");

          // Populate form
          modalBody.html(`
            <div class="mb-3">
              <label for="edit_full_name" class="form-label">Full Name *</label>
              <input type="text" class="form-control" id="edit_full_name" name="full_name" 
                     value="${employee.full_name}" required>
            </div>
            <div class="mb-3">
              <label for="edit_email" class="form-label">Email *</label>
              <input type="email" class="form-control" id="edit_email" name="email" 
                     value="${employee.email}" required>
            </div>
            <div class="mb-3">
              <label for="edit_password" class="form-label">Password</label>
              <input type="password" class="form-control" id="edit_password" name="password" 
                     placeholder="Leave blank to keep current password">
              <small class="text-muted">Minimum 8 characters</small>
            </div>
            <div class="mb-3">
              <label for="edit_role" class="form-label">Role *</label>
              <select class="form-select" id="edit_role" name="role" required>
                <option value="employee" ${
                  employee.role === "employee" ? "selected" : ""
                }>Employee</option>
                <option value="admin" ${
                  employee.role === "admin" ? "selected" : ""
                }>Admin</option>
              </select>
            </div>
            <div class="mb-3">
              <label for="edit_contact_number" class="form-label">Contact Number</label>
              <input type="text" class="form-control" id="edit_contact_number" name="contact_number" 
                     value="${employee.contact_number || ""}">
            </div>
          `);

          $("#edit_employee_id").val(id);
          $("#editEmployeeModal").modal("show");
        } else {
          alert("Error fetching employee data");
        }
      },
      error: function () {
        alert("Error fetching employee data");
      },
    });
  });

  // Delete Employee
  $(".delete-employee").click(function () {
    const id = $(this).data("id");
    const name = $(this).data("name");

    $("#delete_employee_id").val(id);
    $("#delete_employee_name").text(name);
  });

  // Form Validation
  $("#editEmployeeForm").submit(function (e) {
    e.preventDefault();

    // Validate password if provided
    const password = $(this).find('input[name="password"]').val();
    if (password && password.length < 8) {
      alert("Password must be at least 8 characters long");
      return;
    }

    // Submit form via AJAX
    $.ajax({
      url: "../api/update_employee.php",
      type: "POST",
      data: $(this).serialize(),
      dataType: "json",
      success: function (response) {
        if (response.success) {
          alert(response.message);
          $("#editEmployeeModal").modal("hide");
          location.reload(); // Reload page to show updated employee
        } else {
          alert(response.message || "Error updating employee");
        }
      },
      error: function (xhr, status, error) {
        console.error("Error:", xhr.responseText);
        alert("Error updating employee. Please check the console for details.");
      },
    });
  });
});
