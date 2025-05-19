<?php

namespace App\Models;

use App\Models\BaseModel;


class UserModel extends BaseModel
{
    protected $table = 'user_account';
    protected $primaryKey = 'ua_id';

    protected $fillable = [
        'ua_profile_url',
        'ua_first_name',
        'ua_last_name',
        'ua_email',
        'ua_hashed_password',
        'ua_phone_number',
        'ua_role_id',
        'ua_is_active',
        'ua_remember_token',
        'ua_remember_token_expires_at',
        'ua_last_login'
    ];

    protected $timestamps = true;
    protected $useSoftDeletes = true;

    protected $createdAtColumn = 'ua_created_at';
    protected $updatedAtColumn = 'ua_updated_at';
    protected $deletedAtColumn = 'ua_deleted_at';

    private function getSoftDeleteCondition($alias = null)
    {
        if (!$this->useSoftDeletes) {
            return "";
        }
        $column = $alias ? "{$alias}.{$this->deletedAtColumn}" : $this->deletedAtColumn;
        return " AND {$column} IS NULL";
    }

    public function findById($id) {
        $sql = "SELECT ua.*, ur.ur_role_name AS role_name
                FROM {$this->table} ua
                JOIN user_role ur ON ua.ua_role_id = ur.ur_id
                WHERE ua.{$this->primaryKey} = :id"
               . $this->getSoftDeleteCondition('ua');
        
        return $this->queryOne($sql, ['id' => $id]);
    }

    public function findByEmail($email)
    {
        $sql = "SELECT ua.*, ur.ur_role_name AS role_name
                FROM {$this->table} ua
                JOIN user_role ur ON ua.ua_role_id = ur.ur_id
                WHERE ua.ua_email = :email"
               . $this->getSoftDeleteCondition('ua');
        
        return $this->queryOne($sql, ['email' => $email]);
    }

    public function getByRole($roleName)
    {
        $sql = "SELECT ua.*, ur.ur_role_name AS role_name
                FROM {$this->table} ua
                JOIN user_role ur ON ua.ua_role_id = ur.ur_id
                WHERE ur.ur_role_name = :role_name"
               . $this->getSoftDeleteCondition('ua');
        
        return $this->query($sql, ['role_name' => $roleName]);
    }

    public function getNewest()
    {
        $sql = "SELECT * FROM {$this->table} ua WHERE 1=1"
               . $this->getSoftDeleteCondition('ua') .
               " ORDER BY ua.{$this->createdAtColumn} DESC";
        
        return $this->query($sql);
    }

    public function search($searchTerm)
    {
        $sql = "SELECT ua.*, ur.ur_role_name AS role_name
                FROM {$this->table} ua
                LEFT JOIN user_role ur ON ua.ua_role_id = ur.ur_id
                WHERE (ua.ua_first_name ILIKE :search_term 
                       OR ua.ua_last_name ILIKE :search_term 
                       OR ua.ua_email ILIKE :search_term)"
               . $this->getSoftDeleteCondition('ua') .
               " ORDER BY ua.ua_last_name, ua.ua_first_name";
        
        return $this->query($sql, ['search_term' => "%{$searchTerm}%"]);
    }

    public function hashPassword($password)
    {
        return password_hash($password, PASSWORD_BCRYPT);
    }

    public function verifyPassword($password, $hash)
    {
        return password_verify($password, $hash);
    }

    public function createUser(array $data)
    {
        $userData = [];
        $mappings = [
            'profile_url' => 'ua_profile_url',
            'first_name' => 'ua_first_name',
            'last_name' => 'ua_last_name',
            'email' => 'ua_email',
            'password' => 'ua_hashed_password',
            'role_id' => 'ua_role_id',
            'is_active' => 'ua_is_active',
            'phone_number' => 'ua_phone_number'
        ];
        
        foreach ($mappings as $input => $dbField) {
            if (isset($data[$input])) {
                $userData[$dbField] = $data[$input];
            }
        }
        
        if (isset($data['password'])) {
            $userData['ua_hashed_password'] = $this->hashPassword($data['password']);
        }

        if ($this->timestamps) {
            $now = date('Y-m-d H:i:s');
            $userData[$this->createdAtColumn] = $now;
            $userData[$this->updatedAtColumn] = $now;
        }
        
        // Filter $userData to only include $fillable fields + timestamp fields
        $allowedKeys = array_merge($this->fillable, [$this->createdAtColumn, $this->updatedAtColumn, $this->deletedAtColumn]);
        $insertData = array_intersect_key($userData, array_flip($allowedKeys));

        $formattedInsert = $this->formatInsertData($insertData);
        
        $sql = "INSERT INTO {$this->table} ({$formattedInsert['columns']}) VALUES ({$formattedInsert['placeholders']})";
        
        $success = $this->execute($sql, $formattedInsert['filteredData']);
        if ($success) {
            return $this->lastInsertId("{$this->table}_{$this->primaryKey}_seq");
        }
        return false;
    }

