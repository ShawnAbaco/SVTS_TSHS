<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
  <title>Prefect Login</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; font-family: Arial, sans-serif; }

    body {
      background-image: url('/images/lugo.png');
      background-repeat: no-repeat;
      background-size: cover;
      background-position: center;
      position: relative;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }

    body::before {
      content: '';
      position: absolute;
      top: 0; left: 0;
      width: 100%; height: 100%;
      background: inherit;
      filter: blur(3px);
      z-index: -1;
    }

    .page-header {
background: linear-gradient(135deg, rgb(100, 0, 0), rgb(75, 0, 130), rgb(255, 165, 0));
background-repeat: no-repeat;
background-attachment: fixed;
      color: #fff;
      text-align: center;
      padding: 15px;
      font-weight: bold;
      font-size: 22px;
      box-shadow: 0 2px 5px rgba(0,0,0,0.3);
    }

    .adviser-login-left, .adviser-login-right {
      position: fixed;
      top: 15px;
      padding: 8px 16px;
      background: #2e7d32;
      color: #fff;
      font-weight: bold;
      border-radius: 6px;
      text-decoration: none;
      transition: background 0.3s ease;
      border: 1px solid #1b5e20;
      z-index: 10;
    }
    .adviser-login-left { left: 15px; }
    .adviser-login-right { right: 15px; display: none; }
    .adviser-login-left:hover, .adviser-login-right:hover { background: #388e3c; }

    .container { flex-grow: 1; display: flex; justify-content: center; align-items: center; padding: 20px; }
    .login-box {
      display: flex;
      flex-direction: column;
      max-width: 350px;
      width: 100%;
      border-radius: 12px;
      overflow: hidden;
      box-shadow: 0 8px 18px rgba(0,0,0,0.4);
      background: #fff;
    }

    .logo-section { text-align: center; padding: 0; }
    .logo-section img { width: 220px; height: auto; object-fit: contain; }
    .logo-section h3 { margin-top: 8px; font-size: 14px; font-weight: 600; color: #2e7d32; }

    .form-section { padding: 20px; background: #fff; color: #111; }
    .form-section h1 { font-size: 18px; margin-bottom: 15px; text-align: center; color: #2e7d32; }
    .form-group { margin-bottom: 18px; position: relative; }
    .form-section label { font-size: 13px; display: block; margin-bottom: 6px; color: #000; }

    .input-icon-wrapper { position: relative; }
    .input-icon-wrapper i { position: absolute; top: 50%; transform: translateY(-50%); color: #2e7d32; }
    .input-icon-wrapper i.fa-envelope,
    .input-icon-wrapper i.fa-lock { left: 10px; }

    .input-icon-wrapper input {
      width: 100%;
      padding: 10px 12px;
      padding-left: 35px;
      padding-right: 35px;
      border-radius: 6px;
      border: 1px solid #2e7d32;
      font-size: 14px;
      color: #000;
      background: #f9f9f9;
    }
    .input-icon-wrapper input:focus { border-color: #388e3c; outline: none; }

    .toggle-password { right: 10px; cursor: pointer; position: absolute; top: 50%; transform: translateY(-50%); color: #2e7d32; }

    .warning { font-size: 12px; color: #B91C1C; margin-top: 5px; display: none; }

    .form-section button {
      width: 100%;
      padding: 10px;
background: linear-gradient(135deg, rgb(100, 0, 0), rgb(75, 0, 130), rgb(255, 165, 0));
background-repeat: no-repeat;
background-attachment: fixed;
      border: none;
      border-radius: 6px;
      font-size: 14px;
      font-weight: bold;
      color: #fff;
      cursor: pointer;
      transition: background 0.3s ease;
    }
    .form-section button:disabled { background: gray; cursor: not-allowed; }
    .form-section button:hover:enabled { background: #388e3c; }

    .page-footer {
background: linear-gradient(135deg, rgb(100, 0, 0), rgb(75, 0, 130), rgb(255, 165, 0));
background-repeat: no-repeat;
background-attachment: fixed;
      color: #fff;
      text-align: center;
      padding: 10px;
      font-size: 13px;
      font-weight: bold;
      box-shadow: 0 -2px 5px rgba(0,0,0,0.3);
    }

    .modal { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); display: none; justify-content: center; align-items: center; z-index: 1000; }
    .modal-content { background: #fff; padding: 20px; border-radius: 8px; max-width: 320px; width: 90%; text-align: center; box-shadow: 0 5px 15px rgba(0,0,0,0.4); }
    .modal-content h2 { color: #2e7d32; margin-bottom: 10px; font-size: 16px; }
    .modal-content p { color: #333; font-size: 13px; margin-bottom: 15px; }
    .modal-content button { background: #2e7d32; border: none; color: #fff; padding: 6px 14px; border-radius: 5px; font-size: 13px; cursor: pointer; }
    .modal-content button:hover { background: #388e3c; }
  </style>
</head>
<body>

  <div class="page-header">TAGOLOAN SENIOR HIGH SCHOOL</div>

  <a href="/adviser/login" class="adviser-login-left">Adviser Login</a>
  <a href="/adviser/login" class="adviser-login-right">Adviser Login</a>

  <div class="container">
    <div class="login-box">
      <div class="logo-section">
        <img src="/images/Logo.png" alt="TSHS Logo">
        <h3>STUDENT VIOLATION TRACKING SYSTEM</h3>
      </div>
      <div class="form-section">
        <h1>Prefect Login</h1>
        <form action="{{ route('prefect.login') }}" method="POST" id="loginForm">
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
      <h2>Success</h2>
      <p>Login successful!!</p>
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
    const errorModal = document.getElementById('errorModal');
    const errorMessage = document.getElementById('errorMessage');
    const successModal = document.getElementById('successModal');
    const attemptModal = document.getElementById('attemptModal');
    const countdownEl = document.getElementById('countdown');
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');

    let attempts = 0;
    let lockout = false;
    let lockoutTime = 10; // seconds

    // Toggle password visibility
    function togglePassword() {
      const type = passwordInput.type === "password" ? "text" : "password";
      passwordInput.type = type;
      const icon = document.querySelector('.toggle-password');
      icon.classList.toggle("fa-eye");
      icon.classList.toggle("fa-eye-slash");
    }

    loginForm.addEventListener('submit', async function(e) {
      e.preventDefault();
      if (lockout) return;

      const formData = new FormData(loginForm);
      let response = await fetch("{{ route('prefect.login') }}", {
        method: "POST",
        headers: { "X-CSRF-TOKEN": formData.get("_token") },
        body: formData
      });
      let data = await response.json();

      if (data.success) {
        successModal.style.display = 'flex';
        setTimeout(() => { window.location.href = data.redirect; }, 2000);
      } else {
        attempts++;
        // Clear fields when wrong credentials

        passwordInput.value = "";

        if (attempts >= 3) {
          lockout = true;
          loginBtn.disabled = true;
          let timeLeft = lockoutTime;
          countdownEl.innerText = timeLeft;
          attemptModal.style.display = 'flex';

          let timer = setInterval(() => {
            timeLeft--;
            countdownEl.innerText = timeLeft;
            if (timeLeft <= 0) {
              clearInterval(timer);
              attemptModal.style.display = 'none';
              lockout = false;
              attempts = 0;
              loginBtn.disabled = false;
            }
          }, 1000);
        } else {
          errorMessage.innerText = data.message;
          errorModal.style.display = 'flex';
        }
      }
    });

    function closeModal() { errorModal.style.display = 'none'; }
    function closeSuccessModal() {
      successModal.style.display = 'none';
      window.location.href = "{{ route('prefect.dashboard') }}";
    }
  </script>
</body>
</html>
