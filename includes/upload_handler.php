<?php
class UploadHandler {
    private $upload_dir;
    private $allowed_types;
    private $max_size;
    
    public function __construct($upload_dir = '../uploads/', $allowed_types = ['jpg', 'jpeg', 'png'], $max_size = 5242880) {
        $this->upload_dir = $upload_dir;
        $this->allowed_types = $allowed_types;
        $this->max_size = $max_size; // 5MB default
        
        // Create upload directory if it doesn't exist
        if (!is_dir($this->upload_dir)) {
            mkdir($this->upload_dir, 0755, true);
        }
    }
    
    public function uploadFile($file, $device_id) {
        if (!isset($file['error']) || is_array($file['error'])) {
            throw new RuntimeException('Invalid parameters.');
        }
        
        switch ($file['error']) {
            case UPLOAD_ERR_OK:
                break;
            case UPLOAD_ERR_NO_FILE:
                throw new RuntimeException('No file sent.');
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                throw new RuntimeException('Exceeded filesize limit.');
            default:
                throw new RuntimeException('Unknown errors.');
        }
        
        if ($file['size'] > $this->max_size) {
            throw new RuntimeException('Exceeded filesize limit.');
        }
        
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime_type = $finfo->file($file['tmp_name']);
        
        $extension = array_search(
            $mime_type,
            [
                'jpg' => 'image/jpeg',
                'jpeg' => 'image/jpeg',
                'png' => 'image/png'
            ],
            true
        );
        
        if ($extension === false) {
            throw new RuntimeException('Invalid file format.');
        }
        
        // Generate unique filename
        $filename = sprintf(
            '%d_%s.%s',
            $device_id,
            sha1_file($file['tmp_name']),
            $extension
        );
        
        $filepath = $this->upload_dir . $filename;
        
        if (!move_uploaded_file($file['tmp_name'], $filepath)) {
            throw new RuntimeException('Failed to move uploaded file.');
        }
        
        return [
            'filename' => $filename,
            'filepath' => $filepath
        ];
    }
    
    public function deleteFile($filename) {
        $filepath = $this->upload_dir . $filename;
        if (file_exists($filepath)) {
            return unlink($filepath);
        }
        return false;
    }
} 