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
    protected $table = 'users';
    protected $primaryKey = 'id';

    protected $fillable = [
        'profile_url',
        'first_name',
        'last_name',
        'email',
        'password',
        'role_id',
        'is_active',
        'remember_token',
        'remember_token_expires_at',
        'last_login'
    ];

    protected $timestamps = true;
    protected $useSoftDeletes = true;

    public function findByEmail($email)
    {
        return $this->select('users.*, roles.name AS role_name')
                    ->join('roles', 'users.role_id', 'roles.id')
                    ->where('users.email = :email')
                    ->bind(['email' => $email])
                    ->first();
    }

    public function getByRole($roleName)
    {
        return $this->select('users.*, roles.name AS role_name')
                    ->join('roles', 'users.role_id', 'roles.id')
                    ->where('roles.name = :role_name')
                    ->bind(['role_name' => $roleName])
                    ->whereSoftDeleted('users')
                    ->get();
    }

    public function getNewest()
    {
        return $this->orderBy('created_at DESC')
                    ->get();
    }

    public function search($searchTerm)
    {
        return $this->where("first_name ILIKE :search_term OR last_name ILIKE :search_term OR email ILIKE :search_term")
                    ->bind(['search_term' => "%$searchTerm%"])
                    ->whereSoftDeleted()
                    ->orderBy('last_name, first_name')
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
        if (isset($data['password'])) {
            $data['password'] = $this->hashPassword($data['password']);
        }
        
        // No need to manually set timestamps as BaseModel.insert() handles this
        return $this->insert($data);
    }

    public function updateUser($id, array $data)
    {
        if (isset($data['password'])) {
            $data['password'] = $this->hashPassword($data['password']);
        }

        // No need to manually set updated_at as BaseModel.update() handles this
        return $this->update($data, "{$this->primaryKey} = :id", ['id' => $id]);
    }

    public function emailExists($email)
    {
        return $this->exists('email = :email', ['email' => $email]);
    }

    public function getActiveUsers($days = 30)
    {
        $cutoff = date('Y-m-d H:i:s', strtotime("-$days days"));
        return $this->where('is_active = :is_active')
                    ->where('last_login >= :cutoff')
                    ->bind([
                        'is_active' => true,
                        'cutoff' => $cutoff
                    ])
                    ->whereSoftDeleted()
                    ->orderBy('last_login DESC')
                    ->get();
    }

    public function findByRememberToken($token)
    {
        // Add check for token expiration
        return $this->where('remember_token = :token')
                    ->where('remember_token_expires_at > NOW()')
                    ->bind(['token' => $token])
                    ->first();
    }

    public function updateLastLogin($userId)
    {
        return $this->update(
            [
                'last_login' => date('Y-m-d H:i:s')
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
                'remember_token' => $token,
                'remember_token_expires_at' => $expiresAt
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
                'remember_token' => null,
                'remember_token_expires_at' => null
            ],
            "{$this->primaryKey} = :id",
            ['id' => $userId]
        );
    }

    public function getFullName($user)
    {
        return $user['first_name'] . ' ' . $user['last_name'];
    }

    public function activateUser($userId)
    {
        return $this->update(
            ['is_active' => true],
            "{$this->primaryKey} = :id",
            ['id' => $userId]
        );
    }

    public function deactivateUser($userId)
    {
        return $this->update(
            ['is_active' => false],
            "{$this->primaryKey} = :id",
            ['id' => $userId]
        );
    }

    public function getActiveOnly()
    {
        return $this->where('is_active = :is_active')
                    ->bind(['is_active' => true])
                    ->get();
    }

    public function getInactiveUsers($days = 90)
    {
        $cutoff = date('Y-m-d H:i:s', strtotime("-$days days"));

        return $this->where('(last_login IS NULL OR last_login < :cutoff)')
                    ->bind(['cutoff' => $cutoff])
                    ->whereSoftDeleted()
                    ->orderBy('last_login ASC NULLS FIRST')
                    ->get();
    }

    public function getAdmins()
    {
        return $this->where('role = :role')
                    ->bind(['role' => 'admin'])
                    ->get();
    }

    public function getRegularUsers()
    {
        return $this->where('role = :role')
                    ->bind(['role' => 'user'])
                    ->get();
    }

    public function changeRole($userId, $role)
    {
        if (!in_array($role, ['user', 'admin'])) {
            return false;
        }

        return $this->update(
            ['role' => $role],
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
                'remember_token' => null,
                'remember_token_expires_at' => null
            ],
            "remember_token IS NOT NULL AND remember_token_expires_at < NOW()",
            []
        );
    }

    /**
     * Get paginated list of users with optional filters
     * 
     * @param int $page Current page number
     * @param int $perPage Number of items per page
     * @param array $filters Optional filters: search, role, status
     * @return array Paginated user data with pagination information
     */
    public function getPaginatedUsers($page = 1, $perPage = 10, array $filters = [])
    {
        // Start with a base query that joins the roles table
        $this->select('users.*, roles.name AS role_name')
            ->join('roles', 'users.role_id', 'roles.id');

        // Apply filters if provided
        if (!empty($filters)) {
            // Search filter (name or email)
            if (!empty($filters['search'])) {
                $searchTerm = $filters['search'];
                $this->where("(users.first_name LIKE :search OR users.last_name LIKE :search OR users.email LIKE :search)")
                    ->bind(['search' => "%$searchTerm%"]);
            }
            
            // Role filter
            if (!empty($filters['role'])) {
                $this->where("roles.name = :role")
                    ->bind(['role' => $filters['role']]);
            }
            
            // Status filter
            if (isset($filters['status'])) {
                if ($filters['status'] === 'active') {
                    $this->where("users.is_active = :active")
                        ->bind(['active' => true]);
                } elseif ($filters['status'] === 'inactive') {
                    $this->where("users.is_active = :inactive")
                        ->bind(['inactive' => false]);
                }
                // You can add more status filters as needed
            }
        }
        
        // Apply soft delete filter
        $this->whereSoftDeleted('users');
        
        // Sort by ID by default, can be customized
        $this->orderBy('users.id DESC');
         
        // Use the paginate method from BaseModel
        return $this->paginate($page, $perPage);
    }

    public function getAllUsers()
    {
        return $this->select('users.*, roles.name AS role_name')
                    ->join('roles', 'users.role_id', 'roles.id')
                    ->whereSoftDeleted('users')
                    ->orderBy('users.id DESC')
                    ->get();
    }
}