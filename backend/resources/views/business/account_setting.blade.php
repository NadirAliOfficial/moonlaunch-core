<x-business-layout title="Account Settings">
  <div class="space-y-6 ">
    {{-- Header Section --}}
    <div>
      <p class="text-gray-400 mt-1">Manage your admin profile and security settings</p>
    </div>

    {{-- Profile Section --}}
    <div class="bg-gradient-to-br from-[#0a0a0a] to-[#151515] border border-white/10 rounded-2xl p-6">
      <div class="flex items-start gap-6 mb-6">
        <div class="relative group">
          <div class="h-24 w-24 rounded-full bg-gradient-to-br from-yellow-400 to-orange-500 flex items-center justify-center text-black font-bold text-3xl">
            AD
          </div>
          <button class="absolute bottom-0 right-0 h-8 w-8 rounded-full bg-white/10 hover:bg-white/20 border border-white/20 flex items-center justify-center transition opacity-0 group-hover:opacity-100">
            <i class="fas fa-camera text-white text-xs"></i>
          </button>
        </div>
        <div class="flex-1">
          <h2 class="text-2xl font-bold text-white mb-1">Admin User</h2>
          <p class="text-gray-400 mb-3">Platform Administrator</p>
          <div class="flex flex-wrap gap-2">
            <span class="px-3 py-1 bg-yellow-500/20 text-yellow-400 rounded-full text-xs font-semibold">Super Admin</span>
            <span class="px-3 py-1 bg-green-500/20 text-green-400 rounded-full text-xs font-semibold">Active</span>
            <span class="px-3 py-1 bg-blue-500/20 text-blue-400 rounded-full text-xs font-semibold">Verified</span>
          </div>
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="p-4 rounded-lg bg-white/5 border border-white/10">
          <div class="flex items-center gap-3 mb-2">
            <div class="h-10 w-10 rounded-lg bg-blue-500/20 flex items-center justify-center">
              <i class="fas fa-user-shield text-blue-400"></i>
            </div>
            <div>
              <p class="text-gray-400 text-xs">Account Type</p>
              <p class="text-white font-semibold">Administrator</p>
            </div>
          </div>
        </div>
        <div class="p-4 rounded-lg bg-white/5 border border-white/10">
          <div class="flex items-center gap-3 mb-2">
            <div class="h-10 w-10 rounded-lg bg-green-500/20 flex items-center justify-center">
              <i class="fas fa-calendar-check text-green-400"></i>
            </div>
            <div>
              <p class="text-gray-400 text-xs">Member Since</p>
              <p class="text-white font-semibold">Jan 2024</p>
            </div>
          </div>
        </div>
        <div class="p-4 rounded-lg bg-white/5 border border-white/10">
          <div class="flex items-center gap-3 mb-2">
            <div class="h-10 w-10 rounded-lg bg-purple-500/20 flex items-center justify-center">
              <i class="fas fa-clock text-purple-400"></i>
            </div>
            <div>
              <p class="text-gray-400 text-xs">Last Login</p>
              <p class="text-white font-semibold">2 hours ago</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- Profile Information Form --}}
    <div class="bg-gradient-to-br from-[#0a0a0a] to-[#151515] border border-white/10 rounded-2xl p-6">
      <div class="flex items-center gap-3 mb-6">
        <div class="h-10 w-10 rounded-lg bg-gradient-to-br from-yellow-400 to-orange-500 flex items-center justify-center">
          <i class="fas fa-user text-black"></i>
        </div>
        <div>
          <h2 class="text-xl font-bold text-white">Profile Information</h2>
          <p class="text-gray-400 text-sm">Update your account details</p>
        </div>
      </div>

      <form class="space-y-5">
        {{-- Name Field --}}
        <div>
          <label class="block text-gray-300 text-sm font-semibold mb-2">
            Full Name
          </label>
          <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
              <i class="fas fa-user text-gray-500"></i>
            </div>
            <input 
              type="text" 
              value="Admin User"
              class="w-full pl-11 pr-4 py-3 bg-white/5 border border-white/10 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:border-yellow-400/50 focus:ring-2 focus:ring-yellow-400/20 transition"
              placeholder="Enter your full name"
            >
          </div>
        </div>

        {{-- Email Field --}}
        <div>
          <label class="block text-gray-300 text-sm font-semibold mb-2">
            Email Address
          </label>
          <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
              <i class="fas fa-envelope text-gray-500"></i>
            </div>
            <input 
              type="email" 
              value="admin@moonlaunch.com"
              class="w-full pl-11 pr-4 py-3 bg-white/5 border border-white/10 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:border-yellow-400/50 focus:ring-2 focus:ring-yellow-400/20 transition"
              placeholder="Enter your email"
            >
          </div>
          <p class="text-gray-500 text-xs mt-2">We'll send verification email if you change this</p>
        </div>

        {{-- Save Button --}}
        <div class="flex items-center gap-3 pt-4">
          <button 
            type="submit"
            class="px-6 py-3 bg-gradient-to-r from-yellow-400 to-orange-500 hover:from-yellow-500 hover:to-orange-600 rounded-lg text-black font-semibold transition">
            <i class="fas fa-save mr-2"></i>Save Changes
          </button>
          <button 
            type="button"
            class="px-6 py-3 bg-white/5 hover:bg-white/10 border border-white/10 rounded-lg text-white font-semibold transition">
            Cancel
          </button>
        </div>
      </form>
    </div>

    {{-- Password Update Form --}}
    <div class="bg-gradient-to-br from-[#0a0a0a] to-[#151515] border border-white/10 rounded-2xl p-6">
      <div class="flex items-center gap-3 mb-6">
        <div class="h-10 w-10 rounded-lg bg-gradient-to-br from-red-400 to-red-600 flex items-center justify-center">
          <i class="fas fa-lock text-white"></i>
        </div>
        <div>
          <h2 class="text-xl font-bold text-white">Change Password</h2>
          <p class="text-gray-400 text-sm">Update your password to keep your account secure</p>
        </div>
      </div>

      <form class="space-y-5">
        {{-- Current Password --}}
        <div>
          <label class="block text-gray-300 text-sm font-semibold mb-2">
            Current Password
          </label>
          <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
              <i class="fas fa-key text-gray-500"></i>
            </div>
            <input 
              type="password" 
              class="w-full pl-11 pr-12 py-3 bg-white/5 border border-white/10 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:border-red-400/50 focus:ring-2 focus:ring-red-400/20 transition"
              placeholder="Enter current password"
            >
            <button 
              type="button"
              class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-500 hover:text-gray-300 transition">
              <i class="fas fa-eye text-sm"></i>
            </button>
          </div>
        </div>

        {{-- New Password --}}
        <div>
          <label class="block text-gray-300 text-sm font-semibold mb-2">
            New Password
          </label>
          <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
              <i class="fas fa-lock text-gray-500"></i>
            </div>
            <input 
              type="password" 
              class="w-full pl-11 pr-12 py-3 bg-white/5 border border-white/10 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:border-red-400/50 focus:ring-2 focus:ring-red-400/20 transition"
              placeholder="Enter new password"
            >
            <button 
              type="button"
              class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-500 hover:text-gray-300 transition">
              <i class="fas fa-eye text-sm"></i>
            </button>
          </div>
          <div class="mt-2 space-y-1">
            <p class="text-gray-500 text-xs flex items-center gap-2">
              <i class="fas fa-check text-green-400 text-xs"></i>At least 8 characters
            </p>
            <p class="text-gray-500 text-xs flex items-center gap-2">
              <i class="fas fa-times text-red-400 text-xs"></i>Contains uppercase letter
            </p>
            <p class="text-gray-500 text-xs flex items-center gap-2">
              <i class="fas fa-times text-red-400 text-xs"></i>Contains number or special character
            </p>
          </div>
        </div>

        {{-- Confirm New Password --}}
        <div>
          <label class="block text-gray-300 text-sm font-semibold mb-2">
            Confirm New Password
          </label>
          <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
              <i class="fas fa-lock text-gray-500"></i>
            </div>
            <input 
              type="password" 
              class="w-full pl-11 pr-12 py-3 bg-white/5 border border-white/10 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:border-red-400/50 focus:ring-2 focus:ring-red-400/20 transition"
              placeholder="Confirm new password"
            >
            <button 
              type="button"
              class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-500 hover:text-gray-300 transition">
              <i class="fas fa-eye text-sm"></i>
            </button>
          </div>
        </div>

        {{-- Update Password Button --}}
        <div class="flex items-center gap-3 pt-4">
          <button 
            type="submit"
            class="px-6 py-3 bg-gradient-to-r from-red-400 to-red-600 hover:from-red-500 hover:to-red-700 rounded-lg text-white font-semibold transition">
            <i class="fas fa-shield-alt mr-2"></i>Update Password
          </button>
          <button 
            type="button"
            class="px-6 py-3 bg-white/5 hover:bg-white/10 border border-white/10 rounded-lg text-white font-semibold transition">
            Cancel
          </button>
        </div>
      </form>
    </div>

    {{-- Security Settings --}}
    <div class="bg-gradient-to-br from-[#0a0a0a] to-[#151515] border border-white/10 rounded-2xl p-6">
      <div class="flex items-center gap-3 mb-6">
        <div class="h-10 w-10 rounded-lg bg-gradient-to-br from-green-400 to-emerald-500 flex items-center justify-center">
          <i class="fas fa-shield-alt text-white"></i>
        </div>
        <div>
          <h2 class="text-xl font-bold text-white">Security Settings</h2>
          <p class="text-gray-400 text-sm">Manage your account security preferences</p>
        </div>
      </div>

      <div class="space-y-4">
        {{-- Two-Factor Authentication --}}
        <div class="flex items-center justify-between p-4 rounded-lg bg-white/5 border border-white/10">
          <div class="flex items-center gap-4">
            <div class="h-12 w-12 rounded-lg bg-green-500/20 flex items-center justify-center">
              <i class="fas fa-mobile-alt text-green-400"></i>
            </div>
            <div>
              <h3 class="text-white font-semibold">Two-Factor Authentication</h3>
              <p class="text-gray-400 text-sm">Add an extra layer of security</p>
            </div>
          </div>
          <label class="relative inline-flex items-center cursor-pointer">
            <input type="checkbox" class="sr-only peer" checked>
            <div class="w-14 h-7 bg-gray-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-green-500"></div>
          </label>
        </div>

        {{-- Login Alerts --}}
        <div class="flex items-center justify-between p-4 rounded-lg bg-white/5 border border-white/10">
          <div class="flex items-center gap-4">
            <div class="h-12 w-12 rounded-lg bg-blue-500/20 flex items-center justify-center">
              <i class="fas fa-bell text-blue-400"></i>
            </div>
            <div>
              <h3 class="text-white font-semibold">Login Alerts</h3>
              <p class="text-gray-400 text-sm">Get notified of new login attempts</p>
            </div>
          </div>
          <label class="relative inline-flex items-center cursor-pointer">
            <input type="checkbox" class="sr-only peer" checked>
            <div class="w-14 h-7 bg-gray-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-blue-500"></div>
          </label>
        </div>

        {{-- Session Management --}}
        <div class="flex items-center justify-between p-4 rounded-lg bg-white/5 border border-white/10">
          <div class="flex items-center gap-4">
            <div class="h-12 w-12 rounded-lg bg-purple-500/20 flex items-center justify-center">
              <i class="fas fa-clock text-purple-400"></i>
            </div>
            <div>
              <h3 class="text-white font-semibold">Active Sessions</h3>
              <p class="text-gray-400 text-sm">1 active session • Last: 2 hours ago</p>
            </div>
          </div>
          <button class="px-4 py-2 bg-purple-500/20 hover:bg-purple-500/30 border border-purple-500/30 rounded-lg text-purple-300 text-sm font-semibold transition">
            View All
          </button>
        </div>
      </div>
    </div>

    {{-- Danger Zone --}}
    <div class="bg-gradient-to-br from-red-500/5 to-orange-500/5 border border-red-500/20 rounded-2xl p-6">
      <div class="flex items-center gap-3 mb-6">
        <div class="h-10 w-10 rounded-lg bg-red-500/20 flex items-center justify-center">
          <i class="fas fa-exclamation-triangle text-red-400"></i>
        </div>
        <div>
          <h2 class="text-xl font-bold text-red-200">Danger Zone</h2>
          <p class="text-red-300/70 text-sm">Irreversible actions - proceed with caution</p>
        </div>
      </div>

      <div class="space-y-3">
        <div class="flex items-center justify-between p-4 rounded-lg bg-red-500/10 border border-red-500/20">
          <div>
            <h3 class="text-red-200 font-semibold">Log Out All Devices</h3>
            <p class="text-red-300/70 text-sm">Sign out from all active sessions</p>
          </div>
          <button class="px-4 py-2 bg-red-500/20 hover:bg-red-500/30 border border-red-500/30 rounded-lg text-red-300 text-sm font-semibold transition">
            Log Out All
          </button>
        </div>

        <div class="flex items-center justify-between p-4 rounded-lg bg-red-500/10 border border-red-500/20">
          <div>
            <h3 class="text-red-200 font-semibold">Deactivate Account</h3>
            <p class="text-red-300/70 text-sm">Temporarily disable your admin account</p>
          </div>
          <button class="px-4 py-2 bg-red-500/20 hover:bg-red-500/30 border border-red-500/30 rounded-lg text-red-300 text-sm font-semibold transition">
            Deactivate
          </button>
        </div>
      </div>
    </div>

  </div>
</x-business-layout>