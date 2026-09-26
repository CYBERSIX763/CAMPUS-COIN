

<style>
/* =========================================================
   ADMIN SIDEBAR
   ========================================================= */

.admin-sidebar {
    width: 230px;
    min-height: calc(100vh - 60px);

    background: #ffffff;

    border-right: 1px solid #ddd;

    padding: 20px 0;

    flex-shrink: 0;

    box-sizing: border-box;
}


/* Admin sidebar section title */

.admin-sidebar-title {
    padding: 0 22px;
    margin: 0 0 15px 0;

    font-size: 13px;
    font-weight: 600;

    color: #777;

    text-transform: uppercase;
    letter-spacing: 0.5px;
}


/* Admin sidebar links */

.admin-sidebar a {
    display: block;

    padding: 12px 22px;

    color: #333;

    text-decoration: none;

    font-size: 15px;

    transition:
        background-color 0.2s ease,
        color 0.2s ease;
}


/* Hover */

.admin-sidebar a:hover {
    background-color: #f0f4ff;

    color: #2563eb;
}


/* Active/current page */

.admin-sidebar a.active {
    background-color: #e8efff;

    color: #2563eb;

    font-weight: 600;

    border-right: 3px solid #2563eb;
}


/* Account section spacing */

.admin-sidebar .admin-sidebar-title:nth-of-type(2) {
    margin-top: 25px;
}


/* Logout */

.admin-sidebar a[href*="logout"] {
    margin-top: 10px;

    color: #dc2626;
}

.admin-sidebar a[href*="logout"]:hover {
    background-color: #fef2f2;

    color: #b91c1c;
}


/* =========================================================
   ADMIN PAGE LAYOUT
   ========================================================= */

.admin-layout {
    display: flex;

    width: 100%;

    min-height: calc(100vh - 60px);
}


/* Main admin content */

.admin-layout .main-content {
    flex: 1;

    min-width: 0;

    padding: 30px;
}


/* =========================================================
   RESPONSIVE
   ========================================================= */

@media (max-width: 768px) {

    .admin-sidebar {
        width: 190px;
    }

    .admin-sidebar a {
        padding: 11px 15px;
    }

    .admin-sidebar-title {
        padding: 0 15px;
    }

    .admin-layout .main-content {
        padding: 20px;
    }
}
</style>
<aside class="admin-sidebar">

<a href="<?php echo BASE_URL; ?>admin/index.php">
    Dashboard
</a>

<a href="<?php echo BASE_URL; ?>admin/users/index.php">
    Users
</a>

<a href="<?php echo BASE_URL; ?>admin/categories/index.php">
    Categories
</a>

<a href="<?php echo BASE_URL; ?>admin/saving-tips/index.php">
    Saving Tips
</a>

<a href="<?php echo BASE_URL; ?>admin/notifications/index.php">
    Notifications
</a>

<a href="<?php echo BASE_URL; ?>admin/transactions/index.php">
    Transactions
</a>

<a href="<?php echo BASE_URL; ?>admin/reports/index.php">
    Reports
</a>

<a href="<?php echo BASE_URL; ?>admin/logs/index.php">
    Admin logs
</a>

<div class="sidebar-title">
    ACCOUNT
</div>

<a href="<?php echo BASE_URL; ?>admin/profile/index.php">
    Profile
</a>

<a href="<?php echo BASE_URL; ?>authentication/logout.php">
    Logout
</a>

</aside>

