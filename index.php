<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Debre Markos University Health Campus - Online Examination System</title>
    <link href="assets/css/modern-v2.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        /* Enhanced Home Page Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            background: linear-gradient(135deg, #0f2027 0%, #203a43 50%, #2c5364 100%);
            position: relative;
            overflow-x: hidden;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('images/exam.webp') center/cover no-repeat;
            opacity: 0.08;
            z-index: 1;
            pointer-events: none;
        }

        /* Animated Background Particles */
        .particles {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 1;
            pointer-events: none;
        }

        .particle {
            position: absolute;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            animation: float 15s infinite ease-in-out;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0) translateX(0); }
            25% { transform: translateY(-100px) translateX(50px); }
            50% { transform: translateY(-200px) translateX(-50px); }
            75% { transform: translateY(-100px) translateX(100px); }
        }

        /* Modern Header */
        .modern-header {
            background: linear-gradient(135deg, #1a2b4a 0%, #2c5364 100%);
            backdrop-filter: blur(10px);
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.3);
            position: sticky;
            top: 0;
            z-index: 1000;
            border-bottom: 4px solid #d4af37;
        }

        .header-top {
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

        .header-actions {
            display: flex;
            gap: 1rem;
            align-items: center;
        }

        /* Navigation */
        .main-nav {
            background: #1a2b4a;
        }

        .nav-menu {
            list-style: none;
            display: flex;
            gap: 0;
            margin: 0;
            padding: 0;
        }

        .nav-menu li a {
            display: block;
            padding: 1rem 2rem;
            color: white;
            text-decoration: none;
            font-weight: 600;
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
            background: #d4af37;
            transition: all 0.3s ease;
            transform: translateX(-50%);
        }

        .nav-menu li a:hover,
        .nav-menu li a.active {
            background: rgba(212, 175, 55, 0.1);
            color: #d4af37;
        }

        .nav-menu li a:hover::after,
        .nav-menu li a.active::after {
            width: 80%;
        }

        /* Main Content */
        .main-content {
            flex: 1;
            position: relative;
            z-index: 100;
            padding: 3rem 0;
            width: 100%;
        }

        /* Hero Section */
        .hero-section {
            text-align: center;
            padding: 2rem 0;
            margin-bottom: 3rem;
            position: relative;
            z-index: 100;
        }

        .hero-content {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(20px);
            border-radius: 30px;
            padding: 3rem 2.5rem;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.4);
            border: 2px solid rgba(212, 175, 55, 0.4);
            animation: fadeInUp 0.8s ease;
            position: relative;
            z-index: 100;
            max-width: 100%;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .hero-content h1 {
            font-size: 3rem;
            font-weight: 900;
            background: linear-gradient(135deg, #1a2b4a 0%, #2c5364 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 1rem;
            line-height: 1.2;
        }

        .hero-content > p {
            font-size: 1.35rem;
            color: #6c757d;
            font-weight: 500;
            margin-bottom: 2.5rem;
        }

        /* Feature Cards */
        .hero-features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-top: 2.5rem;
            position: relative;
            z-index: 100;
        }

        .feature-card {
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
            border-radius: 20px;
            padding: 2rem 1.5rem;
            text-align: center;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            border: 2px solid transparent;
            position: relative;
            overflow: hidden;
            z-index: 100;
        }

        .feature-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(26, 43, 74, 0.05) 0%, rgba(212, 175, 55, 0.05) 100%);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .feature-card:hover {
            transform: translateY(-10px) scale(1.02);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
            border-color: #d4af37;
        }

        .feature-card:hover::before {
            opacity: 1;
        }

        .feature-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
            filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.1));
            animation: bounce 2s infinite;
        }

        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        .feature-card:hover .feature-icon {
            animation: none;
            transform: scale(1.2) rotate(5deg);
        }

        .stat-number {
            display: block;
            font-size: 3rem;
            font-weight: 900;
            background: linear-gradient(135deg, #1a2b4a 0%, #d4af37 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 0.5rem;
        }

        .feature-card h3 {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1a2b4a;
            margin-bottom: 0.75rem;
        }

        .feature-card p {
            color: #6c757d;
            font-size: 1rem;
            line-height: 1.6;
            margin: 0;
        }

        /* Login Section */
        .login-container {
            margin: 3rem 0;
            animation: fadeInUp 1s ease 0.3s both;
            position: relative;
            z-index: 100;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(20px);
            border-radius: 30px;
            padding: 3rem 2.5rem;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.4);
            border: 2px solid rgba(212, 175, 55, 0.4);
            text-align: center;
            position: relative;
            z-index: 100;
            max-width: 100%;
        }

        .login-card-icon {
            font-size: 5rem;
            margin-bottom: 1.5rem;
            display: inline-block;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }

        .login-card h2 {
            font-size: 2.5rem;
            font-weight: 900;
            background: linear-gradient(135deg, #1a2b4a 0%, #2c5364 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 1rem;
        }

        .login-card > p {
            font-size: 1.15rem;
            color: #6c757d;
            margin-bottom: 2.5rem;
            font-weight: 500;
        }

        .login-buttons {
            display: flex;
            gap: 1.5rem;
            justify-content: center;
            flex-wrap: wrap;
            margin-bottom: 2.5rem;
        }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            padding: 1rem 2.5rem;
            font-size: 1.125rem;
            font-weight: 700;
            text-decoration: none;
            border-radius: 50px;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            position: relative;
            overflow: hidden;
        }

        .btn::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }

        .btn:hover::before {
            width: 300px;
            height: 300px;
        }

        .btn span {
            position: relative;
            z-index: 1;
        }

        .btn-success {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
        }

        .btn-success:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(40, 167, 69, 0.4);
        }

        .btn-primary {
            background: linear-gradient(135deg, #1a2b4a 0%, #2c5364 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(26, 43, 74, 0.4);
        }

        .btn-lg {
            padding: 1.25rem 3rem;
            font-size: 1.25rem;
        }

        .btn-sm {
            padding: 0.75rem 1.5rem;
            font-size: 1rem;
        }

        /* Forgot Password Link */
        .forgot-password-section {
            margin-top: 2.5rem;
            padding-top: 2.5rem;
            border-top: 2px solid rgba(0, 0, 0, 0.1);
        }

        .forgot-password-section p {
            color: #6c757d;
            margin: 0;
            font-size: 1.125rem;
        }

        .forgot-password-section a {
            color: #d4af37;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.3s ease;
            position: relative;
        }

        .forgot-password-section a::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 0;
            height: 2px;
            background: #d4af37;
            transition: width 0.3s ease;
        }

        .forgot-password-section a:hover::after {
            width: 100%;
        }

        /* Footer */
        .modern-footer {
            background: rgba(26, 43, 74, 0.98);
            backdrop-filter: blur(10px);
            color: white;
            padding: 1.5rem 0;
            margin-top: auto;
            border-top: 3px solid #d4af37;
            position: relative;
            z-index: 1000;
        }

        .footer-content {
            text-align: center;
        }

        .footer-content p {
            margin: 0;
            font-size: 1rem;
            color: rgba(255, 255, 255, 0.8);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .hero-content h1 {
                font-size: 2.5rem;
            }

            .hero-content > p {
                font-size: 1.25rem;
            }

            .hero-features {
                grid-template-columns: 1fr;
            }

            .login-card h2 {
                font-size: 2rem;
            }

            .login-buttons {
                flex-direction: column;
            }

            .btn-lg {
                width: 100%;
            }

            .university-name h1 {
                font-size: 1.25rem;
            }

            .nav-menu {
                flex-direction: column;
            }

            .nav-menu li a {
                padding: 0.75rem 1.5rem;
            }
        }

        /* Loading Animation */
        @keyframes shimmer {
            0% { background-position: -1000px 0; }
            100% { background-position: 1000px 0; }
        }

        .shimmer {
            animation: shimmer 2s infinite;
            background: linear-gradient(to right, transparent 0%, rgba(255, 255, 255, 0.3) 50%, transparent 100%);
            background-size: 1000px 100%;
        }
    </style>