    public function updateUser($id, array $data)
    {
        $userData = [];
        $mappings = [
            'ua_profile_url' => 'ua_profile_url',
            'first_name' => 'ua_first_name',
            'last_name' => 'ua_last_name',
            'email' => 'ua_email',
            'password' => 'ua_hashed_password',
            'role_id' => 'ua_role_id',
            'is_active' => 'ua_is_active',
            'phone_number' => 'ua_phone_number'
        ];
        
        foreach ($mappings as $input => $dbField) {
            if (isset($data[$input])) {
                $userData[$dbField] = $data[$input];
            }
        }
        
        if (isset($data['password']) && !empty($data['password'])) {
            $userData['ua_hashed_password'] = $this->hashPassword($data['password']);
        } elseif (isset($data['password']) && empty($data['password'])) {
            // Avoid updating password if it's an empty string in the form
            unset($userData['ua_hashed_password']);
        }

        if ($this->timestamps) {
            $userData[$this->updatedAtColumn] = date('Y-m-d H:i:s');
        }
        
        // Filter $userData to only include $fillable fields + updated_at
        $allowedKeys = array_merge($this->fillable, [$this->updatedAtColumn]);
        $updateData = array_intersect_key($userData, array_flip($allowedKeys));
        
        // Prevent updating primary key or created_at from $fillable if accidentally included
        unset($updateData[$this->primaryKey], $updateData[$this->createdAtColumn], $updateData[$this->deletedAtColumn]);


        if (empty($updateData)) {
            return true; // Nothing to update
        }

        $formattedUpdate = $this->formatUpdateData($updateData);
        $params = $formattedUpdate['filteredData'];
        $params['id'] = $id;
        
        $sql = "UPDATE {$this->table} SET {$formattedUpdate['updateClause']} WHERE {$this->primaryKey} = :id";
        // Do not add soft delete condition here, we should be able to update a soft-deleted record if needed (e.g. to restore it)
        
        return $this->execute($sql, $params);
    }

    public function deleteUser($id, $permanent = false)
    {
        if ($permanent) {
            $sql = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = :id";
            return $this->execute($sql, ['id' => $id]);
        }
        
        if ($this->useSoftDeletes) {
            $data = [
                $this->deletedAtColumn => date('Y-m-d H:i:s')
            ];
            if ($this->timestamps) { // Also update the updated_at column during soft delete
                $data[$this->updatedAtColumn] = date('Y-m-d H:i:s');
            }
            
            $formattedUpdate = $this->formatUpdateData($data);
            $params = $formattedUpdate['filteredData'];
            $params['id'] = $id;

            $sql = "UPDATE {$this->table} SET {$formattedUpdate['updateClause']} 
                    WHERE {$this->primaryKey} = :id AND {$this->deletedAtColumn} IS NULL"; // Only soft delete if not already soft-deleted
            return $this->execute($sql, $params);
        } else {
            // If not using soft deletes, permanent delete is the only option
            $sql = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = :id";
            return $this->execute($sql, ['id' => $id]);
        }
    }

    public function emailExists($email)
    {
        $sql = "SELECT COUNT(*) FROM {$this->table} ua 
                WHERE ua.ua_email = :email" 
               . $this->getSoftDeleteCondition('ua');
        return $this->queryScalar($sql, ['email' => $email]) > 0;
    }

