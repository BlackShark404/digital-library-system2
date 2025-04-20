/**
 * DataTablesHelper.js - A utility script to simplify DataTables implementation
 * for PHP backend applications
 */

class DataTablesHelper {
  /**
   * Initialize a DataTable with server-side processing
   * @param {string} tableId - The ID of the table element
   * @param {string} ajaxUrl - The PHP endpoint for data processing
   * @param {Array} columns - Column definitions
   * @param {Object} options - Additional DataTables options
   * @returns {Object} - The initialized DataTable instance
   */
  static initServerSide(tableId, ajaxUrl, columns, options = {}) {
    // Default options for server-side processing
    const defaultOptions = {
      processing: true,
      serverSide: true,
      ajax: {
        url: ajaxUrl,
        type: 'POST',
        data: function(data) {
          // Add CSRF token if using Laravel or similar framework
          data._token = typeof csrfToken !== 'undefined' ? csrfToken : '';
          return data;
        }
      },
      columns: columns
    };

    // Merge default options with user-provided options
    const tableOptions = { ...defaultOptions, ...options };
    
    // Initialize and return the DataTable
    return $('#' + tableId).DataTable(tableOptions);
  }

  /**
   * Initialize a DataTable with client-side processing
   * @param {string} tableId - The ID of the table element
   * @param {Array} columns - Column definitions (optional)
   * @param {Object} options - Additional DataTables options
   * @returns {Object} - The initialized DataTable instance
   */
  static initClientSide(tableId, columns = [], options = {}) {
    const defaultOptions = {
      processing: true,
      columns: columns
    };

    const tableOptions = { ...defaultOptions, ...options };
    
    return $('#' + tableId).DataTable(tableOptions);
  }
  
  /**
   * Create a custom column renderer for actions (edit, delete, etc.)
   * @param {Array} actions - Array of action objects with properties: name, icon, class, url
   * @returns {Function} - Renderer function for DataTables
   */
  static createActionColumn(actions) {
    return function(data, type, row) {
      let actionButtons = '';
      
      actions.forEach(action => {
        const url = typeof action.url === 'function' ? action.url(row) : action.url + row.id;
        const icon = action.icon || '';
        const btnClass = action.class || 'btn-secondary';
        
        actionButtons += `<button 
          data-id="${row.id}" 
          class="btn btn-sm ${btnClass} ${action.name}-btn" 
          ${action.attributes || ''}>
            ${icon} ${action.name}
          </button> `;
      });
      
      return actionButtons;
    };
  }
  
  /**
   * Add event listeners for action buttons
   * @param {string} tableId - The ID of the table element
   * @param {Object} events - Object mapping action names to handler functions
   */
  static bindActionEvents(tableId, events) {
    const table = $('#' + tableId);
    
    Object.keys(events).forEach(actionName => {
      table.on('click', '.' + actionName + '-btn', function(e) {
        e.preventDefault();
        const id = $(this).data('id');
        const row = $('#' + tableId).DataTable().row($(this).closest('tr'));
        const rowData = row.data();
        
        events[actionName](id, rowData, row);
      });
    });
  }
  
  /**
   * Refresh the DataTable
   * @param {string} tableId - The ID of the table element
   */
  static refreshTable(tableId) {
    $('#' + tableId).DataTable().ajax.reload();
  }

  /**
   * Show a Bootstrap toast notification
   * @param {string} message - The message to display
   * @param {string} type - The notification type ('success', 'error', 'warning', 'info')
   * @param {number} duration - Time in milliseconds before the toast auto-hides
   */
  static showToast(message, type = 'info', duration = 3000) {
    // Define type-specific properties
    const toastTypes = {
      success: {
        icon: '<i class="fas fa-check-circle me-2"></i>',
        bgClass: 'bg-success'
      },
      error: {
        icon: '<i class="fas fa-exclamation-circle me-2"></i>',
        bgClass: 'bg-danger'
      },
      warning: {
        icon: '<i class="fas fa-exclamation-triangle me-2"></i>',
        bgClass: 'bg-warning'
      },
      info: {
        icon: '<i class="fas fa-info-circle me-2"></i>',
        bgClass: 'bg-info'
      }
    };
    
    const toastType = toastTypes[type] || toastTypes.info;
    
    // Create a unique toast ID
    const toastId = 'toast-' + Date.now();
    
    // Create toast HTML
    const toastHtml = `
      <div id="${toastId}" class="toast align-items-center ${toastType.bgClass} text-white border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
          <div class="toast-body">
            ${toastType.icon}${message}
          </div>
          <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
      </div>
    `;
    
    // Check if toast container exists, if not create it
    let toastContainer = $('.toast-container');
    if (toastContainer.length === 0) {
      $('body').append('<div class="toast-container position-fixed bottom-0 end-0 p-3"></div>');
      toastContainer = $('.toast-container');
    }
    
    // Append the toast to container
    toastContainer.append(toastHtml);
    
    // Initialize and show the toast
    const toastElement = new bootstrap.Toast(document.getElementById(toastId), {
      delay: duration,
      autohide: true
    });
    
    toastElement.show();
    
    // Remove toast from DOM after it's hidden
    $(`#${toastId}`).on('hidden.bs.toast', function() {
      $(this).remove();
    });
  }

  /**
   * Handle form submission and update DataTable
   * @param {string} formId - The ID of the form element
   * @param {string} tableId - The ID of the table element
   * @param {string} submitUrl - The PHP endpoint for form submission
   * @param {Function} onSuccess - Callback function on successful submission
   */
  static handleFormSubmit(formId, tableId, submitUrl, onSuccess = null) {
    $('#' + formId).on('submit', function(e) {
      e.preventDefault();
      
      $.ajax({
        url: submitUrl,
        type: 'POST',
        data: $(this).serialize(),
        dataType: 'json',
        success: function(response) {
          if (response.success) {
            // Refresh the DataTable
            DataTablesHelper.refreshTable(tableId);
            
            // Reset the form
            $('#' + formId)[0].reset();
            
            // Close modal if it exists
            $('.modal').modal('hide');
            
            // Show success toast
            DataTablesHelper.showToast(response.message || 'Operation completed successfully', 'success');
            
            // Custom success callback
            if (onSuccess && typeof onSuccess === 'function') {
              onSuccess(response);
            }
          } else {
            // Show error toast
            DataTablesHelper.showToast(response.message || 'An error occurred', 'error');
          }
        },
        error: function(xhr) {
          let errorMessage = 'Server error occurred';
          if (xhr.responseJSON && xhr.responseJSON.message) {
            errorMessage = xhr.responseJSON.message;
          }
          DataTablesHelper.showToast(errorMessage, 'error');
        }
      });
    });
  }
  
  /**
   * Export DataTable data to various formats
   * @param {string} tableId - The ID of the table element
   * @param {string} format - Export format ('csv', 'excel', 'pdf')
   * @param {string} title - Title for the exported file
   */
  static exportData(tableId, format, title) {
    const exportTypes = {
      csv: {
        extend: 'csvHtml5',
        text: 'Export to CSV',
        title: title
      },
      excel: {
        extend: 'excelHtml5',
        text: 'Export to Excel',
        title: title
      },
      pdf: {
        extend: 'pdfHtml5',
        text: 'Export to PDF',
        title: title
      }
    };
    
    if (!exportTypes[format]) {
      DataTablesHelper.showToast(`Invalid export format: ${format}`, 'warning');
      return;
    }
    
    const table = $('#' + tableId).DataTable();
    new $.fn.dataTable.Buttons(table, {
      buttons: [exportTypes[format]]
    });
    
    table.buttons().trigger();
  }
}