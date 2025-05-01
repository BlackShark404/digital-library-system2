/**
 * DataTablesManager Class with Badge Support
 * A class to handle DataTables initialization with advanced features:
 * - Search
 * - Filters
 * - Pagination
 * - Per page selection (customizable placement)
 * - External search and per page controls 
 * - Custom modals (view, edit, delete with confirmation)
 * - Client-side rendering
 * - Table refresh after add/edit/delete operations
 * - Toast notifications (success, error, warning, info)
 * - Bootstrap badges on column data
 */
class DataTablesManager {
  /**
   * @param {string} tableId - The ID of the table element
   * @param {Object} options - Configuration options
   * @param {Array} options.columns - Column definitions with { data, title } objects
   * @param {string} options.ajaxUrl - URL for AJAX data source
   * @param {Function} options.viewRowCallback - Function to call when view action is clicked
   * @param {Function} options.editRowCallback - Function to call when edit action is clicked
   * @param {Function} options.deleteRowCallback - Function to call when delete action is confirmed
   * @param {Object} options.customButtons - Custom buttons configuration
   * @param {Object} options.toastOptions - Toast notification options
   * @param {Object} options.paginationOptions - Pagination and display options
   * @param {string} options.externalSearchId - ID of external search input element
   * @param {string} options.externalPerPageId - ID of external per page select element
   */
  constructor(tableId, options = {}) {
    this.tableId = tableId;
    this.tableElement = document.getElementById(tableId);
    this.dataTable = null;
    this.data = [];
    this.toastTimeouts = new Map(); // Store toast timeouts for better memory management
    
    // Default options with destructuring for better merging
    this.options = {
      columns: [],
      ajaxUrl: '',
      viewRowCallback: null,
      editRowCallback: null,
      deleteRowCallback: null,
      customButtons: {},
      toastOptions: {
        position: 'bottom-right',     // toast position: top-right, top-left, bottom-right, bottom-left
        autoClose: 4000,              // auto close after 4 seconds
        hideProgressBar: false,       // show progress bar
        closeOnClick: true,           // close when clicked
        pauseOnHover: true,           // pause countdown on hover
        draggable: true,              // allow dragging
        enableIcons: true,            // show icons
      },
      paginationOptions: {
        lengthMenu: [10, 25, 50, 100], // Options for rows per page
        pageLength: 10,                // Default rows per page
        lengthChange: true,            // Show length changing controls
      },
      externalSearchId: null,          // ID of external search input
      externalPerPageId: null,         // ID of external per page select
      ...options
    };
    
    // Initialize toast container
    this._initializeToastContainer();
    
    // Initialize table
    this.initialize();
  }
  
