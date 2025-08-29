 <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Prefect Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background-image: url('/images/lugo.png');
            background-repeat: no-repeat;
            background-size: cover;
            background-position: center;
            position: relative;
            overflow: hidden;
        }

        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: inherit;
            filter: blur(3px);
            z-index: -1;
        }

        @media screen and (max-width: 768px) {
            body.android .flex-grow {
                display: flex;
                justify-content: center;
                align-items: center;
            }

            body.android .flex-grow > div {
                transform: scale(0.85); /* Scale down the container */
                max-width: 90%; /* Ensure the width does not exceed the screen size */
            }

            body.android .page-footer {
                display: none; /* Hide footer on Android devices */
            }

            body.android h1.text-xl {
                text-align: center; /* Center text for Android devices */
            }
        }

        .hide {
            display: none;
        }
    </style>
</head>
<body class="bg-gray-100 flex flex-col min-h-screen">

    <!-- Page Header -->
    <div class="text-center py-4 bg-blue-600 text-white">
        <h1 class="text-2xl font-bold">TAGOLOAN SENIOR HIGH SCHOOL</h1>
    </div>

    <!-- Adviser Login Button -->
    <a href="/adviser/login" 
       class="fixed top-4 left-4 bg-blue-500 text-white px-4 py-2 rounded-md font-semibold hover:bg-blue-600 transition duration-300 adviser-login-left">
        Adviser Login
    </a>

    <!-- Main Content -->
    <div class="flex items-center justify-center flex-grow px-4 md:px-0">
        <div class="relative shadow-2xl border border-gray-300 rounded-lg flex flex-col md:flex-row w-full max-w-md md:max-w-lg bg-gray-400">
            <!-- Adviser Login Button for Form -->
            <a href="/adviser/login" 
               class="absolute top-4 right-4 bg-black text-white px-2 py-1 rounded-md text-xs sm:text-sm font-semibold hover:bg-gray-800 transition duration-300 adviser-login-right hidden">
                Adviser Login
            </a>

            <!-- Logo Section -->
            <div class="bg-blue-600 flex flex-col items-center justify-center md:w-1/2 rounded-t-lg md:rounded-l-lg text-white p-4">
                <img src="/images/Logo.png" alt="TSHS Logo" class="w-32 h-32 md:w-48 md:h-48 object-contain">
                <p class="text-sm font-semibold text-center mt-4 px-4">
                    DEVELOPMENT OF STUDENT VIOLATION TRACKING SYSTEM
                </p>
            </div>

            <!-- Login Form -->
            <div class="p-6 md:p-8 w-full md:w-1/2 bg-black-600 text-white rounded-b-lg md:rounded-r-lg">
                <h1 class="text-xl font-bold mb-4">Prefect Login</h1>
                <form action="/prefect/login" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label for="email" class="block text-sm font-medium">Email</label>
                        <input type="email" id="email" name="email" placeholder="Enter your email" 
                               class="w-full mt-1 px-4 py-2 text-sm md:text-base border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-gray-50 text-green-900" 
                               required>
                    </div>
                    <div>
                        <label for="password" class="block text-sm font-medium">Password</label>
                        <input type="password" id="password" name="password" placeholder="Enter your password" 
                               class="w-full mt-1 px-4 py-2 text-sm md:text-base border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-gray-50 text-green-900" 
                               required>
                    </div>
                    <div class="flex items-center">
                        <input type="checkbox" id="show-password" class="mr-2">
                        <label for="show-password" class="text-sm">Show Password</label>
                    </div>
                    @if ($errors->any())
                        <p class="text-sm text-red-500 mt-2">{{ $errors->first() }}</p>
                    @endif
                    <button type="submit" 
                            class="w-full bg-red-500 text-white py-2 rounded-md font-semibold hover:bg-red-600 transition duration-300">
                        Login
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Page Footer -->
    <div class="text-center py-4 bg-blue-600 text-white page-footer">
        <p class="text-lg font-semibold">&copy; 2025 DEVELOPMENT OF STUDENT VIOLATION TRACKING SYSTEM</p>
    </div>

    <script>
        // Detect Android devices and add a class to the body
        if (/android/i.test(navigator.userAgent)) {
            document.body.classList.add('android');
            // Hide the left-side adviser login button on Android devices
            document.querySelector('.adviser-login-left').style.display = 'none';
            // Show the right-side adviser login button for Android
            document.querySelector('.adviser-login-right').classList.remove('hidden');
        }

        // Toggle password visibility
        document.getElementById('show-password').addEventListener('change', function () {
            const passwordField = document.getElementById('password');
            passwordField.type = this.checked ? 'text' : 'password';
        });
    </script>
</body>
</html>
