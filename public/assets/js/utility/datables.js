// datatables-handler.js
// Utility module for handling data tables with DataTables and Axios

/**
 * Initialize and configure a DataTable with AJAX functionality
 * @param {string} tableId - The ID of the table element
 * @param {string} endpoint - The API endpoint URL for fetching data
 * @param {Array} columns - Column definitions
 * @param {Object} options - Additional options for the DataTable
 */
function initDataTable(tableId, endpoint, columns, options = {}) {
    // Select the table element using jQuery
    const $table = $('#' + tableId);
    
    // Check if table exists
    if ($table.length === 0) {
        console.error(`Table with ID '${tableId}' not found`);
        return;
    }
    
    // Default options
    const defaults = {
        processing: true,
        serverSide: true,
        pageLength: options.perPage || 10,
        order: options.sortField ? [[getColumnIndex(columns, options.sortField), options.sortDirection || 'asc']] : [],
        dom: 'Blfrtip',
        buttons: ['copy', 'excel', 'pdf', 'print'],
        language: {
            search: "Search:",
            emptyTable: "No data available",
            info: "Showing _START_ to _END_ of _TOTAL_ entries",
            paginate: {
                first: "First",
                last: "Last",
                next: "Next",
                previous: "Previous"
            }
        }
    };
    
    // Map columns to DataTables format
    const dataTablesColumns = columns.map(column => {
        const dtColumn = {
            data: column.field,
            name: column.field,
            title: column.title || column.field,
            orderable: column.sortable !== false
        };

        // Add custom render function if provided
        if (column.render && typeof column.render === 'function') {
            dtColumn.render = column.render;
        }
        
        return dtColumn;
    });
    
    // Configure ajax data source
    const ajaxConfig = {
        url: endpoint,
        type: 'GET',
        dataType: 'json',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        },
        data: function(d) {
            // Map DataTables request params to your API format
            const params = {
                page: (d.start / d.length) + 1,
                per_page: d.length,
                search: d.search.value
            };
            
            // Add sort parameters
            if (d.order && d.order.length > 0) {
                params.sort_field = dataTablesColumns[d.order[0].column].data;
                params.sort_direction = d.order[0].dir;
            }
            
            // Add any additional filters from options
            if (options.filters) {
                Object.assign(params, options.filters);
            }
            
            return params;
        },
        dataSrc: function(json) {
            // Process the returned data to match DataTables format
            if (json && json.success) {
                // Call onDataLoaded callback if provided
                if (options.onDataLoaded && typeof options.onDataLoaded === 'function') {
                    options.onDataLoaded(json.data);
                }
                
                // If API returns items array, use that, otherwise use data directly
                return json.data.items || json.data;
            } else {
                console.error('Error loading data:', json.message || 'Unknown error');
                showToast('Error', 'Failed to load table data', 'danger');
                return [];
            }
        }
    };
    
    // Initialize DataTable
    const dataTable = $table.DataTable({
        ...defaults,
        ...options,
        columns: dataTablesColumns,
        ajax: ajaxConfig
    });
    
    // Helper function to find column index by field name
    function getColumnIndex(columns, fieldName) {
        return columns.findIndex(col => col.field === fieldName);
    }
    
    // Return an API for controlling the DataTable
    return {
        reload: () => dataTable.ajax.reload(),
        setPage: (page) => {
            const pageIndex = page - 1;
            dataTable.page(pageIndex).draw(false);
        },
        setPerPage: (perPage) => {
            dataTable.page.len(perPage).draw();
        },
        setSort: (field, direction = 'asc') => {
            const columnIndex = getColumnIndex(columns, field);
            if (columnIndex !== -1) {
                dataTable.order([columnIndex, direction]).draw();
            }
        },
        setFilter: (key, value) => {
            // Store filter in options
            if (!options.filters) options.filters = {};
            options.filters[key] = value;
            dataTable.ajax.reload();
        },
        clearFilters: () => {
            options.filters = {};
            dataTable.search('').columns().search('').draw();
        },
        getState: () => {
            const pageInfo = dataTable.page.info();
            const order = dataTable.order();
            
            return {
                page: pageInfo.page + 1,
                perPage: pageInfo.length,
                sortField: order.length > 0 ? dataTablesColumns[order[0][0]].data : null,
                sortDirection: order.length > 0 ? order[0][1] : 'asc',
                filters: options.filters || {}
            };
        },
        getInstance: () => dataTable // Return the actual DataTables instance
    };
}

/**
 * Helper function to show a toast message
 * This is a placeholder - replace with your actual toast implementation
 */
function showToast(title, message, type) {
    // Example implementation - replace with your actual toast implementation
    console.log(`${type.toUpperCase()} - ${title}: ${message}`);
    
    // If using Bootstrap toast
    if (window.bootstrap && bootstrap.Toast) {
        const toastEl = document.createElement('div');
        toastEl.className = `toast align-items-center text-white bg-${type} border-0`;
        toastEl.setAttribute('role', 'alert');
        toastEl.setAttribute('aria-live', 'assertive');
        toastEl.setAttribute('aria-atomic', 'true');
        
        toastEl.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">
                    <strong>${title}</strong>: ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        `;
        
        document.body.appendChild(toastEl);
        const toast = new bootstrap.Toast(toastEl);
        toast.show();
        
        // Remove from DOM after hiding
        toastEl.addEventListener('hidden.bs.toast', () => {
            toastEl.remove();
        });
    }
}