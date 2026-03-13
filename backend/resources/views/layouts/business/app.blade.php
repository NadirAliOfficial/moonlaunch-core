<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>{{ $title ?? 'MoonLaunch Dashboard' }}</title>
 <!-- Font Awesome CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet"> <!-- Stylish Poppins font -->
<!-- Or use Open Sans instead -->
<!-- <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&display=swap" rel="stylesheet"> -->

<style>
  body, button, input, select, textarea {
    font-family: 'Poppins', sans-serif;  /* Poppins Font */
    font-weight: 600;
  }
</style>
</head>
<body class="text-white font-['Lilita_One'] bg-[#050505]">
  <div class="min-h-screen flex
              bg-[radial-gradient(circle_at_20%_15%,rgba(255,200,0,0.18),transparent_35%),radial-gradient(circle_at_60%_55%,rgba(255,140,0,0.10),transparent_40%),radial-gradient(circle_at_90%_30%,rgba(255,255,255,0.06),transparent_35%),linear-gradient(90deg,#050505_0%,#0b0b0b_40%,#050505_100%)]">
    
    {{-- Sidebar --}}
    @include('layouts.business.sidebar')

    {{-- Main Content --}}
    <div class="flex-1 flex flex-col min-w-0">
      {{-- Topbar --}}
      <header class="h-14 bg-black/30 backdrop-blur border-b border-white/10 flex items-center justify-between px-4 md:px-6">
        <div class="flex items-center gap-3">
          {{-- Mobile Sidebar Button --}}
          <button id="openSidebarBtn" class="md:hidden inline-flex items-center justify-center h-9 w-9 rounded-lg border border-gray-200 bg-gray-800 hover:bg-gray-700">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
          </button>
          <div class="text-2xl md:text-3xl font-['Lilita_One'] text-white leading-none">
            {{ $title ?? 'MoonLaunch Dashboard' }}
          </div>
        </div>

        <div class="flex items-center gap-3">
          {{-- Bell Icon for Notifications --}}
          <button class="h-9 w-9 inline-flex items-center justify-center rounded-lg border border-gray-200 bg-gray-800 hover:bg-gray-700">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0a3 3 0 11-6 0m6 0H9" />
            </svg>
          </button>

          {{-- User Avatar --}}
          <div class="flex items-center gap-2">
            <div class="h-9 w-9 rounded-full bg-gray-700 overflow-hidden flex items-center justify-center">
              <span class="text-sm font-semibold text-white">A</span>
            </div>
          </div>
        </div>
      </header>

      <main class="p-4 md:p-6">
        {{ $slot }}
      </main>
    </div>
  </div>

  {{-- Mobile Sidebar Overlay --}}
  <div id="sidebarOverlay" class="fixed inset-0 bg-black/40 hidden md:hidden"></div>

  <script>
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    const openBtn = document.getElementById('openSidebarBtn');
    const closeBtn = document.getElementById('closeSidebarBtn');

    function openSidebar() {
      sidebar.classList.remove('-translate-x-full');
      overlay.classList.remove('hidden');
    }

    function closeSidebar() {
      sidebar.classList.add('-translate-x-full');
      overlay.classList.add('hidden');
    }

    if (openBtn) openBtn.addEventListener('click', openSidebar);
    if (closeBtn) closeBtn.addEventListener('click', closeSidebar);
    if (overlay) overlay.addEventListener('click', closeSidebar);
  </script>
</body>
</html>