  /**
   * Initialize the Toast Container - only once per page load
   * @private
   */
  _initializeToastContainer() {
    // Check if toast container already exists to avoid duplication
    if (!document.getElementById('toastContainer')) {
      // Create and add stylesheet to head once
      const styleElement = document.createElement('style');
      styleElement.textContent = `
        #toastContainer {
          position: fixed;
          z-index: 9999;
          padding: 15px;
          pointer-events: none;
        }
        #toastContainer.top-right {
          top: 15px;
          right: 15px;
        }
        #toastContainer.top-left {
          top: 15px;
          left: 15px;
        }
        #toastContainer.bottom-right {
          bottom: 15px;
          right: 15px;
        }
        #toastContainer.bottom-left {
          bottom: 15px;
          left: 15px;
        }
        .toast {
          position: relative;
          max-width: 350px;
          margin-bottom: 10px;
          border-radius: 5px;
          box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
          color: white;
          padding: 15px 20px;
          overflow: hidden;
          display: flex;
          align-items: center;
          pointer-events: auto;
          opacity: 0;
          transform: translateY(-20px);
          transition: all 0.3s ease-in-out;
        }
        .toast.show {
          opacity: 1;
          transform: translateY(0);
        }
        .toast-icon {
          margin-right: 12px;
          font-size: 20px;
        }
        .toast-content {
          flex: 1;
        }
        .toast-title {
          font-weight: bold;
          margin-bottom: 5px;
        }
        .toast-message {
          font-size: 14px;
        }
        .toast-close {
          margin-left: 10px;
          cursor: pointer;
          opacity: 0.7;
          transition: opacity 0.2s;
          font-size: 18px;
          background: none;
          border: none;
          color: white;
        }
        .toast-close:hover {
          opacity: 1;
        }
        .toast-success {
          background-color: #4caf50;
        }
        .toast-error {
          background-color: #f44336;
        }
        .toast-warning {
          background-color: #ff9800;
        }
        .toast-info {
          background-color: #2196f3;
        }
        .toast-progress {
          position: absolute;
          bottom: 0;
          left: 0;
          height: 3px;
          width: 100%;
          background-color: rgba(255, 255, 255, 0.3);
        }
        .toast-progress-bar {
          height: 100%;
          width: 100%;
          background-color: rgba(255, 255, 255, 0.5);
          transition: width linear;
        }
      `;
      document.head.appendChild(styleElement);
      
      // Create toast container with specified position
      const position = this.options.toastOptions.position || 'top-right';
      const toastContainer = document.createElement('div');
      toastContainer.id = 'toastContainer';
      toastContainer.className = position;
      document.body.appendChild(toastContainer);
    }
  }
  
  /**
   * Create external pagination controls if not existing
   * @private
   */
  _initializeExternalControls() {
    // Set up external search if specified
    if (this.options.externalSearchId) {
      const searchInput = document.getElementById(this.options.externalSearchId);
      if (searchInput) {
        // Clear previous event listeners to prevent duplication
        const newSearchInput = searchInput.cloneNode(true);
        searchInput.parentNode.replaceChild(newSearchInput, searchInput);
        
        // Add event listener for search
        newSearchInput.addEventListener('keyup', (e) => {
          this.dataTable.search(e.target.value).draw();
        });

        // Set placeholder if not already set
        if (!newSearchInput.getAttribute('placeholder')) {
          newSearchInput.setAttribute('placeholder', 'Search records...');
        }
      } else {
        console.warn(`External search element with ID '${this.options.externalSearchId}' not found.`);
      }
    }
    
    // Set up external per page select if specified
    if (this.options.externalPerPageId) {
      const perPageSelect = document.getElementById(this.options.externalPerPageId);
      if (perPageSelect) {
        // Clear previous event listeners to prevent duplication
        const newPerPageSelect = perPageSelect.cloneNode(true);
        perPageSelect.parentNode.replaceChild(newPerPageSelect, perPageSelect);
        
        // Check if select already has options
        if (newPerPageSelect.options.length === 0) {
          // Populate select with options if empty
          this.options.paginationOptions.lengthMenu.forEach(length => {
            const option = document.createElement('option');
            option.value = length;
            option.textContent = length;
            newPerPageSelect.appendChild(option);
          });
          
          // Set default value
          newPerPageSelect.value = this.options.paginationOptions.pageLength;
        }
        
        // Add event listener for per page change
        newPerPageSelect.addEventListener('change', (e) => {
          this.dataTable.page.len(parseInt(e.target.value)).draw();
        });
      } else {
        console.warn(`External per page element with ID '${this.options.externalPerPageId}' not found.`);
      }
    }
  }
  
