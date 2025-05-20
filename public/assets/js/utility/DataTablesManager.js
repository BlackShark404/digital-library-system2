/**
 * DataTablesManager Class with Badge Support
 * A class to handle DataTables initialization with advanced features:
 * - Search
 * - Filters
 * - Pagination
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
   */
  constructor(tableId, options = {}) {
    // Validate tableId
    if (!tableId || typeof tableId !== 'string') {
      throw new Error('DataTablesManager: tableId is required and must be a string');
    }

    this.tableId = tableId;
    this.tableElement = document.getElementById(tableId);

    // Check if table element exists
    if (!this.tableElement) {
      throw new Error(`DataTablesManager: Table element with ID "${tableId}" not found in DOM`);
    }

    // Check if DataTables library exists
    if (typeof $.fn.DataTable !== 'function') {
      throw new Error('DataTablesManager: DataTables library not loaded. Please include DataTables before initializing');
    }

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
      ...options
    };

    // Validate columns if provided
    if (this.options.columns && !Array.isArray(this.options.columns)) {
      throw new Error('DataTablesManager: options.columns must be an array');
    }

    // Validate AJAX URL if provided
    if (this.options.ajaxUrl && typeof this.options.ajaxUrl !== 'string') {
      throw new Error('DataTablesManager: options.ajaxUrl must be a string');
    }

    // Validate callbacks if provided
    const callbackProps = ['viewRowCallback', 'editRowCallback', 'deleteRowCallback'];
    callbackProps.forEach(prop => {
      if (this.options[prop] !== null && typeof this.options[prop] !== 'function') {
        throw new Error(`DataTablesManager: options.${prop} must be a function or null`);
      }
    });

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
   * Show a toast notification
   * @param {string} type - Toast type (success, error, warning, info)
   * @param {string} title - Toast title
   * @param {string} message - Toast message
   * @param {Object} options - Additional options to override defaults
   * @returns {HTMLElement|null} Toast element or null if failed
   */
  showToast(type, title, message, options = {}) {
    try {
      // Validate parameters
      if (!type || typeof type !== 'string') {
        console.error('Toast type must be a string');
        type = 'default';
      }

      // Sanitize inputs to prevent XSS
      title = this._sanitizeHtml(title || 'Notification');
      message = this._sanitizeHtml(message || '');

      // Valid toast types
      const validTypes = ['success', 'error', 'warning', 'info', 'default'];
      if (!validTypes.includes(type)) {
        console.warn(`Invalid toast type: ${type}, falling back to default`);
        type = 'default';
      }

      const toastOptions = { ...this.options.toastOptions, ...options };
      const toastId = `toast-${Date.now()}`;
      const toastContainer = document.getElementById('toastContainer');

      // Check if container exists, create if not
      if (!toastContainer) {
        console.warn('Toast container not found, attempting to recreate');
        this._initializeToastContainer();
        const newContainer = document.getElementById('toastContainer');
        if (!newContainer) {
          throw new Error('Failed to create toast container');
        }
      }

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
      document.getElementById('toastContainer').appendChild(toast);

      // Event delegation for toast interaction
      this._setupToastEvents(toast, toastOptions);

      // Show toast with animation
      setTimeout(() => {
        toast.classList.add('show');

        // Set progress bar animation if enabled
        if (!toastOptions.hideProgressBar) {
          const progressBar = toast.querySelector('.toast-progress-bar');
          if (progressBar) {
            progressBar.style.width = '0%';
            progressBar.style.transition = `width ${toastOptions.autoClose}ms linear`;
          }
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
    } catch (error) {
      console.error('Failed to show toast notification:', error);
      // Don't show a toast about a toast error - could lead to infinite loop
      return null;
    }
  }

  /**
   * Sanitize HTML string to prevent XSS
   * @param {string} html - HTML string to sanitize
   * @returns {string} Sanitized HTML
   * @private
   */
  _sanitizeHtml(html) {
    if (typeof html !== 'string') {
      return '';
    }

    // Create a textarea element to escape the HTML
    const textarea = document.createElement('textarea');
    textarea.textContent = html;
    return textarea.innerHTML;
  }

  /**
   * Setup event listeners for toast
   * @param {HTMLElement} toast - Toast element
   * @param {Object} options - Toast options
   * @private
   */
  _setupToastEvents(toast, options) {
    try {
      const toastId = toast.id;
      const closeButton = toast.querySelector('.toast-close');

      // Ensure elements exist
      if (!closeButton) {
        console.warn('Toast close button not found');
        return;
      }

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
            if (progressBar) {
              try {
                const progressBarWidth = progressBar.getBoundingClientRect().width;
                const parentWidth = progressBar.parentElement.getBoundingClientRect().width;
                const currentWidth = (progressBarWidth / parentWidth) * 100;

                progressBar.style.width = `${currentWidth}%`;
                progressBar.style.transition = 'none';
              } catch (error) {
                console.warn('Error pausing progress bar:', error);
              }
            }
          }
        });

        toast.addEventListener('mouseleave', () => {
          startTime = Date.now();

          // Resume progress bar animation
          if (!options.hideProgressBar) {
            const progressBar = toast.querySelector('.toast-progress-bar');
            if (progressBar) {
              progressBar.style.width = '0%';
              progressBar.style.transition = `width ${remainingTime}ms linear`;
            }
          }

          // Set new timeout with remaining time
          const timeoutId = setTimeout(() => {
            this._closeToast(toast);
          }, remainingTime);
          this.toastTimeouts.set(toastId, timeoutId);
        });

        startTime = Date.now();
      }
    } catch (error) {
      console.error('Error setting up toast events:', error);
    }
  }

  /**
   * Close a toast with animation
   * @param {HTMLElement} toast - Toast element
   * @private
   */
  _closeToast(toast) {
    try {
      if (!toast || !toast.id) {
        console.warn('Invalid toast element provided to _closeToast');
        return;
      }

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
    } catch (error) {
      console.error('Error closing toast:', error);
      // Try force removal as fallback
      if (toast && toast.parentNode) {
        toast.parentNode.removeChild(toast);
      }
    }
  }

  /**
   * Generate Bootstrap badge HTML
   * @param {string|number} value - The value to display in the badge
   * @param {Object} badgeConfig - Badge configuration
   * @returns {string} HTML for the badge
   * @private
   */
  _generateBadgeHtml(value, badgeConfig) {
    try {
      // Handle null/undefined values
      if (value === null || value === undefined) {
        return '';
      }

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

      // Convert value to string for safety
      let displayValue = String(value);
      let badgeType = config.type;

      // Check if we have a value mapping
      if (config.valueMap && config.valueMap[value] !== undefined) {
        const mapping = config.valueMap[value];

        // Handle object mapping (with custom color and display text)
        if (mapping && typeof mapping === 'object') {
          displayValue = mapping.display || displayValue;
          badgeType = mapping.type || badgeType;
        }
        // Handle string mapping (just display text)
        else if (typeof mapping === 'string') {
          displayValue = mapping;
        }
      }

      // Validate badge type
      const validTypes = ['primary', 'secondary', 'success', 'danger', 'warning', 'info', 'light', 'dark'];
      if (!validTypes.includes(badgeType)) {
        console.warn(`Invalid badge type: ${badgeType}, falling back to primary`);
        badgeType = 'primary';
      }

      // Build badge classes with array join for better performance
      const badgeClasses = [
        'badge',
        `bg-${badgeType}`,
        config.pill ? 'rounded-pill' : '',
        config.size ? `badge-${config.size}` : '',
        config.customClass
      ].filter(Boolean).join(' ');

      // Sanitize inputs for XSS prevention
      const safePrefix = this._sanitizeHtml(config.prefix || '');
      const safeSuffix = this._sanitizeHtml(config.suffix || '');
      const safeDisplayValue = this._sanitizeHtml(displayValue);

      // Create badge HTML
      return `<span class="${badgeClasses}">${safePrefix}${safeDisplayValue}${safeSuffix}</span>`;
    } catch (error) {
      console.error('Error generating badge HTML:', error);
      // Return raw value as fallback
      return String(value);
    }
  }

  /**
   * Initialize the DataTable
   */
  initialize() {
    try {
      // Process columns to add badge rendering if configured
      if (!this.options.columns || !Array.isArray(this.options.columns) || this.options.columns.length === 0) {
        throw new Error('No columns defined for DataTable initialization');
      }

      const processedColumns = this.options.columns.map(column => {
        // Validate column object
        if (!column || typeof column !== 'object') {
          throw new Error(`Invalid column configuration: ${JSON.stringify(column)}`);
        }

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

              // Safely handle null/undefined data
              if (data === null || data === undefined) {
                return '';
              }

              // For display, generate badge HTML
              try {
                return this._generateBadgeHtml(data, column.badge);
              } catch (error) {
                console.error(`Badge rendering error for column '${column.data}':`, error);
                return String(data); // Fallback to string representation
              }
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
            try {
              // Verify row has an id property
              if (!row || row.id === undefined) {
                console.warn('Row missing ID, using fallback for action buttons');
                return '<div class="action-buttons"><span class="text-danger">Invalid row data</span></div>';
              }

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
            } catch (error) {
              console.error('Error rendering action buttons:', error);
              return '<div class="action-buttons"><span class="text-danger">Error</span></div>';
            }
          }
        });
      }

      // Prepare DataTable options with error handling
      const dataTableOptions = {
        columns,
        responsive: true,
        processing: true,
        dom: 'Bfrtip',
        buttons: [
          'copy', 'csv', 'excel', 'pdf', 'print',
          ...(this.options.customButtons && typeof this.options.customButtons === 'object'
            ? Object.values(this.options.customButtons)
            : [])
        ],
        language: {
          searchPlaceholder: "Search records",
          emptyTable: "No data available",
          error: "An error occurred while loading data",
          zeroRecords: "No matching records found"
        }
      };

      // Add AJAX options if ajaxUrl is provided
      if (this.options.ajaxUrl && this.options.ajaxUrl.trim() !== '') {
        dataTableOptions.ajax = {
          url: this.options.ajaxUrl,
          dataSrc: (json) => {
            // Validate response data
            if (!json) {
              this.showErrorToast('Data Error', 'Received empty response from server');
              return [];
            }

            // Handle both { data: [...] } format and direct array format
            this.data = Array.isArray(json) ? json : (json.data || []);

            // Validate that data is actually an array
            if (!Array.isArray(this.data)) {
              this.showErrorToast('Data Format Error', 'Received invalid data format from server');
              return [];
            }

            return this.data;
          },
          error: (xhr, error, thrown) => {
            const statusText = xhr ? xhr.statusText : 'Unknown error';
            const status = xhr ? xhr.status : 'Unknown status';
            this.showErrorToast('Data Loading Error', `Failed to load table data (${status}: ${statusText})`);
            console.error('DataTables AJAX error:', error, thrown, xhr);
            return [];
          }
        };
      } else {
        // If no AJAX URL, ensure we have data property
        dataTableOptions.data = this.data;
      }

      // Initialize DataTable with jQuery (since DataTables is jQuery-based)
      try {
        this.dataTable = $(`#${this.tableId}`).DataTable(dataTableOptions);
      } catch (dtError) {
        throw new Error(`DataTable initialization failed: ${dtError.message}`);
      }

      // Attach event listeners with delegation for better performance
      this._attachEventListeners();

      return true;
    } catch (error) {
      console.error('Error initializing DataTable:', error);
      this.showErrorToast('Initialization Error', 'Failed to initialize table: ' + error.message);

      // Create fallback simple table UI to show error
      try {
        if (this.tableElement) {
          const errorMsg = document.createElement('div');
          errorMsg.className = 'alert alert-danger mt-3';
          errorMsg.innerHTML = `<strong>Table Initialization Error:</strong> ${error.message}`;
          this.tableElement.parentNode.insertBefore(errorMsg, this.tableElement.nextSibling);
        }
      } catch (uiError) {
        console.error('Failed to create error UI:', uiError);
      }

      return false;
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
    if (!id) {
      console.warn('_findRowById called with empty ID');
      return null;
    }

    try {
      // Check if data is valid
      if (!Array.isArray(this.data)) {
        console.error('Invalid data array in _findRowById');
        return null;
      }

      // Convert ID to string for consistent comparison
      const strId = String(id);

      // Find the row with matching ID
      const row = this.data.find(row => {
        if (!row) return false;
        return String(row.id) === strId;
      });

      return row || null;
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
      // Validate input
      if (!rowData || !rowData.id) {
        throw new Error('Invalid row data provided for deletion');
      }

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

        try {
          const modalDiv = document.createElement('div');
          modalDiv.innerHTML = modalHtml;
          document.body.appendChild(modalDiv.firstChild);
          modal = document.getElementById(modalId);

          if (!modal) {
            throw new Error('Failed to create modal element');
          }
        } catch (domError) {
          console.error('Error creating delete confirmation modal:', domError);
          // Fallback to confirm dialog if modal creation fails
          if (confirm(`Are you sure you want to delete record #${rowData.id}?`)) {
            try {
              this.options.deleteRowCallback(rowData, this);
            } catch (callbackError) {
              console.error('Error in delete callback:', callbackError);
              this.showErrorToast('Delete Error', `Error deleting record: ${callbackError.message}`);
            }
          }
          return;
        }
      }

      // Check for jQuery
      if (typeof $ !== 'function' || typeof $.fn.modal !== 'function') {
        console.warn('Bootstrap modal functionality not available, falling back to confirm dialog');
        if (confirm(`Are you sure you want to delete record #${rowData.id}?`)) {
          try {
            this.options.deleteRowCallback(rowData, this);
          } catch (callbackError) {
            console.error('Error in delete callback:', callbackError);
            this.showErrorToast('Delete Error', `Error deleting record: ${callbackError.message}`);
          }
        }
        return;
      }

      // Using jQuery for Bootstrap modal
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
          $modal.modal('hide');
        }
      });

      // Show modal with error handling
      try {
        $modal.modal('show');
      } catch (modalError) {
        console.error('Error showing modal:', modalError);
        // Fallback to confirm
        if (confirm(`Are you sure you want to delete record #${rowData.id}?`)) {
          try {
            this.options.deleteRowCallback(rowData, this);
          } catch (callbackError) {
            this.showErrorToast('Delete Error', `Error deleting record: ${callbackError.message}`);
          }
        }
      }
    } catch (error) {
      console.error('Error showing delete modal:', error);
      this.showErrorToast('UI Error', 'Failed to show delete confirmation dialog.');
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
   * Refresh the DataTable with new data
   * @param {Array} [newData] - New data to use (optional)
   * @returns {DataTablesManager} this instance for chaining
   */
  refresh(newData = null) {
    try {
      // Validate data if provided
      if (newData !== null) {
        if (!Array.isArray(newData)) {
          throw new Error('newData must be an array');
        }
        this.data = newData;

        try {
          this.dataTable.clear().rows.add(newData).draw();
        } catch (dtError) {
          console.error('Error during DataTable refresh with new data:', dtError);

          // Fallback approach: destroy and reinitialize
          try {
            this.dataTable.destroy();
            this.initialize();
            this.showWarningToast('Table Refresh', 'Table was refreshed using fallback method');
          } catch (fallbackError) {
            throw new Error(`Failed to refresh table: ${fallbackError.message}`);
          }
        }
      } else {
        // AJAX reload
        try {
          const ajaxPromise = new Promise((resolve, reject) => {
            this.dataTable.ajax.reload(function (json) {
              resolve(json);
            }, true); // true = reset paging
          });

          // Set a timeout to catch potential hang
          const timeoutPromise = new Promise((_, reject) => {
            setTimeout(() => reject(new Error('AJAX refresh timeout after 30 seconds')), 30000);
          });

          // Race the promises
          Promise.race([ajaxPromise, timeoutPromise])
            .catch(error => {
              console.error('AJAX refresh error:', error);
              this.showWarningToast('Refresh Warning', 'Table refresh encountered an issue');
            });
        } catch (ajaxError) {
          console.error('Error during AJAX refresh:', ajaxError);
          this.showErrorToast('Refresh Error', `Failed to refresh table data: ${ajaxError.message}`);
        }
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

      // Verify rowData has required format and ID
      if (!rowData.id) {
        throw new Error('Row data must contain an ID field');
      }

      // Check for duplicate ID
      if (this.data.some(row => row && row.id == rowData.id)) {
        throw new Error(`Record with ID ${rowData.id} already exists`);
      }

      this.data.push(rowData);

      try {
        this.dataTable.row.add(rowData).draw();
      } catch (dtError) {
        console.error('DataTable error when adding row:', dtError);
        // Try to refresh the entire table as fallback
        this.refresh(this.data);
      }

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
      if (!id) {
        throw new Error('ID is required to update a row');
      }

      if (!newData || typeof newData !== 'object') {
        throw new Error('New data must be provided as an object');
      }

      // Find the row index
      const rowIndex = this.data.findIndex(row => row && row.id == id);

      if (rowIndex !== -1) {
        // Ensure ID is not changed
        if (newData.id && newData.id != id) {
          throw new Error('Changing record ID is not allowed');
        }

        // Preserve ID in the updated data
        newData.id = id;

        // Update the data array
        this.data[rowIndex] = { ...this.data[rowIndex], ...newData };

        // Update the DataTable row
        const row = this.dataTable.row(function (idx, data) {
          return data && data.id == id;
        });

        if (row.length) {
          try {
            row.data(this.data[rowIndex]).draw();
            this.showSuccessToast('Update Record', `Record #${id} has been updated`);
          } catch (dtError) {
            console.error('DataTable error when updating row:', dtError);
            // Try to refresh the entire table as fallback
            this.refresh(this.data);
            this.showWarningToast('Update Record', `Record #${id} updated with fallback method`);
          }
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
      if (!id) {
        throw new Error('ID is required to delete a row');
      }

      // Find the row index
      const rowIndex = this.data.findIndex(row => row && row.id == id);

      if (rowIndex !== -1) {
        // Store a reference to the data for potential recovery
        const deletedRow = this.data[rowIndex];

        // Remove from the data array
        this.data.splice(rowIndex, 1);

        // Remove from the DataTable
        const row = this.dataTable.row(function (idx, data) {
          return data && data.id == id;
        });

        if (row.length) {
          try {
            row.remove().draw();
            this.showSuccessToast('Delete Record', `Record #${id} has been deleted`);
          } catch (dtError) {
            console.error('DataTable error when deleting row:', dtError);
            // Try to re-add the row to data array since delete failed in DataTable
            this.data.splice(rowIndex, 0, deletedRow);
            // Try to refresh the entire table as fallback
            this.refresh(this.data);
            throw new Error(`Failed to delete in DataTable: ${dtError.message}`);
          }
        } else {
          // Refresh the entire table to sync with our data array
          this.refresh(this.data);
          this.showWarningToast('Delete Record', `Record #${id} was deleted using fallback method`);
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

      // Remove event listeners with proper jQuery cleanup
      if (this.tableElement) {
        try {
          $(this.tableElement).off();
        } catch (eventError) {
          console.warn('Error removing event listeners:', eventError);
        }
      }

      // Remove any modal elements created by this instance
      try {
        const modal = document.getElementById('deleteConfirmationModal');
        if (modal && modal.parentNode) {
          modal.parentNode.removeChild(modal);
        }
      } catch (modalError) {
        console.warn('Error removing modal element:', modalError);
      }

      // Destroy DataTable instance with error handling
      if (this.dataTable) {
        try {
          this.dataTable.destroy();
        } catch (dtError) {
          console.error('Error destroying DataTable:', dtError);
          // Try alternative destruction if first method fails
          try {
            $(this.tableElement).DataTable().destroy();
          } catch (fallbackError) {
            console.error('Alternative DataTable destruction also failed:', fallbackError);
          }
        }
        this.dataTable = null;
      }

      // Clear data
      this.data = [];

      // Remove any toast container if this is the last instance
      try {
        const instances = document.querySelectorAll('[id^="dataTable"]').length;
        if (instances <= 1) {
          const toastContainer = document.getElementById('toastContainer');
          if (toastContainer && toastContainer.parentNode) {
            toastContainer.parentNode.removeChild(toastContainer);
          }
        }
      } catch (containerError) {
        console.warn('Error removing toast container:', containerError);
      }

      return true;
    } catch (error) {
      console.error('Error during destroy:', error);
      return false;
    }
  }
}