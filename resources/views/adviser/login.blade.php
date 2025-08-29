<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Adviser Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
     <link rel="stylesheet" href="{{ asset('css/adviser/loginstyle.css') }}">
   
</head>
<body class="flex flex-col min-h-screen bg-gray-100 text-black">
    <!-- Page Header -->
    <div class="text-center py-4 bg-black text-white">
        <h1 class="text-xl md:text-2xl font-bold">TAGOLOAN SENIOR HIGH SCHOOL</h1>
    </div>

    <!-- Admin Login Button (Global, Top-Left) -->
    <a href="/prefect/login" 
       class="fixed top-4 left-4 bg-blue-500 text-white px-4 py-2 rounded-md text-sm font-semibold hover:bg-blue-600 transition duration-300 admin-login-left">
        Admin Login
    </a>

    <!-- Login Section -->
    <div class="flex flex-col items-center justify-center flex-grow px-4 md:px-0">
        <div class="relative shadow-2xl border border-gray-300 rounded-lg flex flex-col md:flex-row w-full max-w-md md:max-w-lg bg-gray-400">
            <!-- Admin Login Button for Form -->
            <a href="/admin/login" 
               class="absolute top-4 right-4 bg-blue-500 text-white px-2 py-1 rounded-md text-xs sm:text-sm font-semibold hover:bg-blue-600 transition duration-300 admin-login-right hidden">
                Admin Login
            </a>

            <!-- Logo Section -->
            <div class="flex flex-col items-center justify-center md:w-1/2 rounded-t-lg md:rounded-l-lg bg-gray-300 p-4 md:p-0">
                <img src="/images/Logo.png" alt="Adviser Logo" class="w-32 h-32 md:w-48 md:h-48 object-contain">
                <p class="mt-4 text-center text-sm md:text-lg font-bold px-2 md:px-4">
                    DEVELOPMENT OF STUDENT VIOLATION TRACKING SYSTEM
                </p>
            </div>

            <!-- Login Form -->
            <div class="p-6 md:p-8 w-full md:w-1/2 rounded-b-lg md:rounded-r-lg">
                <h1 class="text-lg md:text-xl font-bold mb-4 md:mb-6">Adviser Login</h1>
                <form action="/adviser/login" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label for="email" class="block text-sm font-medium">Email</label>
                        <input type="email" id="email" name="email" placeholder="Enter your email" 
                               class="w-full mt-1 px-4 py-2 text-sm md:text-base border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                               required>
                    </div>
                    <div>
                        <label for="password" class="block text-sm font-medium">Password</label>
                        <input type="password" id="password" name="password" placeholder="Enter your password" 
                               class="w-full mt-1 px-4 py-2 text-sm md:text-base border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
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
                            class="w-full bg-blue-500 text-white py-2 px-4 text-sm md:text-base rounded-md font-semibold hover:bg-blue-600 transition duration-300">
                        Login
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Page Footer -->
    <div class="text-center py-4 bg-black text-white">
        <p class="text-sm md:text-lg font-semibold">&copy; 2025 DEVELOPMENT OF STUDENT VIOLATION TRACKING SYSTEM</p>
    </div>

    <script>
        // Detect Android devices and add a class to the body
        if (/android/i.test(navigator.userAgent)) {
            document.body.classList.add('android');
            // Hide the left-side admin login button on Android devices
            document.querySelector('.admin-login-left').style.display = 'none';
            // Show the right-side admin login button for Android
            document.querySelector('.admin-login-right').classList.remove('hidden');
        }

        // Toggle password visibility
        document.getElementById('show-password').addEventListener('change', function () {
            const passwordField = document.getElementById('password');
            passwordField.type = this.checked ? 'text' : 'password';
        });
    </script>
</body>
</html>