  /**
   * Show a toast notification
   * @param {string} type - Toast type (success, error, warning, info)
   * @param {string} title - Toast title
   * @param {string} message - Toast message
   * @param {Object} options - Additional options to override defaults
   * @returns {HTMLElement} Toast element
   */
  showToast(type, title, message, options = {}) {
    const toastOptions = { ...this.options.toastOptions, ...options };
    const toastId = `toast-${Date.now()}`;
    const toastContainer = document.getElementById('toastContainer');
    
    // Get the icon based on type
    let icon = '';
    const iconMap = {
      success: toastOptions.enableIcons ? '<i class="fas fa-check-circle"></i>' : '✓',
      error: toastOptions.enableIcons ? '<i class="fas fa-times-circle"></i>' : '✗',
      warning: toastOptions.enableIcons ? '<i class="fas fa-exclamation-triangle"></i>' : '⚠',
      info: toastOptions.enableIcons ? '<i class="fas fa-info-circle"></i>' : 'ℹ',
      default: toastOptions.enableIcons ? '<i class="fas fa-bell"></i>' : '🔔'
    };
    
    icon = iconMap[type] || iconMap.default;
    
    // Create toast element
    const toast = document.createElement('div');
    toast.id = toastId;
    toast.className = `toast toast-${type}`;
    toast.innerHTML = `
      <div class="toast-icon">${icon}</div>
      <div class="toast-content">
        <div class="toast-title">${title}</div>
        <div class="toast-message">${message}</div>
      </div>
      <button class="toast-close">&times;</button>
      ${!toastOptions.hideProgressBar ? '<div class="toast-progress"><div class="toast-progress-bar"></div></div>' : ''}
    `;
    
    // Append toast to container
    toastContainer.appendChild(toast);
    
    // Event delegation for toast interaction
    this._setupToastEvents(toast, toastOptions);
    
    // Show toast with animation
    setTimeout(() => {
      toast.classList.add('show');
      
      // Set progress bar animation if enabled
      if (!toastOptions.hideProgressBar) {
        const progressBar = toast.querySelector('.toast-progress-bar');
        progressBar.style.width = '0%';
        progressBar.style.transition = `width ${toastOptions.autoClose}ms linear`;
      }
      
      // Auto close if enabled
      if (toastOptions.autoClose) {
        const timeoutId = setTimeout(() => {
          this._closeToast(toast);
        }, toastOptions.autoClose);
        
        // Store timeout ID for potential cancellation
        this.toastTimeouts.set(toastId, timeoutId);
      }
    }, 10);
    
    return toast;
  }
  
  /**
   * Setup event listeners for toast
   * @param {HTMLElement} toast - Toast element
   * @param {Object} options - Toast options
   * @private
   */
  _setupToastEvents(toast, options) {
    const toastId = toast.id;
    const closeButton = toast.querySelector('.toast-close');
    
    // Close button event
    closeButton.addEventListener('click', () => {
      this._closeToast(toast);
    });
    
    // Close on click if enabled
    if (options.closeOnClick) {
      toast.addEventListener('click', (e) => {
        if (e.target.classList.contains('toast-close')) return;
        this._closeToast(toast);
      });
    }
    
    // Pause on hover if enabled
    if (options.pauseOnHover && options.autoClose) {
      let remainingTime = options.autoClose;
      let startTime;
      
      toast.addEventListener('mouseenter', () => {
        // Clear existing timeout
        const timeoutId = this.toastTimeouts.get(toastId);
        if (timeoutId) {
          clearTimeout(timeoutId);
          this.toastTimeouts.delete(toastId);
        }
        
        remainingTime -= (Date.now() - startTime);
        
        // Pause progress bar animation
        if (!options.hideProgressBar) {
          const progressBar = toast.querySelector('.toast-progress-bar');
          const progressBarWidth = progressBar.getBoundingClientRect().width;
          const parentWidth = progressBar.parentElement.getBoundingClientRect().width;
          const currentWidth = (progressBarWidth / parentWidth) * 100;
          
          progressBar.style.width = `${currentWidth}%`;
          progressBar.style.transition = 'none';
        }
      });
      
      toast.addEventListener('mouseleave', () => {
        startTime = Date.now();
        
        // Resume progress bar animation
        if (!options.hideProgressBar) {
          const progressBar = toast.querySelector('.toast-progress-bar');
          progressBar.style.width = '0%';
          progressBar.style.transition = `width ${remainingTime}ms linear`;
        }
        
        // Set new timeout with remaining time
        const timeoutId = setTimeout(() => {
          this._closeToast(toast);
        }, remainingTime);
        this.toastTimeouts.set(toastId, timeoutId);
      });
      
      startTime = Date.now();
    }
  }
  
