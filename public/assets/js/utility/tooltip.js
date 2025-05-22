// Apply sidebar state before page loads to prevent layout shifts
(function () {
    // Add no-transition class immediately to prevent any animations
    document.documentElement.classList.add('no-transition');

    // Check localStorage for saved state
    const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';

    // Apply sidebar collapsed state from localStorage to HTML element
    if (isCollapsed) {
        document.documentElement.classList.add('sidebar-collapsed');
    } else {
        document.documentElement.classList.remove('sidebar-collapsed');
    }

    // Also add a class to the body as early as possible
    document.addEventListener('DOMContentLoaded', function () {
        if (isCollapsed) {
            document.body.classList.add('sidebar-collapsed');
        } else {
            document.body.classList.remove('sidebar-collapsed');
        }
    });

    // Handle body class immediately if body is already available
    if (document.body) {
        if (isCollapsed) {
            document.body.classList.add('sidebar-collapsed');
        } else {
            document.body.classList.remove('sidebar-collapsed');
        }
    }

    // Apply mobile sidebar expanded state if needed
    if (window.innerWidth < 768) {
        const isExpanded = localStorage.getItem('sidebarExpanded') === 'true';
        if (isExpanded) {
            document.documentElement.classList.add('sidebar-expanded');
            if (document.body) {
                document.body.classList.add('sidebar-expanded');
            }
        }
    }
})();

document.addEventListener('DOMContentLoaded', function () {
    // Sidebar toggle functionality
    const sidebarToggler = document.getElementById('sidebarToggler');
    const toggleIcon = sidebarToggler.querySelector('i');

    // Ensure body class matches the localStorage state
    const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
    if (isCollapsed) {
        document.body.classList.add('sidebar-collapsed');
        document.documentElement.classList.add('sidebar-collapsed');
    } else {
        document.body.classList.remove('sidebar-collapsed');
        document.documentElement.classList.remove('sidebar-collapsed');
    }

    // Update toggle icon based on current state
    function updateToggleIcon() {
        if (document.body.classList.contains('sidebar-collapsed')) {
            toggleIcon.classList.remove('fa-chevron-left');
            toggleIcon.classList.add('fa-chevron-right');
        } else {
            toggleIcon.classList.remove('fa-chevron-right');
            toggleIcon.classList.add('fa-chevron-left');
        }
    }

    // Update icon immediately
    updateToggleIcon();

    // Remove transition blocking after slight delay to ensure initial state is properly applied
    setTimeout(function () {
        document.documentElement.classList.remove('no-transition');
        document.body.classList.remove('no-transition');
    }, 100);

    // Handle toggle button click
    sidebarToggler.addEventListener('click', function () {
        // Apply transitions only when user intentionally clicks the toggle
        document.body.classList.toggle('sidebar-collapsed');

        // Save state to localStorage
        localStorage.setItem('sidebarCollapsed', document.body.classList.contains('sidebar-collapsed'));

        // Update toggle icon
        updateToggleIcon();
    });

    // Special handling for mobile view
    if (window.innerWidth < 768) {
        // For mobile devices, we use expanded/collapsed model instead
        document.body.classList.remove('sidebar-collapsed');

        sidebarToggler.addEventListener('click', function () {
            document.body.classList.toggle('sidebar-expanded');
            // Save mobile expanded state separately
            localStorage.setItem('sidebarExpanded', document.body.classList.contains('sidebar-expanded'));
        });
    }

    // Handle links to prevent animation during page transitions
    document.querySelectorAll('a').forEach(function (link) {
        // Only apply to internal links that aren't # anchors
        if (link.host === window.location.host && !link.getAttribute('href').startsWith('#')) {
            link.addEventListener('click', function () {
                // Disable transitions before navigation
                document.documentElement.classList.add('no-transition');
                document.body.classList.add('no-transition');
            });
        }
    });

    // Optimize tooltip initialization
    var tooltipOptions = {
        delay: {
            show: 300,
            hide: 100
        },
        trigger: 'hover',
        boundary: 'window'
    };

    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.forEach(function (tooltipTriggerEl) {
        new bootstrap.Tooltip(tooltipTriggerEl, tooltipOptions);
    });
});