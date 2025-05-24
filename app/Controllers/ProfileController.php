<?php

namespace App\Controllers;

use Core\AvatarGenerator;
use Core\CloudinaryService;
use App\Models\ActivityLogModel;

class ProfileController extends BaseController {
    protected $userModel;
    protected $activityLogModel;

    public function __construct()
    {
        $this->userModel = $this->loadModel("UserModel");
        $this->activityLogModel = new ActivityLogModel();
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
            $this->activityLogModel->logActivity($userId, 'PROFILE_UPDATE', "Failed to update profile picture: No image uploaded or upload failed");
            $this->jsonError('No image uploaded or upload failed.', 400);
            return;
        }
        
        $file = $_FILES['profile_image'];
        
        // Validate file type
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $fileType = mime_content_type($file['tmp_name']);
        
        if (!in_array($fileType, $allowedTypes)) {
            $this->activityLogModel->logActivity($userId, 'PROFILE_UPDATE', "Failed to update profile picture: Invalid file type");
            $this->jsonError('Invalid file type. Please upload a JPEG, PNG, GIF, or WebP image.', 400);
            return;
        }
        
        // Validate file size (max 5MB)
        $maxFileSize = 5 * 1024 * 1024; // 5MB in bytes
        if ($file['size'] > $maxFileSize) {
            $this->activityLogModel->logActivity($userId, 'PROFILE_UPDATE', "Failed to update profile picture: File size exceeds limit");
            $this->jsonError('File size exceeds the maximum limit of 5MB.', 400);
            return;
        }
        
        // Get the user from database
        $user = $this->userModel->findById($userId);
        if (!$user) {
            $this->activityLogModel->logActivity($userId, 'PROFILE_UPDATE', "Failed to update profile picture: User not found");
            $this->jsonError('User not found.', 404);
            return;
        }
        
        try {
            // Define upload directory path
            $uploadDir = dirname(dirname(__DIR__)) . '/public/assets/images/profile-pics/';
            
            // Create directory if it doesn't exist
            if (!is_dir($uploadDir)) {
                if (!mkdir($uploadDir, 0755, true)) {
                    throw new \Exception("Failed to create upload directory");
                }
            }
            
            // Check if user has an existing custom profile image
            $currentProfileUrl = $user['ua_profile_url'] ?? '';
            $isDefaultAvatar = $this->isDefaultAvatar($currentProfileUrl);
            
            // Define the filename - use user ID for uniqueness
            $fileExt = pathinfo($file['name'], PATHINFO_EXTENSION);
            $fileName = 'user_' . $userId . '.' . $fileExt;
            $filePath = $uploadDir . $fileName;
            
            // If user already has a profile picture, delete the old one
            if (!$isDefaultAvatar && strpos($currentProfileUrl, '/assets/images/profile-pics/') !== false) {
                $oldFilePath = dirname(dirname(__DIR__)) . parse_url($currentProfileUrl, PHP_URL_PATH);
                if (file_exists($oldFilePath)) {
                    unlink($oldFilePath);
                }
            }
            
            // Process and save the image
            list($width, $height) = getimagesize($file['tmp_name']);
            
            // Create a square crop focusing on the center
            $size = min($width, $height);
            $x = ($width - $size) / 2;
            $y = ($height - $size) / 2;
            
            // Create image resource based on file type
            switch ($fileType) {
                case 'image/jpeg':
                    $sourceImage = imagecreatefromjpeg($file['tmp_name']);
                    break;
                case 'image/png':
                    $sourceImage = imagecreatefrompng($file['tmp_name']);
                    break;
                case 'image/gif':
                    $sourceImage = imagecreatefromgif($file['tmp_name']);
                    break;
                case 'image/webp':
                    $sourceImage = imagecreatefromwebp($file['tmp_name']);
                    break;
                default:
                    throw new \Exception("Unsupported image type");
            }
            
            // Create destination image (300x300)
            $destWidth = 300;
            $destHeight = 300;
            $destImage = imagecreatetruecolor($destWidth, $destHeight);
            
            // Preserve transparency for PNG images
            if ($fileType === 'image/png') {
                imagealphablending($destImage, false);
                imagesavealpha($destImage, true);
            }
            
            // Resize and crop the image
            imagecopyresampled(
                $destImage, $sourceImage,
                0, 0, $x, $y,
                $destWidth, $destHeight, $size, $size
            );
            
            // Save the image based on file type
            $saved = false;
            switch ($fileType) {
                case 'image/jpeg':
                    $saved = imagejpeg($destImage, $filePath, 90); // 90% quality
                    break;
                case 'image/png':
                    $saved = imagepng($destImage, $filePath, 9); // 0-9 compression level
                    break;
                case 'image/gif':
                    $saved = imagegif($destImage, $filePath);
                    break;
                case 'image/webp':
                    $saved = imagewebp($destImage, $filePath, 90);
                    break;
            }
            
            // Clean up
            imagedestroy($sourceImage);
            imagedestroy($destImage);
            
            if (!$saved) {
                throw new \Exception("Failed to save the image");
            }
            
            // Generate the URL for the profile picture
            $profileUrl = '/assets/images/profile-pics/' . $fileName;
            
            // Update the user's profile URL in the database
            $updated = $this->userModel->updateUser($userId, ['profile_url' => $profileUrl]);
            
            if (!$updated) {
                $this->activityLogModel->logActivity($userId, 'PROFILE_UPDATE', "Failed to update profile picture: Database update failed");
                $this->jsonError('Failed to update profile picture in database.', 500);
                return;
            }
            
            // Update session value
            $_SESSION['profile_url'] = $profileUrl;
            
            // Log successful update
            $this->activityLogModel->logActivity($userId, 'PROFILE_UPDATE', "User updated profile picture successfully");
            
            // Return success response
            $this->jsonSuccess(
                ['profile_url' => $profileUrl],
                'Your profile picture has been successfully updated.'
            );
            
        } catch (\Exception $e) {
            $this->activityLogModel->logActivity($userId, 'ERROR', "Failed to upload profile image: " . $e->getMessage());
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
            $this->activityLogModel->logActivity($userId, 'PROFILE_UPDATE', "Failed to update profile info: Missing required fields");
            $this->jsonError('First name and last name are required.', 400);
            return;
        }

