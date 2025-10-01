<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Student Violation Tracking System</title>

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/adviser/login.css') }}">
    
    <style>
        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }
        
        .modal-content {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            max-width: 400px;
            width: 90%;
            text-align: center;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
            position: relative;
        }
        
        .modal h2 {
            color: #e74c3c;
            margin-bottom: 15px;
        }
        
        .modal p {
            margin-bottom: 20px;
            color: #555;
        }
        
        .countdown {
            font-size: 24px;
            font-weight: bold;
            color: #e74c3c;
            margin: 15px 0;
        }
        
        .attempts-counter {
            position: absolute;
            top: 10px;
            right: 10px;
            background-color: #f8f9fa;
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 14px;
            color: #6c757d;
        }
        
        .modal-actions {
            margin-top: 20px;
        }
        
        .ok-btn {
            background-color: #3498db;
            color: white;
            border: none;
            padding: 8px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
        }
        
        .ok-btn:hover {
            background-color: #2980b9;
        }
        
        /* Success Modal Styles */
        .success-modal .modal-content h2 {
            color: #27ae60;
        }
        
        .success-modal .modal-content p {
            color: #2c3e50;
        }
        
        .success-modal .ok-btn {
            background-color: #27ae60;
        }
        
        .success-modal .ok-btn:hover {
            background-color: #219955;
        }
        
        .success-icon {
            font-size: 48px;
            margin-bottom: 15px;
        }
        
        /* Contact Information Styles */
        .contact-info {
            margin-top: 5px;
        }
        
        .contact-link {
            color: #3498db;
            text-decoration: none;
            border-bottom: 1px solid #3498db;
            transition: all 0.2s ease;
            cursor: pointer;
        }
        
        .contact-link:hover {
            color: #2980b9;
            border-bottom: 1px solid #2980b9;
        }
    </style>
</head>
<body style="background: url('http://127.0.0.1:8000/images/tshs.jpg') no-repeat center center fixed; background-size: cover;">

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
        <div class="attempts-counter" id="attemptsCounter">Attempts: 0/3</div>
        
        <div class="login-header">
            <img src="{{ asset('images/logo.png') }}" alt="Logo">
            <h2> Login</h2>
            <p>Sign in to access your dashboard</p>
        </div>

<form id="loginForm" action="{{ route('auth.login') }}" method="POST" novalidate>
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
const attemptsCounter = document.getElementById('attemptsCounter');
const attemptsModal = document.getElementById('attemptsModal');
const successModal = document.getElementById('successModal');
const countdownDisplay = document.getElementById('countdown');
const okBtn = document.getElementById('okBtn');

// Load attempts from localStorage if available
if (localStorage.getItem('loginAttempts')) {
    loginAttempts = parseInt(localStorage.getItem('loginAttempts'));
    updateAttemptsCounter();
}

// Check if user is still in lockout period
const lockoutEnd = localStorage.getItem('lockoutEnd');
if (lockoutEnd && new Date().getTime() < parseInt(lockoutEnd)) {
    const remainingTime = Math.ceil((parseInt(lockoutEnd) - new Date().getTime()) / 1000);
    startLockout(remainingTime);
}

function updateAttemptsCounter() {
    attemptsCounter.textContent = `Attempts: ${loginAttempts}/${maxAttempts}`;
    localStorage.setItem('loginAttempts', loginAttempts);
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
            updateAttemptsCounter();
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
    fetch("{{ route('adviser.login') }}", {
        method: "POST",
        headers: { "X-CSRF-TOKEN": "{{ csrf_token() }}" },
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            // Reset attempts on successful login
            loginAttempts = 0;
            updateAttemptsCounter();
            localStorage.removeItem('lockoutEnd');
            
            // Show success message before redirecting
            showSuccessMessage(data.redirect);
        } else {
            loginAttempts++;
            updateAttemptsCounter();
            
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
        updateAttemptsCounter();
        
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