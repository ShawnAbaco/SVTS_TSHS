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
<script src="{{ asset('js/adviser/login.js') }}"></script>

</body>
</html>
