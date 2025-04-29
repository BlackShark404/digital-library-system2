<?php

namespace App\Models;

use App\Models\BaseModel;

/**
 * User Model
 * 
 * This model represents a user in the system and demonstrates how to
 * extend the BaseModel class with specific functionality.
 */
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

    protected $searchableFields = ['ua_first_name', 'ua_last_name', 'ua_email'];

    protected $timestamps = true;
    protected $useSoftDeletes = true;

    protected $createdAtColumn = 'ua_created_at';
    protected $updatedAtColumn = 'ua_updated_at';
    protected $deletedAtColumn = 'ua_deleted_at';

    public function findById($id) {
        return $this->select('user_account.*, user_role.ur_role_name AS role_name')
                    ->join('user_role', 'user_account.ua_role_id', 'user_role.ur_id')
                    ->where('user_account.ua_id = :id')
                    ->bind(['id' => $id])
                    ->whereSoftDeleted('user_account')
                    ->first();
    }

    public function findByEmail($email)
    {
        return $this->select('user_account.*, user_role.ur_role_name AS role_name')
                    ->join('user_role', 'user_account.ua_role_id', 'user_role.ur_id')
                    ->where('user_account.ua_email = :email')
                    ->bind(['email' => $email])
                    ->first();
    }

    public function getByRole($roleName)
    {
        return $this->select('user_account.*, user_role.ur_role_name AS role_name')
                    ->join('user_role', 'user_account.ua_role_id', 'user_role.ur_id')
                    ->where('user_role.ur_role_name = :role_name')
                    ->bind(['role_name' => $roleName])
                    ->whereSoftDeleted('user_account')
                    ->get();
    }

    public function getNewest()
    {
        return $this->orderBy('ua_created_at DESC')
                    ->get();
    }

    public function search($searchTerm)
    {
        return $this->where("ua_first_name ILIKE :search_term OR ua_last_name ILIKE :search_term OR ua_email ILIKE :search_term")
                    ->bind(['search_term' => "%$searchTerm%"])
                    ->whereSoftDeleted()
                    ->orderBy('ua_last_name, ua_first_name')
                    ->get();
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
        // Map input data fields to database columns
        $userData = [];
        
        // Field mappings from input to database columns
        $mappings = [
            'profile_url' => 'ua_profile_url',
            'first_name' => 'ua_first_name',
            'last_name' => 'ua_last_name',
            'email' => 'ua_email',
            'password' => 'ua_hashed_password',  // Note: Changed to ua_hashed_password
            'role_id' => 'ua_role_id',
            'is_active' => 'ua_is_active',
            'phone_number' => 'ua_phone_number'
        ];
        
        foreach ($mappings as $input => $dbField) {
            if (isset($data[$input])) {
                $userData[$dbField] = $data[$input];
            }
        }
        
        // Hash password if it exists
        if (isset($data['password'])) {
            $userData['ua_hashed_password'] = $this->hashPassword($data['password']);
        }
        
        // No need to manually set timestamps as BaseModel.insert() handles this
        return $this->insert($userData);
    }

    public function updateUser($id, array $data)
    {
        // Map input data fields to database columns
        $userData = [];
        
        // Field mappings from input to database columns
        $mappings = [
            'profile_url' => 'ua_profile_url',
            'first_name' => 'ua_first_name',
            'last_name' => 'ua_last_name',
            'email' => 'ua_email',
            'password' => 'ua_hashed_password',  // Note: Changed to ua_hashed_password
            'role_id' => 'ua_role_id',
            'is_active' => 'ua_is_active',
            'phone_number' => 'ua_phone_number'
        ];
        
        foreach ($mappings as $input => $dbField) {
            if (isset($data[$input])) {
                $userData[$dbField] = $data[$input];
            }
        }
        
        // Hash password if it exists
        if (isset($data['password'])) {
            $userData['ua_hashed_password'] = $this->hashPassword($data['password']);
        }
        
        // No need to manually set updated_at as BaseModel.update() handles this
        return $this->update($userData, "{$this->primaryKey} = :id", ['id' => $id]);
    }

    /**
     * Delete a user by ID
     * 
     * @param int $id The user ID to delete
     * @param bool $permanent Whether to permanently delete the user or use soft delete
     * @return bool Success status
     */
    public function deleteUser($id, $permanent = false)
    {
        if ($permanent && $this->useSoftDeletes) {
            // Permanently delete the user from the database
            return $this->execute(
                "DELETE FROM {$this->table} WHERE {$this->primaryKey} = :id",
                ['id' => $id]
            );
        }
        
        // Use the standard delete method which respects soft deletes
        return $this->delete("{$this->primaryKey} = :id", ['id' => $id]);
    }

    public function emailExists($email)
    {
        return $this->exists('ua_email = :email', ['email' => $email]);
    }

    public function getActiveUsers($days = 30)
    {
        $cutoff = date('Y-m-d H:i:s', strtotime("-$days days"));
        return $this->where('ua_is_active = :is_active')
                    ->where('ua_last_login >= :cutoff')
                    ->bind([
                        'is_active' => true,
                        'cutoff' => $cutoff
                    ])
                    ->whereSoftDeleted()
                    ->orderBy('ua_last_login DESC')
                    ->get();
    }

    public function findByRememberToken($token)
    {
        // Add check for token expiration
        return $this->where('ua_remember_token = :token')
                    ->where('ua_remember_token_expires_at > NOW()')
                    ->bind(['token' => $token])
                    ->first();
    }

    public function updateLastLogin($userId)
    {
        return $this->update(
            [
                'ua_last_login' => date('Y-m-d H:i:s')
            ],
            "{$this->primaryKey} = :id",
            ['id' => $userId]
        );
    }

    public function generateRememberToken($userId, $days = 30)
    {
        $token = bin2hex(random_bytes(32));
        $expiresAt = date('Y-m-d H:i:s', strtotime("+$days days"));

        $this->update(
            [
                'ua_remember_token' => $token,
                'ua_remember_token_expires_at' => $expiresAt
            ],
            "{$this->primaryKey} = :id",
            ['id' => $userId]
        );

        return $token;
    }

    public function clearRememberToken($userId)
    {
        return $this->update(
            [
                'ua_remember_token' => null,
                'ua_remember_token_expires_at' => null
            ],
            "{$this->primaryKey} = :id",
            ['id' => $userId]
        );
    }

    public function getFullName($user)
    {
        return $user['ua_first_name'] . ' ' . $user['ua_last_name'];
    }

    public function activateUser($userId)
    {
        return $this->update(
            ['ua_is_active' => true],
            "{$this->primaryKey} = :id",
            ['id' => $userId]
        );
    }

    public function deactivateUser($userId)
    {
        return $this->update(
            ['ua_is_active' => false],
            "{$this->primaryKey} = :id",
            ['id' => $userId]
        );
    }

    public function getActiveOnly()
    {
        return $this->where('ua_is_active = :is_active')
                    ->bind(['is_active' => true])
                    ->get();
    }

    public function getInactiveUsers($days = 90)
    {
        $cutoff = date('Y-m-d H:i:s', strtotime("-$days days"));

        return $this->where('(ua_last_login IS NULL OR ua_last_login < :cutoff)')
                    ->bind(['cutoff' => $cutoff])
                    ->whereSoftDeleted()
                    ->orderBy('ua_last_login ASC NULLS FIRST')
                    ->get();
    }

    public function getAdmins()
    {
        return $this->select('user_account.*')
                    ->join('user_role', 'user_account.ua_role_id', 'user_role.ur_id')
                    ->where('user_role.ur_role_name = :role')
                    ->bind(['role' => 'admin'])
                    ->get();
    }

    public function getRegularUsers()
    {
        return $this->select('user_account.*')
                    ->join('user_role', 'user_account.ua_role_id', 'user_role.ur_id')
                    ->where('user_role.ur_role_name = :role')
                    ->bind(['role' => 'user'])
                    ->get();
    }

    public function changeRole($userId, $role)
    {
        if (!in_array($role, ['user', 'admin'])) {
            return false;
        }
        
        // Get role ID from role name
        $roleQuery = $this->execute(
            "SELECT ur_id FROM user_role WHERE ur_role_name = :role_name",
            ['role_name' => $role]
        );
        $roleData = $roleQuery->fetch(\PDO::FETCH_ASSOC);
        
        if (!$roleData) {
            return false;
        }
        
        return $this->update(
            ['ua_role_id' => $roleData['ur_id']],
            "{$this->primaryKey} = :id",
            ['id' => $userId]
        );
    }
    
    /**
     * Cleanup expired remember tokens
     * @return bool Success status
     */
    public function cleanupExpiredTokens()
    {
        return $this->update(
            [
                'ua_remember_token' => null,
                'ua_remember_token_expires_at' => null
            ],
            "ua_remember_token IS NOT NULL AND ua_remember_token_expires_at < NOW()",
            []
        );
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
    // Start with a select that includes role name
    $this->select('user_account.*, user_role.ur_role_name AS role_name')
         ->join('user_role', 'user_account.ua_role_id', 'user_role.ur_id')
         ->whereSoftDeleted('user_account');
    
    // Apply role filter if specified
    if (!empty($role)) {
        $this->where('user_role.ur_role_name = :role_name')
             ->bind(['role_name' => $role]);
    }
    
    // Apply status filter if specified
    if (!empty($status)) {
        $isActive = ($status === 'active') ? 1 : 0;
        $this->where('user_account.ua_is_active = :is_active')
             ->bind(['is_active' => $isActive]);
    }
    
    // Order by last name, first name
    $this->orderBy('user_account.ua_last_name, user_account.ua_first_name');
    
    // Get results
    return $this->get();
}

