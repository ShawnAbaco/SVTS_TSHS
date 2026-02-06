// Track login attempts
let loginAttempts = 0;
const maxAttempts = 3;
const lockoutTime = 10; // 10 seconds
let countdownInterval;
let redirectInterval;

const loginForm = document.getElementById("loginForm");
const email = document.getElementById("email");
const password = document.getElementById("password");
const emailError = document.getElementById("emailError");
const passwordError = document.getElementById("passwordError");
const loginBtn = document.getElementById("loginBtn");
const successModal = document.getElementById("successModal");
const tooManyAttemptsModal = document.getElementById("tooManyAttemptsModal");

// Forgot Password Variables
let resetTimer;
let resetTimeLeft = 60;
let currentStep = 1;
let userEmail = "";

// Load attempts from localStorage if available
if (localStorage.getItem("loginAttempts")) {
    loginAttempts = parseInt(localStorage.getItem("loginAttempts"));
}

// Check if user is still in lockout period
const lockoutEnd = localStorage.getItem("lockoutEnd");
if (lockoutEnd && new Date().getTime() < parseInt(lockoutEnd)) {
    const remainingTime = Math.ceil(
        (parseInt(lockoutEnd) - new Date().getTime()) / 1000
    );
    startLockout(remainingTime);
}

function startLockout(seconds) {
    // Disable form and button
    loginBtn.disabled = true;
    loginForm.style.opacity = '0.6';
    loginForm.style.pointerEvents = 'none';

    let timeLeft = seconds;
    updateLoginButtonText(timeLeft);

    countdownInterval = setInterval(() => {
        timeLeft--;
        updateLoginButtonText(timeLeft);

        if (timeLeft <= 0) {
            clearInterval(countdownInterval);
            resetLoginForm();
        }
    }, 1000);
}

function updateLoginButtonText(timeLeft) {
    loginBtn.textContent = `Try Again in ${timeLeft}s`;
    loginBtn.style.background = 'linear-gradient(135deg, #ff6b6b, #ff8e8e)';
    loginBtn.style.cursor = 'not-allowed';
}

function resetLoginForm() {
    loginBtn.disabled = false;
    loginBtn.textContent = "Log In";
    loginBtn.style.background = 'linear-gradient(135deg, #4facfe, #00f2fe)';
    loginBtn.style.cursor = 'pointer';
    loginForm.style.opacity = '1';
    loginForm.style.pointerEvents = 'auto';
    loginAttempts = 0;
    localStorage.removeItem("lockoutEnd");
    localStorage.removeItem("loginAttempts");
}

function showTooManyAttemptsModal() {
    const modal = document.getElementById('tooManyAttemptsModal');
    const countdownElement = document.getElementById('countdownTimer');
    
    modal.style.display = 'flex';
    
    // Start countdown from current lockout time
    const lockoutEnd = localStorage.getItem("lockoutEnd");
    if (lockoutEnd && new Date().getTime() < parseInt(lockoutEnd)) {
        const remainingTime = Math.ceil(
            (parseInt(lockoutEnd) - new Date().getTime()) / 1000
        );
        let timeLeft = remainingTime;
        countdownElement.textContent = timeLeft;
        
        const modalCountdownInterval = setInterval(() => {
            timeLeft--;
            countdownElement.textContent = timeLeft;
            
            if (timeLeft <= 0) {
                clearInterval(modalCountdownInterval);
                closeTooManyAttemptsModal();
            }
        }, 1000);
    }
}

function closeTooManyAttemptsModal() {
    const modal = document.getElementById('tooManyAttemptsModal');
    modal.style.display = 'none';
    
    // DON'T reset the login form here - keep the button disabled and counting down
    // The countdown will continue running and the form will auto-reset when time is up
}

function showSuccessMessage(redirectUrl) {
    // Show success modal
    successModal.style.display = "flex";

    // Start countdown for automatic redirect
    let countdown = 1;

    redirectInterval = setInterval(() => {
        countdown--;

        if (countdown <= 0) {
            clearInterval(redirectInterval);
            window.location.href = redirectUrl;
        }
    }, 1000);
}

