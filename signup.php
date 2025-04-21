<?php
session_start();

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if user is already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

// Generate CAPTCHA if not already set
if (!isset($_SESSION['captcha'])) {
    $_SESSION['captcha'] = rand(1000, 9999);
}

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data with proper validation
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $captcha = $_POST['captcha'] ?? '';

    // Debug information
    error_log("Submitted CAPTCHA: " . $captcha);
    error_log("Session CAPTCHA: " . $_SESSION['captcha']);

    // Validate required fields
    if (empty($username)) {
        $error = "Username is required.";
    } elseif (empty($email)) {
        $error = "Email is required.";
    } elseif (empty($password)) {
        $error = "Password is required.";
    } elseif (empty($confirm_password)) {
        $error = "Please confirm your password.";
    } elseif (empty($captcha)) {
        $error = "CAPTCHA is required.";
    } elseif (!isset($_SESSION['captcha']) || $captcha != $_SESSION['captcha']) {
        $error = "Invalid CAPTCHA. Please try again.";
        // Generate new CAPTCHA after failed attempt
        $_SESSION['captcha'] = rand(1000, 9999);
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } elseif (strlen($password) < 8) {
        $error = "Password must be at least 8 characters long.";
    } else {
        try {
            // Database connection
            $conn = new mysqli("localhost", "root", "", "athena_library");
            
            if ($conn->connect_error) {
                throw new Exception("Connection failed: " . $conn->connect_error);
            }

            // Check if username or email already exists
            $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $conn->error);
            }
            
            $stmt->bind_param("ss", $username, $email);
            if (!$stmt->execute()) {
                throw new Exception("Execute failed: " . $stmt->error);
            }
            
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $error = "Username or email already exists.";
            } else {
                // Hash password
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                
                // Insert new user
                $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
                if (!$stmt) {
                    throw new Exception("Prepare failed: " . $conn->error);
                }
                
                $stmt->bind_param("sss", $username, $email, $hashed_password);
                if (!$stmt->execute()) {
                    throw new Exception("Execute failed: " . $stmt->error);
                }
                
                // Get the last inserted ID
                $user_id = $conn->insert_id;
                
                // Get the created_at timestamp
                $created_at_query = "SELECT created_at FROM users WHERE id = ?";
                $created_at_stmt = $conn->prepare($created_at_query);
                $created_at_stmt->bind_param("i", $user_id);
                $created_at_stmt->execute();
                $created_at_result = $created_at_stmt->get_result();
                $created_at = $created_at_result->fetch_assoc()['created_at'];
                
                $_SESSION['user_id'] = $user_id;
                $_SESSION['username'] = $username;
                $_SESSION['created_at'] = $created_at;
                
                // Clear CAPTCHA after successful signup
                unset($_SESSION['captcha']);
                
                header("Location: dashboard.php");
                exit();
            }
            
            $stmt->close();
            $conn->close();
            
        } catch (Exception $e) {
            error_log("Signup error: " . $e->getMessage());
            $error = "An error occurred. Please try again later.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - Athena Library</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #9b59b6;
            --secondary: #8e44ad;
            --accent: #e67e22;
            --light: #f5eef8;
            --dark: #2c3e50;
            --success: #2ecc71;
            --neutral: #7f8c8d;
            --shadow-sm: 0 2px 4px rgba(0, 0, 0, 0.1);
            --shadow-md: 0 4px 6px rgba(0, 0, 0, 0.1);
            --shadow-lg: 0 10px 15px rgba(0, 0, 0, 0.1);
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            min-height: 100vh;
            display: flex;
            background: var(--light);
            color: var(--dark);
            line-height: 1.6;
        }

        .signup-container {
            width: 100%;
            min-height: 100vh;
            display: flex;
            position: relative;
            overflow: hidden;
        }

        .signup-background {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: 
                linear-gradient(45deg, rgba(155, 89, 182, 0.1), rgba(230, 126, 34, 0.1)),
                url('https://source.unsplash.com/random/1920x1080/?books') center/cover;
            filter: blur(8px);
            transform: scale(1.1);
        }

        .signup-content {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 2rem;
            position: relative;
            z-index: 1;
        }

        .signup-card {
            background: rgba(255, 255, 255, 0.95);
            padding: 3rem;
            border-radius: 24px;
            box-shadow: var(--shadow-lg);
            width: 100%;
            max-width: 1000px;
            position: relative;
            overflow: hidden;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            display: flex;
            gap: 2rem;
        }

        .signup-logo-section {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 2rem;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border-radius: 20px;
            color: white;
            text-align: center;
        }

        .signup-logo {
            font-size: 5rem;
            margin-bottom: 1.5rem;
            animation: float 3s ease-in-out infinite;
        }

        .signup-logo-title {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            font-weight: 700;
            letter-spacing: -0.5px;
        }

        .signup-logo-subtitle {
            font-size: 1.1rem;
            opacity: 0.9;
        }

        .signup-form-section {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .signup-header {
            text-align: center;
            margin-bottom: 2.5rem;
        }

        .signup-title {
            font-size: 2.2rem;
            margin-bottom: 0.5rem;
            background: linear-gradient(45deg, var(--primary), var(--accent));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            font-weight: 700;
            letter-spacing: -0.5px;
        }

        .signup-subtitle {
            color: var(--neutral);
            font-size: 1.1rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
            position: relative;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--primary);
            font-weight: 500;
            font-size: 0.95rem;
        }

        .form-input {
            width: 100%;
            padding: 1rem 1rem 1rem 3rem;
            border: 2px solid rgba(0, 0, 0, 0.1);
            border-radius: 12px;
            font-size: 1rem;
            transition: var(--transition);
            background: rgba(245, 238, 248, 0.8);
            color: var(--dark);
        }

        .form-input:focus {
            outline: none;
            border-color: var(--primary);
            background: white;
            box-shadow: 0 0 0 4px rgba(155, 89, 182, 0.1);
        }

        .form-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--neutral);
            transition: var(--transition);
            font-size: 1.1rem;
        }

        .form-input:focus + .form-icon {
            color: var(--primary);
            transform: translateY(-50%) scale(1.1);
        }

        .password-strength {
            height: 4px;
            background: rgba(0, 0, 0, 0.1);
            border-radius: 2px;
            margin-top: 0.5rem;
            overflow: hidden;
        }

        .strength-bar {
            height: 100%;
            width: 0;
            transition: var(--transition);
        }

        .strength-weak {
            background: var(--accent);
            width: 33%;
        }

        .strength-medium {
            background: #f1c40f;
            width: 66%;
        }

        .strength-strong {
            background: var(--success);
            width: 100%;
        }

        .captcha-container {
            background: rgba(245, 238, 248, 0.8);
            padding: 1.5rem;
            border-radius: 16px;
            margin-bottom: 1.5rem;
            border: 1px solid rgba(0, 0, 0, 0.1);
        }

        .captcha-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1rem;
        }

        .captcha-title {
            font-size: 1rem;
            color: var(--primary);
            font-weight: 600;
        }

        .captcha-refresh {
            background: none;
            border: none;
            color: var(--neutral);
            cursor: pointer;
            transition: var(--transition);
            padding: 0.5rem;
            border-radius: 8px;
        }

        .captcha-refresh:hover {
            color: var(--accent);
            background: rgba(230, 126, 34, 0.1);
        }

        .captcha-box {
            background: linear-gradient(45deg, var(--primary), var(--secondary));
            color: white;
            padding: 1.5rem;
            border-radius: 12px;
            font-size: 1.5rem;
            font-weight: bold;
            letter-spacing: 8px;
            text-align: center;
            box-shadow: var(--shadow-md);
            margin-bottom: 1rem;
            position: relative;
            overflow: hidden;
        }

        .captcha-box::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(45deg, 
                rgba(255, 255, 255, 0.1) 0%,
                rgba(255, 255, 255, 0) 50%,
                rgba(255, 255, 255, 0.1) 100%);
            transform: translateX(-100%);
            animation: shine 2s infinite;
        }

        .error-message {
            color: var(--accent);
            margin-top: 1rem;
            padding: 0.5rem;
            background: rgba(231, 76, 60, 0.1);
            border-radius: 8px;
            text-align: center;
        }

        .signup-btn {
            width: 100%;
            padding: 1rem;
            background: linear-gradient(45deg, var(--primary), var(--secondary));
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            position: relative;
            overflow: hidden;
            box-shadow: var(--shadow-md);
        }

        .signup-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: var(--transition);
        }

        .signup-btn:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        .signup-btn:hover::before {
            left: 100%;
        }

        .login-link {
            text-align: center;
            margin-top: 1.5rem;
        }

        .login-link a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
            transition: var(--transition);
            position: relative;
            font-size: 0.95rem;
        }

        .login-link a::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 0;
            height: 2px;
            background: var(--accent);
            transition: var(--transition);
        }

        .login-link a:hover {
            color: var(--accent);
        }

        .login-link a:hover::after {
            width: 100%;
        }

        @keyframes float {
            0%, 100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-10px);
            }
        }

        @keyframes shine {
            0% {
                transform: translateX(-100%);
            }
            50% {
                transform: translateX(100%);
            }
            100% {
                transform: translateX(100%);
            }
        }

        @media (max-width: 768px) {
            .signup-card {
                flex-direction: column;
                padding: 2rem;
            }

            .signup-logo-section {
                padding: 1.5rem;
                margin-bottom: 2rem;
            }

            .signup-logo {
                font-size: 3.5rem;
            }

            .signup-logo-title {
                font-size: 2rem;
            }

            .signup-title {
                font-size: 1.8rem;
            }
        }
    </style>
