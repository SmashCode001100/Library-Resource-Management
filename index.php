<?php include 'cache_control.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Athena Library Management System</title>
    <link rel="stylesheet" href="css/style.css?v=<?php echo $css_version; ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <script src="js/script.js?v=<?php echo $js_version; ?>" defer></script>
    <style>
        :root {
            /* Modern Color Palette */
            --primary: #2c3e50;
            --primary-light: #34495e;
            --primary-dark: #1a2a3a;
            --secondary: #e74c3c;
            --secondary-light: #ec7063;
            --secondary-dark: #c0392b;
            --accent: #3498db;
            --accent-light: #5dade2;
            --accent-dark: #2980b9;
            --success: #2ecc71;
            --warning: #f1c40f;
            --error: #e74c3c;
            --light: #f8f9fa;
            --dark: #2c3e50;
            --text-light: #ffffff;
            --text-dark: #2c3e50;
            --text-muted: #6c757d;
            
            /* Modern Gradients */
            --gradient-primary: linear-gradient(135deg, var(--primary), var(--primary-dark));
            --gradient-secondary: linear-gradient(135deg, var(--secondary), var(--secondary-dark));
            --gradient-accent: linear-gradient(135deg, var(--accent), var(--accent-dark));
            --gradient-light: linear-gradient(135deg, var(--light), #e9ecef);
            
            /* Modern Shadows */
            --shadow-sm: 0 2px 4px rgba(0,0,0,0.1);
            --shadow-md: 0 4px 8px rgba(0,0,0,0.1);
            --shadow-lg: 0 8px 16px rgba(0,0,0,0.1);
            --shadow-xl: 0 12px 24px rgba(0,0,0,0.1);
            --shadow-xxl: 0 16px 32px rgba(0,0,0,0.1);
            --shadow-inner: inset 0 2px 4px rgba(0,0,0,0.1);
            
            /* Modern Transitions */
            --transition-fast: all 0.2s ease;
            --transition: all 0.3s ease;
            --transition-slow: all 0.5s ease;
            --transition-bounce: all 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55);
            
            /* Modern Spacing */
            --spacing-xs: 0.5rem;
            --spacing-sm: 1rem;
            --spacing-md: 2rem;
            --spacing-lg: 3rem;
            --spacing-xl: 4rem;
            
            /* Modern Border Radius */
            --radius-sm: 4px;
            --radius-md: 8px;
            --radius-lg: 12px;
            --radius-xl: 16px;
            --radius-full: 50%;
            
            /* Modern Z-index */
            --z-index-dropdown: 1000;
            --z-index-sticky: 1020;
            --z-index-fixed: 1030;
            --z-index-modal: 1040;
            --z-index-popover: 1050;
            --z-index-tooltip: 1060;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background-color: var(--light);
            color: var(--text-dark);
            line-height: 1.6;
            overflow-x: hidden;
            position: relative;
        }

        /* Modern Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: var(--light);
            border-radius: var(--radius-sm);
        }

        ::-webkit-scrollbar-thumb {
            background: var(--primary);
            border-radius: var(--radius-sm);
            transition: var(--transition);
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--primary-dark);
        }

        /* Container */
        .container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 var(--spacing-md);
        }

        /* Modern Navbar */
        .navbar {
            background: var(--gradient-primary);
            padding: var(--spacing-sm) 0;
            position: sticky;
            top: 0;
            z-index: var(--z-index-sticky);
            box-shadow: var(--shadow-md);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }

        .navbar-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: var(--spacing-md);
        }

        .logo {
            display: flex;
            align-items: center;
            gap: var(--spacing-sm);
            color: var(--text-light);
            text-decoration: none;
            transition: var(--transition);
        }

        .logo:hover {
            transform: translateY(-2px);
        }

        .logo i {
            font-size: 2rem;
            color: var(--accent);
            transition: var(--transition);
        }

        .logo:hover i {
            transform: rotate(15deg);
        }

        .logo h1 {
            font-size: 1.5rem;
            font-weight: 600;
            background: linear-gradient(to right, var(--text-light), var(--accent-light));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        /* Modern Search Bar */
        .search-bar {
            flex: 1;
            max-width: 500px;
            position: relative;
        }

        .search-bar input {
            width: 100%;
            padding: 0.75rem 1rem;
            border: none;
            border-radius: var(--radius-md);
            background: rgba(255, 255, 255, 0.1);
            color: var(--text-light);
            font-size: 1rem;
            transition: var(--transition);
            backdrop-filter: blur(5px);
            -webkit-backdrop-filter: blur(5px);
        }

        .search-bar input::placeholder {
            color: rgba(255, 255, 255, 0.7);
        }

        .search-bar input:focus {
            background: rgba(255, 255, 255, 0.2);
            outline: none;
            box-shadow: var(--shadow-inner);
        }

        .search-btn {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--text-light);
            cursor: pointer;
            transition: var(--transition);
        }

        .search-btn:hover {
            color: var(--accent);
            transform: translateY(-50%) scale(1.1);
        }

        /* Modern Navigation Buttons */
        .nav-actions {
            display: flex;
            gap: var(--spacing-sm);
        }

        .nav-btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: var(--radius-md);
            background: rgba(255, 255, 255, 0.1);
            color: var(--text-light);
            cursor: pointer;
            transition: var(--transition-bounce);
            display: flex;
            align-items: center;
            gap: var(--spacing-xs);
            font-weight: 500;
            backdrop-filter: blur(5px);
            -webkit-backdrop-filter: blur(5px);
        }

        .nav-btn:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-2px);
        }

        .nav-btn a {
            color: var(--text-light);
            text-decoration: none;
        }

        .nav-btn i {
            font-size: 1.1rem;
            transition: var(--transition);
        }

        .nav-btn:hover i {
            transform: scale(1.1);
        }

        /* Modern Hero Section */
        .hero {
            padding: var(--spacing-xl) 0;
            background: var(--gradient-primary);
            color: var(--text-light);
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><rect width="100" height="100" fill="none"/><path d="M0 50 L100 50" stroke="rgba(255,255,255,0.1)" stroke-width="1"/></svg>');
            opacity: 0.1;
        }

        .hero-content {
            max-width: 800px;
            margin: 0 auto;
            position: relative;
            z-index: 1;
        }

        .hero h2 {
            font-size: 3.5rem;
            font-weight: 700;
            margin-bottom: var(--spacing-md);
            line-height: 1.2;
            background: linear-gradient(to right, var(--text-light), var(--accent-light));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: fadeInUp 1s ease;
        }

        .hero p {
            font-size: 1.25rem;
            margin-bottom: var(--spacing-lg);
            opacity: 0.9;
            animation: fadeInUp 1s ease 0.2s backwards;
        }

        .cta-btn {
            padding: 1rem 2.5rem;
            background: var(--gradient-accent);
            color: var(--text-light);
            border: none;
            border-radius: var(--radius-md);
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition-bounce);
            animation: fadeInUp 1s ease 0.4s backwards;
        }

        .cta-btn:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-lg);
        }

        /* Modern Category Cards */
        .categories {
            display: flex;
            justify-content: center;
            gap: var(--spacing-md);
            overflow-x: auto;
            padding: var(--spacing-sm) 0;
            scrollbar-width: none;
            -ms-overflow-style: none;
        }

        .categories::-webkit-scrollbar {
            display: none;
        }

        .category-card {
            background: white;
            padding: var(--spacing-md);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-md);
            text-align: center;
            transition: var(--transition-bounce);
            min-width: 200px;
            flex-shrink: 0;
            position: relative;
            overflow: hidden;
        }

        .category-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--gradient-accent);
        }

        .category-card:hover {
            transform: translateY(-5px) scale(1.02);
            box-shadow: var(--shadow-lg);
        }

        .category-card i {
            font-size: 2.5rem;
            color: var(--primary);
            margin-bottom: var(--spacing-sm);
            transition: var(--transition);
        }

        .category-card:hover i {
            transform: scale(1.1) rotate(15deg);
            color: var(--accent);
        }

        .category-card h3 {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: var(--spacing-xs);
            color: var(--text-dark);
        }

        /* Modern Book Cards */
        .books-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: var(--spacing-lg);
        }

        .book-card {
            background: white;
            border-radius: var(--radius-lg);
            overflow: hidden;
            box-shadow: var(--shadow-md);
            transition: var(--transition-bounce);
            position: relative;
        }

        .book-card:hover {
            transform: translateY(-5px) scale(1.02);
            box-shadow: var(--shadow-lg);
        }

        .book-img {
            height: 400px;
            overflow: hidden;
            position: relative;
        }

        .book-img::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(to bottom, transparent 0%, rgba(0,0,0,0.1) 100%);
        }

        .book-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: var(--transition);
        }

        .book-card:hover .book-img img {
            transform: scale(1.05);
        }

        .book-details {
            padding: var(--spacing-md);
            background: white;
        }

        .book-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: var(--spacing-sm);
            color: var(--text-dark);
        }

        .book-author {
            color: var(--text-muted);
            margin-bottom: var(--spacing-sm);
        }

        .book-description {
            color: var(--text-muted);
            margin-bottom: var(--spacing-md);
        }

        .book-actions {
            display: flex;
            gap: var(--spacing-sm);
        }

        .book-btn {
            flex: 1;
            padding: 0.75rem;
            border: none;
            border-radius: var(--radius-md);
            cursor: pointer;
            font-weight: 600;
            transition: var(--transition-bounce);
        }

        .primary-btn {
            background: var(--gradient-primary);
            color: var(--text-light);
        }

        .primary-btn:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        .icon-btn {
            width: 40px;
            height: 40px;
            border-radius: var(--radius-full);
            background: var(--light);
            color: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .icon-btn:hover {
            background: var(--primary);
            color: var(--text-light);
            transform: rotate(15deg);
        }

        /* Modern Feature Cards */
        .features {
            display: flex;
            justify-content: center;
            gap: var(--spacing-md);
            overflow-x: auto;
            padding: var(--spacing-sm) 0;
            scrollbar-width: none;
            -ms-overflow-style: none;
        }

        .features::-webkit-scrollbar {
            display: none;
        }

        .feature-card {
            background: white;
            padding: var(--spacing-md);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-md);
            text-align: center;
            transition: var(--transition-bounce);
            min-width: 300px;
            flex-shrink: 0;
            position: relative;
            overflow: hidden;
        }

        .feature-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--gradient-secondary);
        }

        .feature-card:hover {
            transform: translateY(-5px) scale(1.02);
            box-shadow: var(--shadow-lg);
        }

        .feature-icon {
            width: 70px;
            height: 70px;
            margin: 0 auto var(--spacing-sm);
            background: var(--gradient-primary);
            border-radius: var(--radius-full);
            display: flex;
            align-items: center;
            justify-content: center;
            transition: var(--transition-bounce);
        }

        .feature-card:hover .feature-icon {
            background: var(--gradient-accent);
            transform: rotate(15deg) scale(1.1);
        }

        .feature-icon i {
            font-size: 1.75rem;
            color: var(--text-light);
        }

        .feature-title {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: var(--spacing-sm);
            color: var(--text-dark);
        }

        .feature-card p {
            color: var(--text-muted);
            margin-bottom: var(--spacing-md);
            line-height: 1.6;
        }

        /* Modern Footer */
        footer {
            background: var(--gradient-primary);
            color: var(--text-light);
            padding: var(--spacing-xl) 0 var(--spacing-md);
            position: relative;
            overflow: hidden;
        }

        footer::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><rect width="100" height="100" fill="none"/><path d="M0 50 L100 50" stroke="rgba(255,255,255,0.1)" stroke-width="1"/></svg>');
            opacity: 0.1;
        }

        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: var(--spacing-xl);
            margin-bottom: var(--spacing-xl);
            position: relative;
            z-index: 1;
        }

        .footer-about {
            display: flex;
            flex-direction: column;
            gap: var(--spacing-md);
        }

        .footer-logo {
            display: flex;
            align-items: center;
            gap: var(--spacing-sm);
            margin-bottom: var(--spacing-md);
        }

        .footer-logo i {
            font-size: 2rem;
            color: var(--accent);
            transition: var(--transition);
        }

        .footer-logo:hover i {
            transform: rotate(15deg);
        }

        .footer-logo h2 {
            font-size: 1.5rem;
            font-weight: 600;
            background: linear-gradient(to right, var(--text-light), var(--accent-light));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .footer-about p {
            margin-bottom: var(--spacing-md);
            opacity: 0.9;
        }

        .social-links {
            display: flex;
            gap: var(--spacing-sm);
        }

        .social-link {
            width: 40px;
            height: 40px;
            border-radius: var(--radius-full);
            background: rgba(255, 255, 255, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            transition: var(--transition-bounce);
            backdrop-filter: blur(5px);
            -webkit-backdrop-filter: blur(5px);
        }

        .social-link:hover {
            background: var(--accent);
            transform: translateY(-2px) scale(1.1);
        }

        .footer-title {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: var(--spacing-md);
            background: linear-gradient(to right, var(--text-light), var(--accent-light));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .footer-links {
            list-style: none;
        }

        .footer-links li {
            margin-bottom: var(--spacing-sm);
        }

        .footer-links a {
            color: var(--text-light);
            text-decoration: none;
            opacity: 0.9;
            transition: var(--transition);
            display: inline-block;
        }

        .footer-links a:hover {
            opacity: 1;
            color: var(--accent);
            transform: translateX(5px);
        }

        .footer-bottom {
            text-align: center;
            padding-top: var(--spacing-md);
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            position: relative;
            z-index: 1;
        }

        .footer-bottom p {
            opacity: 0.8;
        }

        .footer-bottom a {
            color: var(--accent);
            text-decoration: none;
            transition: var(--transition);
        }

        .footer-bottom a:hover {
            text-decoration: underline;
            color: var(--accent-light);
        }

        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Responsive Design */
        @media (max-width: 992px) {
            .navbar-content {
                flex-direction: column;
            }

            .search-bar {
                width: 100%;
                max-width: none;
            }

            .nav-actions {
                width: 100%;
                justify-content: center;
            }

            .hero h2 {
                font-size: 2.5rem;
            }

            .categories {
                justify-content: flex-start;
                padding-left: var(--spacing-sm);
                padding-right: var(--spacing-sm);
            }
        }

        @media (max-width: 768px) {
            .hero h2 {
                font-size: 2rem;
            }

            .hero p {
                font-size: 1.1rem;
            }

            .section-title {
                font-size: 2rem;
            }

            .book-card {
                max-width: 400px;
                margin: 0 auto;
            }

            .category-card {
                min-width: 180px;
                padding: var(--spacing-sm);
            }

            .category-card i {
                font-size: 2rem;
            }

            .category-card h3 {
                font-size: 1.1rem;
            }
        }

        @media (max-width: 576px) {
            .nav-btn {
                padding: 0.5rem 1rem;
                font-size: 0.9rem;
            }

            .footer-content {
                grid-template-columns: 1fr;
            }

            .category-card {
                min-width: 160px;
            }
        }
    </style>
</head>
<body>
<!-- Paper Texture Overlay -->
<div class="paper-texture"></div>

<!-- Library Ambiance -->
<div class="library-ambiance"></div>

<!-- Background Shapes -->
<div class="bg-shapes">
<div class="bg-shape"></div>
<div class="bg-shape"></div>
<div class="bg-shape"></div>
</div>

<!-- Navbar -->
<nav class="navbar">
<div class="container">
<div class="navbar-content">
    <div class="logo">
        <i class="fas fa-book-open"></i>
        <h1>Athena Library</h1>
    </div>
    
    <div class="search-bar">
        <input type="text" id="search-input" placeholder="Search books, authors, or topics...">
        <button class="search-btn" id="search-button">
            <i class="fas fa-search"></i>
        </button>
    </div>
    
    <div class="nav-actions">
        <button class="nav-btn">
            <i class="fas fa-user"></i>
            <a href="login.php">Login</a>
        </button>
                <button class="nav-btn">
                    <i class="fas fa-bookmark"></i>
                    <a href="signup.php">Sign Up</a>
                </button>
        <button class="nav-btn">
            <i class="fas fa-bookmark"></i>
            <a href="contact.php">Contact Us</a>
        </button>
    </div>
</div>
</div>
</nav>

<!-- Hero Section with 3D Effect -->
<section class="hero">
<div class="container">
<div class="hero-content">
    <h2>Discover. Learn. Grow.</h2>
    <p>Explore our vast collection of books and resources to feed your curiosity and expand your horizons.</p>
    <button class="cta-btn">Explore Collection</button>
</div>
</div>
</section>

<!-- Books Section with 3D Book Effect -->


<!-- Featured Categories Section with 3D Hover -->
<section class="section">
<div class="container">
<div class="section-header">
    <h2 class="section-title">Featured Categories</h2>
</div>
<div class="categories categories-container">
    <div class="category-card">
        <i class="fas fa-flask"></i>
        <h3>Science</h3>
    </div>
    <div class="category-card">
        <i class="fas fa-rocket"></i>
        <h3>Fiction</h3>
    </div>
    <div class="category-card">
        <i class="fas fa-landmark"></i>
        <h3>History</h3>
    </div>
    <div class="category-card">
        <i class="fas fa-search"></i>
        <h3>Mystery</h3>
    </div>
    <div class="category-card">
        <i class="fas fa-laptop-code"></i>
        <h3>Technology</h3>
    </div>
</div>
</div>
</section>
<section class="section" id="search-results">
    <div class="container">
    <div class="section-header">
        <h2 class="section-title">Featured Books</h2>
    </div>
    
    <div class="books-grid" id="books-container">
        <!-- Book cards will be dynamically added here -->
        <!-- Example book card for visual reference -->
        <div class="book-card">
            <div class="bookmark-indicator"></div>
            <div class="book-spine"></div>
            <span class="book-tag">New</span>
            <div class="book-img">
                <img src="/api/placeholder/300/400" alt="Book cover">
            </div>
            <div class="book-details">
                <h3 class="book-title">The Knowledge Library</h3>
                <p class="book-author"><i class="fas fa-feather-alt"></i> John Bookman</p>
                <p class="book-description">An insightful journey through the halls of wisdom and knowledge.</p>
                <div class="book-actions">
                    <button class="book-btn icon-btn" onclick="handleSaveClick(event)">
                        <i class="far fa-bookmark"></i>
                    </button>
                    <a href="#" class="book-btn read-more-btn">
                        <i class="fas fa-book-reader"></i> Read More
                    </a>
                </div>
            </div>
            <div class="book-glow"></div>
        </div>
        <!-- End example book card -->
    </div>
    
    <!-- Pagination with Enhanced Style -->
    <div class="pagination">
        <button class="pagination-btn arrow" id="prev-page" disabled>
            <i class="fas fa-chevron-left"></i>
        </button>
        <button class="pagination-btn active">1</button>
        <button class="pagination-btn">2</button>
        <button class="pagination-btn">3</button>
        <button class="pagination-btn">...</button>
        <button class="pagination-btn">10</button>
        <button class="pagination-btn arrow" id="next-page">
            <i class="fas fa-chevron-right"></i>
        </button>
    </div>
    </div>
    </section>
<!-- Features Section with Enhanced Cards -->
<section class="section">
<div class="container">
<div class="section-header">
    <h2 class="section-title">Our Services</h2>
</div>
<div class="features">
    <div class="feature-card">
        <div class="feature-icon">
            <i class="fas fa-mobile-alt"></i>
        </div>
        <h3 class="feature-title">Mobile Access</h3>
        <p>Access our library on the go with our mobile-friendly platform. Read your favorite books anytime, anywhere.</p>
                <button class="primary-btn">Learn More</button>
    </div>
    <div class="feature-card">
        <div class="feature-icon">
            <i class="fas fa-comments"></i>
        </div>
        <h3 class="feature-title">Book Clubs</h3>
        <p>Join our vibrant community of readers. Discuss, debate and discover new perspectives together.</p>
                <button class="primary-btn">Join Now</button>
    </div>
    <div class="feature-card">
        <div class="feature-icon">
            <i class="fas fa-bell"></i>
        </div>
        <h3 class="feature-title">Notifications</h3>
        <p>Get timely reminders about due dates, new arrivals, and personalized book recommendations.</p>
                <button class="primary-btn">Enable</button>
    </div>
</div>
</div>
</section>

<!-- Enhanced Footer -->
<footer>
    <div class="container">
        <div class="footer-content">
            <div class="footer-about">
                <div class="footer-logo">
                    <i class="fas fa-book-open"></i>
                    <h2>Athena Library</h2>
                </div>
                <p>Empowering minds through literature and knowledge since 1995. Our mission is to make learning accessible to everyone.</p>
            </div>
            <div class="footer-links-section">
                <h3 class="footer-title">Quick Links</h3>
                <ul class="footer-links">
                    <li><a href="index.php" class="nav-link">Home</a></li>
                    <li><a href="about.php">About Us</a></li>
                    <li><a href="contact.php">Contact</a></li>
                </ul>
            </div>
            <div class="footer-links-section">
                <h3 class="footer-title">Support</h3>
                <ul class="footer-links">
                    <li><a href="FAQ.html">FAQ</a></li>
                    <li><a href="help.html">Help Center</a></li>
                    <li><a href="terms.html">Terms of Service</a></li>
                    <li><a href="privacy.html">Privacy Policy</a></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2025 Athena Library. All rights reserved. Designed by <a href="#">Prasant Yadav</a></p>
        </div>
    </div>
</footer>

<!-- JavaScript for dynamic content and animations -->
<script>
// Example script to populate books dynamically (replace with your actual implementation)
document.addEventListener('DOMContentLoaded', function() {
const booksContainer = document.getElementById('books-container');

// Clear any example content
booksContainer.innerHTML = '';

// Sample book data (replace with your actual data)
const books = [
    {
        title: "The Art of Reading",
        author: "Sarah Johnson",
        description: "Discover the transformative power of literature and enhance your reading experience.",
        tag: "Featured",
        image: "/api/placeholder/300/400"
    },
    {
        title: "Digital Revolution",
        author: "Michael Chen",
        description: "An exploration of how technology is reshaping our world and future possibilities.",
        tag: "New",
        image: "/api/placeholder/300/400"
    },
    {
        title: "Mysteries of the Universe",
        author: "Elena Rodriguez",
        description: "Journey through space and time to uncover the secrets of our cosmos.",
        tag: "Popular",
        image: "/api/placeholder/300/400"
    },
    {
        title: "Hidden Histories",
        author: "James Wilson",
        description: "Untold stories and forgotten events that shaped our modern world.",
        tag: "History",
        image: "/api/placeholder/300/400"
    }
];

// Generate book cards
books.forEach(book => {
    const bookCard = document.createElement('div');
    bookCard.className = 'book-card';
    
    bookCard.innerHTML = `
        <div class="bookmark-indicator"></div>
        <div class="book-spine"></div>
        <span class="book-tag">${book.tag}</span>
        <div class="book-img">
            <img src="${book.image}" alt="${book.title} cover">
        </div>
        <div class="book-details">
            <h3 class="book-title">${book.title}</h3>
            <p class="book-author"><i class="fas fa-feather-alt"></i> ${book.author}</p>
            <p class="book-description">${book.description}</p>
            
            </div>
        </div>
        <div class="book-glow"></div>
    `;
    
    booksContainer.appendChild(bookCard);
});

// Category card click effect
const categoryCards = document.querySelectorAll('.category-card');
categoryCards.forEach(card => {
    card.addEventListener('click', function() {
        // Remove active class from all cards
        categoryCards.forEach(c => c.classList.remove('active-category'));
        // Add active class to clicked card
        this.classList.add('active-category');
    });
});

// Pagination functionality
const paginationBtns = document.querySelectorAll('.pagination-btn:not(.arrow)');
paginationBtns.forEach(btn => {
    btn.addEventListener('click', function() {
        paginationBtns.forEach(b => b.classList.remove('active'));
        this.classList.add('active');
    });
});

// Enable/disable pagination arrows based on current page
function updatePaginationArrows() {
    const currentPage = document.querySelector('.pagination-btn.active').textContent;
    const prevBtn = document.getElementById('prev-page');
    const nextBtn = document.getElementById('next-page');
    
    prevBtn.disabled = currentPage === '1';
    nextBtn.disabled = currentPage === '10';
}

updatePaginationArrows();

// Handle pagination arrow clicks
document.getElementById('prev-page').addEventListener('click', function() {
    if(!this.disabled) {
        const activePage = document.querySelector('.pagination-btn.active');
        if(activePage.previousElementSibling && !activePage.previousElementSibling.classList.contains('arrow')) {
            activePage.classList.remove('active');
            activePage.previousElementSibling.classList.add('active');
            updatePaginationArrows();
        }
    }
});

document.getElementById('next-page').addEventListener('click', function() {
    if(!this.disabled) {
        const activePage = document.querySelector('.pagination-btn.active');
        if(activePage.nextElementSibling && !activePage.nextElementSibling.classList.contains('arrow')) {
            activePage.classList.remove('active');
            activePage.nextElementSibling.classList.add('active');
            updatePaginationArrows();
        }
    }
});

// Search functionality
document.getElementById('search-button').addEventListener('click', function() {
    const searchInput = document.getElementById('search-input').value.trim().toLowerCase();
    
    if(searchInput) {
        // In a real implementation, this would trigger an API call or database query
        alert('Searching for: ' + searchInput);
    }
});

// Enable search on enter key
document.getElementById('search-input').addEventListener('keypress', function(e) {
    if(e.key === 'Enter') {
        document.getElementById('search-button').click();
    }
});

// Smooth scrolling for all anchor links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function(e) {
        e.preventDefault();
        const targetId = this.getAttribute('href');
        if(targetId !== '#') {
            document.querySelector(targetId).scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});

// Function to handle save button click
function handleSaveClick(event) {
    event.preventDefault();
    alert('Please login to save books. Redirecting to login page...');
    window.location.href = 'login.php';
}

// Function to display books
function displayBooks(books) {
    const booksContainer = document.getElementById('books-container');
    booksContainer.innerHTML = '';

    books.forEach(book => {
        const volumeInfo = book.volumeInfo;
        const imageLinks = volumeInfo.imageLinks || {};
        const thumbnail = imageLinks.thumbnail || 'assets/default-book.jpg';
        const title = volumeInfo.title || 'Unknown Title';
        const authors = volumeInfo.authors?.join(', ') || 'Unknown Author';
        const description = volumeInfo.description?.substring(0, 150) + '...' || 'No description available';
        const infoLink = volumeInfo.infoLink || '#';

        const bookCard = document.createElement('div');
        bookCard.className = 'book-card';
        bookCard.innerHTML = `
            <div class="bookmark-indicator"></div>
            <div class="book-spine"></div>
            <div class="book-img">
                <img src="${thumbnail}" alt="${title}">
            </div>
            <div class="book-details">
                <h3 class="book-title">${title}</h3>
                <p class="book-author"><i class="fas fa-feather-alt"></i> ${authors}</p>
                <p class="book-description">${description}</p>
                <div class="book-actions">
                    <button class="book-btn icon-btn" onclick="handleSaveClick(event)">
                        <i class="far fa-bookmark"></i>
                    </button>
                    <a href="${infoLink}" target="_blank" class="book-btn read-more-btn">
                        <i class="fas fa-book-reader"></i> Read More
                    </a>
                </div>
            </div>
            <div class="book-glow"></div>
        `;

        booksContainer.appendChild(bookCard);
    });
}
});
</script>
</body>
</html>







<script src="js/script.js"></script>
