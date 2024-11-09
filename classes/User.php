<?php
class User {
    private $conn;
    private $table_name = "users";

    public $id;
    public $username;
    public $password;
    public $full_name;
    public $email;
    public $role;
    public $contact_number;
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAllUsers() {
        $query = "SELECT id, username, full_name, email, role, contact_number, created_at 
                 FROM " . $this->table_name . " ORDER BY created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }

    public function delete($id) {
        try {
            // Check if this is the last admin
            if ($this->isLastAdmin($id)) {
                return [
                    'success' => false,
                    'message' => 'Cannot delete the last admin user'
                ];
            }

            $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            
            if ($stmt->execute(['id' => $id])) {
                return [
                    'success' => true,
                    'message' => 'Employee deleted successfully'
                ];
            }
            
            return [
                'success' => false,
                'message' => 'Error deleting employee'
            ];
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return [
                'success' => false,
                'message' => 'Database error occurred'
            ];
        }
    }

    private function isLastAdmin($id) {
        $query = "SELECT COUNT(*) as admin_count FROM " . $this->table_name . 
                 " WHERE role = 'admin' AND id != :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        
        return $row['admin_count'] == 0;
    }

    public function update($id, $data) {
        try {
            // Validate required fields
            if (empty($data['full_name']) || empty($data['email']) || empty($data['role'])) {
                return ['success' => false, 'message' => 'Required fields are missing'];
            }

            // Check if email exists for other users
            $query = "SELECT id FROM users WHERE email = :email AND id != :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':email', $data['email']);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            if ($stmt->fetch()) {
                return ['success' => false, 'message' => 'Email already exists'];
            }

            // Build update query
            $query = "UPDATE " . $this->table_name . " SET ";
            $sets = [];
            $params = [];
            
            foreach ($data as $key => $value) {
                if ($key === 'password' && !empty($value)) {
                    $sets[] = "$key = :$key";
                    $params[$key] = password_hash($value, PASSWORD_DEFAULT);
                } elseif ($key !== 'password') {
                    $sets[] = "$key = :$key";
                    $params[$key] = $value;
                }
            }
            
            $query .= implode(", ", $sets);
            $query .= " WHERE id = :id";
            $params['id'] = $id;
            
            $stmt = $this->conn->prepare($query);
            
            if ($stmt->execute($params)) {
                return ['success' => true, 'message' => 'Employee updated successfully'];
            }
            
            return ['success' => false, 'message' => 'Error updating employee'];
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return ['success' => false, 'message' => 'Database error occurred'];
        }
    }

    public function getUserById($id) {
        $query = "SELECT id, username, full_name, email, role, contact_number, created_at, last_login 
                 FROM " . $this->table_name . 
                 " WHERE id = :id LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();

        return $stmt->fetch();
    }

    public function login($username, $password) {
        $query = "SELECT id, username, password, role FROM " . $this->table_name . 
                " WHERE username = :username LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":username", $username);
        $stmt->execute();

        if($row = $stmt->fetch()) {
            if(password_verify($password, $row['password'])) {
                $this->updateLastLogin($row['id']);
                return $row;
            }
        }
        return false;
    }

    public function create($data) {
        try {
            // Validate required fields
            if (empty($data['full_name']) || empty($data['email']) || empty($data['password'])) {
                return ['success' => false, 'message' => 'Required fields are missing'];
            }

            // Check if email already exists
            if ($this->emailExists($data['email'])) {
                return ['success' => false, 'message' => 'Email already exists'];
            }

            $query = "INSERT INTO users (full_name, email, password, role, contact_number) 
                      VALUES (:full_name, :email, :password, :role, :contact_number)";
            
            $stmt = $this->conn->prepare($query);
            
            // Hash password
            $password_hash = password_hash($data['password'], PASSWORD_DEFAULT);
            
            // Bind parameters
            $stmt->bindParam(':full_name', $data['full_name']);
            $stmt->bindParam(':email', $data['email']);
            $stmt->bindParam(':password', $password_hash);
            $stmt->bindParam(':role', $data['role']);
            $stmt->bindParam(':contact_number', $data['contact_number']);
            
            if ($stmt->execute()) {
                return ['success' => true, 'message' => 'Employee added successfully'];
            }
            
            return ['success' => false, 'message' => 'Error adding employee'];
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return ['success' => false, 'message' => 'Database error occurred'];
        }
    }

    public function verifyPassword($user_id, $password) {
        $query = "SELECT password FROM " . $this->table_name . " WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $user_id);
        $stmt->execute();

        if ($row = $stmt->fetch()) {
            return password_verify($password, $row['password']);
        }
        return false;
    }

    public function updateLastLogin($id) {
        $query = "UPDATE " . $this->table_name . 
                 " SET last_login = CURRENT_TIMESTAMP 
                 WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        return $stmt->execute(['id' => $id]);
    }

    public function getAllEmployees() {
        try {
            $query = "SELECT * FROM users ORDER BY id DESC";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return [];
        }
    }

    private function emailExists($email) {
        $query = "SELECT id FROM users WHERE email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return $stmt->fetch() !== false;
    }
} 