    public function getActiveUsers($days = 30)
    {
        $cutoff = date('Y-m-d H:i:s', strtotime("-{$days} days"));
        $sql = "SELECT ua.*, ur.ur_role_name AS role_name
                FROM {$this->table} ua
                JOIN user_role ur ON ua.ua_role_id = ur.ur_id
                WHERE ua.ua_is_active = :is_active
                AND ua.ua_last_login >= :cutoff"
               . $this->getSoftDeleteCondition('ua') .
               " ORDER BY ua.ua_last_login DESC";
        
        return $this->query($sql, ['is_active' => true, 'cutoff' => $cutoff]);
    }

    public function findByRememberToken($token)
    {
        $sql = "SELECT ua.* FROM {$this->table} ua
                WHERE ua.ua_remember_token = :token
                AND ua.ua_remember_token_expires_at > :now"
               . $this->getSoftDeleteCondition('ua');
        
        return $this->queryOne($sql, ['token' => $token, 'now' => date('Y-m-d H:i:s')]);
    }

    public function updateLastLogin($userId)
    {
        $sql = "UPDATE {$this->table} SET ua_last_login = :last_login 
                WHERE {$this->primaryKey} = :id";
        return $this->execute($sql, ['last_login' => date('Y-m-d H:i:s'), 'id' => $userId]);
    }

    public function generateRememberToken($userId, $days = 30)
    {
        $token = bin2hex(random_bytes(32));
        $expiresAt = date('Y-m-d H:i:s', strtotime("+{$days} days"));

        $sql = "UPDATE {$this->table} 
                SET ua_remember_token = :token, ua_remember_token_expires_at = :expires_at
                WHERE {$this->primaryKey} = :id";
        
        $this->execute($sql, [
            'token' => $token,
            'expires_at' => $expiresAt,
            'id' => $userId
        ]);
        return $token;
    }

    public function clearRememberToken($userId)
    {
        $sql = "UPDATE {$this->table} 
                SET ua_remember_token = NULL, ua_remember_token_expires_at = NULL
                WHERE {$this->primaryKey} = :id";
        return $this->execute($sql, ['id' => $userId]);
    }

    public function getFullName($user)
    {
        return $user['ua_first_name'] . ' ' . $user['ua_last_name'];
    }

    public function activateUser($userId)
    {
        $sql = "UPDATE {$this->table} SET ua_is_active = :is_active 
                WHERE {$this->primaryKey} = :id";
        return $this->execute($sql, ['is_active' => true, 'id' => $userId]);
    }

    public function deactivateUser($userId)
    {
        $sql = "UPDATE {$this->table} SET ua_is_active = :is_active
                WHERE {$this->primaryKey} = :id";
        return $this->execute($sql, ['is_active' => false, 'id' => $userId]);
    }

    public function getActiveOnly()
    {
        $sql = "SELECT ua.*, ur.ur_role_name AS role_name
                FROM {$this->table} ua
                JOIN user_role ur ON ua.ua_role_id = ur.ur_id
                WHERE ua.ua_is_active = :is_active"
               . $this->getSoftDeleteCondition('ua');
        return $this->query($sql, ['is_active' => true]);
    }

    public function getInactiveUsers($days = 90)
    {
        $cutoff = date('Y-m-d H:i:s', strtotime("-{$days} days"));
        $sql = "SELECT ua.*, ur.ur_role_name AS role_name
                FROM {$this->table} ua
                JOIN user_role ur ON ua.ua_role_id = ur.ur_id
                WHERE (ua.ua_last_login IS NULL OR ua.ua_last_login < :cutoff)"
               . $this->getSoftDeleteCondition('ua') .
               " ORDER BY ua.ua_last_login ASC NULLS FIRST";
        
        return $this->query($sql, ['cutoff' => $cutoff]);
    }

    public function getAdmins()
    {
        $sql = "SELECT ua.*, ur.ur_role_name AS role_name
                FROM {$this->table} ua
                JOIN user_role ur ON ua.ua_role_id = ur.ur_id
                WHERE ur.ur_role_name = :role"
               . $this->getSoftDeleteCondition('ua');
        return $this->query($sql, ['role' => 'admin']);
    }

    public function getRegularUsers()
    {
        $sql = "SELECT ua.*, ur.ur_role_name AS role_name
                FROM {$this->table} ua
                JOIN user_role ur ON ua.ua_role_id = ur.ur_id
                WHERE ur.ur_role_name = :role"
               . $this->getSoftDeleteCondition('ua');
        return $this->query($sql, ['role' => 'user']);
    }

