<x-business-layout title="Admin Toggles">
  <div class="space-y-6">
    {{-- Header Section --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
      <div>
        <p class="text-gray-400 mt-1">Manage platform-wide emergency controls and restrictions</p>
      </div>
      <div class="flex items-center gap-3">
        <span class="px-4 py-2 bg-green-500/20 border border-green-500/30 rounded-lg text-green-400 text-sm font-semibold flex items-center gap-2">
          <i class="fas fa-check-circle"></i>All Systems Normal
        </span>
      </div>
    </div>

    {{-- Critical Warning Banner --}}
    <div class="bg-gradient-to-r from-red-500/10 to-orange-500/10 border border-red-500/30 rounded-2xl p-6">
      <div class="flex items-start gap-4">
        <div class="h-12 w-12 rounded-xl bg-red-500/20 flex items-center justify-center shrink-0">
          <i class="fas fa-shield-alt text-red-400 text-xl"></i>
        </div>
        <div class="flex-1">
          <h3 class="text-red-200 font-bold text-lg mb-2">⚠️ Critical System Controls</h3>
          <p class="text-red-300/80 text-sm">
            These toggles control critical platform functionality. Activating them will immediately affect all users. 
            Use with extreme caution and ensure proper communication with stakeholders before making changes.
          </p>
          <div class="mt-4 flex flex-wrap gap-3">
            <span class="text-xs px-3 py-1 bg-red-500/20 text-red-300 rounded-full">Requires Confirmation</span>
            <span class="text-xs px-3 py-1 bg-red-500/20 text-red-300 rounded-full">Logged & Audited</span>
            <span class="text-xs px-3 py-1 bg-red-500/20 text-red-300 rounded-full">Immediate Effect</span>
          </div>
        </div>
      </div>
    </div>

    {{-- Toggle Status Overview --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-5 gap-5">
      @php
        $toggleStats = [
          ['name' => 'Coin Creation', 'status' => 'active', 'icon' => 'fa-coins', 'color' => 'green'],
          ['name' => 'Trading', 'status' => 'active', 'icon' => 'fa-exchange-alt', 'color' => 'green'],
          ['name' => 'PancakeSwap', 'status' => 'active', 'icon' => 'fa-sync', 'color' => 'green'],
          ['name' => 'Rewards', 'status' => 'active', 'icon' => 'fa-gift', 'color' => 'green'],
          ['name' => 'Blacklist', 'status' => '0 wallets', 'icon' => 'fa-ban', 'color' => 'gray'],
        ];
      @endphp

      @foreach($toggleStats as $toggle)
        <div class="group relative overflow-hidden bg-gradient-to-br from-[#0a0a0a] to-[#151515] border border-white/10 rounded-2xl p-6 hover:border-{{ $toggle['color'] }}-400/50 transition-all duration-300">
          <div class="absolute top-0 right-0 w-24 h-24 bg-{{ $toggle['color'] }}-400/5 rounded-full blur-3xl group-hover:bg-{{ $toggle['color'] }}-400/10 transition-all"></div>
          <div class="relative">
            <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-{{ $toggle['color'] }}-400 to-{{ $toggle['color'] }}-600 flex items-center justify-center mb-4">
              <i class="fas {{ $toggle['icon'] }} text-white"></i>
            </div>
            <h3 class="text-white font-semibold mb-1">{{ $toggle['name'] }}</h3>
            <p class="text-{{ $toggle['color'] }}-400 text-sm font-medium">{{ ucfirst($toggle['status']) }}</p>
          </div>
        </div>
      @endforeach
    </div>

    {{-- Main Toggle Controls --}}
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-5">
      {{-- Platform Controls --}}
      <div class="space-y-5">
        {{-- Disable Coin Creation --}}
        <div class="bg-gradient-to-br from-[#0a0a0a] to-[#151515] border border-white/10 rounded-2xl p-6 hover:border-red-400/30 transition-all group">
          <div class="flex items-start justify-between mb-4">
            <div class="flex items-start gap-4">
              <div class="h-12 w-12 rounded-xl bg-red-500/20 flex items-center justify-center shrink-0">
                <i class="fas fa-coins text-red-400 text-xl"></i>
              </div>
              <div>
                <h3 class="text-white font-bold text-lg mb-1">Disable New Coin Creation</h3>
                <p class="text-gray-400 text-sm">Prevent users from creating new tokens</p>
              </div>
            </div>
            <div class="flex flex-col items-end gap-2">
              <label class="relative inline-flex items-center cursor-pointer">
                <input type="checkbox" class="sr-only peer" id="coinCreationToggle">
                <div class="w-14 h-7 bg-gray-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-red-500"></div>
              </label>
              <span class="text-xs text-green-400 font-semibold">Active</span>
            </div>
          </div>
          
          <div class="grid grid-cols-2 gap-3 mb-4">
            <div class="p-3 rounded-lg bg-white/5">
              <p class="text-gray-400 text-xs mb-1">Coins Created Today</p>
              <p class="text-white font-bold text-xl">47</p>
            </div>
            <div class="p-3 rounded-lg bg-white/5">
              <p class="text-gray-400 text-xs mb-1">Last 7 Days</p>
              <p class="text-white font-bold text-xl">342</p>
            </div>
          </div>

          <a href="/admin/disable-coin-creation" class="block w-full px-4 py-2 text-center bg-white/5 hover:bg-white/10 border border-white/10 rounded-lg text-white text-sm font-semibold transition">
            <i class="fas fa-cog mr-2"></i>Configure Settings
          </a>
        </div>

        {{-- Pause Trading --}}
        <div class="bg-gradient-to-br from-[#0a0a0a] to-[#151515] border border-white/10 rounded-2xl p-6 hover:border-orange-400/30 transition-all group">
          <div class="flex items-start justify-between mb-4">
            <div class="flex items-start gap-4">
              <div class="h-12 w-12 rounded-xl bg-orange-500/20 flex items-center justify-center shrink-0">
                <i class="fas fa-exchange-alt text-orange-400 text-xl"></i>
              </div>
              <div>
                <h3 class="text-white font-bold text-lg mb-1">Pause Trading</h3>
                <p class="text-gray-400 text-sm">Halt all buy/sell transactions</p>
              </div>
            </div>
            <div class="flex flex-col items-end gap-2">
              <label class="relative inline-flex items-center cursor-pointer">
                <input type="checkbox" class="sr-only peer" id="tradingToggle">
                <div class="w-14 h-7 bg-gray-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-orange-500"></div>
              </label>
              <span class="text-xs text-green-400 font-semibold">Active</span>
            </div>
          </div>
          
          <div class="grid grid-cols-2 gap-3 mb-4">
            <div class="p-3 rounded-lg bg-white/5">
              <p class="text-gray-400 text-xs mb-1">Trades Today</p>
              <p class="text-white font-bold text-xl">1,847</p>
            </div>
            <div class="p-3 rounded-lg bg-white/5">
              <p class="text-gray-400 text-xs mb-1">Volume (24h)</p>
              <p class="text-white font-bold text-xl">$2.4M</p>
            </div>
          </div>

          <a href="/admin/pause-trading" class="block w-full px-4 py-2 text-center bg-white/5 hover:bg-white/10 border border-white/10 rounded-lg text-white text-sm font-semibold transition">
            <i class="fas fa-cog mr-2"></i>Configure Settings
          </a>
        </div>

        {{-- Freeze Rewards --}}
        <div class="bg-gradient-to-br from-[#0a0a0a] to-[#151515] border border-white/10 rounded-2xl p-6 hover:border-purple-400/30 transition-all group">
          <div class="flex items-start justify-between mb-4">
            <div class="flex items-start gap-4">
              <div class="h-12 w-12 rounded-xl bg-purple-500/20 flex items-center justify-center shrink-0">
                <i class="fas fa-gift text-purple-400 text-xl"></i>
              </div>
              <div>
                <h3 class="text-white font-bold text-lg mb-1">Freeze Rewards Payouts</h3>
                <p class="text-gray-400 text-sm">Temporarily halt reward distributions</p>
              </div>
            </div>
            <div class="flex flex-col items-end gap-2">
              <label class="relative inline-flex items-center cursor-pointer">
                <input type="checkbox" class="sr-only peer" id="rewardsToggle">
                <div class="w-14 h-7 bg-gray-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-purple-500"></div>
              </label>
              <span class="text-xs text-green-400 font-semibold">Active</span>
            </div>
          </div>
          
          <div class="grid grid-cols-2 gap-3 mb-4">
            <div class="p-3 rounded-lg bg-white/5">
              <p class="text-gray-400 text-xs mb-1">Pending Rewards</p>
              <p class="text-white font-bold text-xl">$18.4K</p>
            </div>
            <div class="p-3 rounded-lg bg-white/5">
              <p class="text-gray-400 text-xs mb-1">Recipients</p>
              <p class="text-white font-bold text-xl">247</p>
            </div>
          </div>

          <a href="/admin/freeze-rewards" class="block w-full px-4 py-2 text-center bg-white/5 hover:bg-white/10 border border-white/10 rounded-lg text-white text-sm font-semibold transition">
            <i class="fas fa-cog mr-2"></i>Configure Settings
          </a>
        </div>
      </div>

      {{-- Migration & Security Controls --}}
      <div class="space-y-5">
        {{-- Pause PancakeSwap Migration --}}
        <div class="bg-gradient-to-br from-[#0a0a0a] to-[#151515] border border-white/10 rounded-2xl p-6 hover:border-blue-400/30 transition-all group">
          <div class="flex items-start justify-between mb-4">
            <div class="flex items-start gap-4">
              <div class="h-12 w-12 rounded-xl bg-blue-500/20 flex items-center justify-center shrink-0">
                <i class="fas fa-sync text-blue-400 text-xl"></i>
              </div>
              <div>
                <h3 class="text-white font-bold text-lg mb-1">Pause PancakeSwap Migration</h3>
                <p class="text-gray-400 text-sm">Disable token migrations to DEX</p>
              </div>
            </div>
            <div class="flex flex-col items-end gap-2">
              <label class="relative inline-flex items-center cursor-pointer">
                <input type="checkbox" class="sr-only peer" id="migrationToggle">
                <div class="w-14 h-7 bg-gray-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-blue-500"></div>
              </label>
              <span class="text-xs text-green-400 font-semibold">Active</span>
            </div>
          </div>
          
          <div class="grid grid-cols-2 gap-3 mb-4">
            <div class="p-3 rounded-lg bg-white/5">
              <p class="text-gray-400 text-xs mb-1">Migrations Today</p>
              <p class="text-white font-bold text-xl">12</p>
            </div>
            <div class="p-3 rounded-lg bg-white/5">
              <p class="text-gray-400 text-xs mb-1">Pending Queue</p>
              <p class="text-white font-bold text-xl">3</p>
            </div>
          </div>

          <a href="/admin/pause-pancakeswap" class="block w-full px-4 py-2 text-center bg-white/5 hover:bg-white/10 border border-white/10 rounded-lg text-white text-sm font-semibold transition">
            <i class="fas fa-cog mr-2"></i>Configure Settings
          </a>
        </div>

        {{-- Blacklist Wallet --}}
        <div class="bg-gradient-to-br from-[#0a0a0a] to-[#151515] border border-white/10 rounded-2xl p-6 hover:border-red-400/30 transition-all group">
          <div class="flex items-start justify-between mb-4">
            <div class="flex items-start gap-4">
              <div class="h-12 w-12 rounded-xl bg-red-500/20 flex items-center justify-center shrink-0">
                <i class="fas fa-ban text-red-400 text-xl"></i>
              </div>
              <div>
                <h3 class="text-white font-bold text-lg mb-1">Blacklist Wallet</h3>
                <p class="text-gray-400 text-sm">Restrict malicious wallet addresses</p>
              </div>
            </div>
          </div>
          
          <div class="mb-4">
            <div class="p-4 rounded-lg bg-white/5 border border-white/10">
              <div class="flex items-center justify-between mb-2">
                <span class="text-gray-400 text-sm">Blacklisted Wallets</span>
                <span class="text-white font-bold text-2xl">0</span>
              </div>
              <p class="text-gray-500 text-xs">No wallets currently restricted</p>
            </div>
          </div>

          <a href="/admin/blacklist-wallet" class="block w-full px-4 py-2 text-center bg-gradient-to-r from-red-400 to-red-600 hover:from-red-500 hover:to-red-700 rounded-lg text-white text-sm font-semibold transition">
            <i class="fas fa-plus mr-2"></i>Add to Blacklist
          </a>
        </div>

        {{-- Recent Activity Log --}}
        <div class="bg-gradient-to-br from-[#0a0a0a] to-[#151515] border border-white/10 rounded-2xl p-6">
          <h3 class="text-white font-bold text-lg mb-4">Recent Toggle Activity</h3>
          
          <div class="space-y-3">
            @php
              $activities = [
                ['action' => 'Trading enabled', 'admin' => 'Admin User', 'time' => '2 hours ago', 'status' => 'success'],
                ['action' => 'Rewards payout processed', 'admin' => 'System', 'time' => '5 hours ago', 'status' => 'info'],
                ['action' => 'Migration approved', 'admin' => 'Admin User', 'time' => '1 day ago', 'status' => 'success'],
              ];
            @endphp

            @foreach($activities as $activity)
              <div class="flex items-center gap-3 p-3 rounded-lg bg-white/5">
                <div class="h-8 w-8 rounded-full bg-{{ $activity['status'] === 'success' ? 'green' : 'blue' }}-500/20 flex items-center justify-center shrink-0">
                  <i class="fas fa-{{ $activity['status'] === 'success' ? 'check' : 'info' }} text-{{ $activity['status'] === 'success' ? 'green' : 'blue' }}-400 text-xs"></i>
                </div>
                <div class="flex-1 min-w-0">
                  <p class="text-white text-sm font-medium">{{ $activity['action'] }}</p>
                  <p class="text-gray-500 text-xs">by {{ $activity['admin'] }} • {{ $activity['time'] }}</p>
                </div>
              </div>
            @endforeach
          </div>
        </div>
      </div>
    </div>

  </div>
</x-business-layout>