  /**
   * Helper functions for different toast types
   * Using method shorthand syntax for conciseness
   */
  showSuccessToast(title, message, options = {}) {
    return this.showToast('success', title, message, options);
  }
  
  showErrorToast(title, message, options = {}) {
    return this.showToast('error', title, message, options);
  }
  
  showWarningToast(title, message, options = {}) {
    return this.showToast('warning', title, message, options);
  }
  
  showInfoToast(title, message, options = {}) {
    return this.showToast('info', title, message, options);
  }
  
  /**
   * Close a toast with animation
   * @param {HTMLElement} toast - Toast element
   * @private
   */
  _closeToast(toast) {
    // Clear timeout if exists
    const toastId = toast.id;
    const timeoutId = this.toastTimeouts.get(toastId);
    if (timeoutId) {
      clearTimeout(timeoutId);
      this.toastTimeouts.delete(toastId);
    }
    
    // Remove with animation
    toast.classList.remove('show');
    setTimeout(() => {
      if (toast.parentNode) {
        toast.parentNode.removeChild(toast);
      }
    }, 300);
  }
  
  /**
   * Generate Bootstrap badge HTML
   * @param {string|number} value - The value to display in the badge
   * @param {Object} badgeConfig - Badge configuration
   * @returns {string} HTML for the badge
   * @private
   */
  _generateBadgeHtml(value, badgeConfig) {
    // Default badge configuration
    const config = {
      type: 'primary',      // Bootstrap color: primary, secondary, success, etc.
      pill: false,          // Whether to use pill style
      size: '',             // Size: '', 'sm', 'lg'
      prefix: '',           // Text to display before the value
      suffix: '',           // Text to display after the value
      customClass: '',      // Additional CSS classes
      valueMap: null,       // Map of values to custom display/color
      ...badgeConfig
    };
    
    // Check if we have a value mapping
    let displayValue = value;
    let badgeType = config.type;
    
    if (config.valueMap && config.valueMap[value] !== undefined) {
      const mapping = config.valueMap[value];
      
      // Handle object mapping (with custom color and display text)
      if (typeof mapping === 'object') {
        displayValue = mapping.display || value;
        badgeType = mapping.type || badgeType;
      } 
      // Handle string mapping (just display text)
      else if (typeof mapping === 'string') {
        displayValue = mapping;
      }
    }
    
    // Build badge classes with array join for better performance
    const badgeClasses = [
      'badge',
      `bg-${badgeType}`,
      config.pill ? 'rounded-pill' : '',
      config.size ? `badge-${config.size}` : '',
      config.customClass
    ].filter(Boolean).join(' ');
    
    // Create badge HTML
    return `<span class="${badgeClasses}">${config.prefix}${displayValue}${config.suffix}</span>`;
  }
  
