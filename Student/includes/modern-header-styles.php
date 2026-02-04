<style>
    /* Modern Header Styles - Matching Student/index.php exactly */
    
    /* Body Background */
    body {
        background: linear-gradient(135deg, #0f2027 0%, #203a43 50%, #2c5364 100%);
        position: relative;
        overflow-x: hidden;
    }

    body::before {
        content: '';
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: url('../images/istockphoto-1772381872-612x612.jpg') center/cover no-repeat;
        opacity: 0.35;
        z-index: 1;
        pointer-events: none;
    }
    
    /* Modern Header */
    .modern-header {
        background: #ffffff;
        box-shadow: 0 4px 30px rgba(0, 0, 0, 0.3);
        position: sticky;
        top: 0;
        z-index: 1000;
        border-bottom: 4px solid #d4af37;
    }

    .header-top {
        background: linear-gradient(135deg, #1a2b4a 0%, #2c5364 100%);
        padding: 1.5rem 0;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    .container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 0 2rem;
    }

    .header-top .container {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 2rem;
    }

    .university-info {
        display: flex;
        align-items: center;
        gap: 1.5rem;
    }

    .university-logo {
        width: 80px;
        height: 80px;
        object-fit: contain;
        filter: drop-shadow(0 2px 8px rgba(0, 0, 0, 0.1));
    }

    .university-name h1 {
        font-size: 1.85rem;
        font-weight: 900;
        color: #ffffff;
        margin: 0;
        line-height: 1.2;
        text-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
        letter-spacing: -0.5px;
    }

    .university-name p {
        font-size: 1.15rem;
        color: #ffd700;
        font-weight: 700;
        margin: 0.35rem 0 0 0;
        text-shadow: 0 2px 6px rgba(0, 0, 0, 0.3);
        letter-spacing: 0.5px;
    }

    /* User Dropdown */
    .user-dropdown {
        position: relative;
    }

    .user-info {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.75rem 1.25rem;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50px;
        cursor: pointer;
        transition: all 0.3s ease;
        color: white;
    }

    .user-info:hover {
        background: rgba(255, 255, 255, 0.2);
    }

    .user-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(135deg, #d4af37 0%, #f4d03f 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 900;
        font-size: 1.25rem;
        color: #1a2b4a;
    }

    .dropdown-menu {
        position: absolute;
        top: calc(100% + 0.5rem);
        right: 0;
        background: white;
        border-radius: 15px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
        min-width: 220px;
        opacity: 0;
        visibility: hidden;
        transform: translateY(-10px);
        transition: all 0.3s ease;
        z-index: 1000;
    }

    .user-dropdown.active .dropdown-menu {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }

    .dropdown-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 1rem 1.25rem;
        color: #1a2b4a;
        text-decoration: none;
        transition: all 0.3s ease;
        font-weight: 600;
    }

    .dropdown-icon {
        width: 20px;
        text-align: center;
        font-size: 1.1rem;
    }

    .dropdown-item:hover {
        background: rgba(212, 175, 55, 0.1);
    }

    .dropdown-item.logout {
        color: #dc3545;
    }

    .dropdown-item.logout:hover {
        background: rgba(220, 53, 69, 0.1);
    }

    .dropdown-divider {
        height: 1px;
        background: rgba(0, 0, 0, 0.1);
        margin: 0.5rem 0;
    }

    /* Navigation */
    .main-nav {
        background: linear-gradient(135deg, #d4af37 0%, #f4d03f 100%);
    }

    .nav-menu {
        list-style: none;
        display: flex;
        gap: 0;
        margin: 0;
        padding: 0;
        justify-content: center;
    }

    .nav-menu li a {
        display: block;
        padding: 1rem 1.5rem;
        color: #1a2b4a;
        text-decoration: none;
        font-weight: 700;
        transition: all 0.3s ease;
        position: relative;
    }

    .nav-menu li a::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 50%;
        width: 0;
        height: 3px;
        background: #1a2b4a;
        transition: all 0.3s ease;
        transform: translateX(-50%);
    }

    .nav-menu li a:hover,
    .nav-menu li a.active {
        background: rgba(26, 43, 74, 0.15);
        color: #1a2b4a;
    }

    .nav-menu li a:hover::after,
    .nav-menu li a.active::after {
        width: 80%;
    }

    /* Main Content - Reduce top padding */
    .main-content {
        padding-top: 2rem !important;
        padding-bottom: 2rem !important;
        position: relative;
        z-index: 100;
    }

    /* Cards - No opacity/transparency */
    .card,
    .stat-card,
    .exam-card,
    .profile-form,
    .content-wrapper,
    .welcome-banner,
    .result-card {
        background: #ffffff !important;
        opacity: 1 !important;
    }

    /* Ensure all card backgrounds are solid white */
    .card {
        background-color: #ffffff !important;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .header-top .container {
            flex-direction: column;
            text-align: center;
        }

        .university-info {
            flex-direction: column;
        }

        .university-name h1 {
            font-size: 1.25rem;
        }

        .university-name p {
            font-size: 0.95rem;
        }

        .nav-menu {
            flex-direction: column;
        }

        .nav-menu li a {
            padding: 0.75rem 1.5rem;
        }

        .user-dropdown {
            width: 100%;
        }

        .dropdown-menu {
            left: 0;
            right: 0;
            min-width: 100%;
        }

        .main-content {
            padding-top: 1.5rem !important;
        }
    }
</style>