/**
 * Get all active users
 * 
 * @return array Active user records
 */
public function getAllActiveUsers()
{
    return $this->select('user_account.*, user_role.ur_role_name AS role_name')
                ->join('user_role', 'user_account.ua_role_id', 'user_role.ur_id')
                ->where('user_account.ua_is_active = :is_active')
                ->bind(['is_active' => 1])
                ->whereSoftDeleted('user_account')
                ->orderBy('user_account.ua_last_name, user_account.ua_first_name')
                ->get();
}

/**
 * Get all inactive users
 * 
 * @return array Inactive user records
 */
public function getAllInactiveUsers()
{
    return $this->select('user_account.*, user_role.ur_role_name AS role_name')
                ->join('user_role', 'user_account.ua_role_id', 'user_role.ur_id')
                ->where('user_account.ua_is_active = :is_active')
                ->bind(['is_active' => 0])
                ->whereSoftDeleted('user_account')
                ->orderBy('user_account.ua_last_name, user_account.ua_first_name')
                ->get();
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
    // Start with a basic select
    $this->select('user_account.*, user_role.ur_role_name AS role_name')
         ->join('user_role', 'user_account.ua_role_id', 'user_role.ur_id')
         ->whereSoftDeleted('user_account');
    
    // Add search conditions
    $this->where("(user_account.ua_first_name LIKE :search_term OR user_account.ua_last_name LIKE :search_term OR user_account.ua_email LIKE :search_term)")
         ->bind(['search_term' => "%$searchTerm%"]);
    
    // Apply role filter if specified
    if (!empty($role)) {
        $this->where('user_role.ur_role_name = :role_name')
             ->bind(['role_name' => $role]);
    }
    
    // Apply status filter if specified
    if (!empty($status)) {
        $isActive = ($status === 'active') ? 1 : 0;
        $this->where('user_account.ua_is_active = :is_active')
             ->bind(['is_active' => $isActive]);
    }
    
    // Order by relevance (name matches first)
    $this->orderBy("CASE 
        WHEN user_account.ua_first_name LIKE :exact_match OR user_account.ua_last_name LIKE :exact_match THEN 1
        WHEN user_account.ua_first_name LIKE :start_match OR user_account.ua_last_name LIKE :start_match THEN 2
        ELSE 3
    END, user_account.ua_last_name, user_account.ua_first_name")
    ->bind([
        'exact_match' => $searchTerm,
        'start_match' => "$searchTerm%"
    ]);
    
    // Get results
    return $this->get();
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
    return $this->select('user_account.*, user_role.ur_role_name AS role_name')
                ->join('user_role', 'user_account.ua_role_id', 'user_role.ur_id')
                ->where('user_account.ua_created_at BETWEEN :start_date AND :end_date')
                ->bind([
                    'start_date' => $startDate . ' 00:00:00',
                    'end_date' => $endDate . ' 23:59:59'
                ])
                ->whereSoftDeleted('user_account')
                ->orderBy('user_account.ua_created_at DESC')
                ->get();
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
    return $this->select('user_account.*, user_role.ur_role_name AS role_name')
                ->join('user_role', 'user_account.ua_role_id', 'user_role.ur_id')
                ->where('user_account.ua_last_login BETWEEN :start_date AND :end_date')
                ->bind([
                    'start_date' => $startDate . ' 00:00:00',
                    'end_date' => $endDate . ' 23:59:59'
                ])
                ->whereSoftDeleted('user_account')
                ->orderBy('user_account.ua_last_login DESC')
                ->get();
}

/**
 * Get users who have never logged in
 * 
 * @return array User records
 */
public function getNeverLoggedInUsers()
{
    return $this->select('user_account.*, user_role.ur_role_name AS role_name')
                ->join('user_role', 'user_account.ua_role_id', 'user_role.ur_id')
                ->whereNull('user_account.ua_last_login')
                ->whereSoftDeleted('user_account')
                ->orderBy('user_account.ua_created_at DESC')
                ->get();
}


/**
 * Get user count by role
 * 
 * @return array Role counts indexed by role name
 */
public function getUserCountByRole()
{
    $roleCounts = [];
    
    $results = $this->select('user_role.ur_role_name AS role_name, COUNT(*) AS user_count')
                    ->join('user_role', 'user_account.ua_role_id', 'user_role.ur_id')
                    ->whereSoftDeleted('user_account')
                    ->groupBy('user_role.ur_role_name')
                    ->get();
    
    foreach ($results as $row) {
        $roleCounts[$row['role_name']] = (int) $row['user_count'];
    }
    
    return $roleCounts;
}

/**
 * Get user registrations by month
 * 
 * @param int $months Number of months to include
 * @return array Monthly registration counts
 */
public function getUserRegistrationsByMonth($months = 12)
{
    $registrations = [];
    
    // Calculate date range
    $endDate = date('Y-m-d');
    $startDate = date('Y-m-d', strtotime("-$months months"));
    
    $sql = "
        SELECT 
            DATE_FORMAT(ua_created_at, '%Y-%m') AS month,
            COUNT(*) AS count
        FROM 
            {$this->table}
        WHERE 
            ua_created_at BETWEEN ? AND ?
            AND {$this->deletedAtColumn} IS NULL
        GROUP BY 
            DATE_FORMAT(ua_created_at, '%Y-%m')
        ORDER BY 
            month ASC
    ";
    
    $stmt = $this->db->prepare($sql);
    $stmt->execute([$startDate, $endDate]);
    $results = $stmt->fetchAll(\PDO::FETCH_ASSOC);
    
    // Fill gaps in the data
    $currentDate = new \DateTime($startDate);
    $endDateTime = new \DateTime($endDate);
    
    while ($currentDate <= $endDateTime) {
        $monthKey = $currentDate->format('Y-m');
        $registrations[$monthKey] = 0;
        $currentDate->modify('+1 month');
    }
    
    // Add actual counts
    foreach ($results as $row) {
        $registrations[$row['month']] = (int) $row['count'];
    }
    
    return $registrations;
}
}