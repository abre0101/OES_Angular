<header class="admin-header">
    <div class="header-left">
        <button class="mobile-menu-btn" onclick="toggleSidebar()">☰</button>
        <div class="header-breadcrumb">
            <span class="breadcrumb-item">Instructor</span>
            <span class="breadcrumb-separator">/</span>
            <span class="breadcrumb-item active"><?php echo $pageTitle ?? 'Dashboard'; ?></span>
        </div>
    </div>
    
    <div class="header-center">
        <div class="header-search">
            <span class="search-icon">🔍</span>
            <input type="text" placeholder="Search questions, exams, students..." class="search-input" id="globalSearchInput" autocomplete="off">
            <div class="search-results" id="searchResults"></div>
        </div>
    </div>
    
    <div class="header-right">
        <div class="header-datetime">
            <div class="header-time" id="currentTime"></div>
            <div class="header-date"><?php echo date('D, M d, Y'); ?></div>
        </div>
        
        <div class="header-notifications">
            <button class="header-icon-btn" title="Notifications">
                <span class="notification-icon">🔔</span>
                <span class="notification-badge">3</span>
            </button>
        </div>
        
        <div class="header-profile" onclick="toggleProfileDropdown(event)">
            <div class="header-profile-avatar">
                <?php echo strtoupper(substr($_SESSION['Name'], 0, 1)); ?>
            </div>
            <div class="header-profile-info">
                <div class="header-profile-name"><?php echo $_SESSION['Name']; ?></div>
                <div class="header-profile-role">Instructor</div>
            </div>
            <button class="header-dropdown-btn">▼</button>
            
            <div class="profile-dropdown">
                <a href="Profile.php" class="dropdown-item">
                    <span class="dropdown-icon">👤</span>
                    <span>My Profile</span>
                </a>
                <a href="Settings.php" class="dropdown-item">
                    <span class="dropdown-icon">⚙️</span>
                    <span>Settings</span>
                </a>
                <div class="dropdown-divider"></div>
                <a href="Logout.php" class="dropdown-item logout">
                    <span class="dropdown-icon">🚪</span>
                    <span>Logout</span>
                </a>
            </div>
        </div>
    </div>
</header>
