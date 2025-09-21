const loginForm = document.getElementById('loginForm');
const loginBtn = document.getElementById('loginBtn');
const emailInput = document.getElementById('email');
const passwordInput = document.getElementById('password');
const errorModal = document.getElementById('errorModal');
const errorMessage = document.getElementById('errorMessage');
const attemptModal = document.getElementById('attemptModal');
const countdownSpan = document.getElementById('countdown');
const successModal = document.getElementById('successModal');

let attemptCount = 0;
const maxAttempts = 5;
const lockoutTime = 10; // seconds

loginForm.addEventListener('submit', function(e) {
  e.preventDefault();

  const formData = new FormData(loginForm);

  fetch(loginForm.dataset.loginUrl, {
    method: "POST",
    headers: {
      "X-CSRF-TOKEN": loginForm.dataset.csrf
    },
    body: formData
  })
  .then(res => res.json())
  .then(data => {
    if (data.success) {
      successModal.style.display = 'flex';
      window.location.href = data.redirect;
    } else {
      attemptCount++;
      if (attemptCount >= maxAttempts) {
        loginBtn.disabled = true;
        attemptModal.style.display = 'flex';

        let timeLeft = lockoutTime;
        countdownSpan.innerText = timeLeft;
        loginBtn.innerText = `Wait (${timeLeft}s)`;

        const countdownInterval = setInterval(() => {
          timeLeft--;
          countdownSpan.innerText = timeLeft;
          loginBtn.innerText = `Wait (${timeLeft}s)`;
          if(timeLeft <= 0){
            clearInterval(countdownInterval);
            attemptModal.style.display = 'none';
            loginBtn.disabled = false;
            loginBtn.innerText = 'Login';
            attemptCount = 0;
          }
        }, 1000);
      } else {
        errorMessage.innerText = data.message;
        errorModal.style.display = 'flex';
      }
    }
  })
  .catch(err => {
    console.error("Login error:", err);
    errorMessage.innerText = "Something went wrong. Please try again.";
    errorModal.style.display = 'flex';
  });
});

function togglePassword() {
  const eyeIcon = document.querySelector('.toggle-password');
  if (passwordInput.type === 'password') {
    passwordInput.type = 'text';
    eyeIcon.classList.remove('fa-eye');
    eyeIcon.classList.add('fa-eye-slash');
  } else {
    passwordInput.type = 'password';
    eyeIcon.classList.remove('fa-eye-slash');
    eyeIcon.classList.add('fa-eye');
  }
}

function closeModal() { errorModal.style.display = 'none'; }

if (/android/i.test(navigator.userAgent)) {
  document.querySelector('.prefect-login-left').style.display = 'none';
  document.querySelector('.prefect-login-right').style.display = 'block';
}
