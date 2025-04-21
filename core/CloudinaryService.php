<?php
// core/CloudinaryService.php

namespace Core;

use Cloudinary\Cloudinary;
use Cloudinary\Api\ApiResponse;
use Cloudinary\Api\Exception\ApiError;
use Cloudinary\Asset\Media;

class CloudinaryService
{
    private Cloudinary $cloudinary;
    
    /**
     * Initialize CloudinaryService with the Cloudinary instance
     */
    public function __construct()
    {
        $this->cloudinary = require_once __DIR__ . '/../config/cloudinary.php';
    }
    
    /**
     * Upload a file to Cloudinary
     * 
     * @param string $filePath Path to the file to upload
     * @param string|null $folder Optional folder name within Cloudinary
     * @param array $options Additional upload options
     * @return ApiResponse Response from Cloudinary
     * @throws \Exception If upload fails
     */
    public function upload(string $filePath, ?string $folder = null, array $options = []): ApiResponse
    {
        try {
            $uploadOptions = [
                'resource_type' => 'auto', // Auto-detect resource type (image, video, raw)
            ];
            
            // Add folder if specified
            if ($folder) {
                $uploadOptions['folder'] = $folder;
            }
            
            // Merge with custom options
            $uploadOptions = array_merge($uploadOptions, $options);
            
            // Perform upload
            $result = $this->cloudinary->uploadApi()->upload($filePath, $uploadOptions);
            
            return $result;
        } catch (ApiError $e) {
            throw new \Exception("Cloudinary upload failed: " . $e->getMessage(), $e->getCode());
        }
    }
    
    /**
     * Upload an image with image-specific options
     * 
     * @param string $imagePath Path to the image file
     * @param string|null $folder Optional folder name
     * @param array $options Additional options (transformation, etc.)
     * @return ApiResponse Response from Cloudinary
     */
    public function uploadImage(string $imagePath, ?string $folder = null, array $options = []): ApiResponse
    {
        // Set image-specific options
        $imageOptions = [
            'resource_type' => 'image',
        ];
        
        return $this->upload($imagePath, $folder, array_merge($imageOptions, $options));
    }
    
    /**
     * Upload a PDF file
     * 
     * @param string $pdfPath Path to the PDF file
     * @param string|null $folder Optional folder name
     * @param array $options Additional options
     * @return ApiResponse Response from Cloudinary
     */
    public function uploadPdf(string $pdfPath, ?string $folder = null, array $options = []): ApiResponse
    {
        // Set PDF-specific options
        $pdfOptions = [
            'resource_type' => 'raw',
        ];
        
        return $this->upload($pdfPath, $folder, array_merge($pdfOptions, $options));
    }
    
    /**
     * Delete a file from Cloudinary
     * 
     * @param string $publicId Public ID of the file to delete
     * @param string $resourceType Type of resource (image, video, raw)
     * @return ApiResponse Response from Cloudinary
     * @throws \Exception If deletion fails
     */
    public function delete(string $publicId, string $resourceType = 'image'): ApiResponse
    {
        try {
            $result = $this->cloudinary->uploadApi()->destroy($publicId, [
                'resource_type' => $resourceType
            ]);
            
            return $result;
        } catch (ApiError $e) {
            throw new \Exception("Cloudinary deletion failed: " . $e->getMessage(), $e->getCode());
        }
    }
    
    /**
     * Generate a URL for a Cloudinary resource
     * 
     * @param string $publicId Public ID of the resource
     * @param array $options Transformation options
     * @return string The URL to the resource
     */
    public function getUrl(string $publicId, array $options = []): string
    {
        $media = new Media($publicId, $this->cloudinary->configuration);
        return $media->toUrl($options);
    }
    
    /**
     * Generate a secure URL with a specified expiration time
     * 
     * @param string $publicId Public ID of the resource
     * @param int $expiresAt Unix timestamp when the URL should expire
     * @param array $options Additional options
     * @return string The signed URL
     */
    public function getSignedUrl(string $publicId, int $expiresAt, array $options = []): string
    {
        $urlOptions = array_merge([
            'sign_url' => true,
            'expires_at' => $expiresAt
        ], $options);
        
        return $this->getUrl($publicId, $urlOptions);
    }
    
    /**
     * Get the response as an array
     * 
     * @param ApiResponse $response The API response
     * @return array The response data as an array
     */
    public function toArray(ApiResponse $response): array
    {
        return $response->getArrayCopy();
    }
}