</head>
<body>
    <!-- Animated Background Particles -->
    <div class="particles" id="particles"></div>

    <!-- Header -->
    <header class="modern-header">
        <div class="header-top">
            <div class="container">
                <div class="university-info">
                    <img src="images/logo1.png" alt="Debre Markos University Health Campus" class="university-logo" onerror="this.style.display='none'">
                    <div class="university-name">
                        <h1>Debre Markos University Health Campus</h1>
                        <p>Online Examination System</p>
                    </div>
                </div>
                <div class="header-actions">
                    <a href="#login" class="btn btn-primary btn-sm">
                        <span>🔐 Login</span>
                    </a>
                </div>
            </div>
        </div>
        <nav class="main-nav">
            <div class="container">
                <ul class="nav-menu">
                    <li><a href="index.php" class="active">🏠 Home</a></li>
                    <li><a href="AboutUs.php">ℹ️ About Us</a></li>
                    <li><a href="Help.php">❓ Help</a></li>
                </ul>
            </div>
        </nav>
    </header>

    <!-- Main Content -->
    <main class="main-content">
        <div class="container">
            <!-- Hero Section -->
            <section class="hero-section">
                <div class="hero-content">
                    <h1>Welcome to Online Examination System</h1>
                    <p>Secure, Efficient, and Modern Examination Platform</p>
                    
                    <div class="hero-features">
                        <div class="feature-card">
                            <div class="feature-icon">👨‍🎓</div>
                            <span class="stat-number">1000+</span>
                            <h3>Students</h3>
                            <p>Active learners taking exams online with confidence and ease</p>
                        </div>
                        <div class="feature-card">
                            <div class="feature-icon">👨‍🏫</div>
                            <span class="stat-number">50+</span>
                            <h3>Instructors</h3>
                            <p>Expert faculty managing assessments and evaluating performance</p>
                        </div>
                        <div class="feature-card">
                            <div class="feature-icon">📊</div>
                            <span class="stat-number">98%</span>
                            <h3>Success Rate</h3>
                            <p>Reliable and secure exam delivery with instant results</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Login Call-to-Action -->
            <section id="login" class="login-container">
                <div class="login-card">
                    <div class="login-card-icon">🎓</div>
                    <h2>Ready to Start?</h2>
                    <p>Access your portal to take exams, view results, and manage your profile</p>
                    
                    <div class="login-buttons">
                        <a href="student-login.php" class="btn btn-success btn-lg">
                            <span>👨‍🎓 Student Login</span>
                        </a>
                        <a href="institute-login.php" class="btn btn-primary btn-lg">
                            <span>👨‍💼 Institute Login</span>
                        </a>
                    </div>
                    
                    <div class="forgot-password-section">
                        <p>
                            <a href="forgot-password-request.php">🔑 Forgot Your Password?</a>
                        </p>
                    </div>
                </div>
            </section>
        </div>
    </main>

    <!-- Footer -->
    <footer class="modern-footer">
        <div class="container">
            <div class="footer-content">
                <p>&copy; 2026 Debre Markos University Health Campus Online Examination System. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script>
        // Create animated particles
        function createParticles() {
            const particlesContainer = document.getElementById('particles');
            const particleCount = 30;

            for (let i = 0; i < particleCount; i++) {
                const particle = document.createElement('div');
                particle.className = 'particle';
                
                const size = Math.random() * 60 + 20;
                particle.style.width = size + 'px';
                particle.style.height = size + 'px';
                particle.style.left = Math.random() * 100 + '%';
                particle.style.top = Math.random() * 100 + '%';
                particle.style.animationDelay = Math.random() * 15 + 's';
                particle.style.animationDuration = (Math.random() * 10 + 10) + 's';
                
                particlesContainer.appendChild(particle);
            }
        }

        // Smooth scroll to login
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Intersection Observer for animations
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -100px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, observerOptions);

        document.querySelectorAll('.feature-card, .login-card').forEach(el => {
            el.style.opacity = '0';
            el.style.transform = 'translateY(30px)';
            el.style.transition = 'all 0.6s ease';
            observer.observe(el);
        });

        // Initialize particles
        createParticles();

        // Add ripple effect to buttons
        document.querySelectorAll('.btn').forEach(button => {
            button.addEventListener('click', function(e) {
                const ripple = document.createElement('span');
                const rect = this.getBoundingClientRect();
                const size = Math.max(rect.width, rect.height);
                const x = e.clientX - rect.left - size / 2;
                const y = e.clientY - rect.top - size / 2;
                
                ripple.style.width = ripple.style.height = size + 'px';
                ripple.style.left = x + 'px';
                ripple.style.top = y + 'px';
                ripple.classList.add('ripple');
                
                this.appendChild(ripple);
                
                setTimeout(() => ripple.remove(), 600);
            });
        });
    </script>
</body>
</html>
