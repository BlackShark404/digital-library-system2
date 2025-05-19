<?php

namespace App\Models;

class ActivityLogModel extends BaseModel
{
    protected $table = 'activity_log';
    protected $primaryKey = 'al_id';
    
    /**
     * Constructor - sets up the activity log model
     */
    public function __construct()
    {
        parent::__construct();
        $this->table = 'activity_log';
    }
    
    /**
     * Get all activity logs with user and activity type details
     * 
     * @return array Array of activity logs
     */
    public function getAllLogs()
    {
        $sql = "
            SELECT 
                al.al_id as id,
                al.ua_id as user_id,
                ua.ua_first_name || ' ' || ua.ua_last_name as username,
                at.at_name as action,
                al.al_description as details,
                al.al_timestamp as timestamp
            FROM 
                activity_log al
            LEFT JOIN 
                user_account ua ON al.ua_id = ua.ua_id
            JOIN 
                activity_type at ON al.at_id = at.at_id
            ORDER BY 
                al.al_timestamp DESC
        ";
        
        return $this->query($sql);
    }
    
    /**
     * Get activity log by ID
     * 
     * @param int $id Activity log ID
     * @return array|null Log details or null if not found
     */
    public function getLogById($id)
    {
        $sql = "
            SELECT 
                al.al_id as id,
                al.ua_id as user_id,
                ua.ua_first_name || ' ' || ua.ua_last_name as username,
                at.at_name as action,
                al.al_description as details,
                al.al_timestamp as timestamp
            FROM 
                activity_log al
            LEFT JOIN 
                user_account ua ON al.ua_id = ua.ua_id
            JOIN 
                activity_type at ON al.at_id = at.at_id
            WHERE 
                al.al_id = :id
        ";
        
        return $this->queryOne($sql, ['id' => $id]);
    }
    
    /**
     * Get all unique action types for filtering
     * 
     * @return array Array of action types
     */
    public function getUniqueActionTypes()
    {
        $sql = "SELECT at_name FROM activity_type ORDER BY at_name";
        return array_column($this->query($sql), 'at_name');
    }
    
    /**
     * Count total records for DataTables
     * 
     * @return int Total count
     */
    public function countData()
    {
        $sql = "SELECT COUNT(*) FROM {$this->table}";
        return $this->queryScalar($sql);
    }
    
    /**
     * Count filtered records for DataTables
     * 
     * @param string $search Search term
     * @param string $actionFilter Optional action type filter
     * @return int Filtered count
     */
    public function countFilteredData($search, $actionFilter = '')
    {
        $sql = "
            SELECT 
                COUNT(*)
            FROM 
                activity_log al
            LEFT JOIN 
                user_account ua ON al.ua_id = ua.ua_id
            JOIN 
                activity_type at ON al.at_id = at.at_id
            WHERE 1=1
        ";
        
        $params = [];
        
        // Add action filter if provided
        if (!empty($actionFilter)) {
            $sql .= " AND at.at_name = :action_filter";
            $params['action_filter'] = $actionFilter;
        }
        
        // Add search filter if provided
        if (!empty($search)) {
            $sql .= "
                AND (
                    ua.ua_first_name ILIKE :search OR
                    ua.ua_last_name ILIKE :search OR
                    at.at_name ILIKE :search OR
                    al.al_description ILIKE :search
                )
            ";
            $params['search'] = "%$search%";
        }
        
        return $this->queryScalar($sql, $params);
    }
    
    /**
     * Get data for DataTables with filtering, ordering and pagination
     * 
     * @param int $start Start position
     * @param int $length Number of records to return
     * @param string $search Search term
     * @param string $orderColumn Column to order by
     * @param string $orderDir Direction to order (asc/desc)
     * @param string $actionFilter Optional action type filter
     * @return array Array of activity logs
     */
    public function getDataTableData($start, $length, $search, $orderColumn, $orderDir, $actionFilter = '')
    {
        // Map front-end column names to database column names
        $columnMap = [
            'id' => 'al.al_id',
            'user_id' => 'al.ua_id',
            'username' => 'ua.ua_first_name',
            'action' => 'at.at_name',
            'details' => 'al.al_description',
            'timestamp' => 'al.al_timestamp'
        ];
        
        // Use mapped column or default to al_id
        $dbOrderColumn = $columnMap[$orderColumn] ?? 'al.al_id';
        
        $sql = "
            SELECT 
                al.al_id as id,
                al.ua_id as user_id,
                ua.ua_first_name || ' ' || ua.ua_last_name as username,
                at.at_name as action,
                al.al_description as details,
                al.al_timestamp as timestamp
            FROM 
                activity_log al
            LEFT JOIN 
                user_account ua ON al.ua_id = ua.ua_id
            JOIN 
                activity_type at ON al.at_id = at.at_id
            WHERE 1=1
        ";
        
        $params = [];
        
        // Add action filter if provided
        if (!empty($actionFilter)) {
            $sql .= " AND at.at_name = :action_filter";
            $params['action_filter'] = $actionFilter;
        }
        
        // Add search filter if provided
        if (!empty($search)) {
            $sql .= "
                AND (
                    ua.ua_first_name ILIKE :search OR
                    ua.ua_last_name ILIKE :search OR
                    at.at_name ILIKE :search OR
                    al.al_description ILIKE :search
                )
            ";
            $params['search'] = "%$search%";
        }
        
        $sql .= "
            ORDER BY {$dbOrderColumn} {$orderDir}
            LIMIT :limit OFFSET :offset
        ";
        
        $params['limit'] = $length;
        $params['offset'] = $start;
        
        return $this->query($sql, $params);
    }
    
    /**
     * Add log record
     * 
     * @param array $data Log data
     * @return int|bool ID of new record or false on failure
     */
    public function createData($data)
    {
        try {
            $sql = "
                INSERT INTO activity_log (ua_id, at_id, al_description, al_timestamp)
                VALUES (:ua_id, :at_id, :description, CURRENT_TIMESTAMP)
                RETURNING al_id
            ";
            
            $params = [
                'ua_id' => $data['user_id'] ?? null,
                'at_id' => $data['action_type_id'],
                'description' => $data['description']
            ];
            
            $id = $this->queryScalar($sql, $params);
            return $id;
        } catch (\Exception $e) {
            error_log("Error creating log entry: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Update log record
     * 
     * @param int $id Log ID
     * @param array $data Updated data
     * @return bool Success or failure
     */
    public function updateData($id, $data)
    {
        try {
            $sql = "
                UPDATE activity_log
                SET al_description = :description
                WHERE al_id = :id
            ";
            
            $params = [
                'id' => $id,
                'description' => $data['description']
            ];
            
            $this->execute($sql, $params);
            return true;
        } catch (\Exception $e) {
            error_log("Error updating log entry: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Delete log record
     * 
     * @param int $id Log ID
     * @return bool Success or failure
     */
    public function deleteData($id)
    {
        try {
            $sql = "DELETE FROM activity_log WHERE al_id = :id";
            $this->execute($sql, ['id' => $id]);
            return true;
        } catch (\Exception $e) {
            error_log("Error deleting log entry: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get activity statistics by action type
     * 
     * @return array Action type statistics
     */
    public function getActivityStats()
    {
        $sql = "
            SELECT 
                at.at_name as action,
                COUNT(*) as count
            FROM 
                activity_log al
            JOIN 
                activity_type at ON al.at_id = at.at_id
            GROUP BY 
                at.at_name
            ORDER BY 
                count DESC
        ";
        
        return $this->query($sql);
    }
} 