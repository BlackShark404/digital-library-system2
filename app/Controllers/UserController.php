<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class UserController extends BaseController{
    protected $userModel;

    public function __construct() 
    {
        $this->userModel = $this->loadModel('UserModel');
    }

    // UserController.php

    public function registerUsers()
    {
        if (!$this->isPost() || !$this->isAjax()) {
            return $this->jsonError('Invalid request method');
        }

        $data = $this->getJsonInput();

        // Validate required fields from the form
        // Added 'role' and 'is_active' which are required in the form
        $requiredFields = ['fname', 'lname', 'email', 'password', 'role', 'is_active', 'confirmPassword'];
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
        if ($data['password'] !== $data['confirmPassword']) {
            return $this->jsonError('Passwords do not match');
        }


        $fname = $data['fname'];
        $lname = $data['lname'];
        $email = $data['email'];
        $password = $data['password'];
        $role = $data['role']; // Get role from form data
        $isActive = filter_var($data['is_active'], FILTER_VALIDATE_BOOLEAN); // Get status and convert '1'/'0' to boolean

        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->jsonError('Invalid email format');
        }

        // Check if email already exists
        if ($this->userModel->emailExists($email)) {
            return $this->jsonError('Email already exists');
        }

        // Validate role (optional but good practice)
        $allowedRoles = ['admin', 'user'];
        if (!in_array($role, $allowedRoles)) {
            return $this->jsonError('Invalid role selected');
        }

        // Create the user using form data
        $userId = $this->userModel->createUser([
            'fname' => $fname,
            'lname' => $lname,
            'email' => $email,
            'password' => $password,
            'role' => $role,         // Use submitted role
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