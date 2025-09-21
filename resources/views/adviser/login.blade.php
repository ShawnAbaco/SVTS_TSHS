<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Student Violation Tracking System</title>

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/adviser/login.css') }}">
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
        <p><strong>Contact:</strong> 0913-123-4567<br>
           <strong>Email:</strong> tshs@gmail.com</p>
    </div>

        </div>
    </div>

    <!-- Login Card shifted slightly right -->
    <div class="login-card">
        <div class="login-header">
            <img src="{{ asset('images/logo.png') }}" alt="Logo">
            <h2>Adviser Login</h2>
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


<footer>
    &copy; {{ date('Y') }} Tagoloan Senior High School • Student Violation Tracking System
</footer>

<script>
const loginForm = document.getElementById('loginForm');
const email = document.getElementById('email');
const password = document.getElementById('password');
const emailError = document.getElementById('emailError');
const passwordError = document.getElementById('passwordError');

loginForm.addEventListener('submit', function(e) {
    e.preventDefault();

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
            window.location.href = data.redirect;
        } else {
            passwordError.textContent = data.message;
            passwordError.classList.add('visible');
        }
    })
    .catch(() => {
        passwordError.textContent = "Something went wrong. Please try again.";
        passwordError.classList.add('visible');
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