// Login Form Submission
loginForm.addEventListener("submit", function (e) {
    e.preventDefault();

    // If user is in lockout period, show modal and prevent form submission
    const lockoutEnd = localStorage.getItem("lockoutEnd");
    if (lockoutEnd && new Date().getTime() < parseInt(lockoutEnd)) {
        const remainingTime = Math.ceil(
            (parseInt(lockoutEnd) - new Date().getTime()) / 1000
        );
        showTooManyAttemptsModal();
        return;
    }

    let valid = true;
    emailError.classList.remove("visible");
    passwordError.classList.remove("visible");

    if (!email.value.trim()) {
        emailError.textContent = "Email is required";
        emailError.classList.add("visible");
        valid = false;
    }
    if (!password.value.trim()) {
        passwordError.textContent = "Password is required";
        passwordError.classList.add("visible");
        valid = false;
    }

    if (!valid) return; // stop if fields are invalid

    const formData = new FormData(loginForm);
    fetch(loginForm.action, {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": document
                .querySelector('meta[name="csrf-token"]')
                .getAttribute("content"),
            Accept: "application/json",
        },
        body: formData,
    })
        .then((res) => res.json())
        .then((data) => {
            if (data.success) {
                // Reset attempts on successful login
                loginAttempts = 0;
                localStorage.removeItem("lockoutEnd");
                localStorage.removeItem("loginAttempts");

                // Show success message before redirecting
                showSuccessMessage(data.redirect);
            } else {
                loginAttempts++;
                localStorage.setItem("loginAttempts", loginAttempts);

                passwordError.textContent = data.message;
                passwordError.classList.add("visible");

                // Check if we've reached the maximum attempts
                if (loginAttempts >= maxAttempts) {
                    // Set lockout end time
                    const lockoutEndTime =
                        new Date().getTime() + lockoutTime * 1000;
                    localStorage.setItem("lockoutEnd", lockoutEndTime);

                    // Show too many attempts modal
                    showTooManyAttemptsModal();
                    
                    // Start lockout
                    startLockout(lockoutTime);
                }
            }
        })
        .catch(() => {
            loginAttempts++;
            localStorage.setItem("loginAttempts", loginAttempts);

            passwordError.textContent =
                "Something went wrong. Please try again.";
            passwordError.classList.add("visible");

            // Check if we've reached the maximum attempts
            if (loginAttempts >= maxAttempts) {
                // Set lockout end time
                const lockoutEndTime =
                    new Date().getTime() + lockoutTime * 1000;
                localStorage.setItem("lockoutEnd", lockoutEndTime);

                // Show too many attempts modal
                showTooManyAttemptsModal();
                
                // Start lockout
                startLockout(lockoutTime);
            }
        });
});

// Close modals when clicking outside
window.addEventListener('click', function(e) {
    const tooManyAttemptsModal = document.getElementById('tooManyAttemptsModal');
    const successModal = document.getElementById('successModal');
    
    if (e.target === tooManyAttemptsModal) {
        // Don't close if still in lockout period - let user see the countdown
        // The modal will stay open until they click OK or time runs out
        return;
    }
    
    if (e.target === successModal) {
        successModal.style.display = 'none';
    }
});

// Close modals with escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const tooManyAttemptsModal = document.getElementById('tooManyAttemptsModal');
        const successModal = document.getElementById('successModal');
        
        if (tooManyAttemptsModal.style.display === 'flex') {
            // Don't allow closing with escape key during lockout
            // User must wait for countdown or click OK button
            return;
        }
        
        if (successModal.style.display === 'flex') {
            successModal.style.display = 'none';
        }
    }
});

// Password Toggle Functionality
function initializeAllPasswordToggles() {
    // Main login password toggle
    const togglePasswordIcon = document.getElementById("togglePasswordIcon");
    if (togglePasswordIcon) {
        togglePasswordIcon.addEventListener("click", function () {
            const passwordInput = document.getElementById("password");
            togglePasswordWithFontAwesome(passwordInput, this);
        });
    }

    // Forgot password modal toggles
    document.querySelectorAll(".toggle-password-reset-icon").forEach((icon) => {
        icon.addEventListener("click", function () {
            const input = this.parentElement.querySelector("input");
            togglePasswordWithFontAwesome(input, this);
        });
    });
}

function togglePasswordWithFontAwesome(input, icon) {
    if (input.type === "password") {
        input.type = "text";
        icon.classList.remove("fa-eye-slash");
        icon.classList.add("fa-eye");
    } else {
        input.type = "password";
        icon.classList.remove("fa-eye");
        icon.classList.add("fa-eye-slash");
    }
}

// Initialize all password toggles when DOM is loaded
document.addEventListener("DOMContentLoaded", function () {
    initializeAllPasswordToggles();
});

// Re-initialize modal toggles when modal opens
function openForgotPasswordModal() {
    document.getElementById("forgotPasswordModal").style.display = "flex";
    resetForgotPasswordForm();

    // Re-initialize password toggles for modal
    setTimeout(() => {
        document
            .querySelectorAll(".toggle-password-reset-icon")
            .forEach((icon) => {
                // Remove existing event listeners
                const newIcon = icon.cloneNode(true);
                icon.parentNode.replaceChild(newIcon, icon);

                // Add new event listener
                newIcon.addEventListener("click", function () {
                    const input = this.parentElement.querySelector("input");
                    togglePasswordWithFontAwesome(input, this);
                });
            });
    }, 100);
}

