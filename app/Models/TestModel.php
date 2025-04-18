<?php

namespace App\Models;

class TestModel extends BaseModel {
    /**
     * Table name for this model
     * @var string
     */
    protected $table = 'users';
    
    /**
     * Primary key column name
     * @var string
     */
    protected $primaryKey = 'id';
    
    /**
     * Fillable attributes (for mass assignment)
     * @var array
     */
    protected $fillable = [
        'full_name',
        'username',
        'email',
        'password',
        'role_id',
        'is_active',
        'remember_token',
        'last_login'
    ];

    protected $timestamps = true;
    protected $useSoftDeletes = true;

    public function getUserById($id) {
        return $this->find($id);
    }
}