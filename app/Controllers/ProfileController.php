<?php

namespace App\Controllers;

use Core\AvatarGenerator;
use Core\CloudinaryService;

class ProfileController extends BaseController {
    protected $userModel;

    public function __construct()
    {
        $this->userModel = $this->loadModel("UserModel");
    }

    public function updateProfilePicture() {
        // Check if the request is AJAX and POST
        if (!$this->isAjax() || !$this->isPost()) {
            $this->redirect('/error/403');
            return;
        }
        
        // Get user ID from session
        $userId = $_SESSION['user_id'] ?? null;
        
        if (!$userId) {
            $this->jsonError('User not authenticated.', 401);
            return;
        }
        
        // Check if file was uploaded
        if (!isset($_FILES['profile_image']) || $_FILES['profile_image']['error'] !== UPLOAD_ERR_OK) {
            $this->jsonError('No image uploaded or upload failed.', 400);
            return;
        }
        
        $file = $_FILES['profile_image'];
        
        // Validate file type
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $fileType = mime_content_type($file['tmp_name']);
        
        if (!in_array($fileType, $allowedTypes)) {
            $this->jsonError('Invalid file type. Please upload a JPEG, PNG, GIF, or WebP image.', 400);
            return;
        }
        
        // Validate file size (max 5MB)
        $maxFileSize = 5 * 1024 * 1024; // 5MB in bytes
        if ($file['size'] > $maxFileSize) {
            $this->jsonError('File size exceeds the maximum limit of 5MB.', 400);
            return;
        }
        
        // Get the user from database
        $user = $this->userModel->findById($userId);
        if (!$user) {
            $this->jsonError('User not found.', 404);
            return;
        }
        
        try {
            // Initialize CloudinaryService
            $cloudinary = new CloudinaryService;
            
            // Check if user has an existing custom profile image (not a default avatar)
            $currentProfileUrl = $user['profile_url'] ?? '';
            $isDefaultAvatar = $this->isDefaultAvatar($currentProfileUrl);
            
            // Define a consistent public ID for the user's profile picture
            $publicId = 'user_' . $userId . '_profile';
            
            // If the user has an existing Cloudinary image, delete it first
            if (!$isDefaultAvatar && strpos($currentProfileUrl, 'cloudinary.com') !== false) {
                try {
                    // Extract public ID from existing URL
                    // Typical Cloudinary URL format: https://res.cloudinary.com/cloud_name/image/upload/v1234567890/folder/public_id.ext
                    $urlParts = parse_url($currentProfileUrl);
                    if (isset($urlParts['path'])) {
                        $pathParts = explode('/', $urlParts['path']);
                        // Remove empty elements and get the relevant parts
                        $pathParts = array_filter($pathParts);
                        
                        // If we found a path that looks like a Cloudinary URL
                        if (count($pathParts) >= 5) {
                            // The public ID is typically the last part without the extension
                            $oldPublicId = pathinfo(end($pathParts), PATHINFO_FILENAME);
                            $folderPath = prev($pathParts); // Get the folder name
                            
                            // Combine folder and file name to create the full public ID
                            $oldFullPublicId = $folderPath . '/' . $oldPublicId;
                            
                            // Delete the old image
                            $cloudinary->delete($oldFullPublicId);
                        }
                    }
                } catch (\Exception $e) {
                    // If deletion fails, just log the error and continue
                    error_log('Failed to delete old profile image: ' . $e->getMessage());
                    // We don't want to abort the upload just because deletion failed
                }
            }
            
            // Upload image to Cloudinary with the consistent public ID
            $uploadResult = $cloudinary->uploadImage(
                $file['tmp_name'],
                'Digital_Library/profile_pictures', // Folder in Cloudinary
                [
                    'public_id' => $publicId, // Use the consistent public ID
                    'overwrite' => true, // Force overwrite of existing images with the same public ID
                    'transformation' => [
                        'width' => 300,
                        'height' => 300,
                        'crop' => 'fill',
                        'gravity' => 'face'
                    ]
                ]
            );
            
            // Extract the URL from the response
            $resultArray = $cloudinary->toArray($uploadResult);
            $profileUrl = $resultArray['secure_url'];
            
            // Update the user's profile URL in the database
            $updated = $this->userModel->updateUser($userId, ['profile_url' => $profileUrl]);
            
            if (!$updated) {
                $this->jsonError('Failed to update profile picture in database.', 500);
                return;
            }
            
            // Update session value
            $_SESSION['profile_url'] = $profileUrl;
            
            // Return success response
            $this->jsonSuccess(
                ['profile_url' => $profileUrl],
                'Your profile picture has been successfully updated.'
            );
            
        } catch (\Exception $e) {
            $this->jsonError('Failed to upload image: ' . $e->getMessage(), 500);
            return;
        }
    }

    public function updateProfileInfo() {
        $avatar = new AvatarGenerator();

        // Check if the request is AJAX and POST
        if (!$this->isAjax() || !$this->isPost()) {
            $this->redirect('/error/403');
            return;
        }

        // Get JSON input from the request body
        $input = $this->getJsonInput();
        $firstName = trim($input['firstName'] ?? '');
        $lastName = trim($input['lastName'] ?? '');
        $phone = trim($input['phone'] ?? '');
        
        // Get user ID from session
        $userId = $_SESSION['user_id'] ?? null;
        
        if (!$userId) {
            $this->jsonError('User not authenticated.', 401);
            return;
        }

        // Validate inputs
        if (empty($firstName) || empty($lastName)) {
            $this->jsonError('First name and last name are required.', 400);
            return;
        }

        // Get the user from database
        $user = $this->userModel->findById($userId);
        if (!$user) {
            $this->jsonError('User not found.', 404);
            return;
        }

        $currentProfileUrl = $user['profile_url'];
        $newName = $firstName . ' ' . $lastName;


        // Check if user has a custom profile image or is using a generated avatar
        $isDefaultAvatar = $this->isDefaultAvatar($currentProfileUrl);

        $newProfileUrl = $currentProfileUrl;
        if ($isDefaultAvatar) {
            // Only update the avatar if it's a default/generated one
            $newProfileUrl = $avatar->updateNameKeepBackground($currentProfileUrl, $newName);
        }

        // Prepare data for update
        $updateData = [
            'profile_url' => $newProfileUrl,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'phone_numbe' => $phone // This will be empty string if not provided
        ];

        // Update the user profile
        $updated = $this->userModel->updateUser($userId, $updateData);
        if (!$updated) {
            $this->jsonError('Failed to update profile. Please try again later.', 500);
            return;
        }

        // Update session values
        $_SESSION['profile_url'] = $newProfileUrl;
        $_SESSION['first_name'] = $firstName;
        $_SESSION['last_name'] = $lastName;
        $_SESSION['full_name'] = $newName;
        if (!empty($phone)) {
            $_SESSION['phone_number'] = $phone;
        }

        // Return success response
        $this->jsonSuccess(
            [], // No additional data needed
            'Your profile has been successfully updated.'
        );
    }

    /**
     * Check if the profile URL is a default/generated avatar
     * 
     * @param string $profileUrl The profile URL to check
     * @return bool True if the URL is a default avatar, false otherwise
     */
    private function isDefaultAvatar($profileUrl) {
        // Check if the URL is from ui-avatars.com or other default avatar services
        return strpos($profileUrl, 'ui-avatars.com') !== false || strpos($profileUrl, 'gravatar.com') !== false;
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
        $userId = $_SESSION['user_id'] ?? null;
        
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
        $userId = $_SESSION['user_id'] ?? null;
        
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