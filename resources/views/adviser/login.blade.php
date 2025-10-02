<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Student Violation Tracking System</title>

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    
    <style>
        /* Reset & Box Sizing */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* Full height body & html */
        html, body {
            height: 100%;
            font-family: "Inter", sans-serif;
            overflow: hidden; /* prevent scrolling */
        }

        /* Body background with overlay */
        body {
            display: flex;
            flex-direction: column;
            position: relative;
            background: url('http://127.0.0.1:8000/images/tshs.jpg') no-repeat center center fixed;
            background-size: cover;
        }

        body::before {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(120deg, rgba(0,0,0,0.65), rgba(0,0,0,0.45));
            background-size: 200% 200%;
            animation: gradientShift 8s ease-in-out infinite;
            backdrop-filter: blur(8px);
            z-index: 0;
        }

        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        /* ===== TOP BAR ===== */
        .top-bar {
            background: rgba(0,0,0,0.65);
            backdrop-filter: blur(8px);
            display: flex;
            align-items: center;
            padding: 15px 30px;
            gap: 15px;
            position: relative;
            z-index: 1;
        }
        .top-bar img { width: 55px; }
        .top-bar h1 {
            color: white;
            font-size: 24px;
            font-weight: 700;
            letter-spacing: 0.5px;
        }

        /* ===== MAIN LAYOUT ===== */
        .main-container {
            flex: 1 0 auto; /* fill remaining space */
            display: grid;
            grid-template-columns: 1fr 1fr;
            align-items: center;
            justify-content: center;
            padding: 50px;
            gap: 40px;
            position: relative;
            z-index: 1;
            max-height: calc(100vh - 120px); /* 120px for top bar + footer */
            overflow: hidden;
        }

        /* Left Welcome Section */
        .welcome-text {
            margin-left: 80px;
            color: white;
            max-width: 500px;
            animation: slideInLeft 0.8s ease-out;
        }
        .welcome-text h2 {
            font-size: 46px;
            font-weight: 700;
            line-height: 1.2;
            margin-bottom: 15px;
        }
        .welcome-text p {
            font-size: 18px;
            opacity: 0.95;
            line-height: 1.6;
        }

        /* Login Card */
        .login-card {
            background: rgba(99,95,95,0.15);
            border: 1px solid rgba(255,255,255,0.25);
            border-radius: 24px;
            backdrop-filter: blur(20px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.4);
            max-width: 420px;
            width: 100%;
            padding: 40px 35px;
            text-align: center;
            animation: fadeIn 0.8s ease-out;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            margin-left: 80px;
            position: relative;
        }
        .login-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 25px 50px rgba(0,0,0,0.5);
        }

        /* Login Header */
        .login-header img { width: 70px; margin-bottom: 12px; }
        .login-header h2 {
            font-size: 30px;
            font-weight: 700;
            color: white;
        }
        .login-header p {
            font-size: 18px;
            color: rgba(255,255,255,0.8);
            margin-bottom: 20px;
        }

        /* Input Fields */
        .form-group {
            display: flex;
            flex-direction: column;
            margin-bottom: 18px;
            text-align: left;
        }
        .form-group label {
            font-size: 18px;
            font-weight: 600;
            color: white;
            margin-bottom: 5px;
        }
        .form-group input {
            padding: 14px;
            border-radius: 12px;
            border: 1px solid rgba(255,255,255,0.4);
            background: rgba(255,255,255,0.1);
            color: white;
            font-size: 18px;
            transition: 0.3s ease;
        }
        .form-group input:focus {
            border-color: #4facfe;
            background: rgba(255,255,255,0.2);
            outline: none;
            box-shadow: 0 0 12px rgba(79,172,254,0.5);
        }
        input::placeholder {
            color: rgba(255,255,255,0.85);
            font-weight: 500;
            letter-spacing: 0.5px;
            font-size: 18px;
        }
        input:focus::placeholder {
            color: rgba(255,255,255,0.6);
        }

        /* Password Toggle */
        .password-wrapper { position: relative; }
        .toggle-password {
            position: absolute;
            right: 15px;
            top: 40px;
            width: 22px;
            cursor: pointer;
            opacity: 0.7;
            transition: 0.2s ease;
        }
        .toggle-password:hover { opacity: 1; }

        /* Button */
        .login-card button {
            width: 100%;
            background: linear-gradient(135deg, #4facfe, #00f2fe);
            color: white;
            padding: 14px;
            border: none;
            border-radius: 12px;
            font-weight: 700;
            font-size: 16px;
            letter-spacing: 0.3px;
            cursor: pointer;
            transition: 0.3s ease;
        }
        .login-card button:hover {
            transform: scale(1.03);
            box-shadow: 0 8px 18px rgba(79,172,254,0.4);
        }

        /* Login Footer Links */
        .login-footer a {
            font-size: 13px;
            color: #00f2fe;
            text-decoration: none;
        }
        .login-footer a:hover { text-decoration: underline; }

        /* Footer fixed at bottom */
        footer {
            flex-shrink: 0; /* stays at bottom */
            background: rgba(0,0,0,0.7);
            color: white;
            text-align: center;
            padding: 12px;
            font-size: 13px;
            z-index: 1;
        }

        /* Animations */
        @keyframes slideInLeft {
            from { opacity: 0; transform: translateX(-30px); }
            to { opacity: 1; transform: translateX(0); }
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* School Highlights */
        .school-highlights {
            margin-top: 30px;
            display: flex;
            flex-direction: column;
            gap: 18px;
        }
        .school-highlights .highlight {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            background: rgba(255,255,255,0.1);
            padding: 12px 16px;
            border-radius: 14px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.2);
            transition: transform 0.2s ease;
        }
        .school-highlights .highlight:hover { transform: translateX(6px); }
        .school-highlights .highlight span { font-size: 24px; flex-shrink: 0; }
        .school-highlights .highlight p { color: white; font-size: 18px; line-height: 1.4; }

        /* Error Text */
        .error-text {
            display: block;
            color: #ff6b6b;
            font-size: 17px;
            margin-top: 4px;
            font-weight: 600;
            opacity: 0;
            transform: translateY(-3px);
            transition: opacity 0.2s ease, transform 0.2s ease;
        }
        .error-text.visible {
            opacity: 1;
            transform: translateY(0);
        }

        /* ===== UPDATED MODAL STYLES ===== */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.6);
            z-index: 1000;
            justify-content: center;
            align-items: center;
            backdrop-filter: blur(5px);
        }
        
        .modal-content {
            background: rgba(99,95,95,0.15);
            border: 1px solid rgba(255,255,255,0.25);
            border-radius: 24px;
            backdrop-filter: blur(20px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.4);
            padding: 30px;
            max-width: 400px;
            width: 90%;
            text-align: center;
            position: relative;
            color: white;
            animation: modalFadeIn 0.3s ease-out;
        }
        
        @keyframes modalFadeIn {
            from { opacity: 0; transform: translateY(-20px) scale(0.95); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }
        
        .modal h2 {
            color: #ff6b6b;
            margin-bottom: 15px;
            font-size: 28px;
            font-weight: 700;
        }
        
        .modal p {
            margin-bottom: 20px;
            color: rgba(255,255,255,0.9);
            font-size: 16px;
            line-height: 1.5;
        }
        
        .countdown {
            font-size: 32px;
            font-weight: bold;
            color: #4facfe;
            margin: 15px 0;
            text-shadow: 0 0 10px rgba(79,172,254,0.5);
        }
        
        /* Hidden attempts counter - removed from modal */
        
        .modal-actions {
            margin-top: 20px;
        }
        
        .ok-btn {
            background: linear-gradient(135deg, #4facfe, #00f2fe);
            color: white;
            border: none;
            padding: 10px 24px;
            border-radius: 12px;
            cursor: pointer;
            font-weight: 600;
            font-size: 16px;
            transition: 0.3s ease;
        }
        
        .ok-btn:hover {
            transform: scale(1.05);
            box-shadow: 0 8px 18px rgba(79,172,254,0.4);
        }
        
        /* Success Modal Styles */
        .success-modal .modal-content h2 {
            color: #2ecc71;
        }
        
        .success-modal .modal-content p {
            color: rgba(255,255,255,0.9);
        }
        
        .success-modal .ok-btn {
            background: linear-gradient(135deg, #2ecc71, #1abc9c);
        }
        
        .success-modal .ok-btn:hover {
            box-shadow: 0 8px 18px rgba(46,204,113,0.4);
        }
        
        .success-icon {
            font-size: 48px;
            margin-bottom: 15px;
            color: #2ecc71;
            text-shadow: 0 0 15px rgba(46,204,113,0.5);
        }
        
        /* Contact Information Styles */
        .contact-info {
            margin-top: 5px;
        }
        
        .contact-link {
            color: #4facfe;
            text-decoration: none;
            border-bottom: 1px solid #4facfe;
            transition: all 0.2s ease;
            cursor: pointer;
        }
        
        .contact-link:hover {
            color: #00f2fe;
            border-bottom: 1px solid #00f2fe;
        }

        /* Responsive */
        @media (max-width: 900px) {
            .main-container {
                grid-template-columns: 1fr;
                text-align: center;
                padding: 30px;
                max-height: calc(100vh - 120px);
            }
            .welcome-text h2 { font-size: 34px; }
            .login-card { margin-left: 0; }
        }
    </style>
</head>
<body>

<header class="top-bar">
    <img src="{{ asset('images/logo.png') }}" alt="System Logo">
    <h1>Student Violation Tracking System</h1>
</header>

<div class="main-container">
    <!-- Left Section with More Details -->
    <div class="welcome-text">
        <h2>Welcome Back!</h2>
        <p>Track and manage student behavior effectively with the Student Violation Tracking System.<br>
           Access violation records, parent notifications, and reports easily.</p>

        <!-- School Highlights -->
        <div class="school-highlights">
            <div class="highlight">
                <span>🏫</span>
                <p><strong>Tagoloan Senior High School</strong><br>Committed to excellence in education.</p>
            </div>
            <div class="highlight">
                <span>🎓</span>
                <p><strong>Student Support</strong><br>Helping students grow with proper guidance.</p>
            </div>
            <div class="highlight">
                <span>📞</span>
                <p>
                    <strong>Contact:</strong> 
                    <span class="contact-info">
                        <a href="tel:09131234567" class="contact-link">0913-123-4567</a>
                    </span><br>
                    <strong>Email:</strong> 
                    <span class="contact-info">
                        <a href="mailto:tshs@gmail.com" class="contact-link">tshs@gmail.com</a>
                    </span>
                </p>
            </div>
        </div>
    </div>

    <!-- Login Card shifted slightly right -->
    <div class="login-card">
        <!-- Removed the attempts counter from the login form -->
        
        <div class="login-header">
            <img src="{{ asset('images/logo.png') }}" alt="Logo">
            <h2> Login</h2>
            <p>Sign in to access your dashboard</p>
        </div>

        <form id="loginForm" action="{{ route('login') }}" method="POST" novalidate>
            @csrf
            <div class="form-group">
                <label for="email">Email Address</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    placeholder="e.g. adviser@gmail.com"
                    required
                    autocomplete="username"
                    pattern="^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$"
                    title="Invalid email format. Example: adviser@gmail.com"
                >
                <small class="error-text" id="emailError">
                    @error('email')
                        {{ $message }}
                    @enderror
                </small>
            </div>

            <div class="form-group password-wrapper">
                <label for="password">Password</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Enter your password"
                    required
                    autocomplete="current-password"
                    minlength="6"
                    maxlength="50"
                >
                <small class="error-text" id="passwordError">
                    @error('password')
                        {{ $message }}
                    @enderror
                </small>
                <img src="{{ asset('images/hide.png') }}" id="togglePassword" class="toggle-password" alt="Toggle Password">
            </div>

            <button type="submit" id="loginBtn">Log In</button>
            <div class="login-footer" style="margin-top: 12px;">
                <a href="#">Forgot Password?</a>
            </div>
        </form>
    </div>
</div>

<!-- Modal for too many attempts -->
<div id="attemptsModal" class="modal">
    <div class="modal-content">
        <h2>Too Many Attempts</h2>
        <p>You have exceeded the maximum number of login attempts. Please wait before trying again.</p>
        <div class="countdown" id="countdown">10</div>
        <p>seconds remaining</p>
        <div class="modal-actions">
            <button class="ok-btn" id="okBtn">OK</button>
        </div>
    </div>
</div>

<!-- Success Modal -->
<div id="successModal" class="modal success-modal">
    <div class="modal-content">
        <div class="success-icon">✅</div>
        <h2>Login Successful!</h2>
        <p>You are being redirected to your dashboard.</p>
        <!-- Removed the Continue button as requested -->
    </div>
</div>

<footer>
    &copy; {{ date('Y') }} Tagoloan Senior High School • Student Violation Tracking System
</footer>

<script>
// Track login attempts
let loginAttempts = 0;
const maxAttempts = 3;
const lockoutTime = 10; // 10 seconds
let countdownInterval;
let redirectInterval;

const loginForm = document.getElementById('loginForm');
const email = document.getElementById('email');
const password = document.getElementById('password');
const emailError = document.getElementById('emailError');
const passwordError = document.getElementById('passwordError');
const loginBtn = document.getElementById('loginBtn');
const attemptsModal = document.getElementById('attemptsModal');
const successModal = document.getElementById('successModal');
const countdownDisplay = document.getElementById('countdown');
const okBtn = document.getElementById('okBtn');

// Load attempts from localStorage if available
if (localStorage.getItem('loginAttempts')) {
    loginAttempts = parseInt(localStorage.getItem('loginAttempts'));
}

// Check if user is still in lockout period
const lockoutEnd = localStorage.getItem('lockoutEnd');
if (lockoutEnd && new Date().getTime() < parseInt(lockoutEnd)) {
    const remainingTime = Math.ceil((parseInt(lockoutEnd) - new Date().getTime()) / 1000);
    startLockout(remainingTime);
}

function startLockout(seconds) {
    // Disable form
    loginBtn.disabled = true;
    
    let timeLeft = seconds;
    updateLoginButtonText(timeLeft);
    
    // Show modal
    attemptsModal.style.display = 'flex';
    
    countdownInterval = setInterval(() => {
        timeLeft--;
        countdownDisplay.textContent = timeLeft;
        updateLoginButtonText(timeLeft);
        
        if (timeLeft <= 0) {
            clearInterval(countdownInterval);
            attemptsModal.style.display = 'none';
            loginBtn.disabled = false;
            loginBtn.textContent = 'Log In';
            loginAttempts = 0;
            localStorage.removeItem('lockoutEnd');
        }
    }, 1000);
}

function updateLoginButtonText(timeLeft) {
    loginBtn.textContent = `Try Again in ${timeLeft}s`;
}

function showSuccessMessage(redirectUrl) {
    // Show success modal
    successModal.style.display = 'flex';
    
    // Start countdown for automatic redirect (1 second instead of 3)
    let countdown = 1;
    
    redirectInterval = setInterval(() => {
        countdown--;
        
        if (countdown <= 0) {
            clearInterval(redirectInterval);
            window.location.href = redirectUrl;
        }
    }, 1000);
}

// Modal button event handlers
okBtn.addEventListener('click', function() {
    // Only hide the modal, don't clear the lockout
    attemptsModal.style.display = 'none';
});

loginForm.addEventListener('submit', function(e) {
    e.preventDefault();
    
    // If user is in lockout period, prevent form submission
    const lockoutEnd = localStorage.getItem('lockoutEnd');
    if (lockoutEnd && new Date().getTime() < parseInt(lockoutEnd)) {
        return;
    }
    
    let valid = true;
    emailError.classList.remove('visible');
    passwordError.classList.remove('visible');

    if (!email.value.trim()) {
        emailError.textContent = "Email is required";
        emailError.classList.add('visible');
        valid = false;
    }
    if (!password.value.trim()) {
        passwordError.textContent = "Password is required";
        passwordError.classList.add('visible');
        valid = false;
    }

    if (!valid) return; // stop if fields are invalid

    const formData = new FormData(loginForm);
    fetch("{{ route('login') }}", {
        method: "POST",
        headers: { "X-CSRF-TOKEN": "{{ csrf_token() }}" },
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            // Reset attempts on successful login
            loginAttempts = 0;
            localStorage.removeItem('lockoutEnd');
            
            // Show success message before redirecting
            showSuccessMessage(data.redirect);
        } else {
            loginAttempts++;
            localStorage.setItem('loginAttempts', loginAttempts);
            
            passwordError.textContent = data.message;
            passwordError.classList.add('visible');
            
            // Check if we've reached the maximum attempts
            if (loginAttempts >= maxAttempts) {
                // Set lockout end time
                const lockoutEndTime = new Date().getTime() + (lockoutTime * 1000);
                localStorage.setItem('lockoutEnd', lockoutEndTime);
                
                // Start lockout
                startLockout(lockoutTime);
            }
        }
    })
    .catch(() => {
        loginAttempts++;
        localStorage.setItem('loginAttempts', loginAttempts);
        
        passwordError.textContent = "Something went wrong. Please try again.";
        passwordError.classList.add('visible');
        
        // Check if we've reached the maximum attempts
        if (loginAttempts >= maxAttempts) {
            // Set lockout end time
            const lockoutEndTime = new Date().getTime() + (lockoutTime * 1000);
            localStorage.setItem('lockoutEnd', lockoutEndTime);
            
            // Start lockout
            startLockout(lockoutTime);
        }
    });
});

const togglePassword = document.getElementById('togglePassword');
const passwordInput = document.getElementById('password');

togglePassword.addEventListener('click', () => {
    // Toggle the type attribute
    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
    passwordInput.setAttribute('type', type);

    // Optional: toggle the icon
    if (type === 'password') {
        togglePassword.src = "{{ asset('images/hide.png') }}";
    } else {
        togglePassword.src = "{{ asset('images/show.png') }}";
    }
});
</script>

</body>
</html>