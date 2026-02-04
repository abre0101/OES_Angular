<?php
/**
 * Audit Logger Utility
 * Logs all system activities to the audit_logs table
 */

class AuditLogger {
    private $con;
    
    public function __construct($connection) {
        $this->con = $connection;
    }
    
    /**
     * Log an action to the audit_logs table
     * 
     * @param int|null $userId - The user ID performing the action
     * @param string $userType - Type of user (admin, instructor, student, department_head, unknown)
     * @param string $action - Description of the action performed
     * @param string|null $tableName - Table affected by the action
     * @param int|null $recordId - ID of the record affected
     * @param string|null $oldValue - Previous value (for updates)
     * @param string|null $newValue - New value (for updates)
     * @param array|null $metadata - Additional context data as associative array
     */
    public function log($userId, $userType, $action, $tableName = null, $recordId = null, $oldValue = null, $newValue = null, $metadata = null) {
        try {
            $ipAddress = $this->getClientIP();
            $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
            
            // Convert metadata array to JSON string
            $metadataJson = null;
            if ($metadata !== null && is_array($metadata)) {
                $metadataJson = json_encode($metadata);
            }
            
            $stmt = $this->con->prepare("INSERT INTO audit_logs (user_id, user_type, action, table_name, record_id, old_value, new_value, ip_address, user_agent, metadata, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
            
            $stmt->bind_param("isssssssss", $userId, $userType, $action, $tableName, $recordId, $oldValue, $newValue, $ipAddress, $userAgent, $metadataJson);
            
            $stmt->execute();
            $stmt->close();
            
            return true;
        } catch(Exception $e) {
            // Silently fail to avoid breaking the main application
            error_log("Audit log error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Log a login attempt
     */
    public function logLogin($userId, $userType, $success = true, $username = null) {
        $action = $success ? "Login successful" : "Login failed";
        if($username) {
            $action .= " - Username: " . $username;
        }
        
        // Build metadata with additional context
        $metadata = [
            'event_type' => 'authentication',
            'success' => $success,
            'username' => $username,
            'timestamp' => date('Y-m-d H:i:s')
        ];
        
        // Use 'authentication' as table name for login events
        return $this->log($userId, $userType, $action, 'authentication', null, null, null, $metadata);
    }
    
    /**
     * Log a logout
     */
    public function logLogout($userId, $userType) {
        $metadata = [
            'event_type' => 'authentication',
            'action_type' => 'logout',
            'timestamp' => date('Y-m-d H:i:s')
        ];
        
        return $this->log($userId, $userType, "Logout", 'authentication', null, null, null, $metadata);
    }
    
    /**
     * Log a create action
     */
    public function logCreate($userId, $userType, $tableName, $recordId, $details = null) {
        $action = "Created new record in " . $tableName;
        if($details) {
            $action .= " - " . $details;
        }
        
        $metadata = [
            'operation' => 'create',
            'details' => $details
        ];
        
        return $this->log($userId, $userType, $action, $tableName, $recordId, null, $details, $metadata);
    }
    
    /**
     * Log an update action with detailed tracking
     */
    public function logUpdate($userId, $userType, $tableName, $recordId, $oldValue = null, $newValue = null, $changedFields = null) {
        $action = "Updated record in " . $tableName;
        
        // Build metadata with changed fields information
        $metadata = [
            'operation' => 'update',
            'record_id' => $recordId
        ];
        
        if ($changedFields !== null && is_array($changedFields)) {
            $metadata['changed_fields'] = $changedFields;
            $fieldNames = array_keys($changedFields);
            $action .= " - Fields: " . implode(', ', $fieldNames);
        }
        
        return $this->log($userId, $userType, $action, $tableName, $recordId, $oldValue, $newValue, $metadata);
    }
    
    /**
     * Log a delete action
     */
    public function logDelete($userId, $userType, $tableName, $recordId, $details = null) {
        $action = "Deleted record from " . $tableName;
        if($details) {
            $action .= " - " . $details;
        }
        
        $metadata = [
            'operation' => 'delete',
            'details' => $details,
            'record_id' => $recordId
        ];
        
        return $this->log($userId, $userType, $action, $tableName, $recordId, $details, null, $metadata);
    }
    
    /**
     * Log a password change
     */
    public function logPasswordChange($userId, $userType, $changedByAdmin = false) {
        $action = $changedByAdmin ? "Password reset by administrator" : "Password changed by user";
        
        $metadata = [
            'event_type' => 'security',
            'action_type' => 'password_change',
            'changed_by_admin' => $changedByAdmin,
            'timestamp' => date('Y-m-d H:i:s')
        ];
        
        return $this->log($userId, $userType, $action, 'authentication', $userId, null, null, $metadata);
    }
    
    /**
     * Log unauthorized access attempt
     */
    public function logUnauthorizedAccess($userId, $userType, $attemptedResource) {
        $action = "Unauthorized access attempt to: " . $attemptedResource;
        
        $metadata = [
            'event_type' => 'security',
            'action_type' => 'unauthorized_access',
            'resource' => $attemptedResource,
            'timestamp' => date('Y-m-d H:i:s')
        ];
        
        return $this->log($userId, $userType, $action, 'security', null, null, null, $metadata);
    }
    
    /**
     * Get client IP address
     */
    private function getClientIP() {
        $ipAddress = '';
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ipAddress = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ipAddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ipAddress = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
        }
        return $ipAddress;
    }
}

/**
 * Helper function to quickly log an audit entry
 */
function logAudit($con, $userId, $userType, $action, $tableName = null, $recordId = null, $oldValue = null, $newValue = null, $metadata = null) {
    $logger = new AuditLogger($con);
    return $logger->log($userId, $userType, $action, $tableName, $recordId, $oldValue, $newValue, $metadata);
}
?>
