// data-tables-handler.js
// Utility module for handling data tables with Axios AJAX

/**
 * Initialize and configure a data table with AJAX functionality
 * @param {string} tableId - The ID of the table element
 * @param {string} endpoint - The API endpoint URL for fetching data
 * @param {Object} columns - Column definitions
 * @param {Object} options - Additional options for the data table
 */
function initDataTable(tableId, endpoint, columns, options = {}) {
    // Select the table element
    const table = document.getElementById(tableId);
    
    // Check if table exists
    if (!table) {
        console.error(`Table with ID '${tableId}' not found`);
        return;
    }
    
    // Initialize state for pagination, sorting, and filtering
    const state = {
        page: 1,
        perPage: options.perPage || 10,
        sortField: options.sortField || null,
        sortDirection: options.sortDirection || 'asc',
        filters: options.filters || {}
    };
    
    // Create table elements if they don't exist
    if (!table.querySelector('thead')) {
        createTableHead(table, columns);
    }
    
    if (!table.querySelector('tbody')) {
        const tbody = document.createElement('tbody');
        table.appendChild(tbody);
    }
    
    // Create pagination controls if needed
    let paginationContainer = null;
    if (options.pagination !== false) {
        paginationContainer = document.createElement('div');
        paginationContainer.className = 'datatable-pagination mt-3';
        table.parentNode.insertBefore(paginationContainer, table.nextSibling);
    }
    
    // Create search box if needed
    if (options.searchable !== false) {
        createSearchBox(table, state);
    }
    
    // Function to load data from the server
    function loadData() {
        // Show loading indicator
        const tbody = table.querySelector('tbody');
        tbody.innerHTML = '<tr><td colspan="' + columns.length + '" class="text-center">Loading...</td></tr>';
        
        // Prepare params for the request
        const params = {
            page: state.page,
            per_page: state.perPage,
            sort_field: state.sortField,
            sort_direction: state.sortDirection,
            ...state.filters
        };
        
        // Fetch data with Axios
        axios.get(endpoint, { 
            params: params,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (response.data && response.data.success) {
                const data = response.data.data;
                
                // Render table rows
                renderTableRows(tbody, data.items || data, columns);
                
                // Update pagination if available
                if (paginationContainer && data.pagination) {
                    renderPagination(paginationContainer, data.pagination, state);
                }
                
                // Call onDataLoaded callback if provided
                if (options.onDataLoaded && typeof options.onDataLoaded === 'function') {
                    options.onDataLoaded(data);
                }
            } else {
                throw new Error(response.data.message || 'Error loading data');
            }
        })
        .catch(error => {
            console.error('Error loading table data:', error);
            tbody.innerHTML = '<tr><td colspan="' + 
                columns.length + 
                '" class="text-center text-danger">Failed to load data</td></tr>';
                
            // Show error toast
            showToast('Error', 'Failed to load table data', 'danger');
        });
    }
    
    // Helper function to create table header
    function createTableHead(table, columns) {
        const thead = document.createElement('thead');
        const headerRow = document.createElement('tr');
        
        columns.forEach(column => {
            const th = document.createElement('th');
            th.textContent = column.title || column.field;
            
            // Add sortable functionality if column is sortable
            if (column.sortable !== false) {
                th.className = 'sortable';
                th.setAttribute('data-field', column.field);
                
                // Add sort indicator
                const sortIcon = document.createElement('i');
                sortIcon.className = 'ms-1 fa fa-sort';
                th.appendChild(sortIcon);
                
                // Add click event for sorting
                th.addEventListener('click', () => {
                    // Update sort state
                    if (state.sortField === column.field) {
                        // Toggle direction if same field
                        state.sortDirection = state.sortDirection === 'asc' ? 'desc' : 'asc';
                    } else {
                        // New sort field
                        state.sortField = column.field;
                        state.sortDirection = 'asc';
                    }
                    
                    // Reset to first page when sorting
                    state.page = 1;
                    
                    // Update UI and reload data
                    updateSortIndicators();
                    loadData();
                });
            }
            
            headerRow.appendChild(th);
        });
        
        thead.appendChild(headerRow);
        table.appendChild(thead);
    }
    
    // Helper function to update sort indicators
    function updateSortIndicators() {
        const allHeaders = table.querySelectorAll('th.sortable');
        allHeaders.forEach(th => {
            const icon = th.querySelector('i');
            const field = th.getAttribute('data-field');
            
            if (field === state.sortField) {
                icon.className = state.sortDirection === 'asc' 
                    ? 'ms-1 fa fa-sort-up' 
                    : 'ms-1 fa fa-sort-down';
            } else {
                icon.className = 'ms-1 fa fa-sort';
            }
        });
    }
    
    // Helper function to create search box
    function createSearchBox(table, state) {
        const searchContainer = document.createElement('div');
        searchContainer.className = 'datatable-search mb-3';
        
        const searchInput = document.createElement('input');
        searchInput.type = 'text';
        searchInput.className = 'form-control';
        searchInput.placeholder = 'Search...';
        
        // Debounced search function
        let searchTimeout;
        searchInput.addEventListener('input', (e) => {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                state.filters.search = e.target.value;
                state.page = 1; // Reset to first page when searching
                loadData();
            }, 500);
        });
        
        searchContainer.appendChild(searchInput);
        table.parentNode.insertBefore(searchContainer, table);
    }
    
    // Helper function to render table rows
    function renderTableRows(tbody, items, columns) {
        tbody.innerHTML = '';
        
        if (!items || items.length === 0) {
            const emptyRow = document.createElement('tr');
            const emptyCell = document.createElement('td');
            emptyCell.colSpan = columns.length;
            emptyCell.className = 'text-center';
            emptyCell.textContent = 'No data available';
            emptyRow.appendChild(emptyCell);
            tbody.appendChild(emptyRow);
            return;
        }
        
        items.forEach(item => {
            const row = document.createElement('tr');
            
            // Set row data id if available
            if (item.id) {
                row.setAttribute('data-id', item.id);
            }
            
            columns.forEach(column => {
                const cell = document.createElement('td');
                
                // Handle special render function if provided
                if (column.render && typeof column.render === 'function') {
                    cell.innerHTML = column.render(item[column.field], item);
                } else {
                    cell.textContent = item[column.field] || '';
                }
                
                row.appendChild(cell);
            });
            
            tbody.appendChild(row);
        });
    }
    
    // Helper function to render pagination
    function renderPagination(container, pagination, state) {
        container.innerHTML = '';
        
        const totalPages = Math.ceil(pagination.total / pagination.per_page);
        if (totalPages <= 1) return;
        
        const nav = document.createElement('nav');
        const ul = document.createElement('ul');
        ul.className = 'pagination';
        
        // Previous button
        const prevItem = document.createElement('li');
        prevItem.className = 'page-item' + (state.page <= 1 ? ' disabled' : '');
        const prevLink = document.createElement('a');
        prevLink.className = 'page-link';
        prevLink.href = '#';
        prevLink.textContent = 'Previous';
        prevLink.addEventListener('click', (e) => {
            e.preventDefault();
            if (state.page > 1) {
                state.page--;
                loadData();
            }
        });
        prevItem.appendChild(prevLink);
        ul.appendChild(prevItem);
        
        // Page numbers (with ellipsis for large page counts)
        const visiblePages = getVisiblePageNumbers(state.page, totalPages);
        visiblePages.forEach(pageNum => {
            if (pageNum === '...') {
                // Ellipsis
                const ellipsisItem = document.createElement('li');
                ellipsisItem.className = 'page-item disabled';
                const ellipsisSpan = document.createElement('span');
                ellipsisSpan.className = 'page-link';
                ellipsisSpan.textContent = '...';
                ellipsisItem.appendChild(ellipsisSpan);
                ul.appendChild(ellipsisItem);
            } else {
                // Regular page number
                const pageItem = document.createElement('li');
                pageItem.className = 'page-item' + (pageNum === state.page ? ' active' : '');
                const pageLink = document.createElement('a');
                pageLink.className = 'page-link';
                pageLink.href = '#';
                pageLink.textContent = pageNum;
                pageLink.addEventListener('click', (e) => {
                    e.preventDefault();
                    state.page = pageNum;
                    loadData();
                });
                pageItem.appendChild(pageLink);
                ul.appendChild(pageItem);
            }
        });
        
        // Next button
        const nextItem = document.createElement('li');
        nextItem.className = 'page-item' + (state.page >= totalPages ? ' disabled' : '');
        const nextLink = document.createElement('a');
        nextLink.className = 'page-link';
        nextLink.href = '#';
        nextLink.textContent = 'Next';
        nextLink.addEventListener('click', (e) => {
            e.preventDefault();
            if (state.page < totalPages) {
                state.page++;
                loadData();
            }
        });
        nextItem.appendChild(nextLink);
        ul.appendChild(nextItem);
        
        nav.appendChild(ul);
        container.appendChild(nav);
    }
    
    // Helper to determine which page numbers to show
    function getVisiblePageNumbers(currentPage, totalPages) {
        // For small page counts, show all pages
        if (totalPages <= 7) {
            return Array.from({ length: totalPages }, (_, i) => i + 1);
        }
        
        // For larger page counts, use ellipsis
        const visible = [1];
        
        if (currentPage > 3) {
            visible.push('...');
        }
        
        const start = Math.max(2, currentPage - 1);
        const end = Math.min(totalPages - 1, currentPage + 1);
        
        for (let i = start; i <= end; i++) {
            visible.push(i);
        }
        
        if (currentPage < totalPages - 2) {
            visible.push('...');
        }
        
        visible.push(totalPages);
        
        return visible;
    }
    
    // Load initial data
    loadData();
    
    // Return the API for controlling the data table
    return {
        reload: loadData,
        setPage: (page) => {
            state.page = page;
            loadData();
        },
        setPerPage: (perPage) => {
            state.perPage = perPage;
            state.page = 1; // Reset to first page
            loadData();
        },
        setSort: (field, direction = 'asc') => {
            state.sortField = field;
            state.sortDirection = direction;
            updateSortIndicators();
            loadData();
        },
        setFilter: (key, value) => {
            state.filters[key] = value;
            state.page = 1; // Reset to first page
            loadData();
        },
        clearFilters: () => {
            state.filters = {};
            state.page = 1;
            loadData();
        },
        getState: () => ({ ...state })
    };
}