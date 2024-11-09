<?php
session_start();
require_once "../config/database.php";
require_once "../includes/auth_check.php";
require_once "../classes/User.php";
require_once "../includes/helpers.php";

// Check if user is admin
if ($_SESSION['role'] !== 'admin') {
    header("Location: dashboard.php");
    exit;
}

$database = new Database();
$db = $database->getConnection();
$user = new User($db);

$success_message = $error_message = "";

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_employee'])) {
        $result = $user->create($_POST);
        if ($result['success']) {
            $success_message = "Employee added successfully!";
        } else {
            $error_message = $result['message'];
        }
    }
    
    if (isset($_POST['edit_employee'])) {
        $employee_id = $_POST['employee_id']; // Get the employee ID
        unset($_POST['edit_employee']); // Remove the submit button value
        unset($_POST['employee_id']); // Remove ID from update data
        
        $result = $user->update($employee_id, $_POST); // Pass both ID and data
        if ($result['success']) {
            $success_message = "Employee updated successfully!";
        } else {
            $error_message = $result['message'];
        }
    }
    
    if (isset($_POST['delete_employee'])) {
        $result = $user->delete($_POST['employee_id']);
        if ($result['success']) {
            $success_message = $result['message'];
        } else {
            $error_message = $result['message'];
        }
    }
}

// Get all employees
$employees = $user->getAllEmployees();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employees - Comprint Services</title>
    <link href="../assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/boxicons/css/boxicons.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
    <style>
        .search-box {
            max-width: 300px;
            margin-bottom: 1.5rem;
        }
        .search-box input {
            border-radius: 20px;
            padding: 0.5rem 1rem;
        }
    </style>
</head>
<body>
    <?php include "../templates/sidebar.php"; ?>
    
    <main class="content">
        <?php include "../templates/header.php"; ?>

        <div class="container-fluid p-4">
            <?php if ($success_message): ?>
                <div class="alert alert-success"><?php echo $success_message; ?></div>
            <?php endif; ?>
            <?php if ($error_message): ?>
                <div class="alert alert-danger"><?php echo $error_message; ?></div>
            <?php endif; ?>

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Employees</h5>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addEmployeeModal">
                        <i class="bx bx-plus"></i> Add Employee
                    </button>
                </div>
                <div class="card-body">
                    <div class="search-box">
                        <input type="text" id="searchInput" class="form-control" placeholder="Search employees...">
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped" id="employeesTable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Full Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Contact</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($employees as $employee): ?>
                                <tr>
                                    <td><?php echo $employee['id']; ?></td>
                                    <td><?php echo h($employee['full_name']); ?></td>
                                    <td><?php echo h($employee['email']); ?></td>
                                    <td><?php echo ucfirst(h($employee['role'])); ?></td>
                                    <td><?php echo h($employee['contact_number']); ?></td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button type="button" class="btn btn-primary edit-employee" 
                                                    data-id="<?php echo $employee['id']; ?>">
                                                <i class="bx bx-edit"></i>
                                            </button>
                                            <button type="button" class="btn btn-danger delete-employee" 
                                                    data-id="<?php echo $employee['id']; ?>"
                                                    data-name="<?php echo h($employee['full_name']); ?>">
                                                <i class="bx bx-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Add Employee Modal -->
    <div class="modal fade" id="addEmployeeModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title">Add Employee</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Full Name *</label>
                            <input type="text" class="form-control" name="full_name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email *</label>
                            <input type="email" class="form-control" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password *</label>
                            <input type="password" class="form-control" name="password" required 
                                   minlength="8">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Role *</label>
                            <select class="form-select" name="role" required>
                                <option value="employee">Employee</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Contact Number</label>
                            <input type="text" class="form-control" name="contact_number">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="add_employee" class="btn btn-primary">Add Employee</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Employee Modal -->
    <div class="modal fade" id="editEmployeeModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <input type="hidden" name="employee_id" id="edit_employee_id">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Employee</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Full Name *</label>
                            <input type="text" class="form-control" name="full_name" id="edit_full_name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email *</label>
                            <input type="email" class="form-control" name="email" id="edit_email" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">New Password (leave blank to keep current)</label>
                            <input type="password" class="form-control" name="password" minlength="8">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Role *</label>
                            <select class="form-select" name="role" id="edit_role" required>
                                <option value="employee">Employee</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Contact Number</label>
                            <input type="text" class="form-control" name="contact_number" id="edit_contact_number">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="edit_employee" class="btn btn-primary">Update Employee</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Employee Modal -->
    <div class="modal fade" id="deleteEmployeeModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <input type="hidden" name="employee_id" id="delete_employee_id">
                    <div class="modal-header">
                        <h5 class="modal-title">Delete Employee</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to delete <span id="delete_employee_name"></span>?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="delete_employee" class="btn btn-danger">Delete</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="../assets/js/bootstrap.bundle.min.js"></script>
    <script>
        // Search functionality
        document.getElementById('searchInput').addEventListener('keyup', function() {
            const searchText = this.value.toLowerCase();
            const table = document.getElementById('employeesTable');
            const rows = table.getElementsByTagName('tr');

            for (let i = 1; i < rows.length; i++) {
                const row = rows[i];
                const cells = row.getElementsByTagName('td');
                let found = false;

                for (let j = 0; j < cells.length; j++) {
                    const cellText = cells[j].textContent || cells[j].innerText;
                    if (cellText.toLowerCase().indexOf(searchText) > -1) {
                        found = true;
                        break;
                    }
                }

                row.style.display = found ? '' : 'none';
            }
        });

        // Edit employee
        document.querySelectorAll('.edit-employee').forEach(button => {
            button.addEventListener('click', function() {
                const id = this.dataset.id;
                fetch(`../api/get_employee.php?id=${id}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            const employee = data.data;
                            document.getElementById('edit_employee_id').value = employee.id;
                            document.getElementById('edit_full_name').value = employee.full_name;
                            document.getElementById('edit_email').value = employee.email;
                            document.getElementById('edit_role').value = employee.role;
                            document.getElementById('edit_contact_number').value = employee.contact_number || '';
                            
                            const editModal = new bootstrap.Modal(document.getElementById('editEmployeeModal'));
                            editModal.show();
                        } else {
                            alert('Error fetching employee data');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Error fetching employee data');
                    });
            });
        });

        // Delete employee
        document.querySelectorAll('.delete-employee').forEach(button => {
            button.addEventListener('click', function() {
                const id = this.dataset.id;
                const name = this.dataset.name;
                
                document.getElementById('delete_employee_id').value = id;
                document.getElementById('delete_employee_name').textContent = name;
                
                const deleteModal = new bootstrap.Modal(document.getElementById('deleteEmployeeModal'));
                deleteModal.show();
            });
        });
    </script>
</body>
</html>