  /**
   * Initialize the DataTable
   */
  initialize() {
    try {
      // Process columns to add badge rendering if configured
      const processedColumns = this.options.columns.map(column => {
        // Check if column has badge configuration
        if (column.badge) {
          // Use object spread for immutability
          return {
            ...column,
            render: (data, type, row) => {
              // For sorting and filtering, use the raw data
              if (type === 'sort' || type === 'filter') {
                return data;
              }
              
              // For display, generate badge HTML
              return this._generateBadgeHtml(data, column.badge);
            }
          };
        }
        
        // Return original column if no badge configuration
        return column;
      });
      
      // Add action column if any callback is provided
      const columns = [...processedColumns];
      
      if (this.options.viewRowCallback || this.options.editRowCallback || this.options.deleteRowCallback) {
        columns.push({
          data: null,
          title: 'Actions',
          orderable: false,
          className: 'actions-column',
          render: (data, type, row) => {
            const buttons = [];
            
            if (this.options.viewRowCallback) {
              buttons.push(`<button class="btn btn-info btn-sm view-btn" data-id="${row.id}">View</button>`);
            }
            
            if (this.options.editRowCallback) {
              buttons.push(`<button class="btn btn-warning btn-sm edit-btn" data-id="${row.id}">Edit</button>`);
            }
            
            if (this.options.deleteRowCallback) {
              buttons.push(`<button class="btn btn-danger btn-sm delete-btn" data-id="${row.id}">Delete</button>`);
            }
            
            return `<div class="action-buttons">${buttons.join(' ')}</div>`;
          }
        });
      }
      
      // Configure DataTable DOM layout based on external controls
      let domLayout = 'Bfrtip';
      
      // Modify DOM layout when using external controls
      if (this.options.externalSearchId || this.options.externalPerPageId) {
        // Customize DOM layout based on which external controls are used
        domLayout = '';
        
        // If not using external search, include filter in DOM
        if (!this.options.externalSearchId) {
          domLayout += 'f';
        }
        
        // Include processing indicator and table
        domLayout += 'rt';
        
        // If not using external per page, include length menu in DOM
        if (!this.options.externalPerPageId && this.options.paginationOptions.lengthChange) {
          domLayout += 'l';
        }
        
        // Include pagination and info
        domLayout += 'ip';
        
        // Include buttons
        domLayout += 'B';
      }
      
      // Initialize DataTable with jQuery (since DataTables is jQuery-based)
      this.dataTable = $(`#${this.tableId}`).DataTable({
        columns,
        responsive: true,
        processing: true,
        dom: domLayout,
        lengthMenu: this.options.paginationOptions.lengthMenu,
        pageLength: this.options.paginationOptions.pageLength,
        lengthChange: this.options.paginationOptions.lengthChange,
        buttons: [
          'copy', 'csv', 'excel', 'pdf', 'print',
          ...(this.options.customButtons ? Object.values(this.options.customButtons) : [])
        ],
        ajax: {
          url: this.options.ajaxUrl,
          dataSrc: (json) => {
            this.data = json.data || json;
            return this.data;
          },
          error: (xhr, error, thrown) => {
            this.showErrorToast('Data Loading Error', 'Failed to load table data. ' + thrown);
            console.error('DataTables AJAX error:', error, thrown);
            return [];
          }
        },
        language: {
          searchPlaceholder: "Search records",
          emptyTable: "No data available",
          lengthMenu: "Show _MENU_ entries per page"
        }
      });
      
      // Initialize external controls after DataTable is created
      this._initializeExternalControls();
      
      // Attach event listeners with delegation for better performance
      this._attachEventListeners();
    } catch (error) {
      console.error('Error initializing DataTable:', error);
      this.showErrorToast('Initialization Error', 'Failed to initialize table: ' + error.message);
    }
  }
  
  /**
   * Attach event listeners for action buttons using event delegation
   * @private
   */
  _attachEventListeners() {
    // Use a single event handler with delegation for all buttons
    const table = $(`#${this.tableId}`);
    
    // Using event delegation for better performance
    table.on('click', '.action-buttons button', (e) => {
      const button = e.currentTarget;
      const id = button.dataset.id;
      const rowData = this._findRowById(id);
      
      if (!rowData) {
        this.showErrorToast('Error', `Record #${id} not found`);
        return;
      }
      
      // Handle different button types
      if (button.classList.contains('view-btn') && this.options.viewRowCallback) {
        this.options.viewRowCallback(rowData, this);
        this.showInfoToast('View Record', `Viewing record #${id}`);
      } 
      else if (button.classList.contains('edit-btn') && this.options.editRowCallback) {
        this.options.editRowCallback(rowData, this);
        this.showWarningToast('Edit Record', `Editing record #${id}`);
      } 
      else if (button.classList.contains('delete-btn') && this.options.deleteRowCallback) {
        this._showDeleteConfirmationModal(rowData);
      }
    });
  }
  
