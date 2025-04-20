// data-fetcher.js
// Utility module for fetching data from API endpoints using Axios

/**
 * Fetches data from a specified API endpoint with optional query parameters
 * @param {string} endpoint - The API endpoint URL
 * @param {Object} params - Optional query parameters
 * @param {Function} onSuccess - Callback function on successful data fetch
 * @param {Function} onError - Callback function on error
 */
function fetchData(endpoint, params = {}, onSuccess = null, onError = null) {
    // Show loading indicator if available
    const loadingElement = document.getElementById('loading-indicator');
    if (loadingElement) loadingElement.classList.remove('d-none');
    
    // Configure request with parameters
    const config = {
        params: params,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    };
    
    axios.get(endpoint, config)
        .then(response => {
            // Hide loading indicator
            if (loadingElement) loadingElement.classList.add('d-none');
            
            // Check if response is successful
            if (response.data && response.data.success) {
                console.log('Data fetched successfully:', response.data);
                
                // Call success callback if provided
                if (onSuccess && typeof onSuccess === 'function') {
                    onSuccess(response.data.data);
                }
                
                // Show success toast
                showToast('Success', response.data.message || 'Data fetched successfully', 'success');
            } else {
                throw new Error(response.data.message || 'Error fetching data');
            }
        })
        .catch(error => {
            // Hide loading indicator
            if (loadingElement) loadingElement.classList.add('d-none');
            
            console.error('Error fetching data:', error);
            
            // Extract error message
            const errorMessage = error.response && error.response.data && error.response.data.message 
                ? error.response.data.message 
                : 'Failed to fetch data. Please try again.';
            
            // Show error toast
            showToast('Error', errorMessage, 'danger');
            
            // Call error callback if provided
            if (onError && typeof onError === 'function') {
                onError(error);
            }
        });
}

/**
 * Populates HTML elements with data fetched from an API endpoint
 * @param {string} endpoint - The API endpoint URL
 * @param {Object} elementMappings - Object mapping data keys to element IDs
 * @param {Object} params - Optional query parameters
 */
function fetchAndPopulate(endpoint, elementMappings, params = {}) {
    fetchData(endpoint, params, 
        // Success callback
        (data) => {
            // Populate elements with received data
            for (const [dataKey, elementId] of Object.entries(elementMappings)) {
                const element = document.getElementById(elementId);
                if (!element) {
                    console.warn(`Element with ID '${elementId}' not found`);
                    continue;
                }
                
                const value = data[dataKey];
                
                // Handle different element types
                if (element.tagName === 'INPUT' || element.tagName === 'TEXTAREA' || element.tagName === 'SELECT') {
                    element.value = value || '';
                } else {
                    element.textContent = value || '';
                }
            }
        },
        // Error callback
        (error) => {
            console.error('Error populating elements:', error);
        }
    );
}

/**
 * Fetches data and renders it in a template
 * @param {string} endpoint - The API endpoint URL
 * @param {string} templateId - ID of the template element
 * @param {string} containerId - ID of the container to render items into
 * @param {Object} params - Optional query parameters
 */
function fetchAndRender(endpoint, templateId, containerId, params = {}) {
    const template = document.getElementById(templateId);
    const container = document.getElementById(containerId);
    
    if (!template || !container) {
        console.error(`Template or container element not found`);
        return;
    }
    
    fetchData(endpoint, params, 
        // Success callback
        (data) => {
            // Clear container
            container.innerHTML = '';
            
            // Check if data is an array
            const items = Array.isArray(data) ? data : [data];
            
            if (items.length === 0) {
                container.innerHTML = '<div class="alert alert-info">No items found</div>';
                return;
            }
            
            // Render each item using template
            items.forEach(item => {
                const templateClone = template.content.cloneNode(true);
                
                // Replace placeholders with data
                const elements = templateClone.querySelectorAll('[data-field]');
                elements.forEach(element => {
                    const field = element.getAttribute('data-field');
                    const value = item[field] || '';
                    
                    if (element.tagName === 'INPUT' || element.tagName === 'TEXTAREA' || element.tagName === 'SELECT') {
                        element.value = value;
                    } else {
                        element.textContent = value;
                    }
                });
                
                // Handle data attributes
                const dataElements = templateClone.querySelectorAll('[data-attr]');
                dataElements.forEach(element => {
                    const dataAttr = element.getAttribute('data-attr');
                    const [attrName, fieldName] = dataAttr.split(':');
                    
                    if (attrName && fieldName && item[fieldName] !== undefined) {
                        element.setAttribute(attrName, item[fieldName]);
                    }
                });
                
                container.appendChild(templateClone);
            });
        },
        // Error callback
        (error) => {
            container.innerHTML = '<div class="alert alert-danger">Failed to load data</div>';
        }
    );
}