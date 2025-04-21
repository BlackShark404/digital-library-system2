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

    protected $searchableFields = ['first_name', 'last_name', 'email']; // Updated from 'name' to 'first_name' and 'last_name'

    protected $timestamps = true;
    protected $useSoftDeletes = true;

    public function findById($id) {
        return $this->select('users.*, roles.name AS role_name')
                    ->join('roles', 'users.role_id', 'roles.id')
                    ->where('users.id = :id')
                    ->bind(['id' => $id])
                    ->whereSoftDeleted('users')
                    ->first();
    }

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

}
