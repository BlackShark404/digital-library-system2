<?php

namespace App\Controllers;

class UserManagementController extends BaseController
{
    protected $userModel;

    public function __construct() 
    {
        parent::__construct();
        $this->userModel = $this->loadModel('UserModel');
    }
    
    /**
     * Display the user management page
     */
    public function index()
    {
        // Check if user has admin permissions
        if (!$this->checkPermission('admin')) {
            $this->redirect('/dashboard');
        }
        
        $filters = [
            'role' => $this->getRequestParam('role', ''),
            'status' => $this->getRequestParam('status', '')
        ];
        
        $this->render('admin/user-management', [
            'filters' => $filters
        ]);
    }
    
    /**
     * API Endpoint: Get all users
     */
    public function getUsers()
    {
        if (!$this->isAjax()) {
            $this->jsonError('Invalid request', 400);
        }
        
        // Get filter parameters
        $role = $this->getRequestParam('role', '');
        $status = $this->getRequestParam('status', '');
        
        try {
            // Get users with filters
            $users = $this->userModel->getUsers($role, $status);
            
            // Format user data for DataTables
            $formattedUsers = [];
            foreach ($users as $user) {
                $formattedUsers[] = [
                    'id' => $user['ua_id'],
                    'first_name' => $user['ua_first_name'],
                    'last_name' => $user['ua_last_name'],
                    'email' => $user['ua_email'],
                    'role' => $user['role_name'],
                    'status' => $user['ua_is_active'] ? 'active' : 'inactive',
                    'registered' => date('M d, Y', strtotime($user['ua_created_at'])),
                    'last_login' => $user['ua_last_login'] 
                        ? date('M d, Y H:i', strtotime($user['ua_last_login'])) 
                        : 'Never',
                ];
            }
            
            $this->jsonSuccess($formattedUsers);
        } catch (\Exception $e) {
            $this->jsonError('Error fetching user data: ' . $e->getMessage(), 500);
        }
    }
    
    /**
     * API Endpoint: Get a specific user
     */
    public function getUser($id)
    {
        try {
            $user = $this->userModel->findById($id);
            
            if (!$user) {
                $this->jsonError('User not found', 404);
            }
            
            $userData = [
                'id' => $user['ua_id'],
                'first_name' => $user['ua_first_name'],
                'last_name' => $user['ua_last_name'],
                'email' => $user['ua_email'],
                'role' => $user['role_name'],
                'role_id' => $user['ua_role_id'],
                'status' => $user['ua_is_active'] ? 'active' : 'inactive',
                'is_active' => $user['ua_is_active'],
                'registered' => date('M d, Y', strtotime($user['ua_created_at'])),
                'last_login' => $user['ua_last_login'] 
                    ? date('M d, Y H:i', strtotime($user['ua_last_login'])) 
                    : 'Never'
            ];
            
            $this->jsonSuccess($userData);
        } catch (\Exception $e) {
            $this->jsonError('Error fetching user: ' . $e->getMessage(), 500);
        }
    }
    
    /**
     * API Endpoint: Create a new user
     */
    public function createUser()
    {
        // Check if request is AJAX and POST
        if (!$this->isAjax() || !$this->isPost()) {
            $this->jsonError('Invalid request method', 400);
        }
        
        // Get JSON input data
        $data = $this->getJsonInput();
        
        // Validate required fields
        $requiredFields = ['first_name', 'last_name', 'email', 'password', 'role_id', 'is_active'];
        foreach ($requiredFields as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                $this->jsonError("Missing required field: $field", 400);
            }
        }
        
        // Check if email already exists
        if ($this->userModel->emailExists($data['email'])) {
            $this->jsonError('Email address already in use', 400);
        }
        
        try {
            // Create user
            $result = $this->userModel->createUser([
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'email' => $data['email'],
                'password' => $data['password'],
                'role_id' => $data['role_id'],
                'is_active' => $data['is_active']
            ]);
            
            if ($result) {
                $this->jsonSuccess([], 'User created successfully');
            } else {
                $this->jsonError('Failed to create user', 500);
            }
        } catch (\Exception $e) {
            $this->jsonError('Error creating user: ' . $e->getMessage(), 500);
        }
    }
    
    /**
     * API Endpoint: Update a user
     */
    public function updateUser($id)
    {
        // Check if request is AJAX
        if (!$this->isAjax()) {
            $this->jsonError('Invalid request', 400);
        }
        
        // Get JSON input data
        $data = $this->getJsonInput();
        
        // Check if user exists
        $user = $this->userModel->findById($id);
        if (!$user) {
            $this->jsonError('User not found', 404);
        }
        
        // Validate required fields
        $requiredFields = ['first_name', 'last_name', 'email', 'role_id', 'is_active'];
        foreach ($requiredFields as $field) {
            if (!isset($data[$field]) || $data[$field] === '') {
                $this->jsonError("Missing required field: $field", 400);
            }
        }
        
        // Check email uniqueness (if changed)
        if ($data['email'] !== $user['ua_email'] && $this->userModel->emailExists($data['email'])) {
            $this->jsonError('Email address already in use', 400);
        }
        
        try {
            // Update user data
            $updateData = [
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'email' => $data['email'],
                'role_id' => $data['role_id'],
                'is_active' => $data['is_active']
            ];
            
            // Add password if provided
            if (isset($data['password']) && !empty($data['password'])) {
                $updateData['password'] = $data['password'];
            }
            
            $result = $this->userModel->updateUser($id, $updateData);
            
            if ($result) {
                $this->jsonSuccess([], 'User updated successfully');
            } else {
                $this->jsonError('Failed to update user', 500);
            }
        } catch (\Exception $e) {
            $this->jsonError('Error updating user: ' . $e->getMessage(), 500);
        }
    }
    
    /**
     * API Endpoint: Delete a user
     */
    public function deleteUser($id)
    {
        // Check if request is AJAX
        if (!$this->isAjax()) {
            $this->jsonError('Invalid request', 400);
        }
        
        // Check if user exists
        $user = $this->userModel->findById($id);
        if (!$user) {
            $this->jsonError('User not found', 404);
        }
        
        try {
            // Use soft delete by default
            $permanent = $this->getRequestParam('permanent', false);
            $result = $this->userModel->deleteUser($id, $permanent);
            
            if ($result) {
                $this->jsonSuccess([], 'User deleted successfully');
            } else {
                $this->jsonError('Failed to delete user', 500);
            }
        } catch (\Exception $e) {
            $this->jsonError('Error deleting user: ' . $e->getMessage(), 500);
        }
    }
    
}