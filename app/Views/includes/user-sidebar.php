<!-- User Sidebar -->
<div class="sidebar bg-light" id="sidebar">
    
    <div class="avatar-wrapper">
        <img src="https://ui-avatars.com/api/?name=<?php echo urlencode(isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'User'); ?>&background=5469d4&color=fff" alt="User" class="avatar">
        <div class="user-info">
            <div class="user-name"><?php echo isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'User'; ?></div>
            <div class="user-role">Reader</div>
        </div>
    </div>

    <hr>

    <ul class="nav flex-column">
        <?php
        // Define sidebar links with their icons and URLs
        $user_links = [
            ['title' => 'Dashboard', 'icon' => 'tachometer-alt', 'url' => '/user/dashboard'],
            ['title' => 'Browse Books', 'icon' => 'books', 'url' => '/user/browse-books'],
            ['title' => 'My Reading Sessions', 'icon' => 'book-open', 'url' => '/user/reading-sessions'],
            ['title' => 'Wishlist', 'icon' => 'heart', 'url' => '/user/wishlist'],
            ['title' => 'My Purchases', 'icon' => 'shopping-bag', 'url' => '/user/purchases'],
            ['title' => 'Profile', 'icon' => 'user-circle', 'url' => '/user/user-profile'],
            ['title' => 'Logout', 'icon' => 'sign-out-alt', 'url' => 'logout']
        ];

        // Get current page filename
        $current_page = basename($_SERVER['PHP_SELF']);

        // Display each link
        foreach ($user_links as $link) {
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