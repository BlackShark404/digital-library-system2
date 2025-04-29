<?php

// Simple function to check if user is admin

use Core\Session;

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title : 'Online Reading Platform'; ?></title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Custom CSS -->
    <style>
        :root {
            --primary-color: #3a86ff;
            --primary-hover: #2667cc;
            --sidebar-width: 250px;
            --sidebar-collapsed-width: 64px;
            --sidebar-bg: #ffffff;
            --sidebar-text: #333333;
            --admin-sidebar-bg: #1e293b;
            --admin-sidebar-text: #f1f5f9;
            --header-height: 60px;
            --transition-speed: 0.3s;
            --border-radius: 8px;
            --box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        body {
            display: flex;
            min-height: 100vh;
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fa;
            overflow-x: hidden;
            flex-direction: column;
        }

        html {
            scroll-behavior: smooth;
        }

        /* Sidebar Styles */
        .sidebar {
            width: var(--sidebar-width);
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            z-index: 100;
            padding-top: var(--header-height);
            box-shadow: var(--box-shadow);
            transition: all var(--transition-speed) ease;
        }

        .sidebar .nav-link {
            color: inherit;
            padding: 12px 20px;
            margin: 4px 8px;
            border-radius: var(--border-radius);
            transition: all 0.2s ease;
            white-space: nowrap;
            overflow: hidden;
            display: flex;
            align-items: center;
        }

        .sidebar .nav-link:hover {
            background-color: rgba(0, 0, 0, 0.05);
        }

        .sidebar .nav-link.active {
            background-color: var(--primary-color);
            color: white;
        }

        .sidebar-header {
            padding: 16px 20px;
            display: flex;
            align-items: center;
        }

        .sidebar-header i {
            font-size: 1.25rem;
        }

        .sidebar-header h5 {
            margin: 0;
            margin-left: 10px;
            font-weight: 600;
        }

        /* Navbar Styles */
        .navbar {
            position: fixed;
            top: 0;
            right: 0;
            left: 0;
            z-index: 200;
            padding-left: calc(var(--sidebar-width) + 15px);
            transition: all var(--transition-speed) ease;
            height: var(--header-height);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            background-color: white !important;
        }

        .navbar-brand {
            font-weight: 600;
            font-size: 1.2rem;
        }
        
        .navbar-title {
            font-size: 1.5rem;
            font-weight: bold;
            text-decoration: none;
            display: flex;
            align-items: center;
        }

        .blue-text {
            color: blue;
        }


        /* Content Wrapper */
        .content-wrapper {
            flex: 1;
            margin-left: var(--sidebar-width);
            padding-top: calc(var(--header-height) + 20px);
            padding-bottom: 30px;
            transition: all var(--transition-speed) ease;
            min-height: calc(100vh - 60px - 56px); 
        }

        .avatar-wrapper {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 0.1rem 1rem;
            padding-top: 2.5rem;
        }

        .avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border: 3px solid white;
        }

        .avatar-wrapper-compact {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.1rem;
        }

        .avatar-compact {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            object-fit: cover;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08);
            border: 2px solid white;
        }


        .user-info {
            margin-top: 0.75rem;
            text-align: center;
            transition: var(--transition);
        }

        .user-name {
            font-weight: 600;
            color: var(--secondary-color);
            margin-bottom: 0.25rem;
        }

        .user-role {
            font-size: 0.85rem;
            color: #8697a8;
        }

        /* Sidebar Toggler */
        .sidebar-toggler {
            position: fixed;
            top: 16px;
            left: calc(var(--sidebar-width) - 40px);
            z-index: 300;
            background-color: white;
            border-radius: 50%;
            box-shadow: var(--box-shadow);
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all var(--transition-speed) ease;
            border: none;
        }

        .footer {
            margin-left: var(--sidebar-width);
            transition: all var(--transition-speed) ease;
        }

        body.sidebar-collapsed .footer {
            margin-left: var(--sidebar-collapsed-width);
        }



        /* Collapsed Sidebar States */
        body.no-transition .sidebar,
        body.no-transition .content-wrapper,
        body.no-transition .navbar,
        body.no-transition .sidebar-toggler {
            transition: none !important;
        }

        body.sidebar-collapsed .sidebar {
            width: var(--sidebar-collapsed-width);
        }

        body.sidebar-collapsed .sidebar .nav-link span {
            opacity: 0;
            display: none;
        }

        body.sidebar-collapsed .sidebar .nav-link {
            justify-content: center;
            padding: 12px 0;
        }

        body.sidebar-collapsed .sidebar .nav-link i {
            margin-right: 0;
            font-size: 1.2rem;
        }

        body.sidebar-collapsed .sidebar-toggler {
            left: calc(var(--sidebar-collapsed-width) - 16px);
        }

        body.sidebar-collapsed .content-wrapper,
        body.sidebar-collapsed .navbar {
            margin-left: var(--sidebar-collapsed-width);
            padding-left: 15px;
        }

        body.sidebar-collapsed .sidebar-header h5 {
            display: none;
        }

        /* Avatar Adjustments for Collapsed Sidebar */
        body.sidebar-collapsed .avatar-wrapper {
            padding: 0.1rem 0;
            padding-top: 1.25rem;
            padding-left: 12px;
            align-items: flex-start;
        }

        body.sidebar-collapsed .avatar {
            width: 40px;
            height: 40px;
        }

        body.sidebar-collapsed .user-info {
            display: none;
        }

        /* Icon Styles */
        .sidebar .nav-link i {
            min-width: 24px;
            margin-right: 10px;
            text-align: center;
        }

        .d-sidebar-collapsed-block {
            display: none;
        }

        body.sidebar-collapsed .d-sidebar-collapsed-block {
            display: block;
        }

        /* Add these styles to your CSS */
        .profile-picture-container {
            position: relative;
            width: 150px;
            height: 150px;
            border-radius: 50%;
            overflow: hidden;
        }
        
        .profile-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: filter 0.3s ease;
        }
        
        .profile-picture-overlay {
            transition: opacity 0.3s ease;
            opacity: 0;
        }
        
        .profile-picture-container:hover .profile-image {
            filter: brightness(80%);
        }
        
        .profile-picture-container:hover .profile-picture-overlay {
            opacity: 1;
        }
        
        .icon-box {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .info-group {
            padding-bottom: 8px;
        }
        
        .reading-activity-item {
            transition: transform 0.2s ease;
        }
        
        .reading-activity-item:hover {
            transform: translateY(-3px);
        }

        /* Tooltip Styles */
        .tooltip {
            pointer-events: none;
            opacity: 0;
            transition: opacity 0.1s ease;
        }

        .tooltip.show {
            opacity: 0.9;
        }

         

        .tooltip .tooltip-inner {
            background-color: #333;
            border-radius: 4px;
            padding: 6px 10px;
        }

        /* Responsive for Mobile */
        @media (max-width: 768px) {
            .sidebar {
                margin-left: calc(-1 * var(--sidebar-width));
                box-shadow: none;
            }

            .content-wrapper,
            .navbar {
                margin-left: 0;
                padding-left: 15px;
            }

            .sidebar-toggler {
                left: 10px;
                background-color: var(--primary-color);
                color: white;
            }

            body.sidebar-expanded .sidebar {
                margin-left: 0;
                width: var(--sidebar-width);
                box-shadow: var(--box-shadow);
            }

            body.sidebar-expanded .sidebar .nav-link span {
                display: inline-block;
                opacity: 1;
            }

            body.sidebar-expanded .sidebar .nav-link {
                text-align: left;
                padding: 12px 20px;
                justify-content: flex-start;
            }

            body.sidebar-expanded .sidebar .nav-link i {
                margin-right: 10px;
            }
            
            /* Add a backdrop for mobile sidebar */
            body.sidebar-expanded::before {
                content: "";
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0, 0, 0, 0.5);
                z-index: 99;
            }
        }

        /* Admin Sidebar Specific Styles */
        .sidebar.bg-dark {
            background-color: var(--admin-sidebar-bg) !important;
            color: var(--admin-sidebar-text);
        }

        .sidebar.bg-dark .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }

        .sidebar.bg-dark .nav-link.active {
            background-color: rgba(255, 255, 255, 0.2);
        }

        /* Custom Button Styles */
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-primary:hover {
            background-color: var(--primary-hover);
            border-color: var(--primary-hover);
        }

        @media (max-width: 768px) {
            .footer {
                margin-left: 0;
            } 
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container-fluid">
            <div class="navbar-brand">
                <a href="/" class="navbar-title">
                    <i class="fas fa-book me-2"></i>Book<span class="blue-text">Sync</span>
                </a>
            </div>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle me-1"></i>
                            User
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item" href="<?= Session::get("profile_route") ?>">
                                    <i class="fas fa-id-badge me-2"></i>Profile
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="/logout">
                                <i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>


    <!-- Sidebar Toggle Button -->
    <button class="sidebar-toggler" id="sidebarToggler">
        <i class="fas fa-chevron-left"></i>
    </button>

    <?php
    // Include appropriate sidebar based on user role
    include $sidebarPath;
    ?>

    <!-- Main Content -->
    <div class="content-wrapper">
        <div class="container-fluid">