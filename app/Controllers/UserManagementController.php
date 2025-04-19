<?php

namespace App\Controllers;

use Core\AvatarGenerator;

class UserManagementController extends BaseController{
    protected $userModel;

    public function __construct() 
    {
        $this->userModel = $this->loadModel('UserModel');
    }

    public function registerUsers()
    {
        $avatar = new AvatarGenerator();

        if (!$this->isPost() || !$this->isAjax()) {
            return $this->jsonError('Invalid request method');
        }

        $data = $this->getJsonInput();

        // Validate required fields from the form
        // Added 'role' and 'is_active' which are required in the form
        $requiredFields = ['first_name', 'last_name', 'email', 'password', 'confirm_password', 'role_id', 'is_active'];
        foreach ($requiredFields as $field) {
            if (!isset($data[$field]) || $data[$field] === '') { // Check for existence and non-empty string
                // Allow '0' for is_active
                if ($field === 'is_active' && $data[$field] === '0') {
                    continue;
                }
                return $this->jsonError(ucfirst($field) . ' field is required');
            }
        }

        // Add password confirmation check
        if ($data['password'] !== $data['confirm_password']) {
            return $this->jsonError('Passwords do not match');
        }

        $profileUrl = $avatar->generate($data['first_name'] . ' ' . $data['last_name']);
        $firstName = $data['first_name'];
        $lastName = $data['last_name'];
        $email = $data['email'];
        $password = $data['password'];
        $roleId = (int) $data['role_id']; 
        $isActive = filter_var($data['is_active'], FILTER_VALIDATE_BOOLEAN); // Get status and convert '1'/'0' to boolean
        
        // Validate email form at
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->jsonError('Invalid email format');
        }

        // Check if email already exists
        if ($this->userModel->emailExists($email)) {
            return $this->jsonError('Email already exists');
        }

        // Validate role (optional but good practice)
        $allowedRoles = [1, 2];

        if (!in_array($roleId, $allowedRoles)) {
            return $this->jsonError('Invalid role selected');
        }

        // Create the user using form data
        $userId = $this->userModel->createUser([
            'profile_url' => $profileUrl,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $email,
            'password' => $password,
            'role_id' => $roleId,         // Use submitted role
            'is_active' => $isActive // Use submitted status
        ]);

        if ($userId) {
            // Optionally, you might want to redirect or refresh the user list here
            return $this->jsonSuccess(
                ['User added successfully'], 
                'User added successfully'
                
            );
        } else {
            return $this->jsonError('Failed to add user. Please try again.');
        }
    }
}