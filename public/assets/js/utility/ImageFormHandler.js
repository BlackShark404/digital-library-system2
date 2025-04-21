/**
 * Handle image upload form submission with preview functionality
 * @param {string} formId - The ID of the form element
 * @param {string} imageInputId - The ID of the file input element
 * @param {string} previewId - The ID of the image preview element
 * @param {string} endpoint - The API endpoint for uploading the image
 * @param {Object} options - Additional options
 * @param {boolean} options.closeModal - Whether to close the modal after successful upload
 * @param {string} options.modalId - The ID of the modal to close
 * @param {boolean} options.reloadPage - Whether to reload the page after successful upload
 * @param {number} options.reloadDelay - Delay in ms before reloading the page
 * @param {Function} options.onSuccess - Callback function to execute on successful upload
 * @param {Function} options.onError - Callback function to execute on upload error
 */
function handleImageUpload(formId, imageInputId, previewId, endpoint, options = {}) {
    const form = document.getElementById(formId);
    const imageInput = document.getElementById(imageInputId);
    const preview = document.getElementById(previewId);
    
    // Default options
    const defaultOptions = {
        closeModal: true,
        modalId: null,
        reloadPage: true,
        reloadDelay: 1500,
        onSuccess: null,
        onError: null
    };
    
    // Merge provided options with defaults
    const settings = { ...defaultOptions, ...options };
    
    // Set up image preview functionality
    if (imageInput && preview) {
        imageInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                };
                reader.readAsDataURL(this.files[0]);
            }
        });
    }
    
    // Handle form submission
    if (form) {
        form.addEventListener('submit', function(event) {
            event.preventDefault();
            
            // Validate file input
            if (!imageInput || !imageInput.files || !imageInput.files[0]) {
                showToast('Error', 'Please select an image to upload', 'danger');
                return;
            }
            
            // Create form data for submission
            const formData = new FormData(this);
            
            // Send request
            axios.post(endpoint, formData, {
                headers: {
                    'Content-Type': 'multipart/form-data',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                console.log('Image upload response:', response.data);
                
                // Show success message
                showToast('Success', response.data.message || 'Image uploaded successfully', 'success');
                
                // Update image sources if needed
                if (response.data.data && response.data.data.profile_url) {
                    const oldSrc = preview.getAttribute('data-original-src') || preview.src;
                    const newSrc = response.data.data.profile_url;
                    
                    // Update all images with the same source
                    const images = document.querySelectorAll(`img[src="${oldSrc}"]`);
                    images.forEach(img => {
                        img.src = newSrc;
                    });
                }
                
                // Close modal if requested
                if (settings.closeModal) {
                    const modalId = settings.modalId || form.closest('.modal')?.id;
                    if (modalId) {
                        const modalElement = bootstrap.Modal.getInstance(document.getElementById(modalId));
                        if (modalElement) {
                            modalElement.hide();
                        }
                    }
                }
                
                // Execute success callback if provided
                if (typeof settings.onSuccess === 'function') {
                    settings.onSuccess(response.data);
                }
                
                // Reload page if requested
                if (settings.reloadPage) {
                    setTimeout(() => {
                        location.reload();
                    }, settings.reloadDelay);
                }
            })
            .catch(error => {
                console.error('Image upload error:', error);
                
                // Get error message
                const errorMessage = error.response && error.response.data && error.response.data.message 
                    ? error.response.data.message 
                    : 'Failed to upload image. Please try again.';
                
                // Show error message
                showToast('Error', errorMessage, 'danger');
                
                // Execute error callback if provided
                if (typeof settings.onError === 'function') {
                    settings.onError(error);
                }
            });
        });
    }
}