<?php
/**
 * Audit Helper Utility
 * Helper functions to track detailed changes for audit logging
 */

/**
 * Compare old and new data arrays and return changed fields
 * 
 * @param array $oldData - Original data
 * @param array $newData - Updated data
 * @param array $fieldsToTrack - Specific fields to track (optional, tracks all if not provided)
 * @return array - Array of changed fields with old and new values
 */
function getChangedFields($oldData, $newData, $fieldsToTrack = null) {
    $changes = [];
    
    // If specific fields to track are provided, use those; otherwise track all
    $fields = $fieldsToTrack ?? array_keys($newData);
    
    foreach ($fields as $field) {
        // Skip password fields for security
        if (stripos($field, 'password') !== false) {
            continue;
        }
        
        $oldValue = $oldData[$field] ?? null;
        $newValue = $newData[$field] ?? null;
        
        // Check if value changed
        if ($oldValue != $newValue) {
            $changes[$field] = [
                'old' => $oldValue,
                'new' => $newValue
            ];
        }
    }
    
    return $changes;
}

/**
 * Fetch record data before update for comparison
 * 
 * @param mysqli $con - Database connection
 * @param string $tableName - Table name
 * @param string $idColumn - Primary key column name
 * @param int $recordId - Record ID
 * @return array|null - Record data or null if not found
 */
function fetchRecordForAudit($con, $tableName, $idColumn, $recordId) {
    try {
        $stmt = $con->prepare("SELECT * FROM `$tableName` WHERE `$idColumn` = ? LIMIT 1");
        $stmt->bind_param("i", $recordId);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        $stmt->close();
        return $data;
    } catch (Exception $e) {
        error_log("Audit helper error: " . $e->getMessage());
        return null;
    }
}

/**
 * Format changed fields for display
 * 
 * @param array $changes - Changed fields array from getChangedFields()
 * @return string - Formatted string of changes
 */
function formatChanges($changes) {
    if (empty($changes)) {
        return "No changes detected";
    }
    
    $formatted = [];
    foreach ($changes as $field => $values) {
        $oldVal = $values['old'] ?? 'NULL';
        $newVal = $values['new'] ?? 'NULL';
        $formatted[] = "$field: '$oldVal' → '$newVal'";
    }
    
    return implode(', ', $formatted);
}

/**
 * Create a summary of record data for audit logging
 * 
 * @param array $data - Record data
 * @param array $keyFields - Key fields to include in summary
 * @return string - Summary string
 */
function createRecordSummary($data, $keyFields = ['name', 'code', 'username', 'email']) {
    $summary = [];
    
    foreach ($keyFields as $field) {
        // Check various naming conventions
        $possibleFields = [
            $field,
            $field . '_name',
            'full_' . $field,
            strtolower($field)
        ];
        
        foreach ($possibleFields as $possibleField) {
            if (isset($data[$possibleField]) && !empty($data[$possibleField])) {
                $summary[] = $data[$possibleField];
                break;
            }
        }
    }
    
    return implode(' - ', $summary);
}
?>