  /**
   * Find a row by its ID with error handling
   * @param {number|string} id - Row ID
   * @returns {Object|null} Row data or null if not found
   * @private
   */
  _findRowById(id) {
    if (!id) return null;
    
    try {
      return this.data.find(row => row && row.id == id) || null;
    } catch (error) {
      console.error('Error finding row by ID:', error);
      return null;
    }
  }
  
  /**
   * Show delete confirmation modal
   * @param {Object} rowData - Row data
   * @private
   */
  _showDeleteConfirmationModal(rowData) {
    try {
      // Create modal if it doesn't exist
      const modalId = 'deleteConfirmationModal';
      let modal = document.getElementById(modalId);
      
      if (!modal) {
        const modalHtml = `
          <div class="modal fade" id="${modalId}" tabindex="-1" role="dialog" aria-labelledby="deleteConfirmationModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="deleteConfirmationModalLabel">Confirm Delete</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <div class="modal-body">
                  Are you sure you want to delete this record?
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                  <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
                </div>
              </div>
            </div>
          </div>
        `;
        
        const modalDiv = document.createElement('div');
        modalDiv.innerHTML = modalHtml;
        document.body.appendChild(modalDiv.firstChild);
        modal = document.getElementById(modalId);
      }
      
      // Using jQuery for Bootstrap modal - could be replaced with pure JS if using Bootstrap 5+
      const $modal = $(modal);
      
      // Clean up previous handlers to prevent memory leaks
      $('#confirmDeleteBtn').off('click');
      
      // Attach new delete confirmation handler
      $('#confirmDeleteBtn').on('click', () => {
        try {
          this.options.deleteRowCallback(rowData, this);
          $modal.modal('hide');
        } catch (error) {
          console.error('Error in delete confirmation:', error);
          this.showErrorToast('Delete Error', `Error deleting record: ${error.message}`);
        }
      });
      
      // Show modal
      $modal.modal('show');
    } catch (error) {
      console.error('Error showing delete modal:', error);
      this.showErrorToast('UI Error', 'Failed to show delete confirmation dialog.');
    }
  }
  
  /**
   * Refresh the DataTable with new data
   * @param {Array} [newData] - New data to use (optional)
   * @returns {DataTablesManager} this instance for chaining
   */
  refresh(newData = null) {
    try {
      if (newData) {
        this.data = newData;
        this.dataTable.clear().rows.add(newData).draw();
      } else {
        this.dataTable.ajax.reload();
      }
    } catch (error) {
      console.error('Error refreshing table:', error);
      this.showErrorToast('Refresh Error', `Failed to refresh table: ${error.message}`);
    }
    
    return this;
  }
  
  /**
   * Add a new row to the DataTable
   * @param {Object} rowData - Row data
   * @returns {DataTablesManager} this instance for chaining
   */
  addRow(rowData) {
    try {
      if (!rowData) {
        throw new Error('No data provided to add row');
      }
      
      this.data.push(rowData);
      this.dataTable.row.add(rowData).draw();
      
      // Show success toast
      this.showSuccessToast('Add Record', `New record #${rowData.id} has been added`);
    } catch (error) {
      console.error('Error adding row:', error);
      this.showErrorToast('Add Error', `Failed to add record: ${error.message}`);
    }
    
    return this;
  }
  
