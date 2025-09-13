<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
  <title>Adviser Login</title>
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
      color: #111;
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

    /* HEADER & FOOTER */
    .page-header { 
      background:rgb(0, 42, 105); 
      color: white; 
      text-align: center; 
      padding: 15px; 
      font-weight: bold; 
      font-size: 22px; 
      box-shadow: 0 2px 5px rgba(0,0,0,0.3); 
    }

    .page-footer { 
      background:rgb(0, 42, 105); 
      color: white; 
      text-align: center; 
      padding: 10px; 
      font-size: 13px; 
      font-weight: bold; 
      box-shadow: 0 -2px 5px rgba(0,0,0,0.3); 
    }

    /* LINKS */
    .prefect-login-left, .prefect-login-right {
      position: fixed; top: 15px; 
      padding: 8px 16px; 
      background: black; 
      color: white; 
      font-weight: bold; 
      border-radius: 6px;
      text-decoration: none; 
      transition: 0.3s ease; 
      border: 1px solidrgb(1, 39, 97); 
      z-index: 10;
    }
    .prefect-login-left { left: 15px; }
    .prefect-login-right { right: 15px; display: none; }
    .prefect-login-left:hover, .prefect-login-right:hover { background:rgb(0, 42, 105); color: #fff; }

    /* CONTAINER */
    .container { 
      flex-grow: 1; 
      display: flex; 
      justify-content: center; 
      align-items: center; 
      padding: 20px; 
    }

    /* LOGIN BOX */
    .login-box { 
      display: flex; 
      flex-direction: column; 
      max-width: 350px; 
      width: 100%; 
      border-radius: 12px; 
      overflow: hidden; 
      box-shadow: 0 8px 18px rgba(0,0,0,0.3); 
      background: #fff; 
      border: 2px solidrgb(0, 40, 100);
    }

    /* LOGO SECTION */
    .logo-section { text-align: center; padding: 0; }
    .logo-section img { width: 220px; height: auto; object-fit: contain; }
    .logo-section p { font-size: 13px; margin-top: 8px; font-weight: 600; color:rgb(0, 42, 105); }

    /* FORM */
    .form-section { padding: 20px; }
    .form-section h1 { font-size: 25px; margin-bottom: 15px; text-align: center; color:rgb(0, 42, 105); }
    .form-group { margin-bottom: 18px; position: relative; }
    .form-section label { font-size: 13px; display: block; margin-bottom: 6px; color: black;font-weight: bold; }

    /* INPUTS */
    .input-icon-wrapper { position: relative; }
    .input-icon-wrapper i { position: absolute; top: 50%; transform: translateY(-50%); color:rgb(0, 42, 105); }
    .input-icon-wrapper i.fa-envelope, .input-icon-wrapper i.fa-lock { left: 10px; }

    .input-icon-wrapper input { 
      width: 100%; 
      padding: 10px 12px; 
      padding-left: 35px; 
      padding-right: 35px; 
      border-radius: 6px; 
      border: 1px rgb(0, 42, 105); 
      font-size: 14px; 
      color: black; 
      background: #fff; 
    }
    .input-icon-wrapper input:focus { border-color: black; outline: none; }
    .toggle-password { right: 10px; cursor: pointer; position: absolute; top: 50%; transform: translateY(-50%); color:rgb(0, 42, 105); }

    /* WARNINGS */
.warning { 
    font-size: 12px; 
    color: #B91C1C; 
    margin-top: 5px; 
    display: none; 
    font-weight: bold; 
}


    /* BUTTON */
    .form-section button { 
      width: 100%; 
      padding: 10px; 
      background:rgb(0, 42, 105); 
      border: none; 
      border-radius: 6px; 
      font-size: 14px; 
      font-weight: bold; 
      color: white; 
      cursor: pointer; 
      transition: background 0.3s ease; 
    }
    .form-section button:hover { background: black; }

    /* MODALS */
    .modal { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); display: none; justify-content: center; align-items: center; z-index: 1000; }
    .modal-content { background: #fff; padding: 20px; border-radius: 8px; max-width: 320px; width: 90%; text-align: center; box-shadow: 0 5px 15px rgba(0,0,0,0.4); border: 2px solid #0d47a1; }
    .modal-content h2 { color: #B91C1C; margin-bottom: 10px; font-size: 16px; }
    .modal-content p { color: #111; font-size: 13px; margin-bottom: 15px; }
    .modal-content button { background: #0d47a1; border: none; color: #fff; padding: 6px 14px; border-radius: 5px; font-size: 13px; cursor: pointer; }
    .modal-content button:hover { background: black; }
  </style>
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
      let valid = true;
      if (!emailInput.value || !/\S+@\S+\.\S+/.test(emailInput.value)) {
        emailWarning.style.display = 'block';
        emailInput.style.borderColor = '#B91C1C';
        valid = false;
      } else {
        emailWarning.style.display = 'none';
        emailInput.style.borderColor = '#0d47a1';
      }
      if (!passwordInput.value) {
        passwordWarning.style.display = 'block';
        passwordInput.style.borderColor = '#B91C1C';
        valid = false;
      } else {
        passwordWarning.style.display = 'none';
        passwordInput.style.borderColor = '#0d47a1';
      }
      if (!valid) { e.preventDefault(); return; }

      @if ($errors->any())
        e.preventDefault();
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
      document.querySelector('.prefect-login-left').style.display = 'none';
      document.querySelector('.prefect-login-right').style.display = 'block';
    }
  </script>
</body>
</html>
