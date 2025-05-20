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
    this.tableId = tableId;
    this.dataTable = null;
    this.data = [];
    
    // Default options
    this.options = {
      columns: [],
      ajaxUrl: '',
      viewRowCallback: null,
      editRowCallback: null,
      deleteRowCallback: null,
      customButtons: {},
      toastOptions: {
        position: 'bottom-right',     // toast position: top-right, top-left, bottom-right, bottom-left
        autoClose: 4000,              // auto close after 5 seconds
        hideProgressBar: false,       // show progress bar
        closeOnClick: true,           // close when clicked
        pauseOnHover: true,           // pause countdown on hover
        draggable: true,              // allow dragging
        enableIcons: true,            // show icons
      },
      ...options
    };
    
    // Initialize toast container if it doesn't exist
    this._initializeToastContainer();
    
    // Initialize table
    this.initialize();
  }
  
  /**
   * Initialize the Toast Container
   * @private
   */
  _initializeToastContainer() {
    // Check if toast container already exists
    if ($('#toastContainer').length === 0) {
      // Toast container styles
      const toastContainerStyles = `
        <style>
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
        </style>
      `;
      
      // Add styles to document head
      $('head').append(toastContainerStyles);
      
      // Create toast container
      const position = this.options.toastOptions.position || 'top-right';
      $('body').append(`<div id="toastContainer" class="${position}"></div>`);
    }
  }
  
  /**
   * Show a toast notification
   * @param {string} type - Toast type (success, error, warning, info)
   * @param {string} title - Toast title
   * @param {string} message - Toast message
   * @param {Object} options - Additional options to override defaults
   * @returns {Object} Toast element
   */
  showToast(type, title, message, options = {}) {
    const toastOptions = { ...this.options.toastOptions, ...options };
    const toastId = `toast-${Date.now()}`;
    
    // Get the icon based on type
    let icon = '';
    switch (type) {
      case 'success':
        icon = '<i class="fas fa-check-circle"></i>';
        if (!toastOptions.enableIcons) icon = '✓';
        break;
      case 'error':
        icon = '<i class="fas fa-times-circle"></i>';
        if (!toastOptions.enableIcons) icon = '✗';
        break;
      case 'warning':
        icon = '<i class="fas fa-exclamation-triangle"></i>';
        if (!toastOptions.enableIcons) icon = '⚠';
        break;
      case 'info':
        icon = '<i class="fas fa-info-circle"></i>';
        if (!toastOptions.enableIcons) icon = 'ℹ';
        break;
      default:
        icon = '<i class="fas fa-bell"></i>';
        if (!toastOptions.enableIcons) icon = '🔔';
    }
    
    // Create toast HTML
    const toastHtml = `
      <div id="${toastId}" class="toast toast-${type}">
        <div class="toast-icon">${icon}</div>
        <div class="toast-content">
          <div class="toast-title">${title}</div>
          <div class="toast-message">${message}</div>
        </div>
        <button class="toast-close">&times;</button>
        ${!toastOptions.hideProgressBar ? '<div class="toast-progress"><div class="toast-progress-bar"></div></div>' : ''}
      </div>
    `;
    
    // Append toast to container
    $('#toastContainer').append(toastHtml);
    const $toast = $(`#${toastId}`);
    
    // Show toast with animation
    setTimeout(() => {
      $toast.addClass('show');
      
      // Set progress bar animation if enabled
      if (!toastOptions.hideProgressBar) {
        $toast.find('.toast-progress-bar').css({
          'width': '0%',
          'transition': `width ${toastOptions.autoClose}ms linear`
        });
      }
      
      // Auto close if enabled
      if (toastOptions.autoClose) {
        setTimeout(() => {
          this._closeToast($toast);
        }, toastOptions.autoClose);
      }
    }, 10);
    
    // Attach close event
    $toast.find('.toast-close').on('click', () => {
      this._closeToast($toast);
    });
    
    // Close on click if enabled
    if (toastOptions.closeOnClick) {
      $toast.on('click', function(e) {
        if ($(e.target).hasClass('toast-close')) return;
        this._closeToast($toast);
      }.bind(this));
    }
    
    // Pause on hover if enabled
    if (toastOptions.pauseOnHover && toastOptions.autoClose) {
      let remainingTime = toastOptions.autoClose;
      let startTime;
      let timeoutId;
      
      $toast.on('mouseenter', function() {
        clearTimeout(timeoutId);
        remainingTime -= (Date.now() - startTime);
        
        // Pause progress bar animation
        if (!toastOptions.hideProgressBar) {
          const $progressBar = $toast.find('.toast-progress-bar');
          const currentWidth = $progressBar.width() / $progressBar.parent().width() * 100;
          $progressBar.css({
            'width': `${currentWidth}%`,
            'transition': 'none'
          });
        }
      });
      
      $toast.on('mouseleave', function() {
        startTime = Date.now();
        
        // Resume progress bar animation
        if (!toastOptions.hideProgressBar) {
          const $progressBar = $toast.find('.toast-progress-bar');
          $progressBar.css({
            'width': '0%',
            'transition': `width ${remainingTime}ms linear`
          });
        }
        
        timeoutId = setTimeout(() => {
          this._closeToast($toast);
        }, remainingTime);
      }.bind(this));
      
      startTime = Date.now();
    }
    
    return $toast;
  }
  
  /**
   * Show a success toast
   * @param {string} title - Toast title
   * @param {string} message - Toast message
   * @param {Object} options - Additional options
   * @returns {Object} Toast element
   */
  showSuccessToast(title, message, options = {}) {
    return this.showToast('success', title, message, options);
  }
  
  /**
   * Show an error toast
   * @param {string} title - Toast title
   * @param {string} message - Toast message
   * @param {Object} options - Additional options
   * @returns {Object} Toast element
   */
  showErrorToast(title, message, options = {}) {
    return this.showToast('error', title, message, options);
  }
  
  /**
   * Show a warning toast
   * @param {string} title - Toast title
   * @param {string} message - Toast message
   * @param {Object} options - Additional options
   * @returns {Object} Toast element
   */
  showWarningToast(title, message, options = {}) {
    return this.showToast('warning', title, message, options);
  }
  
  /**
   * Show an info toast
   * @param {string} title - Toast title
   * @param {string} message - Toast message
   * @param {Object} options - Additional options
   * @returns {Object} Toast element
   */
  showInfoToast(title, message, options = {}) {
    return this.showToast('info', title, message, options);
  }
  
  /**
   * Close a toast
   * @param {Object} $toast - Toast jQuery element
   * @private
   */
  _closeToast($toast) {
    $toast.removeClass('show');
    setTimeout(() => {
      $toast.remove();
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
    const defaultConfig = {
      type: 'primary',      // Bootstrap color: primary, secondary, success, danger, warning, info, light, dark
      pill: false,          // Whether to use pill style
      size: '',             // Size: '', 'sm', 'lg'
      prefix: '',           // Text to display before the value
      suffix: '',           // Text to display after the value
      customClass: '',      // Additional CSS classes
      valueMap: null        // Map of values to custom display/color
    };
    
    // Merge with provided config
    const config = { ...defaultConfig, ...badgeConfig };
    
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
    
    // Build badge classes
    let badgeClasses = `badge bg-${badgeType}`;
    if (config.pill) badgeClasses += ' rounded-pill';
    if (config.size) badgeClasses += ` badge-${config.size}`;
    if (config.customClass) badgeClasses += ` ${config.customClass}`;
    
    // Create badge HTML
    return `<span class="${badgeClasses}">${config.prefix}${displayValue}${config.suffix}</span>`;
  }
  
  /**
   * Initialize the DataTable
   */
  initialize() {
    // Process columns to add badge rendering if configured
    const processedColumns = this.options.columns.map(column => {
      // Check if column has badge configuration
      if (column.badge) {
        // Clone the column configuration to avoid modifying the original
        const newColumn = { ...column };
        
        // Add render function for badge
        newColumn.render = (data, type, row) => {
          // For sorting and filtering, use the raw data
          if (type === 'sort' || type === 'filter') {
            return data;
          }
          
          // For display, generate badge HTML
          return this._generateBadgeHtml(data, column.badge);
        };
        
        return newColumn;
      }
      
      // Return original column if no badge configuration
      return column;
    });
    
    // Prepare columns with action buttons
    const columns = [...processedColumns];
    
    // Add action column if any callback is provided
    if (this.options.viewRowCallback || this.options.editRowCallback || this.options.deleteRowCallback) {
      columns.push({
        data: null,
        title: 'Actions',
        orderable: false,
        className: 'actions-column',
        render: (data, type, row) => {
          let actionsHtml = '<div class="action-buttons">';
          
          if (this.options.viewRowCallback) {
            actionsHtml += `<button class="btn btn-info btn-sm view-btn" data-id="${row.id}">View</button> `;
          }
          
          if (this.options.editRowCallback) {
            actionsHtml += `<button class="btn btn-warning btn-sm edit-btn" data-id="${row.id}">Edit</button> `;
          }
          
          if (this.options.deleteRowCallback) {
            actionsHtml += `<button class="btn btn-danger btn-sm delete-btn" data-id="${row.id}">Delete</button>`;
          }
          
          actionsHtml += '</div>';
          return actionsHtml;
        }
      });
    }
    
    // Initialize DataTable
    this.dataTable = $(`#${this.tableId}`).DataTable({
      columns: columns,
      responsive: true,
      processing: true,
      dom: 'Bfrtip',
      buttons: [
        'copy', 'csv', 'excel', 'pdf', 'print',
        ...(this.options.customButtons ? Object.values(this.options.customButtons) : [])
      ],
      ajax: {
        url: this.options.ajaxUrl,
        dataSrc: (json) => {
          this.data = json.data || json;
          return this.data;
        }
      },
      language: {
        searchPlaceholder: "Search records",
        emptyTable: "No data available"
      }
    });
    
    // Attach event listeners
    this._attachEventListeners();
  }
  
  /**
   * Attach event listeners for action buttons
   * @private
   */
  _attachEventListeners() {
    const table = $(`#${this.tableId}`);
    
    // View button click handler
    if (this.options.viewRowCallback) {
      table.on('click', '.view-btn', (e) => {
        const id = $(e.currentTarget).data('id');
        const rowData = this._findRowById(id);
        this.options.viewRowCallback(rowData, this);
        
        // Show info toast
        this.showInfoToast('View Record', `Viewing record #${id}`);
      });
    }
    
    // Edit button click handler
    if (this.options.editRowCallback) {
      table.on('click', '.edit-btn', (e) => {
        const id = $(e.currentTarget).data('id');
        const rowData = this._findRowById(id);
        this.options.editRowCallback(rowData, this);
        
        // Show warning toast
        this.showWarningToast('Edit Record', `Editing record #${id}`);
      });
    }
    
    // Delete button click handler with confirmation modal
    if (this.options.deleteRowCallback) {
      table.on('click', '.delete-btn', (e) => {
        const id = $(e.currentTarget).data('id');
        const rowData = this._findRowById(id);
        
        // Show confirmation modal
        this._showDeleteConfirmationModal(rowData);
      });
    }
  }
  
  /**
   * Find a row by its ID
   * @param {number|string} id - Row ID
   * @returns {Object|null} Row data or null if not found
   * @private
   */
  _findRowById(id) {
    return this.data.find(row => row.id == id) || null;
  }
  
  /**
   * Show delete confirmation modal
   * @param {Object} rowData - Row data
   * @private
   */
  _showDeleteConfirmationModal(rowData) {
    // Create modal if it doesn't exist
    let modalId = 'deleteConfirmationModal';
    let modal = $(`#${modalId}`);
    
    if (modal.length === 0) {
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
      
      $('body').append(modalHtml);
      modal = $(`#${modalId}`);
    }
    
    // Attach delete confirmation handler
    $('#confirmDeleteBtn').off('click').on('click', () => {
      this.options.deleteRowCallback(rowData, this);
      modal.modal('hide');
      
      // Show error toast for delete (since it's a destructive action)
      this.showErrorToast('Delete Record', `Record #${rowData.id} has been deleted`);
    });
    
    // Show modal
    modal.modal('show');
  }
  
  /**
   * Refresh the DataTable with new data
   * @param {Array} [newData] - New data to use (optional)
   * @returns {DataTablesManager} this instance for chaining
   */
  refresh(newData = null) {
    if (newData) {
      this.data = newData;
      this.dataTable.clear().rows.add(newData).draw();
    } else {
      this.dataTable.ajax.reload();
    }
    
    
    return this;
  }
  
  /**
   * Add a new row to the DataTable
   * @param {Object} rowData - Row data
   * @returns {DataTablesManager} this instance for chaining
   */
  addRow(rowData) {
    this.data.push(rowData);
    this.dataTable.row.add(rowData).draw();
    
    // Show success toast
    this.showSuccessToast('Add Record', `New record #${rowData.id} has been added`);
    
    return this;
  }
  
  /**
   * Update a row in the DataTable
   * @param {number|string} id - Row ID
   * @param {Object} newData - New row data
   * @returns {DataTablesManager} this instance for chaining
   */
  updateRow(id, newData) {
    // Find the row index
    const rowIndex = this.data.findIndex(row => row.id == id);
    
    if (rowIndex !== -1) {
      // Update the data array
      this.data[rowIndex] = { ...this.data[rowIndex], ...newData };
      
      // Update the DataTable row
      const row = this.dataTable.row(function(idx, data) {
        return data.id == id;
      });
      
      if (row.length) {
        row.data(this.data[rowIndex]).draw();
        
        // Show success toast
        this.showSuccessToast('Update Record', `Record #${id} has been updated`);
      }
    } else {
      // Show error toast if record not found
      this.showErrorToast('Update Error', `Record #${id} not found`);
    }
    
    return this;
  }
  
  /**
   * Delete a row from the DataTable
   * @param {number|string} id - Row ID
   * @returns {DataTablesManager} this instance for chaining
   */
  deleteRow(id) {
    // Find the row index
    const rowIndex = this.data.findIndex(row => row.id == id);
    
    if (rowIndex !== -1) {
      // Remove from the data array
      this.data.splice(rowIndex, 1);
      
      // Remove from the DataTable
      const row = this.dataTable.row(function(idx, data) {
        return data.id == id;
      });
      
      if (row.length) {
        row.remove().draw();
        
        // Show error toast (for destructive action)
        this.showErrorToast('Delete Record', `Record #${id} has been deleted`);
      }
    } else {
      // Show error toast if record not found
      this.showErrorToast('Delete Error', `Record #${id} not found`);
    }
    
    return this;
  }
  
  /**
   * Apply filters to the DataTable
   * @param {Object} filters - Filter criteria
   * @returns {DataTablesManager} this instance for chaining
   */
  applyFilters(filters) {
    // Clear existing custom filters
    $.fn.dataTable.ext.search.pop();
    
    // Add custom filter function
    if (Object.keys(filters).length > 0) {
      $.fn.dataTable.ext.search.push((settings, data, dataIndex, rowData) => {
        // Check all filter criteria
        for (const [key, value] of Object.entries(filters)) {
          if (rowData[key] !== value) {
            return false;
          }
        }
        return true;
      });
      
      // Show info toast
      this.showInfoToast('Filters Applied', 'Table data has been filtered');
    } else {
      // Show info toast for filter removal
      this.showInfoToast('Filters Removed', 'All filters have been cleared');
    }
    
    // Redraw the table
    this.dataTable.draw();
    return this;
  }
  
  /**
   * Get the currently selected rows
   * @returns {Array} Selected row data
   */
  getSelectedRows() {
    return this.dataTable.rows({ selected: true }).data().toArray();
  }
}