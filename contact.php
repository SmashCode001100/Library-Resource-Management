<?php
session_start();

// Initialize variables
$name = $email = $subject = $message = '';
$formError = false;
$errorMessage = '';
$successMessage = '';
$formSubmitted = false; // Add this line to define the missing variable

// Form submission handler
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    // Set formSubmitted to true when form is submitted
    $formSubmitted = true;
    
    // Sanitize and validate inputs
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $subject = filter_input(INPUT_POST, 'subject', FILTER_SANITIZE_STRING);
    $message = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_STRING);
    
    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $formError = true;
        $errorMessage = 'Please enter a valid email address.';
    }
    // Validate other fields are not empty
    elseif (empty($name) || empty($subject) || empty($message)) {
        $formError = true;
        $errorMessage = 'All fields are required.';
    }
    // All validations passed
    else {
        // Recipient email address
        $to = 'prasantyadav960@gmail.com'; // Your email address
        $emailSubject = 'New Message from Athena Library Contact Form: ' . $subject;
        $headers = "From: " . $email . "\r\n";
        $headers .= "Reply-To: " . $email . "\r\n";
        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
        
        $body = "Name: " . $name . "\n";
        $body .= "Email: " . $email . "\n";
        $body .= "Subject: " . $subject . "\n";
        $body .= "Message:\n" . $message . "\n";
        
        // Send the email
        if (mail($to, $emailSubject, $body, $headers)) {
            // Email sent successfully
            $successMessage = 'Thank you for your message! We will get back to you soon.';
            // Clear form fields after successful submission
            $name = $email = $subject = $message = '';
        } else {
            // Error sending email
            $formError = true;
            $errorMessage = 'There was an error sending your message. Please try again later.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - Athena Library Management System</title>
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

        html, body {
            height: 100%;
            overflow-x: hidden;
            scroll-behavior: smooth;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--cream);
            color: var(--dark);
            line-height: 1.6;
            display: flex;
            flex-direction: column;
            overflow-y: auto;
            min-height: 100vh;
        }

        /* Scrollbar styling */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        ::-webkit-scrollbar-thumb {
            background: var(--primary);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--secondary);
        }

        /* Header Styles */
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

        .nav-link.active {
            opacity: 1;
            font-weight: 600;
        }

        /* Main Content */
        .main-content {
            flex: 1;
            padding: 3rem 1rem;
        }

        .page-header {
            text-align: center;
            margin-bottom: 2rem;
            animation: fadeInDown 1s;
        }

        .page-title {
            font-size: 2.5rem;
            color: var(--dark);
            margin-bottom: 0.5rem;
            font-weight: 700;
        }

        .page-subtitle {
            font-size: 1.1rem;
            color: var(--neutral);
            max-width: 700px;
            margin: 0 auto;
        }

        .contact-container {
            display: flex;
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: var(--shadow);
            animation: fadeInUp 1s;
        }

        .contact-info {
            flex: 1;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            padding: 3rem;
            position: relative;
        }

        .contact-info::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('https://source.unsplash.com/random/800x600/?library') center/cover;
            opacity: 0.1;
        }

        .contact-info-content {
            position: relative;
            z-index: 2;
        }

        .contact-info-title {
            font-size: 1.8rem;
            margin-bottom: 1.5rem;
            font-weight: 600;
        }

        .contact-info-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 2rem;
            transition: var(--transition);
        }

        .contact-info-item:hover {
            transform: translateX(10px);
        }

        .contact-info-item i {
            font-size: 1.5rem;
            margin-right: 1rem;
            margin-top: 0.3rem;
            color: var(--accent);
        }

        .contact-info-text h3 {
            font-size: 1.2rem;
            margin-bottom: 0.5rem;
        }

        .contact-info-text p {
            opacity: 0.9;
            line-height: 1.6;
        }

        .social-links {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
        }

        .social-link {
            width: 40px;
            height: 40px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: var(--transition);
            color: white;
            text-decoration: none;
        }

        .social-link:hover {
            background: white;
            color: var(--primary);
            transform: translateY(-3px);
        }

        .contact-form-container {
            flex: 1.5;
            padding: 3rem;
            animation: slideInRight 0.8s ease-out;
        }

        .contact-form-title {
            font-size: 1.8rem;
            margin-bottom: 0.5rem;
            color: var(--dark);
            font-weight: 600;
        }

        .contact-form-subtitle {
            color: var(--neutral);
            margin-bottom: 2rem;
        }

        /* Form Styles */
        .form-group {
            margin-bottom: 1.5rem;
            position: relative;
            animation: fadeIn 0.5s ease-out;
            animation-fill-mode: both;
        }

        .form-group:nth-child(1) { animation-delay: 0.1s; }
        .form-group:nth-child(2) { animation-delay: 0.2s; }
        .form-group:nth-child(3) { animation-delay: 0.3s; }
        .form-group:nth-child(4) { animation-delay: 0.4s; }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: var(--dark);
            transition: var(--transition);
        }

        .form-input {
            width: 100%;
            padding: 1rem 1.2rem;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            transition: var(--transition);
            font-size: 1rem;
            background: white;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }

        .form-input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(44, 62, 80, 0.1);
            outline: none;
            transform: translateY(-2px);
        }

        .form-input::placeholder {
            color: var(--neutral);
            opacity: 0.7;
        }

        textarea.form-input {
            resize: vertical;
            min-height: 150px;
            padding: 1.2rem;
        }

        /* Submit Button Styles */
        .submit-btn {
            padding: 1rem 2.5rem;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            border: none;
            border-radius: 50px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            gap: 0.8rem;
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(44, 62, 80, 0.2);
            margin-top: 1rem;
        }

        .submit-btn i {
            transition: var(--transition);
        }

        .submit-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(44, 62, 80, 0.3);
        }

        .submit-btn:hover i {
            transform: translateX(5px);
        }

        .submit-btn:active {
            transform: translateY(0);
            box-shadow: 0 4px 15px rgba(44, 62, 80, 0.2);
        }

        .submit-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: var(--transition);
        }

        .submit-btn:hover::before {
            left: 100%;
        }

        .submit-btn:disabled {
            background: var(--neutral);
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        .submit-btn:disabled:hover {
            transform: none;
            box-shadow: none;
        }

        .submit-btn:disabled i {
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        /* Map section */
        .map-section {
            margin-top: 3rem;
            animation: fadeInUp 1s;
        }

        .map-container {
            height: 400px;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: var(--shadow);
        }

        .map-container iframe {
            width: 100%;
            height: 100%;
            border: 0;
        }

        /* Alert Messages */
        .alert {
            padding: 1rem;
            border-radius: 5px;
            margin-bottom: 1.5rem;
            font-size: 0.95rem;
            animation: fadeIn 0.5s;
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        /* Footer */
        footer {
            background: var(--dark);
            color: white;
            padding: 1.5rem 0;
            text-align: center;
        }

        .footer-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .footer-links {
            display: flex;
            gap: 1.5rem;
        }

        .footer-link {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            transition: var(--transition);
            font-size: 0.9rem;
        }

        .footer-link:hover {
            color: white;
        }

        .copyright {
            font-size: 0.9rem;
            opacity: 0.8;
        }

        /* Animations */
        .animate-fade-in {
            animation: fadeIn 1s;
        }

        .animate-slide-up {
            animation: slideUp 0.8s;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

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

        /* FAQ Section */
        .faq-section {
            margin-top: 3rem;
            background: white;
            border-radius: 20px;
            padding: 2rem;
            box-shadow: var(--shadow);
            animation: fadeInUp 1s;
        }

        .faq-title {
            font-size: 1.8rem;
            margin-bottom: 1.5rem;
            text-align: center;
            color: var(--dark);
        }

        .faq-container {
            max-width: 800px;
            margin: 0 auto;
        }

        .faq-item {
            margin-bottom: 1rem;
            border: 1px solid #e0e0e0;
            border-radius: 10px;
            overflow: hidden;
            transition: var(--transition);
        }

        .faq-item:hover {
            box-shadow: var(--shadow);
        }

        .faq-question {
            padding: 1rem;
            background: var(--light);
            font-weight: 600;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: var(--transition);
        }

        .faq-question:hover {
            background: #e9ecef;
        }

        .faq-answer {
            padding: 0;
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease, padding 0.3s ease;
        }

        .faq-answer.active {
            padding: 1rem;
            max-height: 300px;
        }

        .faq-toggle {
            transition: transform 0.3s ease;
        }

        .faq-toggle.active {
            transform: rotate(180deg);
        }

        /* Responsive Design */
        @media (max-width: 992px) {
            .contact-container {
                flex-direction: column;
            }
            
            .contact-info, .contact-form-container {
                width: 100%;
            }
        }

        @media (max-width: 768px) {
            .nav-links {
                display: none;
            }
            
            .contact-form-container, .contact-info {
                padding: 2rem;
            }
            
            .page-title {
                font-size: 2rem;
            }
        }

        @media (max-width: 576px) {
            .footer-content {
                flex-direction: column;
                gap: 1rem;
            }
            
            .contact-info-item {
                flex-direction: column;
            }
            
            .contact-info-item i {
                margin-bottom: 0.5rem;
            }
            
            .main-content {
                padding: 2rem 1rem;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <div class="container">
            <div class="navbar-content">
                <div class="logo">
                    <i class="fas fa-book-open"></i>
                    <h1>Athena Library</h1>
                </div>
                
                <div class="nav-links">
                    <a href="<?php echo isset($_SESSION['user_id']) ? 'dashboard.php' : 'index.php'; ?>" class="nav-link">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                    <a href="index.php" class="nav-link">Home</a>
                    <a href="about.php" class="nav-link">About</a>
                    <a href="login.php" class="nav-link">Login</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="main-content">
        <div class="container">
            <!-- Page Header -->
            <div class="page-header animate-fade-in">
                <h1 class="page-title">Contact Us</h1>
                <p class="page-subtitle">We'd love to hear from you! Feel free to reach out with any questions, feedback, or inquiries about our library services.</p>
            </div>
            
            <!-- Contact Section -->
            <div class="contact-container animate-slide-up">
                <div class="contact-info">
                    <div class="contact-info-content">
                        <h2 class="contact-info-title">Get in Touch</h2>
                        
                        <div class="contact-info-item">
                            <i class="fas fa-map-marker-alt"></i>
                            <div class="contact-info-text">
                                <h3>Visit Us</h3>
                                <p>Jalandhar - Delhi, Grand Trunk Rd<br> Phagwara, Punjab 144411</p>
                            </div>
                        </div>
                        
                        <div class="contact-info-item">
                            <i class="fas fa-phone-alt"></i>
                            <div class="contact-info-text">
                                <h3>Call Us</h3>
                                <p>Main Line: (555) 123-4567<br>Reference Desk: (555) 123-4568</p>
                            </div>
                        </div>
                        
                        <div class="contact-info-item">
                            <i class="fas fa-envelope"></i>
                            <div class="contact-info-text">
                                <h3>Email Us</h3>
                                <p>info@athenalibrary.org<br>support@athenalibrary.org</p>
                            </div>
                        </div>
                        
                        <div class="contact-info-item">
                            <i class="fas fa-clock"></i>
                            <div class="contact-info-text">
                                <h3>Opening Hours</h3>
                                <p>Monday - Friday: 8:00 AM - 8:00 PM<br>Saturday: 10:00 AM - 6:00 PM<br>Sunday: 12:00 PM - 5:00 PM</p>
                            </div>
                        </div>
                        
                        <div class="social-links">
                            <a href="#" class="social-link"><i class="fab fa-facebook-f"></i></a>
                            <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
                            <a href="#" class="social-link"><i class="fab fa-linkedin-in"></i></a>
                        </div>
                    </div>
                </div>
                
                <div class="contact-form-container">
                    <h2 class="contact-form-title">Send a Message</h2>

                    <p class="contact-form-subtitle">Fill out the form below and we'll get back to you as soon as possible.</p>
                    
                    <?php if ($formError): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle"></i> <?php echo $errorMessage; ?>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($formSubmitted && !$formError): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i> <?php echo $successMessage; ?>
                    </div>
                    <?php endif; ?>
                    
                    <form action="https://api.web3forms.com/submit" method="POST">

<!-- Replace with your Access Key -->
<input type="hidden" name="access_key" value="5fdf458d-064d-4329-9693-e89cc78b8b1b">

<!-- Form Inputs. Each input must have a name="" attribute -->
<div class="form-group">
    <label for="name" class="form-label">Your Name</label>
    <input type="text" id="name" name="name" class="form-input" placeholder="Enter your full name" required value="<?php echo htmlspecialchars($name); ?>">
</div>

<div class="form-group">
    <label for="email" class="form-label">Email Address</label>
    <input type="email" id="email" name="email" class="form-input" placeholder="Enter your email address" required value="<?php echo htmlspecialchars($email); ?>">
</div>

<div class="form-group">
    <label for="contact" class="form-label">Contact</label>
    <input type="number" id="contactc" name="contact" class="form-input" placeholder="Enter your contact" required value="<?php echo htmlspecialchars($contact); ?>">
</div>



<div class="form-group">
    <label for="subject" class="form-label">Subject</label>
    <input type="text" id="subject" name="subject" class="form-input" placeholder="What is this regarding?" required value="<?php echo htmlspecialchars($subject); ?>">
</div>

<div class="form-group">
    <label for="message" class="form-label">Message</label>
    <textarea id="message" name="message" class="form-input" placeholder="Write your message here..." required><?php echo htmlspecialchars($message); ?></textarea>
</div>

<!-- Honeypot Spam Protection -->
<input type="checkbox" name="botcheck" class="hidden" style="display: none;">

<!-- Custom Confirmation / Success Page -->
<!-- <input type="hidden" name="redirect" value="https://mywebsite.com/thanks.html"> -->

<button type="submit">
    <i class="fas fa-paper-plane"></i> Send Message
</button>

</form>

                </div>
            </div>
            
            <!-- Map Section -->
            <div class="map-section animate-fade-in">
                <div class="map-container">
                    <!-- Replace with your actual Google Maps embed code -->
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d28447.03267999674!2d75.70299918456013!3d31.247666073717212!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x391a5f5e9c489cf3%3A0x4049a5409d53c300!2sLovely%20Professional%20University!5e0!3m2!1sen!2sin!4v1744629016199!5m2!1sen!2sin" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>                </div>
            </div>
            
            <!-- FAQ Section -->
            <div class="faq-section animate-slide-up">
                <h2 class="faq-title">Frequently Asked Questions</h2>
                <div class="faq-container">
                    <div class="faq-item">
                        <div class="faq-question" onclick="toggleFAQ(this)">
                            <span>How do I get a library card?</span>
                            <i class="fas fa-chevron-down faq-toggle"></i>
                        </div>
                        <div class="faq-answer">
                            <p>To get a library card, please visit our main desk with a valid photo ID and proof of address. If you're a student, you can also bring your student ID. The process takes about 5 minutes, and your card will be valid for 3 years.</p>
                        </div>
                    </div>
                    
                    <div class="faq-item">
                        <div class="faq-question" onclick="toggleFAQ(this)">
                            <span>What is the late fee for overdue books?</span>
                            <i class="fas fa-chevron-down faq-toggle"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Late fees are $0.25 per day per item for most materials, with a maximum late fee of $10 per item. Special collections may have different rates. You can renew most items online to avoid late fees.</p>
                        </div>
                    </div>
                    
                    <div class="faq-item">
                        <div class="faq-question" onclick="toggleFAQ(this)">
                            <span>How many books can I check out at once?</span>
                            <i class="fas fa-chevron-down faq-toggle"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Standard library cards allow up to 20 items to be checked out at one time. Premium memberships allow up to 30 items. Digital resources like e-books and audiobooks have separate limits.</p>
                        </div>
                    </div>
                    
                    <div class="faq-item">
                        <div class="faq-question" onclick="toggleFAQ(this)">
                            <span>Do you offer inter-library loans?</span>
                            <i class="fas fa-chevron-down faq-toggle"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Yes, we participate in the national inter-library loan program. If we don't have a book you need, we can request it from another library. This service may take 1-2 weeks, and a small fee may apply for shipping.</p>
                        </div>
                    </div>
                    
                    <div class="faq-item">
                        <div class="faq-question" onclick="toggleFAQ(this)">
                            <span>Can I reserve a study room?</span>
                            <i class="fas fa-chevron-down faq-toggle"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Yes, we have study rooms available for reservation. You can book them online through your library account or by calling our front desk. Rooms can be reserved up to 2 weeks in advance for up to 3 hours per day.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-links">
                    <a href="privacy.html" class="footer-link">Privacy Policy</a>
                    <a href="terms.html" class="footer-link">Terms of Service</a>
                    <a href="help.html" class="footer-link">Help Center</a>
                </div>
                
                <div class="copyright">
                    <p>&copy; <?php echo date('Y'); ?> Athena Library. All rights reserved.</p>
                </div>
            </div>
        </div>
    </footer>

    <script>
        // Toggle FAQ answers
        function toggleFAQ(element) {
            const answer = element.nextElementSibling;
            const icon = element.querySelector('.faq-toggle');
            
            // Toggle active class
            answer.classList.toggle('active');
            icon.classList.toggle('active');
        }
        
        // Form validation and animation
        document.addEventListener('DOMContentLoaded', function() {
            const formInputs = document.querySelectorAll('.form-input');
            
            formInputs.forEach(input => {
                input.addEventListener('focus', function() {
                    this.style.transform = 'translateX(5px)';
                    this.style.borderColor = 'var(--primary)';
                });
                
                input.addEventListener('blur', function() {
                    this.style.transform = 'translateX(0)';
                    if (!this.value) {
                        this.style.borderColor = '#e0e0e0';
                    }
                });
            });
            
            // Form validation
            const contactForm = document.getElementById('contact-form');
            
            if (contactForm) {
                contactForm.addEventListener('submit', function(e) {
                    let isValid = true;
                    const nameInput = document.getElementById('name');
                    const emailInput = document.getElementById('email');
                    const subjectInput = document.getElementById('subject');
                    const messageInput = document.getElementById('message');
                    
                    // Simple validation
                    if (!nameInput.value.trim()) {
                        isValid = false;
                        nameInput.style.borderColor = 'var(--danger)';
                    }
                    
                    if (!emailInput.value.trim()) {
                        isValid = false;
                        emailInput.style.borderColor = 'var(--danger)';
                    } else {
                        // Basic email validation
                        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                        if (!emailPattern.test(emailInput.value)) {
                            isValid = false;
                            emailInput.style.borderColor = 'var(--danger)';
                        }
                    }
                    
                    if (!subjectInput.value.trim()) {
                        isValid = false;
                        subjectInput.style.borderColor = 'var(--danger)';
                    }
                    
                    if (!messageInput.value.trim()) {
                        isValid = false;
                        messageInput.style.borderColor = 'var(--danger)';
                    }
                    
                    if (!isValid) {
                        e.preventDefault();
                    } else {
                        // Show loading state
                        const submitBtn = document.querySelector('.submit-btn');
                        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
                        submitBtn.disabled = true;
                    }
                });
            }
        });
    </script>
</body>
</html>