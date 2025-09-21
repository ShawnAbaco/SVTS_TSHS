<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Student Discipline System</title>
    <link rel="stylesheet" href="{{ asset('css/adviser/login.css') }}">
</head>
<body>
    <!-- Header with Logo, Title, and Login -->
    <header class="top-bar">
        <div class="header-left">
            <img src="{{ asset('images/logo.png') }}" alt="System Logo" class="logo">
            <h1>Student Discipline & Violation Tracking System</h1>
        </div>

<div class="top-right-login">
    <form id="loginForm" action="{{ route('auth.login') }}" method="POST">
        @csrf
        <input type="email" id="email" name="email" placeholder="Email" required>

        <div class="password-wrapper">
            <input type="password" id="password" name="password" placeholder="Password" required>
            <img src="{{ asset('images/hide.png') }}" id="togglePassword" class="toggle-password" alt="Toggle Password">
        </div>

        <button type="submit" id="loginBtn">Log In</button>
    </form>
</div>



<!-- Error Modal -->
<div id="errorModal" class="modal hidden">
    <p id="errorMessage"></p>
    <button onclick="closeModal()">Close</button>
</div>

<!-- Attempt Lockout Modal -->
<div id="attemptModal" class="modal hidden">
    <p>Please wait <span id="countdown"></span> seconds before trying again.</p>
</div>

<!-- Success Modal -->
<div id="successModal" class="modal hidden">
    <p>Login successful!</p>
</div>

    </header>

    <div class="main-container">
        <!-- Left Info Section -->
        <div class="left-section">
            <p class="subtitle">Ensuring accountability, transparency, and discipline monitoring.</p>

            <div class="features">
                <div class="feature">
                    <span class="icon">📊</span>
                    <p><strong>Track Violations</strong> Monitor student discipline records with ease.</p>
                </div>
                <div class="feature">
                    <span class="icon">👨‍🏫</span>
                    <p><strong>Adviser Reports</strong> Advisers and Prefects can manage cases in real-time.</p>
                </div>
                <div class="feature">
                    <span class="icon">🎓</span>
                    <p><strong>Strand Info</strong> Supports STEM | HUMSS | ABM | GAS | TVL strands.</p>
                </div>
            </div>
        </div>

        <!-- Right Picture Boxes (2x2 Grid) -->
        <div class="right-section">
            <div class="image-boxes">
                <div class="box"><img src="{{ asset('images/sample1.jpg') }}" alt="Showcase 1"></div>
                <div class="box"><img src="{{ asset('images/sample2.jpg') }}" alt="Showcase 2"></div>
                <div class="box"><img src="{{ asset('images/sample3.jpg') }}" alt="Showcase 3"></div>
                <div class="box"><img src="{{ asset('images/sample4.jpg') }}" alt="Showcase 4"></div>
            </div>
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

const togglePasswordIcon = document.getElementById('togglePassword');

togglePasswordIcon.addEventListener('click', () => {
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        togglePasswordIcon.src = "{{ asset('images/hide.png') }}"; // your "eye closed" icon
    } else {
        passwordInput.type = 'password';
        togglePasswordIcon.src = "{{ asset('images/show.png') }}"; // your "eye open" icon
    }
});

  </script>

</body>
</html>
