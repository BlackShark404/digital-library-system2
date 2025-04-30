/**
 * DataTablesManager Class with Customizable Action Buttons
 * A class to handle DataTables initialization with advanced features:
 * - Search
 * - Filters
 * - Pagination
 * - Custom modals (view, edit, delete with confirmation)
 * - Client-side rendering
 * - Table refresh after add/edit/delete operations
 * - Toast notifications (success, error, warning, info)
 * - Bootstrap badges on column data
 * - Highly customizable action buttons (buttons, icons, dropdowns, etc.)
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
   * @param {Object} options.actionButtons - Custom action buttons configuration
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
      // Default action buttons configuration
      actionButtons: {
        // Column configuration
        column: {
          title: 'Actions',
          width: '',
          class: 'actions-column',
          style: '',
        },
        // Container configuration
        container: {
          tag: 'div',
          class: 'action-buttons',
          style: '',
        },
        // Default action button configurations
        buttons: {
          view: {
            enabled: true,
            type: 'button',      // button, icon, link, dropdown, custom
            text: 'View',        // Button text or title attribute for icons
            class: 'btn btn-info btn-sm',
            icon: '',            // Icon class (e.g., 'fas fa-eye') or leave empty for no icon
            attributes: {},      // Additional HTML attributes as key-value pairs
            tooltip: '',         // Tooltip text (if empty, text will be used)
            position: 0,         // Position in the actions container (0 = first)
            template: '',        // Custom HTML template (for custom type)
            modalId: '',         // ID of modal to trigger (optional)
          },
          edit: {
            enabled: true,
            type: 'button',
            text: 'Edit',
            class: 'btn btn-warning btn-sm',
            icon: '',
            attributes: {},
            tooltip: '',
            position: 1,
            template: '',
            modalId: '',
          },
          delete: {
            enabled: true,
            type: 'button',
            text: 'Delete',
            class: 'btn btn-danger btn-sm',
            icon: '',
            attributes: {},
            tooltip: '',
            position: 2,
            template: '',
            modalId: 'deleteConfirmationModal',
          },
          // Add more custom actions as needed
        },
      },
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
    
    // Merge provided action buttons with defaults
    if (options.actionButtons) {
      // Deep merge column and container settings
      if (options.actionButtons.column) {
        this.options.actionButtons.column = {
          ...this.options.actionButtons.column,
          ...options.actionButtons.column
        };
      }
      
      if (options.actionButtons.container) {
        this.options.actionButtons.container = {
          ...this.options.actionButtons.container,
          ...options.actionButtons.container
        };
      }
      
      // Merge button configurations
      if (options.actionButtons.buttons) {
        for (const [buttonKey, buttonConfig] of Object.entries(options.actionButtons.buttons)) {
          if (this.options.actionButtons.buttons[buttonKey]) {
            // Update existing button configuration
            this.options.actionButtons.buttons[buttonKey] = {
              ...this.options.actionButtons.buttons[buttonKey],
              ...buttonConfig
            };
          } else {
            // Add new button configuration
            this.options.actionButtons.buttons[buttonKey] = buttonConfig;
          }
        }
      }
    }
    
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
   * Generate action button HTML based on button config
   * @param {Object} buttonConfig - Button configuration
   * @param {Object} row - Row data
   * @returns {string} HTML for the button
   * @private
   */
  _generateActionButtonHtml(buttonConfig, row) {
    if (!buttonConfig || !buttonConfig.enabled) {
      return '';
    }
    
    // Generate data attributes from attributes object
    let dataAttributes = '';
    if (buttonConfig.attributes) {
      for (const [key, value] of Object.entries(buttonConfig.attributes)) {
        dataAttributes += ` ${key}="${value}"`;
      }
    }
    
    // Add data-id attribute
    dataAttributes += ` data-id="${row.id}"`;
    
    // Add modal trigger if specified
    let modalTrigger = '';
    if (buttonConfig.modalId) {
      modalTrigger = ` data-toggle="modal" data-target="#${buttonConfig.modalId}"`;
    }
    
    // Generate the button based on type
    switch (buttonConfig.type) {
      case 'button':
        // Standard button
        return `<button class="${buttonConfig.class}" ${dataAttributes}${modalTrigger} 
                ${buttonConfig.tooltip ? `title="${buttonConfig.tooltip}"` : ''}>
                ${buttonConfig.icon ? `<i class="${buttonConfig.icon}"></i> ` : ''}
                ${buttonConfig.text}
                </button>`;
      
      case 'icon':
        // Icon only button
        return `<button class="${buttonConfig.class}" ${dataAttributes}${modalTrigger} 
                ${buttonConfig.tooltip || buttonConfig.text ? `title="${buttonConfig.tooltip || buttonConfig.text}"` : ''}>
                <i class="${buttonConfig.icon || 'fas fa-ellipsis-h'}"></i>
                </button>`;
      
      case 'link':
        // Link style button
        return `<a href="javascript:void(0)" class="${buttonConfig.class}" ${dataAttributes}${modalTrigger} 
                ${buttonConfig.tooltip ? `title="${buttonConfig.tooltip}"` : ''}>
                ${buttonConfig.icon ? `<i class="${buttonConfig.icon}"></i> ` : ''}
                ${buttonConfig.text}
                </a>`;
      
      case 'dropdown':
        // Dropdown item - should be used within a dropdown container
        return `<a class="dropdown-item ${buttonConfig.class}" href="javascript:void(0)" ${dataAttributes}${modalTrigger}>
                ${buttonConfig.icon ? `<i class="${buttonConfig.icon}"></i> ` : ''}
                ${buttonConfig.text}
                </a>`;
      
      case 'custom':
        // Custom template with replacements
        if (buttonConfig.template) {
          let template = buttonConfig.template;
          
          // Replace placeholders with actual values
          template = template.replace(/\{id\}/g, row.id);
          template = template.replace(/\{text\}/g, buttonConfig.text);
          template = template.replace(/\{icon\}/g, buttonConfig.icon ? `<i class="${buttonConfig.icon}"></i>` : '');
          
          // Replace other row data placeholders
          for (const [key, value] of Object.entries(row)) {
            template = template.replace(new RegExp(`\\{${key}\\}`, 'g'), value);
          }
          
          return template;
        }
        return '';
      
      default:
        // Default to button if type is not recognized
        return `<button class="${buttonConfig.class}" ${dataAttributes}>
                ${buttonConfig.icon ? `<i class="${buttonConfig.icon}"></i> ` : ''}
                ${buttonConfig.text}
                </button>`;
    }
  }
  
  /**
   * Generate dropdown container for action buttons
   * @param {Array} dropdownItems - HTML for dropdown items
   * @param {Object} dropdownConfig - Dropdown configuration
   * @returns {string} HTML for the dropdown
   * @private
   */
  _generateDropdownHtml(dropdownItems, dropdownConfig = {}) {
    // Default dropdown configuration
    const config = {
      buttonText: 'Actions',
      buttonIcon: 'fas fa-cog',
      buttonClass: 'btn btn-secondary btn-sm dropdown-toggle',
      menuClass: 'dropdown-menu dropdown-menu-right',
      ...dropdownConfig
    };
    
    return `
      <div class="dropdown">
        <button class="${config.buttonClass}" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          ${config.buttonIcon ? `<i class="${config.buttonIcon}"></i> ` : ''}${config.buttonText}
        </button>
        <div class="${config.menuClass}">
          ${dropdownItems.join('')}
        </div>
      </div>
    `;
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
    
    // Get all action buttons that have callbacks
    const hasViewButton = this.options.viewRowCallback && 
                         this.options.actionButtons.buttons.view && 
                         this.options.actionButtons.buttons.view.enabled;
                         
    const hasEditButton = this.options.editRowCallback && 
                         this.options.actionButtons.buttons.edit && 
                         this.options.actionButtons.buttons.edit.enabled;
                         
    const hasDeleteButton = this.options.deleteRowCallback && 
                           this.options.actionButtons.buttons.delete && 
                           this.options.actionButtons.buttons.delete.enabled;
    
    // Get custom action buttons
    const customActionButtons = Object.entries(this.options.actionButtons.buttons)
      .filter(([key]) => !['view', 'edit', 'delete'].includes(key))
      .filter(([_, config]) => config.enabled);
    
    // Add action column if any action button is enabled
    if (hasViewButton || hasEditButton || hasDeleteButton || customActionButtons.length > 0) {
      const actionColumnConfig = this.options.actionButtons.column;
      const actionContainerConfig = this.options.actionButtons.container;
      
      columns.push({
        data: null,
        title: actionColumnConfig.title || 'Actions',
        orderable: false,
        width: actionColumnConfig.width || '',
        className: actionColumnConfig.class || 'actions-column',
        createdCell: function(td, cellData, rowData, row, col) {
          if (actionColumnConfig.style) {
            $(td).attr('style', actionColumnConfig.style);
          }
        },
        render: (data, type, row) => {
          if (type !== 'display') {
            return '';
          }
          
          // Container for action buttons
          const containerTag = actionContainerConfig.tag || 'div';
          const containerClass = actionContainerConfig.class || 'action-buttons';
          const containerStyle = actionContainerConfig.style ? ` style="${actionContainerConfig.style}"` : '';
          
          // Determine if we should use dropdown layout
          const useDropdown = this.options.actionButtons.layout === 'dropdown';
          
          // Get all enabled action buttons sorted by position
          const allActionButtons = [];
          
          // Standard action buttons (view, edit, delete)
          if (hasViewButton) {
            allActionButtons.push({
              config: this.options.actionButtons.buttons.view,
              type: 'view'
            });
          }
          
          if (hasEditButton) {
            allActionButtons.push({
              config: this.options.actionButtons.buttons.edit,
              type: 'edit'
            });
          }
          
          if (hasDeleteButton) {
            allActionButtons.push({
              config: this.options.actionButtons.buttons.delete,
              type: 'delete'
            });
          }
          
          // Add custom action buttons
          customActionButtons.forEach(([key, config]) => {
            allActionButtons.push({
              config: config,
              type: key
            });
          });
          
          // Sort buttons by position
          allActionButtons.sort((a, b) => (a.config.position || 0) - (b.config.position || 0));
          
          if (useDropdown) {
            // Create dropdown items
            const dropdownItems = allActionButtons.map(button => {
              // Force dropdown type for items
              const dropdownConfig = { ...button.config, type: 'dropdown' };
              return this._generateActionButtonHtml(dropdownConfig, row);
            });
            
            // Create dropdown container
            return this._generateDropdownHtml(
              dropdownItems, 
              this.options.actionButtons.dropdown || {}
            );
          } else {
            // Regular action buttons layout
            const actionButtonsHtml = allActionButtons.map(button => 
              this._generateActionButtonHtml(button.config, row)
            ).join(' ');
            
            return `<${containerTag} class="${containerClass}"${containerStyle}>${actionButtonsHtml}</${containerTag}>`;
          }
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
    
    // Initialize tooltips if Bootstrap 4/5 is available
    if (typeof $.fn.tooltip === 'function') {
      $(`#${this.tableId}`).tooltip({
        selector: '[title]',
        container: 'body'
      });
    }
  }
  
  /**
   * Attach event listeners for action buttons
   * @private
   */
  _attachEventListeners() {
    const table = $(`#${this.tableId}`);
    
    // View button click handler
    if (this.options.viewRowCallback) {
      const viewConfig = this.options.actionButtons.buttons.view;
      table.on('click', `.view-btn, [data-action="view"], .${viewConfig.class.split(' ').join('.')}`, (e) => {
        const id = $(e.currentTarget).data('id');
        const rowData = this._findRowById(id);
        this.options.viewRowCallback(rowData, this);
        
        // Show info toast
        this.showInfoToast('View Record', `Viewing record #${id}`);
      });
    }
    
    // Edit button click handler
    if (this.options.editRowCallback) {
      const editConfig = this.options.actionButtons.buttons.edit;
      table.on('click', `.edit-btn, [data-action="edit"], .${editConfig.class.split(' ').join('.')}`, (e) => {
        const id = $(e.currentTarget).data('id');
        const rowData = this._findRowById(id);
        this.options.editRowCallback(rowData, this);
        
        // Show warning toast
        this.showWarningToast('Edit Record', `Editing record #${id}`);
      });
    }
    
    // Delete button click handler with confirmation modal
    if (this.options.deleteRowCallback) {
      const deleteConfig = this.options.actionButtons.buttons.delete;
      const modalSelector = deleteConfig.modalId || 'deleteConfirmationModal';
      
      table.on('click', `.delete-btn, [data-action="delete"], .${deleteConfig.class.split(' ').join('.')}`, (e) => {
        // Only handle click if not a modal trigger
        if (!deleteConfig.modalId) {
          const id = $(e.currentTarget).data('id');
          const rowData = this._findRowById(id);
          
          // Show confirmation modal
          this._showDeleteConfirmationModal(rowData, modalSelector);
        }
      });
    }
    
    // Custom action buttons
    const customActionButtons = Object.entries(this.options.actionButtons.buttons)
      .filter(([key]) => !['view', 'edit', 'delete'].includes(key));
    
    customActionButtons.forEach(([key, config]) => {
      if (config.callback) {
        table.on('click', `[data-action="${key}"], .${config.class.split(' ').join('.')}`, (e) => {
          const id = $(e.currentTarget).data('id');
          const rowData = this._findRowById(id);
          config.callback(rowData, this);
          
          // Show toast if configured
          if (config.toast) {
            const toastType = config.toast.type || 'info';
            const toastTitle = config.toast.title || `${key.charAt(0).toUpperCase() + key.slice(1)} Action`;
            const toastMessage = config.toast.message || `Action performed on record #${id}`;
            this.showToast(toastType, toastTitle, toastMessage, config.toast.options || {});
          }
        });
      }
    });
  }
}