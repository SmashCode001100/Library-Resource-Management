<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About - Athena Library</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <style>
        :root {
            --primary: #2c3e50;
            --secondary: #1a2a3a;
            --accent: #e74c3c;
            --light: #f8f9fa;
            --cream: #f9f5eb;
            --dark: #2c3e50;
            --success: #27ae60;
            --neutral: #7f8c8d;
            --shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--cream);
            color: var(--dark);
            line-height: 1.6;
            overflow-x: hidden;
        }

        .navbar {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            padding: 1rem 0;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: var(--shadow);
        }

        .container {
            width: 90%;
            max-width: 1200px;
            margin: 0 auto;
        }

        .navbar-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 0.8rem;
            cursor: pointer;
            text-decoration: none;
            color: white;
        }

        .logo i {
            font-size: 2rem;
            transition: var(--transition);
        }

        .logo:hover i {
            transform: rotate(15deg);
        }

        .logo h1 {
            font-size: 1.8rem;
            font-weight: 600;
        }

        .nav-links {
            display: flex;
            gap: 2rem;
        }

        .nav-link {
            color: white;
            text-decoration: none;
            font-weight: 500;
            transition: var(--transition);
            opacity: 0.9;
            position: relative;
            padding: 0.5rem 0;
        }

        .nav-link::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 2px;
            background: white;
            transition: var(--transition);
        }

        .nav-link:hover {
            opacity: 1;
        }

        .nav-link:hover::after {
            width: 100%;
        }

        .hero {
            min-height: 80vh;
            background: linear-gradient(rgba(44, 62, 80, 0.8), rgba(26, 42, 58, 0.8)),
                        url('https://source.unsplash.com/random/1920x1080/?library') center/cover;
            display: flex;
            align-items: center;
            position: relative;
            overflow: hidden;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(45deg, rgba(44, 62, 80, 0.9), rgba(26, 42, 58, 0.9));
            z-index: 1;
        }

        .hero-content {
            position: relative;
            z-index: 2;
            max-width: 800px;
            margin: 0 auto;
            padding: 2rem;
            text-align: center;
            color: white;
        }

        .hero h1 {
            font-size: 4rem;
            margin-bottom: 1.5rem;
            font-weight: 700;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
            animation: fadeInDown 1s;
        }

        .hero p {
            font-size: 1.4rem;
            opacity: 0.9;
            margin-bottom: 2rem;
            animation: fadeInUp 1s;
        }

        .about-section {
            padding: 5rem 0;
            background: white;
        }

        .about-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-top: 3rem;
        }

        .about-card {
            background: white;
            padding: 2.5rem;
            border-radius: 20px;
            box-shadow: var(--shadow);
            transition: var(--transition);
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .about-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: var(--primary);
            transform: scaleX(0);
            transition: var(--transition);
        }

        .about-card:hover::before {
            transform: scaleX(1);
        }

        .about-card:hover {
            transform: translateY(-10px);
        }

        .about-card i {
            font-size: 3.5rem;
            color: var(--primary);
            margin-bottom: 1.5rem;
            transition: var(--transition);
        }

        .about-card:hover i {
            transform: scale(1.1);
        }

        .about-card h3 {
            font-size: 1.8rem;
            margin-bottom: 1rem;
            color: var(--primary);
        }

        .about-card p {
            color: var(--neutral);
            font-size: 1.1rem;
        }

        .profile-section {
            padding: 5rem 0;
            background: var(--light);
        }

        .profile-container {
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: 3rem;
            align-items: center;
        }

        .profile-image {
            position: relative;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: var(--shadow);
            transition: var(--transition);
        }

        .profile-image:hover {
            transform: scale(1.02);
        }

        .profile-image img {
            width: 100%;
            height: auto;
            display: block;
        }

        .profile-content {
            padding: 2rem;
        }

        .profile-content h2 {
            font-size: 2.8rem;
            margin-bottom: 1.5rem;
            color: var(--primary);
            position: relative;
            display: inline-block;
        }

        .profile-content h2::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 0;
            width: 60px;
            height: 4px;
            background: var(--accent);
            border-radius: 2px;
        }

        .profile-content p {
            margin-bottom: 1.5rem;
            color: var(--neutral);
            font-size: 1.1rem;
        }

        .skills {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            margin-top: 2rem;
        }

        .skill-tag {
            background: var(--primary);
            color: white;
            padding: 0.8rem 1.5rem;
            border-radius: 30px;
            font-size: 1rem;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .skill-tag i {
            font-size: 1.2rem;
        }

        .skill-tag:hover {
            background: var(--secondary);
            transform: translateY(-2px);
        }

        .contact-section {
            padding: 5rem 0;
            background: white;
            text-align: center;
        }

        .contact-section h2 {
            font-size: 2.8rem;
            margin-bottom: 1.5rem;
            color: var(--primary);
        }

        .contact-section p {
            font-size: 1.2rem;
            color: var(--neutral);
            margin-bottom: 2rem;
        }

        .contact-links {
            display: flex;
            justify-content: center;
            gap: 2rem;
            margin-top: 2rem;
        }

        .contact-link {
            color: var(--primary);
            font-size: 2.5rem;
            transition: var(--transition);
            position: relative;
        }

        .contact-link::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 0;
            height: 2px;
            background: var(--accent);
            transition: var(--transition);
        }

        .contact-link:hover {
            color: var(--accent);
            transform: translateY(-3px);
        }

        .contact-link:hover::after {
            width: 100%;
        }

        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
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

        @media (max-width: 992px) {
            .profile-container {
                grid-template-columns: 1fr;
            }

            .hero h1 {
                font-size: 3rem;
            }

            .profile-content h2 {
                font-size: 2.3rem;
            }
        }

        @media (max-width: 768px) {
            .nav-links {
                display: none;
            }

            .about-grid {
                grid-template-columns: 1fr;
            }

            .hero h1 {
                font-size: 2.5rem;
            }

            .hero p {
                font-size: 1.2rem;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <div class="container">
            <div class="navbar-content">
                <a href="index.php" class="logo">
                    <i class="fas fa-book-open"></i>
                    <h1>Athena Library</h1>
                </a>
                
                <div class="nav-links">
                    <a href="<?php echo isset($_SESSION['user_id']) ? 'dashboard.php' : 'index.php'; ?>" class="nav-link">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                    <a href="index.php" class="nav-link">Home</a>
                    <a href="about.php" class="nav-link">About</a>
                    <a href="contact.php" class="nav-link">Contact</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <h1>About Me</h1>
            <p>Passionate about books, technology, and creating meaningful experiences for readers</p>
        </div>
    </section>

    <!-- About Section -->
    <section class="about-section">
        <div class="container">
            <div class="about-grid">
                <div class="about-card">
                    <i class="fas fa-book-reader"></i>
                    <h3>Book Enthusiast</h3>
                    <p>With a deep love for literature and a passion for sharing knowledge, I've dedicated myself to creating a welcoming space for readers of all kinds.</p>
                </div>
                <div class="about-card">
                    <i class="fas fa-laptop-code"></i>
                    <h3>Tech Innovator</h3>
                    <p>Combining my technical expertise with my love for books to create innovative solutions that enhance the reading experience.</p>
                </div>
                <div class="about-card">
                    <i class="fas fa-users"></i>
                    <h3>Community Builder</h3>
                    <p>Committed to fostering a vibrant community of readers and learners through engaging events and meaningful connections.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Profile Section -->
    <section class="profile-section">
        <div class="container">
            <div class="profile-container">
                <div class="profile-image">
                <img src="css/IMG_7383.jpg" alt="Profile Picture" class="profile-image">                </div>
                <div class="profile-content">
                    <h2>Hello, I'm Prasant Yadav</h2>
                    <p>I'm a passionate librarian and technology enthusiast with over 2 years of experience in the field. My journey in the world of books began when I was a child, and it has shaped my career and personal growth ever since.</p>
                    <p>I believe that libraries are more than just repositories of books – they are vibrant community spaces that foster learning, creativity, and connection. Through Athena Library, I aim to create an inclusive environment where readers of all ages and backgrounds can discover the joy of reading.</p>
                    <p>When I'm not curating our collection or organizing community events, you can find me exploring new technologies that can enhance the library experience or diving into my latest book obsession.</p>
                    
                    <div class="skills">
                        <span class="skill-tag"><i class="fas fa-book"></i> Library Management</span>
                        <span class="skill-tag"><i class="fas fa-laptop"></i> Digital Literacy</span>
                        <span class="skill-tag"><i class="fas fa-users"></i> Community Engagement</span>
                        <span class="skill-tag"><i class="fas fa-code"></i> Technology Integration</span>
                        <span class="skill-tag"><i class="fas fa-calendar"></i> Event Planning</span>
                        <span class="skill-tag"><i class="fas fa-search"></i> Research</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section class="contact-section">
        <div class="container">
            <h2>Let's Connect</h2>
            <p>I'm always excited to meet fellow book lovers and discuss new ideas.</p>
            <div class="contact-links">
                <a href="https://www.linkedin.com/in/prasant-yadav-055390284/" class="contact-link"><i class="fab fa-linkedin"></i></a>
                <a href="https://twitter.com/PrasantYadav10" class="contact-link"><i class="fab fa-twitter"></i></a>
                <a href="https://github.com/SmashCode001100" class="contact-link"><i class="fab fa-github"></i></a>
                <a href="mailto:prasantyadav10@gmail.com" class="contact-link"><i class="fas fa-envelope"></i></a>
            </div>
        </div>
    </section>

    <script>
        // Add animation to elements when they come into view
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = 1;
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        });

        document.querySelectorAll('.about-card, .profile-container, .contact-section').forEach((el) => {
            el.style.opacity = 0;
            el.style.transform = 'translateY(20px)';
            el.style.transition = 'all 0.6s ease-out';
            observer.observe(el);
        });
    </script>
</body>
</html> 