    public function changeRole($userId, $roleName)
    {
        if (!in_array($roleName, ['user', 'admin'])) {
            return false;
        }
        
        $roleSql = "SELECT ur_id FROM user_role WHERE ur_role_name = :role_name";
        $roleData = $this->queryOne($roleSql, ['role_name' => $roleName]);
        
        if (!$roleData || !isset($roleData['ur_id'])) {
            return false;
        }
        
        $updateSql = "UPDATE {$this->table} SET ua_role_id = :role_id 
                      WHERE {$this->primaryKey} = :id";
        return $this->execute($updateSql, ['role_id' => $roleData['ur_id'], 'id' => $userId]);
    }
    
    /**
     * Cleanup expired remember tokens
     * @return bool Success status
     */
    public function cleanupExpiredTokens()
    {
        $sql = "UPDATE {$this->table} 
                SET ua_remember_token = NULL, ua_remember_token_expires_at = NULL
                WHERE ua_remember_token IS NOT NULL AND ua_remember_token_expires_at < :now";
        // Soft delete condition is not typically applied here, as we are cleaning up tokens
        // regardless of user's soft delete status. If required, it can be added.
        return $this->execute($sql, ['now' => date('Y-m-d H:i:s')]);
    }

/**
 * These are additional methods to add to the UserModel class
 * to support the DataTablesManager integration
 */

/**
 * Get all users with optional filtering
 * 
 * @param string $role Optional role filter ('admin' or 'user')
 * @param string $status Optional status filter ('active' or 'inactive')
 * @return array User records with role information
 */
public function getUsers($role = '', $status = '')
{
    $params = [];
    $sql = "SELECT ua.*, ur.ur_role_name AS role_name
            FROM {$this->table} ua
            JOIN user_role ur ON ua.ua_role_id = ur.ur_id
            WHERE 1=1" . $this->getSoftDeleteCondition('ua');
    
    if (!empty($role)) {
        $sql .= " AND ur.ur_role_name = :role_name";
        $params['role_name'] = $role;
    }
    
    if (!empty($status)) {
        $isActive = ($status === 'active');
        $sql .= " AND ua.ua_is_active = :is_active";
        $params['is_active'] = $isActive;
    }
    
    $sql .= " ORDER BY ua.ua_last_name, ua.ua_first_name";
    return $this->query($sql, $params);
}

/**
 * Get all active users
 * 
 * @return array Active user records
 */
public function getAllActiveUsers()
{
    $sql = "SELECT ua.*, ur.ur_role_name AS role_name
            FROM {$this->table} ua
            JOIN user_role ur ON ua.ua_role_id = ur.ur_id
            WHERE ua.ua_is_active = :is_active"
           . $this->getSoftDeleteCondition('ua') .
           " ORDER BY ua.ua_last_name, ua.ua_first_name";
    return $this->query($sql, ['is_active' => true]);
}

/**
 * Get all inactive users
 * 
 * @return array Inactive user records
 */
public function getAllInactiveUsers()
{
    $sql = "SELECT ua.*, ur.ur_role_name AS role_name
            FROM {$this->table} ua
            JOIN user_role ur ON ua.ua_role_id = ur.ur_id
            WHERE ua.ua_is_active = :is_active"
           . $this->getSoftDeleteCondition('ua') .
           " ORDER BY ua.ua_last_name, ua.ua_first_name";
    return $this->query($sql, ['is_active' => false]);
}

/**
 * Search users by name or email with filtering
 * 
 * @param string $searchTerm The search term
 * @param string $role Optional role filter
 * @param string $status Optional status filter
 * @return array Matching user records
 */
public function searchUsers($searchTerm, $role = '', $status = '')
{
    $params = [
        'search_term_like' => "%{$searchTerm}%",
        'exact_match' => $searchTerm,
        'start_match' => "{$searchTerm}%"
    ];

    $sql = "SELECT ua.*, ur.ur_role_name AS role_name
            FROM {$this->table} ua
            JOIN user_role ur ON ua.ua_role_id = ur.ur_id
            WHERE (ua.ua_first_name ILIKE :search_term_like 
                   OR ua.ua_last_name ILIKE :search_term_like 
                   OR ua.ua_email ILIKE :search_term_like)"
           . $this->getSoftDeleteCondition('ua');
    
    if (!empty($role)) {
        $sql .= " AND ur.ur_role_name = :role_name";
        $params['role_name'] = $role;
    }
    
    if (!empty($status)) {
        $isActive = ($status === 'active');
        $sql .= " AND ua.ua_is_active = :is_active";
        $params['is_active'] = $isActive;
    }
    
    $sql .= " ORDER BY CASE 
                WHEN ua.ua_first_name ILIKE :exact_match OR ua.ua_last_name ILIKE :exact_match THEN 1
                WHEN ua.ua_first_name ILIKE :start_match OR ua.ua_last_name ILIKE :start_match THEN 2
                ELSE 3
              END, ua.ua_last_name, ua.ua_first_name";
    
    return $this->query($sql, $params);
}

/**
 * Get user statistics
 * 
 * @param int $userId User ID
 * @return array User statistics
 */
public function getUserStats($userId)
{
    // In a real implementation, these would be actual database queries
    // For this example, we'll return placeholder data
    return [
        'logins' => rand(1, 100),
        'purchases' => rand(0, 10),
        'sessions' => rand(1, 50),
        'hours' => rand(1, 200),
        'comments' => rand(0, 30),
        'ratings' => rand(0, 20)
    ];
}

/**
 * Get users by registration date range
 * 
 * @param string $startDate Start date in YYYY-MM-DD format
 * @param string $endDate End date in YYYY-MM-DD format
 * @return array User records
 */
public function getUsersByRegistrationDate($startDate, $endDate)
{
    $sql = "SELECT ua.*, ur.ur_role_name AS role_name
            FROM {$this->table} ua
            JOIN user_role ur ON ua.ua_role_id = ur.ur_id
            WHERE ua.{$this->createdAtColumn} BETWEEN :start_date AND :end_date"
           . $this->getSoftDeleteCondition('ua') .
           " ORDER BY ua.{$this->createdAtColumn} DESC";
    
    return $this->query($sql, [
        'start_date' => $startDate . ' 00:00:00',
        'end_date' => $endDate . ' 23:59:59'
    ]);
}

/**
 * Get users by last login date range
 * 
 * @param string $startDate Start date in YYYY-MM-DD format
 * @param string $endDate End date in YYYY-MM-DD format
 * @return array User records
 */
public function getUsersByLastLogin($startDate, $endDate)
{
    $sql = "SELECT ua.*, ur.ur_role_name AS role_name
            FROM {$this->table} ua
            JOIN user_role ur ON ua.ua_role_id = ur.ur_id
            WHERE ua.ua_last_login BETWEEN :start_date AND :end_date"
           . $this->getSoftDeleteCondition('ua') .
           " ORDER BY ua.ua_last_login DESC";
            
    return $this->query($sql, [
        'start_date' => $startDate . ' 00:00:00',
        'end_date' => $endDate . ' 23:59:59'
    ]);
}

/**
 * Get users who have never logged in
 * 
 * @return array User records
 */
public function getNeverLoggedInUsers()
{
    $sql = "SELECT ua.*, ur.ur_role_name AS role_name
            FROM {$this->table} ua
            JOIN user_role ur ON ua.ua_role_id = ur.ur_id
            WHERE ua.ua_last_login IS NULL"
           . $this->getSoftDeleteCondition('ua') .
           " ORDER BY ua.{$this->createdAtColumn} DESC";
           
    return $this->query($sql);
}

/**
 * Get user count by role
 * 
 * @return array Role counts indexed by role name
 */
public function getUserCountByRole()
{
    $roleCounts = [];
    $sql = "SELECT ur.ur_role_name AS role_name, COUNT(ua.{$this->primaryKey}) AS user_count
            FROM {$this->table} ua
            JOIN user_role ur ON ua.ua_role_id = ur.ur_id
            WHERE 1=1"
           . $this->getSoftDeleteCondition('ua') .
           " GROUP BY ur.ur_role_name";
            
    $results = $this->query($sql);
    
    foreach ($results as $row) {
        $roleCounts[$row['role_name']] = (int) $row['user_count'];
    }
    
    return $roleCounts;
}
}