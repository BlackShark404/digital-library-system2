/**
 * DynamicTable.js
 * A JavaScript library for handling server-side rendered tables with CRUD operations
 * Dependencies: Axios, Bootstrap, Font Awesome
 */

class DynamicTable {
  /**
   * Initialize the DynamicTable
   * @param {Object} config - Configuration options
   * @param {string} config.tableId - ID of the table element
   * @param {string} config.endpoint - Base API endpoint for CRUD operations
   * @param {Object} config.columns - Column definitions with field names and display labels
   * @param {string} config.primaryKey - Primary key field name
   * @param {string} config.paginationId - ID of the pagination container
   * @param {string} config.searchId - ID of the search input field
   * @param {string} config.modalId - ID of the modal container
   * @param {Function} config.customFormatter - Optional custom formatter for table cells
   * @param {Object} config.formFields - Form field definitions for create/update modal
   */
  constructor(config) {
    this.tableId = config.tableId;
    this.endpoint = config.endpoint;
    this.columns = config.columns;
    this.primaryKey = config.primaryKey;
    this.paginationId = config.paginationId;
    this.searchId = config.searchId;
    this.modalId = config.modalId;
    this.customFormatter = config.customFormatter || null;
    this.formFields = config.formFields;
    this.currentPage = 1;
    this.perPage = config.perPage || 10;
    this.lastSearch = '';
    this.sortField = config.defaultSortField || null;
    this.sortOrder = config.defaultSortOrder || 'asc';
    
    this.init();
  }

  /**
   * Initialize the table and attach event listeners
   */
  init() {
    this.loadData();
    this.setupEventListeners();
    this.setupModal();
  }

