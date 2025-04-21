<?php

namespace App\Controllers;

use Core\AvatarGenerator;

class ProfileController extends BaseController {
    protected $userModel;

    public function __construct()
    {
        $this->userModel = $this->loadModel("UserModel");
    }

    public function updateProfileInfo() {
        $avatar = new AvatarGenerator();

    }

    public function changePassword() {
        // Check if the request is AJAX and POST
        if (!$this->isAjax() || !$this->isPost()) {
            $this->redirect('/error/403');
            return;
        }

        // Get JSON input from the request body
        $input = $this->getJsonInput();
        $currentPassword = $input['currentPassword'] ?? '';
        $newPassword = $input['newPassword'] ?? '';
        $confirmPassword = $input['confirmPassword'] ?? '';
        
        // Get user ID from session
        $userId = (int) $_SESSION['user_id'] ?? null;
        
        if (!$userId) {
            $this->jsonError('User not authenticated.', 401);
            return;
        }

        // Validate inputs
        if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
            $this->jsonError('All password fields are required.', 400);
            return;
        }

        // Validate password match
        if ($newPassword !== $confirmPassword) {
            $this->jsonError('New password and confirmation do not match.', 400);
            return;
        }

        // Validate password strength
        if (strlen($newPassword) < 8) {
            $this->jsonError('Password must be at least 8 characters long.', 400);
            return;
        }

        // Get the user from database
        $user = $this->userModel->findById($userId);
        if (!$user) {
            $this->jsonError('User not found.', 404);
            return;
        }

        // Verify current password
        if (!$this->userModel->verifyPassword($currentPassword, $user['password'])) {
            $this->jsonError('Current password is incorrect.', 401);
            return;
        }

        // Update the password
        $updated = $this->userModel->updateUser($userId, ['password' => $newPassword]);
        if (!$updated) {
            $this->jsonError('Failed to update password. Please try again later.', 500);
            return;
        }

        // Return success response
        $this->jsonSuccess(
            [], // No additional data needed
            'Your password has been successfully updated.'
        );
    }

    public function deleteAccount() {
        // Check if the request is AJAX
        if (!$this->isAjax() || !$this->isPost()) {
            $this->redirect('/error/403');
            return;
        }

        // Get JSON input from the request body
        $input = $this->getJsonInput();
        $password = $input['password'] ?? '';
        
        // Get user ID from session
        $userId = (int) $_SESSION['user_id'] ?? null;
        
        if (!$userId || empty($password)) {
            $this->jsonError('Invalid request. Missing user ID or password.', 400);
            return;
        }

        // Get the user from database
        $user = $this->userModel->findById($userId);
        if (!$user) {
            $this->jsonError('User not found.', 404);
            return;
        }

        // Verify password
        if (!password_verify($password, $user['password'])) {
            $this->jsonError('Incorrect password. Account deletion canceled.', 401);
            return;
        }

        // Delete user account
        $deleted = $this->userModel->deleteUser($userId);
        if (!$deleted) {
            $this->jsonError('Failed to delete account. Please try again later.', 500);
            return;
        }

        // Destroy session
        session_unset();
        session_destroy();

        // Return success response with redirect URL
        $this->jsonSuccess(
            ['redirect_url' => '/'],
            'Your account has been successfully deleted.'
        );
    }

    
}