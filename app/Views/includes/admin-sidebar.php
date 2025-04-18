<!-- Admin Sidebar -->
<div class="sidebar bg-dark text-white" id="sidebar">
    <div class="avatar-wrapper">
        <img src="https://ui-avatars.com/api/?name=Admin+User&background=1a2236&color=fff" alt="Admin" class="avatar">
        <div class="user-info">
            <div class="user-name">Admin User</div>
            <div class="user-role">Administrator</div>
        </div>
    </div>

    <hr >

    <ul class="nav flex-column">
        <?php
        // Define sidebar links with their icons and URLs
        $admin_links = [
            ['title' => 'Dashboard', 'icon' => 'tachometer-alt', 'url' => '/admin/dashboard'],
            ['title' => 'User Management', 'icon' => 'users', 'url' => '/admin/user-management'],
            ['title' => 'Book Management', 'icon' => 'book', 'url' => '/admin/book-management'],
            ['title' => 'Reading Sessions', 'icon' => 'history', 'url' => '/admin/reading-sessions'],
            ['title' => 'Purchases', 'icon' => 'shopping-cart', 'url' => '/admin/purchases'],
            ['title' => 'Activity Log', 'icon' => 'clipboard-list', 'url' => '/admin/activity-logs'],
            ['title' => 'Logout', 'icon' => 'sign-out-alt', 'url' => '/logout']
        ];

        // Get current page filename
        $current_page = basename($_SERVER['PHP_SELF']);

        // Display each link
        foreach ($admin_links as $link) {
            $active = (strpos($current_page, basename($link['url'])) !== false) ? 'active' : '';
            echo '<li class="nav-item">';
            echo '<a class="nav-link ' . $active . '" href="' . $link['url'] . '" data-bs-toggle="tooltip" data-bs-placement="right" data-bs-title="' . $link['title'] . '">';
            echo '<i class="fas fa-' . $link['icon'] . '"></i>';
            echo '<span>' . $link['title'] . '</span>';
            echo '</a>';
            echo '</li>';
        }
        ?>
    </ul>
</div>