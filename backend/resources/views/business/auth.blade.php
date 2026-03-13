<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>MoonLaunch | Admin Panel</title>

  <link href="https://fonts.googleapis.com/css2?family=Lilita+One&display=swap" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen">

  <div class="min-h-screen flex items-center justify-center px-4 py-10
              bg-[linear-gradient(rgba(0,0,0,0.45),rgba(0,0,0,0.45)),url('/assets/bg.png')]
              bg-cover bg-center">
    <div class="w-full max-w-[430px] rounded-2xl overflow-hidden bg-white shadow-2xl">

      <!-- Top Section with Gradient from Yellow to Orange -->
      <div class="bg-gradient-to-r from-yellow-400 to-orange-500 py-5 flex items-center justify-center">
        <img src="{{ asset('assets/logo.png') }}" 
            class="h-10 w-auto brightness-110 contrast-125 drop-shadow-[0_1px_2px_rgba(0,0,0,0.15)]" 
            alt="MoonLaunch">
      </div>
      
      <div class="px-6 sm:px-7 py-6">

        {{-- LOGIN STEP 1: Email & Password --}}
        <div id="view-login" style="display: {{ session('otp_sent') || session('forgot_otp_sent') || session('reset_password') ? 'none' : 'block' }}">
          <h2 class="text-center font-extrabold text-base text-[#E66F52]">Admin Login</h2>
          <p class="text-center text-[11px] text-gray-500 mt-1 mb-5">
            Hi Admin! Welcome back to your dashboard.
          </p>

          @if(session('error'))
            <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg">
              <p class="text-red-600 text-xs text-center">{{ session('error') }}</p>
            </div>
          @endif

          @if(session('password_reset_success'))
            <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded-lg">
              <p class="text-green-600 text-xs text-center">{{ session('password_reset_success') }}</p>
            </div>
          @endif

          <form action="{{ route('admin.login.submit') }}" method="POST" class="space-y-3">
            @csrf
            <div>
              <label class="block text-[11px] font-extrabold mb-1">Email</label>
              <input type="email" name="email" value="{{ old('email') }}" placeholder="admin@example.com" required
                     class="w-full rounded-lg border border-gray-200 bg-gray-100 px-3 py-2 text-xs
                            focus:bg-white focus:outline-none focus:ring-4 focus:ring-[#E66F52]/20 focus:border-[#E66F52]">
            </div>

            <div>
              <label class="block text-[11px] font-extrabold mb-1">Password</label>
              <div class="relative">
                <input id="login_password" type="password" name="password" placeholder="***************" required
                       class="w-full rounded-lg border border-gray-200 bg-gray-100 px-3 py-2 text-xs pr-10
                              focus:bg-white focus:outline-none focus:ring-4 focus:ring-[#E66F52]/20 focus:border-[#E66F52]">
                <button type="button" onclick="togglePass('login_password')"
                        class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-500 text-sm px-2">👁️</button>
              </div>
            </div>

            <div class="text-right">
              <button type="button" onclick="showForgotPassword()" 
                      class="text-[11px] text-[#E66F52] font-bold hover:underline">
                Forgot Password?
              </button>
            </div>

            <!-- Login Button with Gradient from Yellow to Orange -->
            <button type="submit"
                    class="w-full rounded-full bg-gradient-to-r from-yellow-400 to-orange-500 py-2.5 text-white text-xs font-extrabold
                           shadow-lg hover:brightness-95 transition">
              Admin Sign In
            </button>
          </form>
        </div>

        {{-- LOGIN STEP 2: OTP Verification --}}
        <div id="view-otp" style="display: {{ session('otp_sent') ? 'block' : 'none' }}">
          <h2 class="text-center font-extrabold text-base text-[#E66F52]">Enter OTP</h2>
          <p class="text-center text-[11px] text-gray-500 mt-1 mb-5">
            We've sent a 6-digit code to your email. Please enter it below.
          </p>

          @if(session('success'))
            <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded-lg">
              <p class="text-green-600 text-xs text-center">{{ session('success') }}</p>
            </div>
          @endif

          @if(session('otp_error'))
            <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg">
              <p class="text-red-600 text-xs text-center">{{ session('otp_error') }}</p>
            </div>
          @endif

          <form action="{{ route('admin.verify.otp') }}" method="POST" class="space-y-3">
            @csrf
            <div>
              <label class="block text-[11px] font-extrabold mb-1">OTP Code</label>
              <input type="text" name="otp" placeholder="Enter 6-digit code" required maxlength="6"
                     class="w-full rounded-lg border border-gray-200 bg-gray-100 px-3 py-2 text-xs text-center tracking-widest
                            focus:bg-white focus:outline-none focus:ring-4 focus:ring-[#E66F52]/20 focus:border-[#E66F52]">
            </div>

            <button type="submit"
                    class="w-full rounded-full bg-gradient-to-r from-yellow-400 to-orange-500 py-2.5 text-white text-xs font-extrabold
                           shadow-lg hover:brightness-95 transition">
              Verify OTP
            </button>

            <div class="flex items-center gap-3 my-2">
              <div class="h-px bg-gray-200 flex-1"></div>
              <div class="text-[11px] text-gray-400">Or</div>
              <div class="h-px bg-gray-200 flex-1"></div>
            </div>

            <p class="text-center text-[11px] mt-3">
              Didn't receive the code?
              <a href="{{ route('admin.resend.otp') }}" class="text-[#E66F52] font-bold hover:underline">Resend OTP</a>
            </p>

            <p class="text-center text-[11px] mt-2">
              <a href="{{ route('admin.login') }}" class="text-gray-500 hover:text-[#E66F52]">Back to Login</a>
            </p>
          </form>
        </div>

        {{-- FORGOT PASSWORD STEP 1: Enter Email --}}
        <div id="view-forgot-password" style="display: none">
          <h2 class="text-center font-extrabold text-base text-[#E66F52]">Forgot Password</h2>
          <p class="text-center text-[11px] text-gray-500 mt-1 mb-5">
            Enter your email address and we'll send you an OTP to reset your password.
          </p>

          @if(session('forgot_error'))
            <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg">
              <p class="text-red-600 text-xs text-center">{{ session('forgot_error') }}</p>
            </div>
          @endif

          <form action="{{ route('admin.forgot.password.submit') }}" method="POST" class="space-y-3">
            @csrf
            <div>
              <label class="block text-[11px] font-extrabold mb-1">Email</label>
              <input type="email" name="email" value="{{ old('email') }}" placeholder="admin@example.com" required
                     class="w-full rounded-lg border border-gray-200 bg-gray-100 px-3 py-2 text-xs
                            focus:bg-white focus:outline-none focus:ring-4 focus:ring-[#E66F52]/20 focus:border-[#E66F52]">
            </div>

            <button type="submit"
                    class="w-full rounded-full bg-gradient-to-r from-yellow-400 to-orange-500 py-2.5 text-white text-xs font-extrabold
                           shadow-lg hover:brightness-95 transition">
              Send OTP
            </button>

            <p class="text-center text-[11px] mt-3">
              <button type="button" onclick="showLogin()" class="text-gray-500 hover:text-[#E66F52]">
                Back to Login
              </button>
            </p>
          </form>
        </div>

        {{-- FORGOT PASSWORD STEP 2: Verify OTP --}}
        <div id="view-forgot-otp" style="display: {{ session('forgot_otp_sent') ? 'block' : 'none' }}">
          <h2 class="text-center font-extrabold text-base text-[#E66F52]">Verify OTP</h2>
          <p class="text-center text-[11px] text-gray-500 mt-1 mb-5">
            We've sent a 6-digit code to your email. Please enter it below.
          </p>

          @if(session('forgot_success'))
            <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded-lg">
              <p class="text-green-600 text-xs text-center">{{ session('forgot_success') }}</p>
            </div>
          @endif

          @if(session('forgot_otp_error'))
            <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg">
              <p class="text-red-600 text-xs text-center">{{ session('forgot_otp_error') }}</p>
            </div>
          @endif

          <form action="{{ route('admin.forgot.password.verify.otp') }}" method="POST" class="space-y-3">
            @csrf
            <div>
              <label class="block text-[11px] font-extrabold mb-1">OTP Code</label>
              <input type="text" name="otp" placeholder="Enter 6-digit code" required maxlength="6"
                     class="w-full rounded-lg border border-gray-200 bg-gray-100 px-3 py-2 text-xs text-center tracking-widest
                            focus:bg-white focus:outline-none focus:ring-4 focus:ring-[#E66F52]/20 focus:border-[#E66F52]">
            </div>

            <button type="submit"
                    class="w-full rounded-full bg-gradient-to-r from-yellow-400 to-orange-500 py-2.5 text-white text-xs font-extrabold
                           shadow-lg hover:brightness-95 transition">
              Verify OTP
            </button>

            <div class="flex items-center gap-3 my-2">
              <div class="h-px bg-gray-200 flex-1"></div>
              <div class="text-[11px] text-gray-400">Or</div>
              <div class="h-px bg-gray-200 flex-1"></div>
            </div>

            <p class="text-center text-[11px] mt-3">
              Didn't receive the code?
              <a href="{{ route('admin.forgot.password.resend.otp') }}" class="text-[#E66F52] font-bold hover:underline">Resend OTP</a>
            </p>

            <p class="text-center text-[11px] mt-2">
              <a href="{{ route('admin.login') }}" class="text-gray-500 hover:text-[#E66F52]">Back to Login</a>
            </p>
          </form>
        </div>

        {{-- FORGOT PASSWORD STEP 3: Reset Password --}}
        <div id="view-reset-password" style="display: {{ session('reset_password') ? 'block' : 'none' }}">
          <h2 class="text-center font-extrabold text-base text-[#E66F52]">Reset Password</h2>
          <p class="text-center text-[11px] text-gray-500 mt-1 mb-5">
            Please enter your new password below.
          </p>

          @if(session('reset_error'))
            <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg">
              <p class="text-red-600 text-xs text-center">{{ session('reset_error') }}</p>
            </div>
          @endif

          <form action="{{ route('admin.reset.password.submit') }}" method="POST" class="space-y-3">
            @csrf
            <div>
              <label class="block text-[11px] font-extrabold mb-1">New Password</label>
              <div class="relative">
                <input id="new_password" type="password" name="password" placeholder="***************" required
                       class="w-full rounded-lg border border-gray-200 bg-gray-100 px-3 py-2 text-xs pr-10
                              focus:bg-white focus:outline-none focus:ring-4 focus:ring-[#E66F52]/20 focus:border-[#E66F52]">
                <button type="button" onclick="togglePass('new_password')"
                        class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-500 text-sm px-2">👁️</button>
              </div>
            </div>

            <div>
              <label class="block text-[11px] font-extrabold mb-1">Confirm Password</label>
              <div class="relative">
                <input id="confirm_password" type="password" name="password_confirmation" placeholder="***************" required
                       class="w-full rounded-lg border border-gray-200 bg-gray-100 px-3 py-2 text-xs pr-10
                              focus:bg-white focus:outline-none focus:ring-4 focus:ring-[#E66F52]/20 focus:border-[#E66F52]">
                <button type="button" onclick="togglePass('confirm_password')"
                        class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-500 text-sm px-2">👁️</button>
              </div>
            </div>

            <button type="submit"
                    class="w-full rounded-full bg-gradient-to-r from-yellow-400 to-orange-500 py-2.5 text-white text-xs font-extrabold
                           shadow-lg hover:brightness-95 transition">
              Reset Password
            </button>
          </form>
        </div>

      </div>
    </div>
  </div>

  <script>
    function togglePass(id) {
      const el = document.getElementById(id);
      if(!el) return;
      el.type = el.type === 'password' ? 'text' : 'password';
    }

    function showForgotPassword() {
      document.getElementById('view-login').style.display = 'none';
      document.getElementById('view-forgot-password').style.display = 'block';
    }

    function showLogin() {
      document.getElementById('view-forgot-password').style.display = 'none';
      document.getElementById('view-login').style.display = 'block';
    }
  </script>

</body>
</html>