  /**
   * Update a row in the DataTable
   * @param {number|string} id - Row ID
   * @param {Object} newData - New row data
   * @returns {DataTablesManager} this instance for chaining
   */
  updateRow(id, newData) {
    try {
      // Find the row index
      const rowIndex = this.data.findIndex(row => row && row.id == id);
      
      if (rowIndex !== -1) {
        // Update the data array
        this.data[rowIndex] = { ...this.data[rowIndex], ...newData };
        
        // Update the DataTable row
        const row = this.dataTable.row(function(idx, data) {
          return data && data.id == id;
        });
        
        if (row.length) {
          row.data(this.data[rowIndex]).draw();
          this.showSuccessToast('Update Record', `Record #${id} has been updated`);
        } else {
          throw new Error(`Row found in data array but not in DataTable`);
        }
      } else {
        this.showErrorToast('Update Error', `Record #${id} not found`);
      }
    } catch (error) {
      console.error('Error updating row:', error);
      this.showErrorToast('Update Error', `Failed to update record: ${error.message}`);
    }
    
    return this;
  }
  
  /**
   * Delete a row from the DataTable
   * @param {number|string} id - Row ID
   * @returns {DataTablesManager} this instance for chaining
   */
  deleteRow(id) {
    try {
      // Find the row index
      const rowIndex = this.data.findIndex(row => row && row.id == id);
      
      if (rowIndex !== -1) {
        // Remove from the data array
        this.data.splice(rowIndex, 1);
        
        // Remove from the DataTable
        const row = this.dataTable.row(function(idx, data) {
          return data && data.id == id;
        });
        
        if (row.length) {
          row.remove().draw();
          this.showSuccessToast('Delete Record', `Record #${id} has been deleted`);
        } else {
          throw new Error(`Row found in data array but not in DataTable`);
        }
      } else {
        this.showErrorToast('Delete Error', `Record #${id} not found`);
      }
    } catch (error) {
      console.error('Error deleting row:', error);
      this.showErrorToast('Delete Error', `Failed to delete record: ${error.message}`);
    }
    
    return this;
  }
  
  /**
   * Apply filters to the DataTable
   * @param {Object} filters - Filter criteria
   * @returns {DataTablesManager} this instance for chaining
   */
  applyFilters(filters) {
    try {
      // Clear existing custom filters
      $.fn.dataTable.ext.search.pop();
      
      // Add custom filter function if filters exist
      if (filters && Object.keys(filters).length > 0) {
        $.fn.dataTable.ext.search.push((settings, data, dataIndex, rowData) => {
          // Check if this is our table
          if (settings.nTable.id !== this.tableId) {
            return true; // Skip filtering for other tables
          }
          
          // Check all filter criteria
          for (const [key, value] of Object.entries(filters)) {
            if (rowData[key] !== value) {
              return false;
            }
          }
          return true;
        });
        
        this.showInfoToast('Filters Applied', 'Table data has been filtered');
      } else {
        this.showInfoToast('Filters Removed', 'All filters have been cleared');
      }
      
      // Redraw the table
      this.dataTable.draw();
    } catch (error) {
      console.error('Error applying filters:', error);
      this.showErrorToast('Filter Error', `Failed to apply filters: ${error.message}`);
    }
    
    return this;
  }
  
  /**
   * Get the currently selected rows
   * @returns {Array} Selected row data
   */
  getSelectedRows() {
    try {
      return this.dataTable.rows({ selected: true }).data().toArray();
    } catch (error) {
      console.error('Error getting selected rows:', error);
      this.showErrorToast('Selection Error', `Failed to get selected rows: ${error.message}`);
      return [];
    }
  }
  
  /**
   * Set the page length (rows per page)
   * @param {number} length - Number of rows per page
   * @returns {DataTablesManager} this instance for chaining
   */
  setPageLength(length) {
    try {
      if (typeof length !== 'number' || length <= 0) {
        throw new Error('Invalid page length value');
      }
      
      this.dataTable.page.len(length).draw();
      
      // Update external per page dropdown if exists
      if (this.options.externalPerPageId) {
        const perPageSelect = document.getElementById(this.options.externalPerPageId);
        if (perPageSelect) {
          perPageSelect.value = length;
        }
      }
      
      this.showInfoToast('Display Updated', `Now showing ${length} records per page`);
    } catch (error) {
      console.error('Error setting page length:', error);
      this.showErrorToast('Page Length Error', `Failed to update page length: ${error.message}`);
    }
    
    return this;
  }
  