// Initialize password toggles when DOM is loaded
document.addEventListener("DOMContentLoaded", function () {
    initializePasswordToggles();
});

// Forgot Password Functions
function openForgotPasswordModal() {
    document.getElementById("forgotPasswordModal").style.display = "flex";
    resetForgotPasswordForm();

    // Re-initialize password toggles for modal
    setTimeout(initializePasswordToggles, 100);
}

function closeForgotPasswordModal() {
    document.getElementById("forgotPasswordModal").style.display = "none";
    resetForgotPasswordForm();
}

function resetForgotPasswordForm() {
    document.getElementById("forgotPasswordForm").reset();
    document.querySelectorAll(".error-text").forEach((el) => {
        el.textContent = "";
        el.classList.remove("visible");
    });

    // Reset steps
    currentStep = 1;
    updateStepIndicator();

    // Remove messages
    const existingMessages = document.querySelectorAll(
        ".success-message, .error-message"
    );
    existingMessages.forEach((msg) => msg.remove());

    clearResetTimers();
    userEmail = "";
}

function clearResetTimers() {
    if (resetTimer) clearInterval(resetTimer);
    resetTimeLeft = 60;
}

function updateStepIndicator() {
    // Hide all steps
    document.querySelectorAll(".forgot-step").forEach((step) => {
        step.classList.remove("active");
    });

    // Show current step
    document
        .getElementById(`forgot-step-${currentStep}`)
        .classList.add("active");

    // Update step indicators
    document.querySelectorAll(".step").forEach((step, index) => {
        step.classList.remove("active", "completed");
        if (index + 1 === currentStep) {
            step.classList.add("active");
        } else if (index + 1 < currentStep) {
            step.classList.add("completed");
        }
    });
}

function nextStep() {
    if (currentStep < 3) {
        currentStep++;
        updateStepIndicator();
    }
}

function prevStep() {
    if (currentStep > 1) {
        currentStep--;
        updateStepIndicator();
    }
}

async function sendResetCode() {
    const email = document.getElementById("forgot_email").value;
    const sendBtn = document.getElementById("send-reset-btn");

    // Validate email
    if (
        !email ||
        !email.match(/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/)
    ) {
        showForgotError(
            "forgot_email_error",
            "Please enter a valid email address"
        );
        return;
    }

    sendBtn.disabled = true;
    sendBtn.textContent = "Sending...";

    try {
        const response = await fetch("/password/forgot", {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": document
                    .querySelector('meta[name="csrf-token"]')
                    .getAttribute("content"),
                "Content-Type": "application/json",
                Accept: "application/json",
            },
            body: JSON.stringify({ email: email }),
        });

        const data = await response.json();

        if (response.ok) {
            showForgotSuccess(
                data.message || "Verification code sent to your email!"
            );

            // Store email for later use
            userEmail = email;

            // Move to next step
            nextStep();
            document.getElementById("reset-email-display").textContent = email;

            // Start resend countdown
            startResetCountdown();
        } else {
            showForgotError(
                "forgot_email_error",
                data.message || "Failed to send verification code"
            );
        }
    } catch (error) {
        console.error("Error sending reset code:", error);
        showForgotError(
            "forgot_email_error",
            "An error occurred. Please try again."
        );
    } finally {
        sendBtn.disabled = false;
        sendBtn.textContent = "Send Verification Code";
    }
}

async function verifyResetCode() {
    const verificationCode = document.getElementById("reset_code").value;
    const verifyBtn = document.getElementById("verify-code-btn");

    if (!verificationCode || verificationCode.length !== 6) {
        showForgotError(
            "reset_code_error",
            "Please enter a valid 6-digit verification code"
        );
        return;
    }

    verifyBtn.disabled = true;
    verifyBtn.textContent = "Verifying...";

    try {
        const response = await fetch("/password/verify-code", {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": document
                    .querySelector('meta[name="csrf-token"]')
                    .getAttribute("content"),
                "Content-Type": "application/json",
                Accept: "application/json",
            },
            body: JSON.stringify({
                email: userEmail,
                verification_code: verificationCode,
            }),
        });

        const data = await response.json();

        if (response.ok) {
            showForgotSuccess("Code verified successfully!");

            // Move to next step after successful verification
            setTimeout(() => {
                nextStep();
            }, 1000);
        } else {
            showForgotError(
                "reset_code_error",
                data.message || "Invalid verification code"
            );
        }
    } catch (error) {
        console.error("Error verifying code:", error);
        showForgotError(
            "reset_code_error",
            "An error occurred. Please try again."
        );
    } finally {
        verifyBtn.disabled = false;
        verifyBtn.textContent = "Verify Code";
    }
}

