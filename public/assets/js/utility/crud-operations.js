// crud-operations.js
// Utility module for CRUD operations using Axios

/**
 * Class for handling CRUD operations with Axios
 */
class CrudOperations {
    /**
     * Create a new CRUD operations handler
     * @param {string} baseUrl - Base URL for API endpoints
     * @param {Object} options - Configuration options
     */
    constructor(baseUrl, options = {}) {
        this.baseUrl = baseUrl.endsWith('/') ? baseUrl : baseUrl + '/';
        this.options = {
            idField: 'id',
            toastMessages: true,
            confirmDelete: true,
            reloadAfterAction: true,
            onCreated: null,
            onUpdated: null,
            onDeleted: null,
            onError: null,
            ...options
        };
        
        // Configure axios instance
        this.axios = axios.create({
            baseURL: this.baseUrl,
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                ...options.headers
            }
        });
    }
    
    /**
     * Get all items or a specific item by ID
     * @param {string|number} id - Optional ID to fetch specific item
     * @param {Object} params - Optional query parameters
     * @returns {Promise} - Axios promise
     */
    get(id = null, params = {}) {
        const endpoint = id ? `${id}` : '';
        
        return this.axios.get(endpoint, { params })
            .then(response => {
                if (!response.data || !response.data.success) {
                    throw new Error(response.data.message || 'Failed to fetch data');
                }
                return response.data.data;
            })
            .catch(error => {
                this._handleError(error, 'Error fetching data');
                throw error;
            });
    }
    
    /**
     * Create a new item
     * @param {Object} data - Item data
     * @returns {Promise} - Axios promise
     */
    create(data) {
        return this.axios.post('', data)
            .then(response => {
                if (!response.data || !response.data.success) {
                    throw new Error(response.data.message || 'Failed to create item');
                }
                
                if (this.options.toastMessages) {
                    showToast('Success', response.data.message || 'Item created successfully', 'success');
                }
                
                if (this.options.onCreated && typeof this.options.onCreated === 'function') {
                    this.options.onCreated(response.data.data);
                }
                
                return response.data.data;
            })
            .catch(error => {
                this._handleError(error, 'Error creating item');
                throw error;
            });
    }
    
    /**
     * Update an existing item
     * @param {string|number} id - Item ID
     * @param {Object} data - Updated data
     * @returns {Promise} - Axios promise
     */
    update(id, data) {
        return this.axios.put(`${id}`, data)
            .then(response => {
                if (!response.data || !response.data.success) {
                    throw new Error(response.data.message || 'Failed to update item');
                }
                
                if (this.options.toastMessages) {
                    showToast('Success', response.data.message || 'Item updated successfully', 'success');
                }
                
                if (this.options.onUpdated && typeof this.options.onUpdated === 'function') {
                    this.options.onUpdated(response.data.data);
                }
                
                return response.data.data;
            })
            .catch(error => {
                this._handleError(error, 'Error updating item');
                throw error;
            });
    }
    
    /**
     * Delete an item
     * @param {string|number} id - Item ID
     * @returns {Promise} - Axios promise
     */
    delete(id) {
        // Confirm deletion if enabled
        if (this.options.confirmDelete) {
            if (!confirm('Are you sure you want to delete this item?')) {
                return Promise.reject(new Error('Delete operation cancelled'));
            }
        }
        
        return this.axios.delete(`${id}`)
            .then(response => {
                if (!response.data || !response.data.success) {
                    throw new Error(response.data.message || 'Failed to delete item');
                }
                
                if (this.options.toastMessages) {
                    showToast('Success', response.data.message || 'Item deleted successfully', 'success');
                }
                
                if (this.options.onDeleted && typeof this.options.onDeleted === 'function') {
                    this.options.onDeleted(id);
                }
                
                return true;
            })
            .catch(error => {
                this._handleError(error, 'Error deleting item');
                throw error;
            });
    }
    
    /**
     * Handle form submission for create or update operations
     * @param {HTMLFormElement} form - Form element
     * @param {string} mode - 'create' or 'update'
     * @param {Function} onSuccess - Optional success callback
     */
    handleFormSubmit(form, mode = 'create', onSuccess = null) {
        if (!form) {
            console.error('Form element not provided');
            return;
        }
        
        form.addEventListener('submit', (event) => {
            event.preventDefault();
            
            // Get form data
            const formData = new FormData(form);
            const data = {};
            formData.forEach((value, key) => {
                data[key] = value;
            });
            
            // Determine if we're creating or updating
            const isUpdate = mode === 'update';
            let itemId = null;
            
            if (isUpdate) {
                // Get item ID from form or other source
                itemId = data[this.options.idField] || form.getAttribute('data-id');
                
                if (!itemId) {
                    if (this.options.toastMessages) {
                        showToast('Error', 'Item ID not found for update operation', 'danger');
                    }
                    return;
                }
                
                delete data[this.options.idField]; // Remove ID from update data if present
            }
            
            // Perform the operation
            const operation = isUpdate 
                ? this.update(itemId, data)
                : this.create(data);
            
            operation
                .then(result => {
                    // Reset form after successful create
                    if (!isUpdate) {
                        form.reset();
                    }
                    
                    // Hide modal if form is in a modal
                    const modalElement = bootstrap.Modal.getInstance(form.closest('.modal'));
                    if (modalElement) {
                        modalElement.hide();
                    }
                    
                    // Call success callback if provided
                    if (onSuccess && typeof onSuccess === 'function') {
                        onSuccess(result);
                    }
                    
                    // Reload page if configured
                    if (this.options.reloadAfterAction) {
                        setTimeout(() => {
                            window.location.reload();
                        }, 1000);
                    }
                })
                .catch(error => {
                    console.error('Form submission error:', error);
                });
        });
    }
    
    /**
     * Set up click handlers for delete buttons
     * @param {string} selector - CSS selector for delete buttons
     * @param {Function} getItemId - Function to extract item ID from element
     */
    setupDeleteButtons(selector, getItemId = null) {
        const buttons = document.querySelectorAll(selector);
        
        buttons.forEach(button => {
            button.addEventListener('click', (event) => {
                event.preventDefault();
                
                // Get item ID
                let itemId;
                if (getItemId && typeof getItemId === 'function') {
                    itemId = getItemId(button);
                } else {
                    itemId = button.getAttribute('data-id');
                }
                
                if (!itemId) {
                    if (this.options.toastMessages) {
                        showToast('Error', 'Item ID not found for delete operation', 'danger');
                    }
                    return;
                }
                
                // Perform delete operation
                this.delete(itemId)
                    .then(() => {
                        // Remove row from table if applicable
                        const row = button.closest('tr');
                        if (row) {
                            row.remove();
                        }
                        
                        // Reload page if configured
                        if (this.options.reloadAfterAction) {
                            setTimeout(() => {
                                window.location.reload();
                            }, 1000);
                        }
                    })
                    .catch(error => {
                        console.error('Delete operation error:', error);
                    });
            });
        });
    }
    
    /**
     * Populate form fields with item data
     * @param {HTMLFormElement} form - Form element
     * @param {Object} data - Item data
     */
    populateForm(form, data) {
        if (!form || !data) return;
        
        // For each form field, set value from data
        Array.from(form.elements).forEach(field => {
            if (field.name && data[field.name] !== undefined) {
                if (field.type === 'checkbox') {
                    field.checked = !!data[field.name];
                } else if (field.type === 'radio') {
                    field.checked = field.value == data[field.name];
                } else {
                    field.value = data[field.name];
                }
            }
        });
        
        // Set form data-id attribute for update operations
        if (data[this.options.idField]) {
            form.setAttribute('data-id', data[this.options.idField]);
        }
    }
    
    /**
     * Handle error from API responses
     * @param {Error} error - Error object
     * @param {string} defaultMessage - Default error message
     * @private
     */
    _handleError(error, defaultMessage) {
        console.error(defaultMessage, error);
        
        // Extract error message
        const errorMessage = error.response && error.response.data && error.response.data.message 
            ? error.response.data.message 
            : defaultMessage;
        
        // Show toast if enabled
        if (this.options.toastMessages) {
            showToast('Error', errorMessage, 'danger');
        }
        
        // Call error callback if provided
        if (this.options.onError && typeof this.options.onError === 'function') {
            this.options.onError(error, errorMessage);
        }
    }
}