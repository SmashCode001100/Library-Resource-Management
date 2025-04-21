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
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $captcha = $_POST['captcha'] ?? '';

    // Debug information
    error_log("Submitted CAPTCHA: " . $captcha);
    error_log("Session CAPTCHA: " . $_SESSION['captcha']);

    if (empty($username) || empty($password)) {
        $error = "Please enter both username and password.";
    } elseif (empty($captcha)) {
        $error = "CAPTCHA is required.";
    } elseif (!isset($_SESSION['captcha']) || $captcha != $_SESSION['captcha']) {
        $error = "Invalid CAPTCHA. Please try again.";
        // Generate new CAPTCHA after failed attempt
        $_SESSION['captcha'] = rand(1000, 9999);
    } else {
        try {
            $conn = new mysqli("localhost", "root", "", "athena_library");
            
            if ($conn->connect_error) {
                throw new Exception("Connection failed: " . $conn->connect_error);
            }

            $stmt = $conn->prepare("SELECT id, username, password, created_at FROM users WHERE username = ?");
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $conn->error);
            }
            
            $stmt->bind_param("s", $username);
            if (!$stmt->execute()) {
                throw new Exception("Execute failed: " . $stmt->error);
            }
            
            $result = $stmt->get_result();
            
            if ($result->num_rows == 1) {
                $user = $result->fetch_assoc();
                if (password_verify($password, $user['password'])) {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['created_at'] = $user['created_at'];
                    
                    // Clear CAPTCHA after successful login
                    unset($_SESSION['captcha']);
                    
                    header("Location: dashboard.php");
                    exit();
                } else {
                    $error = "Invalid username or password.";
                }
            } else {
                $error = "Invalid username or password.";
            }
            
            $stmt->close();
            $conn->close();
            
        } catch (Exception $e) {
            error_log("Login error: " . $e->getMessage());
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
    <title>Login - Athena Library</title>
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

        .login-container {
            width: 100%;
            min-height: 100vh;
            display: flex;
            position: relative;
            overflow: hidden;
        }

        .login-background {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: 
                linear-gradient(45deg, rgba(155, 89, 182, 0.1), rgba(230, 126, 34, 0.1)),
                url('https://source.unsplash.com/random/1920x1080/?library') center/cover;
            filter: blur(8px);
            transform: scale(1.1);
        }

        .login-content {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 2rem;
            position: relative;
            z-index: 1;
        }

        .login-card {
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

        .login-logo-section {
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

        .login-logo {
            font-size: 5rem;
            margin-bottom: 1.5rem;
            animation: float 3s ease-in-out infinite;
        }

        .login-logo-title {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            font-weight: 700;
            letter-spacing: -0.5px;
        }

        .login-logo-subtitle {
            font-size: 1.1rem;
            opacity: 0.9;
        }

        .login-form-section {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .login-header {
            text-align: center;
            margin-bottom: 2.5rem;
        }

        .login-title {
            font-size: 2.2rem;
            margin-bottom: 0.5rem;
            background: linear-gradient(45deg, var(--primary), var(--accent));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            font-weight: 700;
            letter-spacing: -0.5px;
        }

        .login-subtitle {
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

        .login-btn {
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

        .login-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: var(--transition);
        }

        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        .login-btn:hover::before {
            left: 100%;
        }

        .signup-link {
            text-align: center;
            margin-top: 1.5rem;
        }

        .signup-link a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
            transition: var(--transition);
            position: relative;
            font-size: 0.95rem;
        }

        .signup-link a::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 0;
            height: 2px;
            background: var(--accent);
            transition: var(--transition);
        }

        .signup-link a:hover {
            color: var(--accent);
        }

        .signup-link a:hover::after {
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
            .login-card {
                flex-direction: column;
                padding: 2rem;
            }

            .login-logo-section {
                padding: 1.5rem;
                margin-bottom: 2rem;
            }

            .login-logo {
                font-size: 3.5rem;
            }

            .login-logo-title {
                font-size: 2rem;
            }

            .login-title {
                font-size: 1.8rem;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-background"></div>
        <div class="login-content">
            <div class="login-card">
                <div class="login-logo-section">
                    <i class="fas fa-book-open login-logo"></i>
                    <h1 class="login-logo-title">Athena Library</h1>
                    <p class="login-logo-subtitle">Your Gateway to Knowledge</p>
                </div>
                
                <div class="login-form-section">
                    <div class="login-header">
                        <h1 class="login-title">Welcome Back</h1>
                        <p class="login-subtitle">Sign in to your account</p>
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
                            <label class="form-label" for="password">Password</label>
                            <input type="password" id="password" name="password" class="form-input" placeholder="Enter your password" required>
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
                        
                        <button type="submit" class="login-btn">Sign In</button>
                        
                        <div class="signup-link">
                            <p>Don't have an account? <a href="signup.php">Sign Up</a></p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
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