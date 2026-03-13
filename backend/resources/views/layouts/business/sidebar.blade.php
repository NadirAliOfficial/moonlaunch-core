@php
  $path = request()->path();
  $itemBase = "group flex items-center gap-3 px-4 py-3 rounded-xl transition";
  $inactive = "bg-transparent hover:bg-gradient-to-r hover:from-yellow-400 hover:to-orange-500";
  $active = "bg-gradient-to-r from-yellow-400 to-orange-500";
  $disabled = "bg-transparent opacity-50 cursor-not-allowed";
  $iconWrap = "h-10 w-10 rounded-full bg-white/10 ring-1 ring-white/10 grid place-items-center shrink-0";
@endphp

<aside id="sidebar"
  class="w-64 text-white shrink-0
         fixed md:static inset-y-0 left-0 z-50
         transform -translate-x-full md:translate-x-0 transition-transform duration-200
         bg-[radial-gradient(circle_at_20%_15%,rgba(255,200,0,0.18),transparent_35%),radial-gradient(circle_at_60%_55%,rgba(255,140,0,0.10),transparent_40%),radial-gradient(circle_at_90%_30%,rgba(255,255,255,0.06),transparent_35%),linear-gradient(90deg,#050505_0%,#0b0b0b_40%,#050505_100%)]">
  {{-- Top Logo --}}
  <div class="h-20 flex items-center justify-center px-4 border-b border-white/10">
    <img src="{{ asset('assets/logo.png') }}" 
         alt="MoonLaunch Logo" 
         class="h-12 w-auto object-contain brightness-110">
  </div>

  <nav class="p-4 space-y-3 overflow-y-auto" style="max-height: calc(100vh - 5rem);">
    {{-- Dashboard - BUILT (No Subheadings) --}}
    <a href="/admin/dashboard" class="{{ $itemBase }} {{ $path === 'admin/dashboard' ? $active : $inactive }}">
      <span class="h-9 w-9 rounded-full bg-gradient-to-r from-yellow-400 to-orange-500 p-[2px] flex items-center justify-center shrink-0">
        <span class="h-full w-full rounded-full bg-[#0b0b0b] flex items-center justify-center">
          <i class="fas fa-tachometer-alt bg-gradient-to-r from-yellow-400 to-orange-500 bg-clip-text text-transparent"></i>
        </span>
      </span>
      <span class="text-[17px] leading-none text-white/90 tracking-normal">Dashboard</span>
    </a>

    {{-- Token Management - NOT BUILT (Disabled) --}}
    <div class="{{ $itemBase }} {{ $disabled }}">
      <div class="flex items-center gap-3 flex-1">
        <span class="h-9 w-9 rounded-full bg-gradient-to-r from-yellow-400 to-orange-500 p-[2px] flex items-center justify-center shrink-0">
          <span class="h-full w-full rounded-full bg-[#0b0b0b] flex items-center justify-center">
            <i class="fas fa-coins bg-gradient-to-r from-yellow-400 to-orange-500 bg-clip-text text-transparent"></i>
          </span>
        </span>
        <span class="text-[17px] leading-none text-white/90 tracking-normal">Token Management</span>
      </div>
      <button class="ml-auto text-yellow-400" onclick="toggleSubmenu('tokensSubmenu', 'tokensChevron')">
        <i class="fas fa-chevron-down transition-transform duration-200" id="tokensChevron"></i>
      </button>
    </div>

    {{-- Token Management Submenus - NOT BUILT (Disabled) --}}
    <div id="tokensSubmenu" class="pl-6 space-y-2 hidden">
      <div class="{{ $itemBase }} {{ $disabled }}">
        <span class="text-white/90 text-sm">Token List</span>
      </div>
      <div class="{{ $itemBase }} {{ $disabled }}">
        <span class="text-white/90 text-sm">Token Command Center</span>
      </div>
      <div class="{{ $itemBase }} {{ $disabled }}">
        <span class="text-white/90 text-sm">Flagging & Moderation</span>
      </div>
    </div>

    {{-- Liquidity & Migration - NOT BUILT (Disabled) --}}
    <div class="{{ $itemBase }} {{ $disabled }}">
      <div class="flex items-center gap-3 flex-1">
        <span class="h-9 w-9 rounded-full bg-gradient-to-r from-yellow-400 to-orange-500 p-[2px] flex items-center justify-center shrink-0">
          <span class="h-full w-full rounded-full bg-[#0b0b0b] flex items-center justify-center">
            <i class="fas fa-exchange-alt bg-gradient-to-r from-yellow-400 to-orange-500 bg-clip-text text-transparent"></i>
          </span>
        </span>
        <span class="text-[17px] leading-none text-white/90 tracking-normal">Liquidity & Migration</span>
      </div>
      <button class="ml-auto text-yellow-400" onclick="toggleSubmenu('liquiditySubmenu', 'liquidityChevron')">
        <i class="fas fa-chevron-down transition-transform duration-200" id="liquidityChevron"></i>
      </button>
    </div>

    {{-- Liquidity & Migration Submenus - NOT BUILT (Disabled) --}}
    <div id="liquiditySubmenu" class="pl-6 space-y-2 hidden">
      <div class="{{ $itemBase }} {{ $disabled }}">
        <span class="text-white/90 text-sm">Threshold Monitor (25.1 BNB)</span>
      </div>
      <div class="{{ $itemBase }} {{ $disabled }}">
        <span class="text-white/90 text-sm">PancakeSwap Launch</span>
      </div>
      <div class="{{ $itemBase }} {{ $disabled }}">
        <span class="text-white/90 text-sm">Migration Queue</span>
      </div>
    </div>

    {{-- User Management - BUILT --}}
    <div class="{{ $itemBase }} {{ str_starts_with($path, 'admin/users') ? $active : $inactive }}">
      <a href="/admin/users" class="flex items-center gap-3 flex-1">
        <span class="h-9 w-9 rounded-full bg-gradient-to-r from-yellow-400 to-orange-500 p-[2px] flex items-center justify-center shrink-0">
          <span class="h-full w-full rounded-full bg-[#0b0b0b] flex items-center justify-center">
            <i class="fas fa-users bg-gradient-to-r from-yellow-400 to-orange-500 bg-clip-text text-transparent"></i>
          </span>
        </span>
        <span class="text-[17px] leading-none text-white/90 tracking-normal">User Management</span>
      </a>
      <button class="ml-auto text-yellow-400" onclick="toggleSubmenu('usersSubmenu', 'usersChevron')">
        <i class="fas fa-chevron-down transition-transform duration-200" id="usersChevron"></i>
      </button>
    </div>

    {{-- User Management Submenus - BUILT --}}
    <div id="usersSubmenu" class="pl-6 space-y-2 hidden">
      <a href="/admin/users" class="{{ $itemBase }} {{ $path === 'admin/users/list' ? $active : $inactive }}">
        <span class="text-white/90 text-sm">User List</span>
      </a>
      <a href="/admin/users/abuse-reports" class="{{ $itemBase }} {{ $path === 'admin/users/abuse-reports' ? $active : $inactive }}">
        <span class="text-white/90 text-sm">Abuse Reports</span>
      </a>
    </div>

    {{-- Fees & Revenue - NOT BUILT (Disabled) --}}
    <div class="{{ $itemBase }} {{ $disabled }}">
      <div class="flex items-center gap-3 flex-1">
        <span class="h-9 w-9 rounded-full bg-gradient-to-r from-yellow-400 to-orange-500 p-[2px] flex items-center justify-center shrink-0">
          <span class="h-full w-full rounded-full bg-[#0b0b0b] flex items-center justify-center">
            <i class="fas fa-dollar-sign bg-gradient-to-r from-yellow-400 to-orange-500 bg-clip-text text-transparent"></i>
          </span>
        </span>
        <span class="text-[17px] leading-none text-white/90 tracking-normal">Fees & Revenue</span>
      </div>
      <button class="ml-auto text-yellow-400" onclick="toggleSubmenu('feesSubmenu', 'feesChevron')">
        <i class="fas fa-chevron-down transition-transform duration-200" id="feesChevron"></i>
      </button>
    </div>

    {{-- Fees & Revenue Submenus - NOT BUILT (Disabled) --}}
    <div id="feesSubmenu" class="pl-6 space-y-2 hidden">
      <div class="{{ $itemBase }} {{ $disabled }}">
        <span class="text-white/90 text-sm">Fee Configuration</span>
      </div>
      <div class="{{ $itemBase }} {{ $disabled }}">
        <span class="text-white/90 text-sm">Fee History & Versions</span>
      </div>
    </div>

    {{-- Rewards & Payouts - NOT BUILT (Disabled) --}}
    <div class="{{ $itemBase }} {{ $disabled }}">
      <div class="flex items-center gap-3 flex-1">
        <span class="h-9 w-9 rounded-full bg-gradient-to-r from-yellow-400 to-orange-500 p-[2px] flex items-center justify-center shrink-0">
          <span class="h-full w-full rounded-full bg-[#0b0b0b] flex items-center justify-center">
            <i class="fas fa-gift bg-gradient-to-r from-yellow-400 to-orange-500 bg-clip-text text-transparent"></i>
          </span>
        </span>
        <span class="text-[17px] leading-none text-white/90 tracking-normal">Rewards & Payouts</span>
      </div>
      <button class="ml-auto text-yellow-400" onclick="toggleSubmenu('rewardsSubmenu', 'rewardsChevron')">
        <i class="fas fa-chevron-down transition-transform duration-200" id="rewardsChevron"></i>
      </button>
    </div>

    {{-- Rewards & Payouts Submenus - NOT BUILT (Disabled) --}}
    <div id="rewardsSubmenu" class="pl-6 space-y-2 hidden">
      <div class="{{ $itemBase }} {{ $disabled }}">
        <span class="text-white/90 text-sm">Manual Payouts</span>
      </div>
      <div class="{{ $itemBase }} {{ $disabled }}">
        <span class="text-white/90 text-sm">Scheduled Payouts</span>
      </div>
    </div>

    {{-- Admin Controls (Kill Switches) - NOT BUILT (Disabled) --}}
    <div class="{{ $itemBase }} {{ $disabled }}">
      <div class="flex items-center gap-3 flex-1">
        <span class="h-9 w-9 rounded-full bg-gradient-to-r from-yellow-400 to-orange-500 p-[2px] flex items-center justify-center shrink-0">
          <span class="h-full w-full rounded-full bg-[#0b0b0b] flex items-center justify-center">
            <i class="fas fa-toggle-on bg-gradient-to-r from-yellow-400 to-orange-500 bg-clip-text text-transparent"></i>
          </span>
        </span>
        <span class="text-[17px] leading-none text-white/90 tracking-normal">Admin Controls</span>
      </div>
      <button class="ml-auto text-yellow-400" onclick="toggleSubmenu('controlsSubmenu', 'controlsChevron')">
        <i class="fas fa-chevron-down transition-transform duration-200" id="controlsChevron"></i>
      </button>
    </div>

    {{-- Admin Controls Submenus - NOT BUILT (Disabled) --}}
    <div id="controlsSubmenu" class="pl-6 space-y-2 hidden">
      <div class="{{ $itemBase }} {{ $disabled }}">
        <span class="text-white/90 text-sm">Pause/Unpause Trading</span>
      </div>
      <div class="{{ $itemBase }} {{ $disabled }}">
        <span class="text-white/90 text-sm">Disable Coin Creation</span>
      </div>
      <div class="{{ $itemBase }} {{ $disabled }}">
        <span class="text-white/90 text-sm">Pause PancakeSwap Migration</span>
      </div>
      <div class="{{ $itemBase }} {{ $disabled }}">
        <span class="text-white/90 text-sm">Freeze Rewards Payouts</span>
      </div>
      <div class="{{ $itemBase }} {{ $disabled }}">
        <span class="text-white/90 text-sm">Blacklist Wallet</span>
      </div>
    </div>

    {{-- Audit Log - BUILT --}}
    <a href="/admin/audit-log" class="{{ $itemBase }} {{ $path === 'admin/audit-log' ? $active : $inactive }}">
      <span class="h-9 w-9 rounded-full bg-gradient-to-r from-yellow-400 to-orange-500 p-[2px] flex items-center justify-center shrink-0">
        <span class="h-full w-full rounded-full bg-[#0b0b0b] flex items-center justify-center">
          <i class="fas fa-clipboard-list bg-gradient-to-r from-yellow-400 to-orange-500 bg-clip-text text-transparent"></i>
        </span>
      </span>
      <span class="text-[17px] leading-none text-white/90 tracking-normal">Audit Log</span>
    </a>

    {{-- Role Management - BUILT --}}
    <a href="{{ route('admin.roles') }}" class="{{ $itemBase }} {{ $path === 'admin/roles' ? $active : $inactive }}">
      <span class="h-9 w-9 rounded-full bg-gradient-to-r from-yellow-400 to-orange-500 p-[2px] flex items-center justify-center shrink-0">
        <span class="h-full w-full rounded-full bg-[#0b0b0b] flex items-center justify-center">
          <i class="fas fa-user-shield bg-gradient-to-r from-yellow-400 to-orange-500 bg-clip-text text-transparent"></i>
        </span>
      </span>
      <span class="text-[17px] leading-none text-white/90 tracking-normal">Role Management</span>
    </a>

    {{-- Account Settings - NOT BUILT (Disabled) --}}
    <div class="{{ $itemBase }} {{ $disabled }}">
      <span class="h-9 w-9 rounded-full bg-gradient-to-r from-yellow-400 to-orange-500 p-[2px] flex items-center justify-center shrink-0">
        <span class="h-full w-full rounded-full bg-[#0b0b0b] flex items-center justify-center">
          <i class="fas fa-cogs bg-gradient-to-r from-yellow-400 to-orange-500 bg-clip-text text-transparent"></i>
        </span>
      </span>
      <span class="text-[17px] leading-none text-white/90 tracking-normal">Account Settings</span>
    </div>

    {{-- Logout - BUILT --}}
    <div class="{{ $itemBase }}">
      <form method="POST" action="{{ route('admin.logout') }}" onsubmit="return confirm('Are you sure you want to logout?');" class="w-full">
        @csrf
        <button type="submit" class="flex items-center gap-3 flex-1 w-full text-left bg-transparent border-0 cursor-pointer">
          <span class="h-9 w-9 rounded-full bg-gradient-to-r from-yellow-400 to-orange-500 p-[2px] flex items-center justify-center shrink-0">
            <span class="h-full w-full rounded-full bg-[#0b0b0b] flex items-center justify-center">
              <i class="fas fa-sign-out-alt bg-gradient-to-r from-yellow-400 to-orange-500 bg-clip-text text-transparent"></i>
            </span>
          </span>
          <span class="text-[17px] leading-none text-white/90 tracking-normal">Logout</span>
        </button>
      </form>
    </div>
  </nav>
</aside>

<script>
  function toggleSubmenu(submenuId, chevronId) {
    const submenu = document.getElementById(submenuId);
    const chevron = document.getElementById(chevronId);
    
    if (submenu) {
      submenu.classList.toggle('hidden');
    }
    
    if (chevron) {
      chevron.classList.toggle('rotate-180');
    }
  }
</script>