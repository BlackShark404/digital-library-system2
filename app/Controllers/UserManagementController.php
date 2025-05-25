<?php

namespace App\Controllers;

use Core\AvatarGenerator;
use App\Models\ActivityLogModel;

class UserManagementController extends BaseController
{
    protected $userModel;
    protected $activityLogModel;

    public function __construct() 
    {
        parent::__construct();
        $this->userModel = $this->loadModel('UserModel');
        $this->activityLogModel = new ActivityLogModel();
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
                    'profile_url' => $user['ua_profile_url'],
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
            $this->activityLogModel->logActivity($_SESSION['user_id'] ?? null, 'ERROR', "Error fetching user data: " . $e->getMessage());
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
                $this->activityLogModel->logActivity($_SESSION['user_id'] ?? null, 'USER_VIEW', "Failed to view user: user with ID $id not found");
                $this->jsonError('User not found', 404);
            }
            
            $userData = [
                'id' => $user['ua_id'],
                'first_name' => $user['ua_first_name'],
                'last_name' => $user['ua_last_name'],
                'profile_url' => $user['ua_profile_url'],
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
            
            $this->activityLogModel->logActivity($_SESSION['user_id'] ?? null, 'USER_VIEW', "Admin viewed user profile: " . $user['ua_first_name'] . ' ' . $user['ua_last_name'] . " (ID: $id)");
            $this->jsonSuccess($userData);
        } catch (\Exception $e) {
            $this->activityLogModel->logActivity($_SESSION['user_id'] ?? null, 'ERROR', "Error fetching user: " . $e->getMessage());
            $this->jsonError('Error fetching user: ' . $e->getMessage(), 500);
        }
    }
    
    /**
     * API Endpoint: Create a new user
     */
    public function createUser()
    {
        $avatar = new AvatarGenerator();

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
                $this->activityLogModel->logActivity($_SESSION['user_id'] ?? null, 'REGISTER', "Failed to create user: missing required field $field");
                $this->jsonError("Missing required field: $field", 400);
            }
        }
        
        // Check if email already exists
        if ($this->userModel->emailExists($data['email'])) {
            $this->activityLogModel->logActivity($_SESSION['user_id'] ?? null, 'REGISTER', "Failed to create user: email already exists (" . $data['email'] . ")");
            $this->jsonError('Email address already in use', 400);
        }

        $profileUrl = $avatar->generate($data['first_name'] . ' ' . $data['last_name']);
        
        try {
            // Create user
            $result = $this->userModel->createUser([
                'profile_url' => $profileUrl,
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'email' => $data['email'],
                'password' => $data['password'],
                'role_id' => $data['role_id'],
                'is_active' => $data['is_active']
            ]);
            
            if ($result) {
                $roleName = $data['role_id'] == 2 ? 'admin' : 'user';
                $this->activityLogModel->logActivity($_SESSION['user_id'] ?? null, 'REGISTER', "Admin created new $roleName account: " . $data['first_name'] . ' ' . $data['last_name'] . " (" . $data['email'] . ")");
                $this->jsonSuccess([], 'User created successfully');
            } else {
                $this->activityLogModel->logActivity($_SESSION['user_id'] ?? null, 'REGISTER', "Failed to create user: database error");
                $this->jsonError('Failed to create user', 500);
            }
        } catch (\Exception $e) {
            $this->activityLogModel->logActivity($_SESSION['user_id'] ?? null, 'ERROR', "Error creating user: " . $e->getMessage());
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
            $this->activityLogModel->logActivity($_SESSION['user_id'] ?? null, 'PROFILE_UPDATE', "Failed to update user status: user with ID $id not found");
            $this->jsonError('User not found', 404);
        }
        
        // Only validate is_active field - it's the only one that can be modified
        if (!isset($data['is_active']) || $data['is_active'] === '') {
            $this->activityLogModel->logActivity($_SESSION['user_id'] ?? null, 'PROFILE_UPDATE', "Failed to update user status: missing required field is_active");
            $this->jsonError("Missing required field: is_active", 400);
        }
        
        try {
            // Update only the status
            $updateData = [
                'is_active' => $data['is_active']
            ];
            
            $result = $this->userModel->updateUser($id, $updateData);
            
            if ($result) {
                // Log status change
                if ($user['ua_is_active'] != $data['is_active']) {
                    $status = $data['is_active'] ? 'active' : 'inactive';
                    $this->activityLogModel->logActivity($_SESSION['user_id'] ?? null, 'PROFILE_UPDATE', "Admin updated user status (ID: $id, Name: {$user['ua_first_name']} {$user['ua_last_name']}): status changed to '$status'");
                    $this->jsonSuccess([], 'User status updated successfully');
                } else {
                    // No actual change in status
                    $this->jsonSuccess([], 'No change in user status');
                }
            } else {
                $this->activityLogModel->logActivity($_SESSION['user_id'] ?? null, 'PROFILE_UPDATE', "Failed to update user status: database error");
                $this->jsonError('Failed to update user status', 500);
            }
        } catch (\Exception $e) {
            $this->activityLogModel->logActivity($_SESSION['user_id'] ?? null, 'ERROR', "Error updating user status: " . $e->getMessage());
            $this->jsonError('Error updating user status: ' . $e->getMessage(), 500);
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
            $this->activityLogModel->logActivity($_SESSION['user_id'] ?? null, 'USER_DELETE', "Failed to delete user: user with ID $id not found");
            $this->jsonError('User not found', 404);
        }
        
        try {
            // Use soft delete by default
            $permanent = $this->getRequestParam('permanent', false);
            $result = $this->userModel->deleteUser($id, $permanent);
            
            if ($result) {
                $deleteType = $permanent ? 'permanently deleted' : 'deactivated';
                $this->activityLogModel->logActivity($_SESSION['user_id'] ?? null, 'USER_DELETE', "Admin $deleteType user: {$user['ua_first_name']} {$user['ua_last_name']} (ID: $id)");
                $this->jsonSuccess([], "User $deleteType successfully");
            } else {
                $this->activityLogModel->logActivity($_SESSION['user_id'] ?? null, 'USER_DELETE', "Failed to delete user: database error");
                $this->jsonError('Failed to delete user', 500);
            }
        } catch (\Exception $e) {
            $this->activityLogModel->logActivity($_SESSION['user_id'] ?? null, 'ERROR', "Error deleting user: " . $e->getMessage());
            $this->jsonError('Error deleting user: ' . $e->getMessage(), 500);
        }
    }
    
}