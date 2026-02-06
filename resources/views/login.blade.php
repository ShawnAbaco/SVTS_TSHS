<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Student Violation Tracking System</title>

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
</head>

<body>
    <!-- Background Image -->
    <div class="bg-image">
        <img src="{{ asset('images/tshs.jpg') }}" class="bg-img">
    </div>
    <header class="top-bar">
        <img src="{{ asset('images/logo.png') }}" alt="System Logo">
        <h1>Student Violation Tracking System</h1>
    </header>

    <div class="main-container">
        <!-- Left Section with More Details -->
        <div class="welcome-text">
            <h2>Welcome Back!</h2>

            <p class="welcome-lead">
                Track and manage student behavior effectively with the Student Violation Tracking System.
            </p>
            <p class="welcome-sub">
                Access violation records, parent notifications, and reports easily.
            </p>

            <!-- School Highlights -->
            <div class="school-highlights">
                <div class="highlight">
                    <span>🏫</span>
                    <div class="highlight-content">
                        <strong>Tagoloan Senior High School</strong>
                        <span>Committed to excellence in education.</span>
                    </div>
                </div>
                <div class="highlight">
                    <span>🎓</span>
                    <div class="highlight-content">
                        <strong>Student Support</strong>
                        <span>Helping students grow with proper guidance.</span>
                    </div>
                </div>
                <div class="highlight">
                    <span>📞</span>
                    <div class="highlight-content">
                        <strong>Contact & Email</strong>
                        <div class="contact-links">
                            <a href="tel:09131234567" class="contact-link">
                                <i class="fas fa-phone"></i>0913-123-4567
                            </a>
                            <a href="mailto:tshssvts@gmail.com" class="contact-link">
                                <i class="fas fa-envelope"></i>tshssvts@gmail.com
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Login Card -->
        <div class="login-card">
            <div class="login-header">
                <img src="{{ asset('images/logo.png') }}" alt="Logo">
                <h2>Login</h2>
                <p>Sign in to access your dashboard</p>
            </div>

            <form id="loginForm" action="{{ route('login') }}" method="POST" novalidate>
                @csrf
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" placeholder="e.g. example@gmail.com" required
                        autocomplete="username" pattern="^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$"
                        title="Invalid email format. Example: example@gmail.com">
                    <small class="error-text" id="emailError">
                        @error('email')
                            {{ $message }}
                        @enderror
                    </small>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>

                    <div class="password-wrapper">
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
                        <i class="fas fa-eye-slash toggle-password-icon" id="togglePasswordIcon"></i>
                    </div>

                    <small class="error-text" id="passwordError">
                        @error('password')
                            {{ $message }}
                        @enderror
                    </small>
                </div>

                <button type="submit" id="loginBtn">Log In</button>
                <div class="login-footer">
                    <a href="#" onclick="openForgotPasswordModal()">Forgot Password?</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Forgot Password Modal -->
    <div id="forgotPasswordModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Reset Password</h2>
                <span class="close">&times;</span>
            </div>
            <div class="modal-body">
                <!-- Step Indicator -->
                <div class="step-indicator">
                    <div class="step active" id="step-1">1</div>
                    <div class="step-line left"></div>
                    <div class="step" id="step-2">2</div>
                    <div class="step-line right"></div>
                    <div class="step" id="step-3">3</div>
                </div>

                <form id="forgotPasswordForm">
                    @csrf

                    <!-- Step 1: Enter Email -->
                    <div id="forgot-step-1" class="forgot-step active">
                        <p class="step-description">
                            Enter your email address to receive a verification code.
                        </p>
                        <div class="form-group">
                            <label for="forgot_email">Email Address</label>
                            <input type="email" id="forgot_email" name="email"
                                placeholder="Enter your email address" required>
                            <small class="error-text" id="forgot_email_error"></small>
                        </div>
                        <div class="form-actions">
                            <button type="button" class="btn-cancel"
                                onclick="closeForgotPasswordModal()">Cancel</button>
                            <button type="button" class="btn-primary" onclick="sendResetCode()"
                                id="send-reset-btn">
                                Send Verification Code
                            </button>
                        </div>
                    </div>

                    <!-- Step 2: Enter Verification Code -->
                    <div id="forgot-step-2" class="forgot-step">
                        <div class="verification-section">
                            <p class="verification-info">
                                We sent a 6-digit verification code to <strong id="reset-email-display"></strong>
                            </p>
                            <div class="form-group">
                                <label for="reset_code">Verification Code</label>
                                <input type="text" id="reset_code" name="verification_code" maxlength="6"
                                    placeholder="000000" required
                                    oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                <small class="error-text" id="reset_code_error"></small>
                            </div>
                            <div class="resend-section">
                                <button type="button" class="btn-secondary" onclick="sendResetCode()"
                                    id="resend-reset-btn" disabled>
                                    Resend Code (<span id="resend-reset-timer">60</span>s)
                                </button>
                            </div>
                            <div class="form-actions">
                                <button type="button" class="btn-cancel" onclick="prevStep()">Back</button>
                                <button type="button" class="btn-primary" onclick="verifyResetCode()"
                                    id="verify-code-btn">
                                    Verify Code
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Step 3: Set New Password -->
                    <div id="forgot-step-3" class="forgot-step">
                        <p class="step-description">
                            Create a new password for your account.
                        </p>
                        <div class="form-group">
                            <label for="new_password_reset">New Password</label>
                            <div class="password-wrapper">
                                <input type="password" id="new_password_reset" name="new_password"
                                    placeholder="Enter new password" required minlength="6">
                                <i class="fas fa-eye-slash toggle-password-reset-icon"></i>
                            </div>
                            <small class="error-text" id="new_password_reset_error"></small>
                        </div>

                        <div class="form-group">
                            <label for="confirm_password_reset">Confirm New Password</label>
                            <div class="password-wrapper">
                                <input type="password" id="confirm_password_reset" name="new_password_confirmation"
                                    placeholder="Confirm new password" required minlength="6">
                                <i class="fas fa-eye-slash toggle-password-reset-icon"></i>
                            </div>
                            <small class="error-text" id="confirm_password_reset_error"></small>
                        </div>

                        <div class="form-actions">
                            <button type="button" class="btn-cancel" onclick="prevStep()">Back</button>
                            <button type="submit" class="btn-primary" id="reset-password-btn">
                                Reset Password
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Success Modal -->
    <div id="successModal" class="modal success-modal">
        <div class="modal-content">
            <div class="success-icon">✅</div>
            <h2>Login Successful!</h2>
            <p>You are being redirected to your dashboard.</p>
        </div>
    </div>

    <!-- Too Many Attempts Modal -->
    <div id="tooManyAttemptsModal" class="modal too-many-attempts-modal">
        <div class="modal-content">
            <div class="attempts-icon">⚠️</div>
            <h2>Too Many Attempts</h2>
            <p>You have exceeded the maximum number of login attempts. Please try again in <span
                    id="countdownTimer">60</span> seconds.</p>
            <div class="modal-actions">
                <button class="ok-btn" onclick="closeTooManyAttemptsModal()">OK</button>
            </div>
        </div>
    </div>

    <!--<footer>
        &copy; {{ date('Y') }} Tagoloan Senior High School • Student Violation Tracking System
    </footer>-->

    <script src="{{ asset('js/login.js') }}"></script>
</body>

</html>