</head>
<body>
    <div class="signup-container">
        <div class="signup-background"></div>
        <div class="signup-content">
            <div class="signup-card">
                <div class="signup-logo-section">
                    <i class="fas fa-book-open signup-logo"></i>
                    <h1 class="signup-logo-title">Athena Library</h1>
                    <p class="signup-logo-subtitle">Your Gateway to Knowledge</p>
                </div>
                
                <div class="signup-form-section">
                    <div class="signup-header">
                        <h1 class="signup-title">Create Account</h1>
                        <p class="signup-subtitle">Join our library community today</p>
                        <?php if (!empty($error)): ?>
                            <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <form class="auth-form" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                        <div class="form-group">
                            <label class="form-label" for="username">Username</label>
                            <input type="text" id="username" name="username" class="form-input" placeholder="Enter your username" required>
                            <i class="fas fa-user form-icon"></i>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label" for="email">Email</label>
                            <input type="email" id="email" name="email" class="form-input" placeholder="Enter your email" required>
                            <i class="fas fa-envelope form-icon"></i>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label" for="password">Password</label>
                            <input type="password" id="password" name="password" class="form-input" placeholder="Create a password" required>
                            <i class="fas fa-lock form-icon"></i>
                            <div class="password-strength">
                                <div class="strength-bar"></div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label" for="confirm_password">Confirm Password</label>
                            <input type="password" id="confirm_password" name="confirm_password" class="form-input" placeholder="Confirm your password" required>
                            <i class="fas fa-lock form-icon"></i>
                        </div>

                        <div class="captcha-container">
                            <div class="captcha-header">
                                <span class="captcha-title">Enter CAPTCHA</span>
                                <button type="button" class="captcha-refresh" onclick="refreshCaptcha()">
                                    <i class="fas fa-sync-alt"></i>
                                </button>
                            </div>
                            <div class="captcha-box"><?php echo $_SESSION['captcha']; ?></div>
                            <input type="text" class="form-input" name="captcha" placeholder="Type the code above" required>
                        </div>
                        
                        <button type="submit" class="signup-btn">Create Account</button>
                        
                        <div class="login-link">
                            <p>Already have an account? <a href="login.php">Sign In</a></p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Password strength indicator
        const passwordInput = document.getElementById('password');
        const strengthBar = document.querySelector('.strength-bar');
        
        passwordInput.addEventListener('input', function() {
            const password = this.value;
            let strength = 0;
            
            if (password.length >= 8) strength++;
            if (password.match(/[a-z]/) && password.match(/[A-Z]/)) strength++;
            if (password.match(/[0-9]/)) strength++;
            if (password.match(/[^a-zA-Z0-9]/)) strength++;
            
            strengthBar.className = 'strength-bar';
            if (strength <= 1) {
                strengthBar.classList.add('strength-weak');
            } else if (strength <= 3) {
                strengthBar.classList.add('strength-medium');
            } else {
                strengthBar.classList.add('strength-strong');
            }
        });

        function refreshCaptcha() {
            const captchaBox = document.querySelector('.captcha-box');
            // Add animation class
            captchaBox.style.animation = 'shine 2s';
            
            fetch('refresh_captcha.php')
                .then(response => response.json())
                .then(data => {
                    captchaBox.textContent = data.captcha;
                    // Remove animation after it completes
                    setTimeout(() => {
                        captchaBox.style.animation = 'none';
                    }, 2000);
                })
                .catch(error => {
                    console.error('Error refreshing CAPTCHA:', error);
                    alert('Failed to refresh CAPTCHA. Please try again.');
                    // Remove animation on error
                    captchaBox.style.animation = 'none';
                });
        }
    </script>
</body>
</html> 