  /**
   * Get the current page length (rows per page)
   * @returns {number} Current page length
   */
  getPageLength() {
    return this.dataTable.page.len();
  }
  
  /**
   * Set search term programmatically
   * @param {string} term - Search term
   * @returns {DataTablesManager} this instance for chaining
   */
  setSearch(term) {
    try {
      this.dataTable.search(term).draw();
      
      // Update external search input if exists
      if (this.options.externalSearchId) {
        const searchInput = document.getElementById(this.options.externalSearchId);
        if (searchInput) {
          searchInput.value = term;
        }
      }
    } catch (error) {
      console.error('Error setting search term:', error);
      this.showErrorToast('Search Error', `Failed to set search term: ${error.message}`);
    }
    
    return this;
  }
  
  /**
   * Get available page length options
   * @returns {Array} Page length options
   */
  getPageLengthOptions() {
    return this.options.paginationOptions.lengthMenu;
  }
  
  /**
   * Update pagination options and refresh the table
   * @param {Object} options - Pagination options
   * @param {Array} options.lengthMenu - Options for rows per page
   * @param {number} options.pageLength - Default rows per page
   * @returns {DataTablesManager} this instance for chaining
   */
  updatePaginationOptions(options) {
    try {
      // Update options
      if (options.lengthMenu) {
        this.options.paginationOptions.lengthMenu = options.lengthMenu;
      }
      
      if (options.pageLength) {
        this.options.paginationOptions.pageLength = options.pageLength;
      }
      
      // Update DataTable settings
      this.dataTable.page.len(this.options.paginationOptions.pageLength);
      
      // Update external per page select if exists
      if (this.options.externalPerPageId) {
        const perPageSelect = document.getElementById(this.options.externalPerPageId);
        if (perPageSelect) {
          // Clear existing options
          perPageSelect.innerHTML = '';
          
          // Add new options
          this.options.paginationOptions.lengthMenu.forEach(length => {
            const option = document.createElement('option');
            option.value = length;
            option.textContent = length;
            perPageSelect.appendChild(option);
          });
          
          // Set selected value
          perPageSelect.value = this.options.paginationOptions.pageLength;
        }
      }
      
      // Redraw table
      this.dataTable.draw();
      
      this.showInfoToast('Pagination Updated', 'Table pagination options have been updated');
    } catch (error) {
      console.error('Error updating pagination options:', error);
      this.showErrorToast('Pagination Error', `Failed to update pagination options: ${error.message}`);
    }
    
    return this;
  }
  
  /**
   * Clean up resources when instance is no longer needed
   * Call this method to prevent memory leaks
   */
  destroy() {
    try {
      // Clear all toast timeouts
      for (const timeoutId of this.toastTimeouts.values()) {
        clearTimeout(timeoutId);
      }
      this.toastTimeouts.clear();
      
      // Remove event listeners from external controls
      if (this.options.externalSearchId) {
        const searchInput = document.getElementById(this.options.externalSearchId);
        if (searchInput) {
          const newSearchInput = searchInput.cloneNode(true);
          searchInput.parentNode.replaceChild(newSearchInput, searchInput);
        }
      }
      
      if (this.options.externalPerPageId) {
        const perPageSelect = document.getElementById(this.options.externalPerPageId);
        if (perPageSelect) {
          const newPerPageSelect = perPageSelect.cloneNode(true);
          perPageSelect.parentNode.replaceChild(newPerPageSelect, perPageSelect);
        }
      }
      
      // Remove event listeners
      if (this.tableElement) {
        $(this.tableElement).off('click', '.action-buttons button');
      }
      
      // Destroy DataTable instance
      if (this.dataTable) {
        this.dataTable.destroy();
        this.dataTable = null;
      }
      
      // Clear data
      this.data = [];
    } catch (error) {
      console.error('Error during destroy:', error);
    }
  }
}