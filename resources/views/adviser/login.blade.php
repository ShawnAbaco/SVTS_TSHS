<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
  <title>Adviser Login</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
    <link rel="stylesheet" href="{{ asset('css/adviser/login.css') }}">

</head>
<body>

  <div class="page-header">TAGOLOAN SENIOR HIGH SCHOOL</div>

  <a href="/prefect/login" class="prefect-login-left">Prefect Login</a>
  <a href="/prefect/login" class="prefect-login-right">Prefect Login</a>

  <div class="container">
    <div class="login-box">
      <div class="logo-section">
        <img src="/images/Logo.png" alt="TSHS Logo">
        <h3>STUDENT VIOLATION TRACKING SYSTEM</h3>

      </div>
      <div class="form-section">
        <h1>Adviser </h1>
        <form action="{{ route('adviser.login') }}" method="POST" id="loginForm">
  @csrf
  <div class="form-group">
    <label for="email">Email</label>
    <div class="input-icon-wrapper">
      <i class="fa fa-envelope"></i>
      <input type="email" id="email" name="email" placeholder="Enter your email">
    </div>
    <div class="warning" id="emailWarning">Please enter a valid email.</div>
  </div>

  <div class="form-group">
    <label for="password">Password</label>
    <div class="input-icon-wrapper">
      <i class="fa fa-lock"></i>
      <input type="password" id="password" name="password" placeholder="Enter your password">
      <i class="fa fa-eye toggle-password" onclick="togglePassword()"></i>
    </div>
    <div class="warning" id="passwordWarning">Password is required.</div>
  </div>

  <button type="submit" id="loginBtn">Login</button>

  <!-- Forgot Password link -->
  <div style="text-align:center; margin-top:10px;">
    <a href="/forgot-password" style="font-size:13px; color:rgb(255, 18, 1); font-weight:bold; text-decoration:none;">
      Forgot Password?
    </a>
  </div>
</form>

      </div>
    </div>
  </div>

  <div class="page-footer">&copy; 2025 DEVELOPMENT OF STUDENT VIOLATION TRACKING SYSTEM</div>

  <!-- Invalid credentials modal -->
  <div id="errorModal" class="modal">
    <div class="modal-content">
      <h2>Error</h2>
      <p id="errorMessage">Invalid credentials. Please try again.</p>
      <button onclick="closeModal()">OK</button>
    </div>
  </div>

<!-- Success modal -->
<div id="successModal" class="modal">
  <div class="modal-content">
    <h2 style="color:green;">Login Successful</h2>
    <p>Redirecting to dashboard...</p>
  </div>
</div>


  <!-- Too many attempts modal -->
  <div id="attemptModal" class="modal">
    <div class="modal-content">
      <h2>Too Many Attempts</h2>
      <p>Please wait <span id="countdown">10</span> seconds before trying again.</p>
    </div>
  </div>

  <script>
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
    e.preventDefault(); // stop normal submit

    const formData = new FormData(loginForm);

    fetch("{{ route('adviser.login') }}", {
      method: "POST",
      headers: {
        "X-CSRF-TOKEN": "{{ csrf_token() }}"
      },
      body: formData
    })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        // Show success modal briefly
        successModal.style.display = 'flex';

        // Redirect immediately
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

  // Android button switcher
  if (/android/i.test(navigator.userAgent)) {
    document.querySelector('.prefect-login-left').style.display = 'none';
    document.querySelector('.prefect-login-right').style.display = 'block';
  }
  </script>
</body>
</html>