        // Get the user from database
        $user = $this->userModel->findById($userId);
        if (!$user) {
            $this->activityLogModel->logActivity($userId, 'PROFILE_UPDATE', "Failed to update profile info: User not found");
            $this->jsonError('User not found.', 404);
            return;
        }

        $currentProfileUrl = $user['ua_profile_url'];
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
            'firstName' => $firstName,
            'lastName' => $lastName,
            'phone' => $phone // Use the field name that matches the form field
        ];
        
        // Debug log
        error_log("ProfileController updateData: " . json_encode($updateData));

        // Update the user profile
        $updated = $this->userModel->updateUser($user['ua_id'], $updateData);
        if (!$updated) {
            $this->activityLogModel->logActivity($userId, 'PROFILE_UPDATE', "Failed to update profile info: Database update failed");
            $this->jsonError('Failed to update profile. Please try again later.', 500);
            return;
        }

        // Update session values
        $_SESSION['profile_url'] = $newProfileUrl;
        $_SESSION['first_name'] = $firstName;
        $_SESSION['last_name'] = $lastName;
        $_SESSION['full_name'] = $newName;
        $_SESSION['phone_number'] = $phone; // Always update phone number in session, even if empty

        // Log successful update
        $changes = [];
        if ($user['ua_first_name'] != $firstName || $user['ua_last_name'] != $lastName) {
            $changes[] = "name changed from '{$user['ua_first_name']} {$user['ua_last_name']}' to '$firstName $lastName'";
        }
        if ($user['ua_phone_number'] != $phone) {
            $changes[] = "phone number updated";
        }
        
        $changesStr = !empty($changes) ? implode(', ', $changes) : "profile info updated";
        $this->activityLogModel->logActivity($userId, 'PROFILE_UPDATE', "User updated profile info: $changesStr");

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
        // Local profile images will be stored in /assets/images/profile-pics/
        return strpos($profileUrl, 'ui-avatars.com') !== false || 
               strpos($profileUrl, 'gravatar.com') !== false || 
               empty($profileUrl);
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
            $this->activityLogModel->logActivity($userId, 'PROFILE_UPDATE', "Failed to change password: Missing required fields");
            $this->jsonError('All password fields are required.', 400);
            return;
        }

        // Validate password match
        if ($newPassword !== $confirmPassword) {
            $this->activityLogModel->logActivity($userId, 'PROFILE_UPDATE', "Failed to change password: Passwords do not match");
            $this->jsonError('New password and confirmation do not match.', 400);
            return;
        }

        // Validate password strength
        if (strlen($newPassword) < 8) {
            $this->activityLogModel->logActivity($userId, 'PROFILE_UPDATE', "Failed to change password: Password too short");
            $this->jsonError('Password must be at least 8 characters long.', 400);
            return;
        }

        // Get the user from database
        $user = $this->userModel->findById($userId);
        if (!$user) {
            $this->activityLogModel->logActivity($userId, 'PROFILE_UPDATE', "Failed to change password: User not found");
            $this->jsonError('User not found.', 404);
            return;
        }

        // Verify current password - use ua_hashed_password which is the actual column name in the database
        if (!$this->userModel->verifyPassword($currentPassword, $user['ua_hashed_password'])) {
            $this->activityLogModel->logActivity($userId, 'PROFILE_UPDATE', "Failed to change password: Incorrect current password");
            $this->jsonError('Current password is incorrect.', 401);
            return;
        }
        
        // Add debug logging
        error_log("Password verification passed for user ID: $userId");

        // Update the password
        $updated = $this->userModel->updateUser($userId, ['password' => $newPassword]);
        if (!$updated) {
            $this->activityLogModel->logActivity($userId, 'PROFILE_UPDATE', "Failed to change password: Database update failed");
            $this->jsonError('Failed to update password. Please try again later.', 500);
            return;
        }

        // Log successful password change
        $this->activityLogModel->logActivity($userId, 'PROFILE_UPDATE', "User changed their password successfully");

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
            $this->activityLogModel->logActivity($userId, 'USER_DELETE', "Failed to delete account: User not found");
            $this->jsonError('User not found.', 404);
            return;
        }

        // Verify password - use ua_hashed_password which is the actual column name in the database
        if (!password_verify($password, $user['ua_hashed_password'])) {
            $this->activityLogModel->logActivity($userId, 'USER_DELETE', "Failed to delete account: Incorrect password");
            $this->jsonError('Incorrect password. Account deletion canceled.', 401);
            return;
        }
        
        // Add debug logging
        error_log("Password verification passed for account deletion. User ID: $userId");

        // Log account deletion before deleting
        $this->activityLogModel->logActivity($userId, 'USER_DELETE', "User deleted their account: {$user['ua_first_name']} {$user['ua_last_name']} ({$user['ua_email']})");

        // Delete user account
        $deleted = $this->userModel->deleteUser($userId);
        if (!$deleted) {
            $this->activityLogModel->logActivity($userId, 'USER_DELETE', "Failed to delete account: Database error");
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