function startResetCountdown() {
    const resendBtn = document.getElementById("resend-reset-btn");
    const timerSpan = document.getElementById("resend-reset-timer");

    resendBtn.disabled = true;
    resetTimeLeft = 60;

    resetTimer = setInterval(() => {
        resetTimeLeft--;
        timerSpan.textContent = resetTimeLeft;

        if (resetTimeLeft <= 0) {
            clearInterval(resetTimer);
            resendBtn.disabled = false;
            resendBtn.innerHTML = "Resend Code";
        }
    }, 1000);
}

// Form submission
document
    .getElementById("forgotPasswordForm")
    .addEventListener("submit", async function (e) {
        e.preventDefault();
        await resetPassword();
    });

async function resetPassword() {
    const verificationCode = document.getElementById("reset_code").value;
    const newPassword = document.getElementById("new_password_reset").value;
    const confirmPassword = document.getElementById(
        "confirm_password_reset"
    ).value;
    const submitBtn = document.getElementById("reset-password-btn");

    // Clear previous errors
    document.querySelectorAll(".error-text").forEach((el) => {
        el.textContent = "";
        el.classList.remove("visible");
    });

    // Validate verification code
    if (!verificationCode || verificationCode.length !== 6) {
        showForgotError(
            "reset_code_error",
            "Please enter a valid 6-digit verification code"
        );
        return;
    }

    // Validate passwords
    if (newPassword.length < 6) {
        showForgotError(
            "new_password_reset_error",
            "Password must be at least 6 characters long"
        );
        return;
    }

    if (newPassword !== confirmPassword) {
        showForgotError(
            "confirm_password_reset_error",
            "Passwords do not match"
        );
        return;
    }

    submitBtn.disabled = true;
    submitBtn.textContent = "Resetting Password...";

    try {
        const response = await fetch("/password/reset", {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": document
                    .querySelector('meta[name="csrf-token"]')
                    .getAttribute("content"),
                "Content-Type": "application/json",
                Accept: "application/json",
            },
            body: JSON.stringify({
                email: userEmail,
                verification_code: verificationCode,
                new_password: newPassword,
                new_password_confirmation: confirmPassword,
            }),
        });

        const data = await response.json();

        if (response.ok) {
            showForgotSuccess(
                data.message ||
                    "Password reset successfully! You can now login with your new password."
            );
            setTimeout(() => {
                closeForgotPasswordModal();
            }, 3000);
        } else {
            if (data.errors) {
                // Handle validation errors
                Object.keys(data.errors).forEach((field) => {
                    const errorElement = document.getElementById(
                        field + "_error"
                    );
                    if (errorElement) {
                        errorElement.textContent = data.errors[field][0];
                        errorElement.classList.add("visible");
                    }
                });
            } else {
                showForgotError(
                    "reset_code_error",
                    data.message || "Failed to reset password"
                );
            }
        }
    } catch (error) {
        console.error("Error resetting password:", error);
        showForgotError(
            "reset_code_error",
            "An error occurred. Please try again."
        );
    } finally {
        submitBtn.disabled = false;
        submitBtn.textContent = "Reset Password";
    }
}

function showForgotError(elementId, message) {
    const errorElement = document.getElementById(elementId);
    if (errorElement) {
        errorElement.textContent = message;
        errorElement.classList.add("visible");
    }
}

function showForgotSuccess(message) {
    // Remove existing messages
    const existingMessages = document.querySelectorAll(
        ".success-message, .error-message"
    );
    existingMessages.forEach((msg) => msg.remove());

    const successDiv = document.createElement("div");
    successDiv.className = "success-message";
    successDiv.textContent = message;

    const modalBody = document.querySelector(".modal-body");
    modalBody.insertBefore(successDiv, modalBody.firstChild);

    // Auto remove after 5 seconds
    setTimeout(() => {
        if (successDiv.parentNode) {
            successDiv.remove();
        }
    }, 5000);
}

function showForgotErrorMsg(message) {
    // Remove existing messages
    const existingMessages = document.querySelectorAll(
        ".success-message, .error-message"
    );
    existingMessages.forEach((msg) => msg.remove());

    const errorDiv = document.createElement("div");
    errorDiv.className = "error-message";
    errorDiv.textContent = message;

    const modalBody = document.querySelector(".modal-body");
    modalBody.insertBefore(errorDiv, modalBody.firstChild);

    // Auto remove after 5 seconds
    setTimeout(() => {
        if (errorDiv.parentNode) {
            errorDiv.remove();
        }
    }, 5000);
}

// Close modal when clicking X
document
    .querySelector("#forgotPasswordModal .close")
    .addEventListener("click", closeForgotPasswordModal);

// Close modal with escape key
document.addEventListener("keydown", function (event) {
    if (event.key === "Escape") {
        closeForgotPasswordModal();
    }
});