  /**
   * Set up event listeners for search, pagination, and table actions
   */
  setupEventListeners() {
    // Search input handler with debounce
    const searchInput = document.getElementById(this.searchId);
    if (searchInput) {
      let debounceTimer;
      searchInput.addEventListener('input', (e) => {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
          this.lastSearch = e.target.value;
          this.currentPage = 1;
          this.loadData();
        }, 500);
      });
    }

    // Add event listener for pagination
    const paginationContainer = document.getElementById(this.paginationId);
    if (paginationContainer) {
      paginationContainer.addEventListener('click', (e) => {
        if (e.target.tagName === 'A' && e.target.dataset.page) {
          e.preventDefault();
          this.currentPage = parseInt(e.target.dataset.page);
          this.loadData();
        }
      });
    }

    // Add event listeners for column sorting
    const tableElement = document.getElementById(this.tableId);
    if (tableElement) {
      tableElement.addEventListener('click', (e) => {
        const sortHeader = e.target.closest('th[data-sort]');
        if (sortHeader) {
          const field = sortHeader.dataset.sort;
          if (this.sortField === field) {
            this.sortOrder = this.sortOrder === 'asc' ? 'desc' : 'asc';
          } else {
            this.sortField = field;
            this.sortOrder = 'asc';
          }
          this.loadData();
        }
      });
    }

    // Setup add button click event
    const addButton = document.querySelector(`[data-action="add-${this.tableId}"]`);
    if (addButton) {
      addButton.addEventListener('click', () => this.openCreateModal());
    }
  }

  /**
   * Setup the CRUD modal
   */
  setupModal() {
    // Create modal template if it doesn't exist
    if (!document.getElementById(this.modalId)) {
      const modalTemplate = `
        <div class="modal fade" id="${this.modalId}" tabindex="-1" aria-labelledby="${this.modalId}Label" aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="${this.modalId}Label">Record</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <form id="${this.modalId}-form">
                  <input type="hidden" id="${this.modalId}-id" name="${this.primaryKey}">
                  <!-- Form fields will be injected here -->
                </form>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="${this.modalId}-save">Save</button>
              </div>
            </div>
          </div>
        </div>
      `;
      
      const modalContainer = document.createElement('div');
      modalContainer.innerHTML = modalTemplate;
      document.body.appendChild(modalContainer);

      // Create toast container if it doesn't exist
      if (!document.getElementById('toast-container')) {
        const toastContainer = document.createElement('div');
        toastContainer.id = 'toast-container';
        toastContainer.className = 'toast-container position-fixed bottom-0 end-0 p-3';
        document.body.appendChild(toastContainer);
      }

      // Add event listener for save button
      document.getElementById(`${this.modalId}-save`).addEventListener('click', () => {
        this.saveRecord();
      });

      // Generate form fields based on configuration
      this.generateFormFields();
    }
  }

  /**
   * Generate form fields for the modal based on configuration
   */
  generateFormFields() {
    const form = document.getElementById(`${this.modalId}-form`);
    const idField = document.getElementById(`${this.modalId}-id`);
    
    // Clear existing fields except the ID field
    while (form.childNodes.length > 0) {
      if (form.firstChild !== idField) {
        form.removeChild(form.firstChild);
      } else {
        form.removeChild(form.firstChild);
        break;
      }
    }
    
    // Add the ID field back
    form.appendChild(idField);
    
    // Generate form fields based on configuration
    for (const field in this.formFields) {
      const config = this.formFields[field];
      const formGroup = document.createElement('div');
      formGroup.className = 'mb-3';
      
      const label = document.createElement('label');
      label.htmlFor = `${this.modalId}-${field}`;
      label.className = 'form-label';
      label.textContent = config.label;
      
      let input;
      
      switch (config.type) {
        case 'select':
          input = document.createElement('select');
          input.className = 'form-select';
          
          if (config.options) {
            for (const option of config.options) {
              const optElement = document.createElement('option');
              optElement.value = option.value;
              optElement.textContent = option.label;
              input.appendChild(optElement);
            }
          }
          break;
          
        case 'textarea':
          input = document.createElement('textarea');
          input.className = 'form-control';
          input.rows = config.rows || 3;
          break;
          
        case 'checkbox':
          const checkDiv = document.createElement('div');
          checkDiv.className = 'form-check';
          
          input = document.createElement('input');
          input.className = 'form-check-input';
          input.type = 'checkbox';
          
          label.className = 'form-check-label';
          
          checkDiv.appendChild(input);
          checkDiv.appendChild(label);
          formGroup.appendChild(checkDiv);
          break;
          
        default:
          input = document.createElement('input');
          input.className = 'form-control';
          input.type = config.type || 'text';
      }
      
      input.id = `${this.modalId}-${field}`;
      input.name = field;
      
      if (config.required) {
        input.required = true;
      }
      
      if (config.placeholder) {
        input.placeholder = config.placeholder;
      }
      
      if (config.type !== 'checkbox') {
        formGroup.appendChild(label);
        formGroup.appendChild(input);
      }
      
      form.appendChild(formGroup);
    }
  }

  /**
   * Load data from the server and update the table
   */
  loadData() {
    const params = {
      page: this.currentPage,
      per_page: this.perPage,
      search: this.lastSearch
    };
    
    if (this.sortField) {
      params.sort_field = this.sortField;
      params.sort_order = this.sortOrder;
    }
    
    // Show loading state
    this.showLoading();
    
    axios.get(this.endpoint, { params })
      .then(response => {
        this.updateTable(response.data);
        this.updatePagination(response.data.pagination);
      })
      .catch(error => {
        console.error('Error loading data:', error);
        this.showToast('error', 'Failed to load data', 'An error occurred while loading data.');
      })
      .finally(() => {
        this.hideLoading();
      });
  }

  /**
   * Update the table with new data
   * @param {Object} data - Data from the server
   */
  updateTable(data) {
    const table = document.getElementById(this.tableId);
    const tbody = table.querySelector('tbody');
    
    // Clear existing rows
    tbody.innerHTML = '';
    
    // Add new rows
    if (data.records && data.records.length > 0) {
      for (const record of data.records) {
        const row = document.createElement('tr');
        
        // Add data cells
        for (const column in this.columns) {
          const cell = document.createElement('td');
          
          if (this.customFormatter && typeof this.customFormatter === 'function') {
            cell.innerHTML = this.customFormatter(column, record[column], record);
          } else {
            cell.textContent = record[column] || '';
          }
          
          row.appendChild(cell);
        }
        
        // Add action buttons
        const actionsCell = document.createElement('td');
        actionsCell.className = 'text-end';
        
        // Edit button
        const editButton = document.createElement('button');
        editButton.className = 'btn btn-sm btn-outline-primary me-1';
        editButton.innerHTML = '<i class="fa fa-edit"></i>';
        editButton.addEventListener('click', () => this.openEditModal(record));
        
        // Delete button
        const deleteButton = document.createElement('button');
        deleteButton.className = 'btn btn-sm btn-outline-danger';
        deleteButton.innerHTML = '<i class="fa fa-trash"></i>';
        deleteButton.addEventListener('click', () => this.confirmDelete(record));
        
        actionsCell.appendChild(editButton);
        actionsCell.appendChild(deleteButton);
        row.appendChild(actionsCell);
        
        tbody.appendChild(row);
      }
    } else {
      // Show no records message
      const row = document.createElement('tr');
      const cell = document.createElement('td');
      cell.colSpan = Object.keys(this.columns).length + 1;
      cell.className = 'text-center';
      cell.textContent = 'No records found';
      row.appendChild(cell);
      tbody.appendChild(row);
    }
  }

  /**
   * Update pagination controls
   * @param {Object} pagination - Pagination data from the server
   */
  updatePagination(pagination) {
    const container = document.getElementById(this.paginationId);
    if (!container) return;
    
    container.innerHTML = '';
    
    if (pagination.total_pages <= 1) return;
    
    const ul = document.createElement('ul');
    ul.className = 'pagination justify-content-center';
    
    // Previous button
    const prevLi = document.createElement('li');
    prevLi.className = `page-item ${pagination.current_page === 1 ? 'disabled' : ''}`;
    
    const prevLink = document.createElement('a');
    prevLink.className = 'page-link';
    prevLink.href = '#';
    prevLink.dataset.page = pagination.current_page - 1;
    prevLink.innerHTML = '&laquo;';
    
    prevLi.appendChild(prevLink);
    ul.appendChild(prevLi);
    
    // Page numbers
    let startPage = Math.max(1, pagination.current_page - 2);
    let endPage = Math.min(pagination.total_pages, startPage + 4);
    
    if (endPage - startPage < 4) {
      startPage = Math.max(1, endPage - 4);
    }
    
    for (let i = startPage; i <= endPage; i++) {
      const li = document.createElement('li');
      li.className = `page-item ${i === pagination.current_page ? 'active' : ''}`;
      
      const link = document.createElement('a');
      link.className = 'page-link';
      link.href = '#';
      link.dataset.page = i;
      link.textContent = i;
      
      li.appendChild(link);
      ul.appendChild(li);
    }
    
    // Next button
    const nextLi = document.createElement('li');
    nextLi.className = `page-item ${pagination.current_page === pagination.total_pages ? 'disabled' : ''}`;
    
    const nextLink = document.createElement('a');
    nextLink.className = 'page-link';
    nextLink.href = '#';
    nextLink.dataset.page = pagination.current_page + 1;
    nextLink.innerHTML = '&raquo;';
    
    nextLi.appendChild(nextLink);
    ul.appendChild(nextLi);
    
    container.appendChild(ul);
  }

  /**
   * Open the create modal
   */
  openCreateModal() {
    const form = document.getElementById(`${this.modalId}-form`);
    form.reset();
    document.getElementById(`${this.modalId}-id`).value = '';
    
    const modal = new bootstrap.Modal(document.getElementById(this.modalId));
    document.getElementById(`${this.modalId}Label`).textContent = 'Create Record';
    modal.show();
  }

  /**
   * Open the edit modal with record data
   * @param {Object} record - The record to edit
   */
  openEditModal(record) {
    const form = document.getElementById(`${this.modalId}-form`);
    form.reset();
    
    // Set form values
    document.getElementById(`${this.modalId}-id`).value = record[this.primaryKey];
    
    for (const field in this.formFields) {
      const input = document.getElementById(`${this.modalId}-${field}`);
      if (input) {
        if (input.type === 'checkbox') {
          input.checked = Boolean(record[field]);
        } else {
          input.value = record[field] || '';
        }
      }
    }
    
    const modal = new bootstrap.Modal(document.getElementById(this.modalId));
    document.getElementById(`${this.modalId}Label`).textContent = 'Edit Record';
    modal.show();
  }

  /**
   * Save a record (create or update)
   */
  saveRecord() {
    const form = document.getElementById(`${this.modalId}-form`);
    const formData = new FormData(form);
    const id = formData.get(this.primaryKey);
    
    const data = {};
    formData.forEach((value, key) => {
      data[key] = value;
    });
    
    // Handle checkboxes
    for (const field in this.formFields) {
      if (this.formFields[field].type === 'checkbox') {
        const input = document.getElementById(`${this.modalId}-${field}`);
        data[field] = input.checked ? 1 : 0;
      }
    }
    
    let method, url, successMessage;
    
    if (id) {
      // Update
      method = 'put';
      url = `${this.endpoint}/${id}`;
      successMessage = 'Record updated successfully';
    } else {
      // Create
      method = 'post';
      url = this.endpoint;
      successMessage = 'Record created successfully';
      delete data[this.primaryKey]; // Remove empty ID for creation
    }
    
    axios({
      method,
      url,
      data
    })
      .then(response => {
        // Close modal
        const modalElement = document.getElementById(this.modalId);
        const modal = bootstrap.Modal.getInstance(modalElement);
        modal.hide();
        
        // Reload data
        this.loadData();
        
        // Show success message
        this.showToast('success', id ? 'Update Successful' : 'Create Successful', successMessage);
      })
      .catch(error => {
        console.error('Error saving record:', error);
        
        let errorMessage = 'An error occurred while saving the record.';
        
        if (error.response && error.response.data && error.response.data.message) {
          errorMessage = error.response.data.message;
        }
        
        this.showToast('error', 'Error', errorMessage);
      });
  }

  /**
   * Confirm delete operation
   * @param {Object} record - The record to delete
   */
  confirmDelete(record) {
    if (confirm(`Are you sure you want to delete this record?`)) {
      this.deleteRecord(record[this.primaryKey]);
    }
  }

  /**
   * Delete a record
   * @param {string|number} id - The record ID to delete
   */
  deleteRecord(id) {
    axios.delete(`${this.endpoint}/${id}`)
      .then(response => {
        // Reload data
        this.loadData();
        
        // Show success message
        this.showToast('success', 'Delete Successful', 'Record deleted successfully');
      })
      .catch(error => {
        console.error('Error deleting record:', error);
        
        let errorMessage = 'An error occurred while deleting the record.';
        
        if (error.response && error.response.data && error.response.data.message) {
          errorMessage = error.response.data.message;
        }
        
        this.showToast('error', 'Error', errorMessage);
      });
  }

  /**
   * Show a toast notification
   * @param {string} type - The toast type (success, error, warning, info)
   * @param {string} title - The toast title
   * @param {string} message - The toast message
   */
  showToast(type, title, message) {
    const container = document.getElementById('toast-container');
    
    const toastId = `toast-${Date.now()}`;
    
    let icon, bgClass;
    switch (type) {
      case 'success':
        icon = 'fa-check-circle';
        bgClass = 'bg-success';
        break;
      case 'error':
        icon = 'fa-exclamation-circle';
        bgClass = 'bg-danger';
        break;
      case 'warning':
        icon = 'fa-exclamation-triangle';
        bgClass = 'bg-warning';
        break;
      case 'info':
      default:
        icon = 'fa-info-circle';
        bgClass = 'bg-info';
        break;
    }
    
    const toastHTML = `
      <div class="toast" id="${toastId}" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header ${bgClass} text-white">
          <i class="fas ${icon} me-2"></i>
          <strong class="me-auto">${title}</strong>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body">
          ${message}
        </div>
      </div>
    `;
    
    container.insertAdjacentHTML('beforeend', toastHTML);
    
    const toastElement = document.getElementById(toastId);
    const toast = new bootstrap.Toast(toastElement, {
      autohide: true,
      delay: 5000
    });
    
    toast.show();
    
    // Remove the toast from DOM after it's hidden
    toastElement.addEventListener('hidden.bs.toast', () => {
      toastElement.remove();
    });
  }

  /**
   * Show loading state
   */
  showLoading() {
    const table = document.getElementById(this.tableId);
    table.classList.add('loading');
    
    // Check if loading overlay exists, create if not
    let loadingOverlay = table.nextElementSibling;
    if (!loadingOverlay || !loadingOverlay.classList.contains('loading-overlay')) {
      loadingOverlay = document.createElement('div');
      loadingOverlay.className = 'loading-overlay d-none';
      loadingOverlay.innerHTML = '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>';
      table.parentNode.insertBefore(loadingOverlay, table.nextSibling);
    }
    
    loadingOverlay.classList.remove('d-none');
  }

  /**
   * Hide loading state
   */
  hideLoading() {
    const table = document.getElementById(this.tableId);
    table.classList.remove('loading');
    
    const loadingOverlay = table.nextElementSibling;
    if (loadingOverlay && loadingOverlay.classList.contains('loading-overlay')) {
      loadingOverlay.classList.add('d-none');
    }
  }
}

/**
 * Helper CSS for loading state
 */
(function() {
  const style = document.createElement('style');
  style.textContent = `
    .loading-overlay {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(255, 255, 255, 0.7);
      display: flex;
      justify-content: center;
      align-items: center;
      z-index: 1000;
    }
    
    .table-responsive {
      position: relative;
    }
  `;
  document.head.appendChild(style);
})();