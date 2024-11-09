<?php
class Device {
    private $conn;
    private $table_name = "devices";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getDeviceById($id) {
        try {
            $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->execute(['id' => $id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    public function addDeviceHistory($device_id, $user_id, $data) {
        $query = "INSERT INTO device_history (device_id, user_id, status_change, notes) 
                 VALUES (:device_id, :user_id, :status_change, :notes)";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(":device_id", $device_id);
        $stmt->bindParam(":user_id", $user_id);
        $stmt->bindParam(":status_change", $data['status_change']);
        $stmt->bindParam(":notes", $data['notes']);
        
        return $stmt->execute();
    }

    public function getDeviceHistory($device_id) {
        $query = "SELECT dh.*, u.full_name as user_name 
                 FROM device_history dh
                 LEFT JOIN users u ON dh.user_id = u.id
                 WHERE dh.device_id = :device_id 
                 ORDER BY dh.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":device_id", $device_id);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }

    public function getTotalDevices() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch();
        return $row['total'];
    }

    public function getDevicesByStatus($status) {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name . 
                " WHERE status = :status";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":status", $status);
        $stmt->execute();
        $row = $stmt->fetch();
        return $row['total'];
    }

    public function getRecentDevices($limit = 5) {
        $query = "SELECT * FROM " . $this->table_name . 
                " ORDER BY created_at DESC LIMIT :limit";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(":limit", $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getDeviceTypeStats() {
        $query = "SELECT device_type, COUNT(*) as count FROM " . $this->table_name . 
                " GROUP BY device_type";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getStatusStats() {
        $query = "SELECT status, COUNT(*) as count FROM " . $this->table_name . 
                " GROUP BY status";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function create($data) {
        $query = "INSERT INTO " . $this->table_name . " 
                  (device_name, device_type, problem_description, client_name, 
                   client_contact, status, assigned_to";
        
        if ($data['device_type'] == 'Computer') {
            $query .= ", ram_size, drive_size";
        }
        
        $query .= ") VALUES (:device_name, :device_type, :problem_description, 
                            :client_name, :client_contact, :status, :assigned_to";
        
        if ($data['device_type'] == 'Computer') {
            $query .= ", :ram_size, :drive_size";
        }
        
        $query .= ")";
        
        $stmt = $this->conn->prepare($query);
        
        $params = [
            'device_name' => $data['device_name'],
            'device_type' => $data['device_type'],
            'problem_description' => $data['problem_description'],
            'client_name' => $data['client_name'],
            'client_contact' => $data['client_contact'],
            'status' => $data['status'],
            'assigned_to' => $data['assigned_to']
        ];
        
        if ($data['device_type'] == 'Computer') {
            $params['ram_size'] = $data['ram_size'];
            $params['drive_size'] = $data['drive_size'];
        }
        
        return $stmt->execute($params);
    }

    public function update($id, $data) {
        try {
            // Validate required fields
            $required_fields = ['device_name', 'device_type', 'problem_description', 
                              'client_name', 'client_contact', 'status'];
            
            foreach ($required_fields as $field) {
                if (empty($data[$field])) {
                    return [
                        'success' => false,
                        'message' => ucfirst(str_replace('_', ' ', $field)) . ' is required'
                    ];
                }
            }

            // Start building the query
            $query = "UPDATE " . $this->table_name . " SET 
                     device_name = :device_name,
                     device_type = :device_type,
                     problem_description = :problem_description,
                     client_name = :client_name,
                     client_contact = :client_contact,
                     status = :status";

            // Add computer-specific fields if device type is Computer
            if ($data['device_type'] == 'Computer') {
                $query .= ", ram_size = :ram_size, drive_size = :drive_size";
            } else {
                $query .= ", ram_size = NULL, drive_size = NULL";
            }

            $query .= " WHERE id = :id";

            $stmt = $this->conn->prepare($query);

            // Bind the base parameters
            $params = [
                'id' => $id,
                'device_name' => $data['device_name'],
                'device_type' => $data['device_type'],
                'problem_description' => $data['problem_description'],
                'client_name' => $data['client_name'],
                'client_contact' => $data['client_contact'],
                'status' => $data['status']
            ];

            // Bind computer-specific parameters if needed
            if ($data['device_type'] == 'Computer') {
                $params['ram_size'] = $data['ram_size'] ?? null;
                $params['drive_size'] = $data['drive_size'] ?? null;
            }

            if ($stmt->execute($params)) {
                return [
                    'success' => true,
                    'message' => 'Device updated successfully'
                ];
            }

            return [
                'success' => false,
                'message' => 'Error updating device'
            ];
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return [
                'success' => false,
                'message' => 'Database error occurred'
            ];
        }
    }

    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        
        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    public function getFilteredDevices($search) {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name;
        $params = [];
        
        if (!empty($search)) {
            $query .= " WHERE device_name LIKE :search 
                       OR device_type LIKE :search 
                       OR client_name LIKE :search 
                       OR status LIKE :search";
            $params[':search'] = "%{$search}%";
        }
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        $row = $stmt->fetch();
        return $row['total'];
    }

    public function getDevicesForDataTable($start, $length, $search, $order_column, $order_dir) {
        $columns = ['id', 'device_name', 'device_type', 'client_name', 'status', 'created_at'];
        
        $query = "SELECT * FROM " . $this->table_name;
        $params = [];
        
        if (!empty($search)) {
            $query .= " WHERE device_name LIKE :search 
                       OR device_type LIKE :search 
                       OR client_name LIKE :search 
                       OR status LIKE :search";
            $params[':search'] = "%{$search}%";
        }
        
        if (isset($columns[$order_column])) {
            $query .= " ORDER BY " . $columns[$order_column] . " " . $order_dir;
        }
        
        $query .= " LIMIT :start, :length";
        $params[':start'] = (int) $start;
        $params[':length'] = (int) $length;
        
        $stmt = $this->conn->prepare($query);
        
        // Bind parameters
        foreach ($params as $key => &$value) {
            if (in_array($key, [':start', ':length'])) {
                $stmt->bindValue($key, $value, PDO::PARAM_INT);
            } else {
                $stmt->bindValue($key, $value);
            }
        }
        
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getReportData($start_date, $end_date) {
        $query = "SELECT d.*, 
                 CASE 
                    WHEN d.status = 'Completed' THEN 
                        (SELECT MAX(created_at) FROM device_history 
                         WHERE device_id = d.id AND status_change = 'Completed')
                    ELSE NULL 
                 END as completion_date,
                 CASE 
                    WHEN d.status = 'Completed' THEN 
                        DATEDIFF((SELECT MAX(created_at) FROM device_history 
                                WHERE device_id = d.id AND status_change = 'Completed'), 
                                d.created_at)
                    ELSE DATEDIFF(CURRENT_DATE, d.created_at)
                 END as duration
                 FROM " . $this->table_name . " d
                 WHERE d.created_at BETWEEN :start_date AND :end_date
                 ORDER BY d.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":start_date", $start_date);
        $stmt->bindParam(":end_date", $end_date);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }

    public function getStatusSummary($start_date, $end_date) {
        $query = "SELECT status, COUNT(*) as count 
                 FROM " . $this->table_name . "
                 WHERE created_at BETWEEN :start_date AND :end_date
                 GROUP BY status
                 ORDER BY status";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":start_date", $start_date);
        $stmt->bindParam(":end_date", $end_date);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }

    public function getTypeSummary($start_date, $end_date) {
        $query = "SELECT device_type, COUNT(*) as count 
                 FROM " . $this->table_name . "
                 WHERE created_at BETWEEN :start_date AND :end_date
                 GROUP BY device_type
                 ORDER BY count DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":start_date", $start_date);
        $stmt->bindParam(":end_date", $end_date);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }

    public function addAttachment($device_id, $filename, $filepath) {
        $query = "INSERT INTO device_attachments (device_id, file_name, file_path) 
                 VALUES (:device_id, :file_name, :file_path)";
        
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            'device_id' => $device_id,
            'file_name' => $filename,
            'file_path' => $filepath
        ]);
    }

    public function getAttachments($device_id) {
        $query = "SELECT * FROM device_attachments 
                 WHERE device_id = :device_id 
                 ORDER BY uploaded_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute(['device_id' => $device_id]);
        return $stmt->fetchAll();
    }

    public function deleteAttachment($id) {
        $query = "DELETE FROM device_attachments WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute(['id' => $id]);
    }

    public function getAttachmentById($id) {
        $query = "SELECT * FROM device_attachments WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    public function getAllDevices() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }
} 