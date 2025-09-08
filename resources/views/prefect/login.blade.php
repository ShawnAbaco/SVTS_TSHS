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

    .page-header { background: white; color: #111; text-align: center; padding: 15px; font-weight: bold; font-size: 22px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }

    .adviser-login-left, .adviser-login-right {
      position: fixed; top: 15px; padding: 8px 16px; background: white; color: #111; font-weight: bold; border-radius: 6px;
      text-decoration: none; transition: background 0.3s ease; border: 1px solid #ccc; z-index: 10;
    }
    .adviser-login-left { left: 15px; }
    .adviser-login-right { right: 15px; display: none; }
    .adviser-login-left:hover, .adviser-login-right:hover { background: #f0f0f0; }

    .container { flex-grow: 1; display: flex; justify-content: center; align-items: center; padding: 20px; }

    .login-box { display: flex; flex-direction: column; max-width: 350px; width: 100%; border-radius: 12px; overflow: hidden; box-shadow: 0 8px 18px rgba(0,0,0,0.2); background: white; }

    .logo-section { text-align: center; padding: 0; }
    .logo-section img { width: 220px; height: auto; object-fit: contain; }
    .logo-section p { font-size: 13px; margin-top: 8px; font-weight: 600; color: #111; }

    .form-section { padding: 20px; background: white; color: #111; }
    .form-section h1 { font-size: 18px; margin-bottom: 15px; text-align: center; }
    .form-group { margin-bottom: 18px; position: relative; }
    .form-section label { font-size: 13px; display: block; margin-bottom: 6px; }

    .input-icon-wrapper { position: relative; }
    .input-icon-wrapper i { position: absolute; top: 50%; transform: translateY(-50%); color: #999; }
    .input-icon-wrapper i.fa-envelope, .input-icon-wrapper i.fa-lock { left: 10px; }

    .input-icon-wrapper input { width: 100%; padding: 10px 12px; padding-left: 35px; padding-right: 35px; border-radius: 6px; border: 1px solid #ccc; font-size: 14px; color: #111; background: #fff; }
    .input-icon-wrapper input:focus { border-color: #999; outline: none; }
    .toggle-password { right: 10px; cursor: pointer; position: absolute; top: 50%; transform: translateY(-50%); color: #999; }

    .warning { font-size: 12px; color: #B91C1C; margin-top: 5px; display: none; }

    .form-section button { width: 100%; padding: 10px; background: white; border: 1px solid #ccc; border-radius: 6px; font-size: 14px; font-weight: bold; color: #111; cursor: pointer; transition: background 0.3s ease; }
    .form-section button:hover { background: #f0f0f0; }

    .page-footer { background: white; color: #111; text-align: center; padding: 10px; font-size: 13px; font-weight: bold; box-shadow: 0 -2px 5px rgba(0,0,0,0.1); }

    /* Modals */
    .modal { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); display: none; justify-content: center; align-items: center; z-index: 1000; }
    .modal-content { background: white; padding: 20px; border-radius: 8px; max-width: 320px; width: 90%; text-align: center; box-shadow: 0 5px 15px rgba(0,0,0,0.4); }
    .modal-content h2 { color: #B91C1C; margin-bottom: 10px; font-size: 16px; }
    .modal-content p { color: #333; font-size: 13px; margin-bottom: 15px; }
    .modal-content button { background: white; border: 1px solid #ccc; color: #111; padding: 6px 14px; border-radius: 5px; font-size: 13px; cursor: pointer; }
    .modal-content button:hover { background: #f0f0f0; }
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
        <p>DEVELOPMENT OF STUDENT VIOLATION TRACKING SYSTEM</p>
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
    const emailWarning = document.getElementById('emailWarning');
    const passwordWarning = document.getElementById('passwordWarning');
    const errorModal = document.getElementById('errorModal');
    const attemptModal = document.getElementById('attemptModal');
    const countdownSpan = document.getElementById('countdown');

    let attemptCount = 0;
    const maxAttempts = 3;
    const lockoutTime = 10; // seconds

    loginForm.addEventListener('submit', function(e) {
      // Client-side validation
      let valid = true;
      if (!emailInput.value || !/\S+@\S+\.\S+/.test(emailInput.value)) {
        emailWarning.style.display = 'block';
        emailInput.style.borderColor = '#B91C1C';
        valid = false;
      } else {
        emailWarning.style.display = 'none';
        emailInput.style.borderColor = '#ccc';
      }
      if (!passwordInput.value) {
        passwordWarning.style.display = 'block';
        passwordInput.style.borderColor = '#B91C1C';
        valid = false;
      } else {
        passwordWarning.style.display = 'none';
        passwordInput.style.borderColor = '#ccc';
      }
      if (!valid) { e.preventDefault(); return; }

      // Only increment attempts if Laravel returns an error
      @if ($errors->any())
        e.preventDefault(); // prevent default form submission
        attemptCount++;

        if(attemptCount >= maxAttempts) {
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
        }

        // Show invalid credentials modal
        const errorMsg = "{{ $errors->first() }}";
        document.getElementById('errorMessage').innerText = errorMsg.includes('email') || errorMsg.includes('password') ? "Invalid credentials. Please try again." : errorMsg;
        errorModal.style.display = 'flex';
      @endif
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
      document.querySelector('.adviser-login-left').style.display = 'none';
      document.querySelector('.adviser-login-right').style.display = 'block';
    }
  </script>
</body>
</html>
