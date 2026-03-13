<x-business-layout title="Dashboard">
  <div class="space-y-6">
    {{-- Header Section --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
      <div>
        <h1 class="text-3xl font-bold bg-gradient-to-r from-yellow-400 to-orange-500 bg-clip-text text-transparent">
          Dashboard Overview
        </h1>
        <p class="text-gray-400 mt-1">Monitor your platform's performance in real-time</p>
      </div>
      <div class="flex items-center gap-3">
        <button class="px-4 py-2 bg-white/5 hover:bg-white/10 border border-white/10 rounded-lg text-white text-sm transition">
          <i class="fas fa-download mr-2"></i>Export Report
        </button>
        <button class="px-4 py-2 bg-gradient-to-r from-yellow-400 to-orange-500 hover:from-yellow-500 hover:to-orange-600 rounded-lg text-black font-semibold text-sm transition">
          <i class="fas fa-plus mr-2"></i>New Coin
        </button>
      </div>
    </div>

    {{-- Key Metrics Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-5">
      {{-- Total Volume --}}
      <div class="group relative overflow-hidden bg-gradient-to-br from-[#0a0a0a] to-[#151515] border border-white/10 rounded-2xl p-6 hover:border-yellow-400/50 transition-all duration-300">
        <div class="absolute top-0 right-0 w-32 h-32 bg-yellow-400/5 rounded-full blur-3xl group-hover:bg-yellow-400/10 transition-all"></div>
        <div class="relative">
          <div class="flex items-center justify-between mb-4">
            <div class="h-12 w-12 rounded-xl bg-gradient-to-br from-yellow-400 to-orange-500 flex items-center justify-center">
              <i class="fas fa-chart-line text-black text-xl"></i>
            </div>
            <span class="text-green-400 text-sm font-semibold flex items-center gap-1">
              <i class="fas fa-arrow-up text-xs"></i>12.5%
            </span>
          </div>
          <h3 class="text-gray-400 text-sm font-medium mb-1">Total Volume (24h)</h3>
          <p class="text-3xl font-bold text-white">$2.4M</p>
          <p class="text-gray-500 text-xs mt-2">+$264K from yesterday</p>
        </div>
      </div>

      {{-- Active Coins --}}
      <div class="group relative overflow-hidden bg-gradient-to-br from-[#0a0a0a] to-[#151515] border border-white/10 rounded-2xl p-6 hover:border-yellow-400/50 transition-all duration-300">
        <div class="absolute top-0 right-0 w-32 h-32 bg-orange-400/5 rounded-full blur-3xl group-hover:bg-orange-400/10 transition-all"></div>
        <div class="relative">
          <div class="flex items-center justify-between mb-4">
            <div class="h-12 w-12 rounded-xl bg-gradient-to-br from-orange-400 to-red-500 flex items-center justify-center">
              <i class="fas fa-coins text-white text-xl"></i>
            </div>
            <span class="text-green-400 text-sm font-semibold flex items-center gap-1">
              <i class="fas fa-arrow-up text-xs"></i>8.2%
            </span>
          </div>
          <h3 class="text-gray-400 text-sm font-medium mb-1">Active Coins</h3>
          <p class="text-3xl font-bold text-white">1,247</p>
          <p class="text-gray-500 text-xs mt-2">+94 this week</p>
        </div>
      </div>

      {{-- Total Users --}}
      <div class="group relative overflow-hidden bg-gradient-to-br from-[#0a0a0a] to-[#151515] border border-white/10 rounded-2xl p-6 hover:border-yellow-400/50 transition-all duration-300">
        <div class="absolute top-0 right-0 w-32 h-32 bg-blue-400/5 rounded-full blur-3xl group-hover:bg-blue-400/10 transition-all"></div>
        <div class="relative">
          <div class="flex items-center justify-between mb-4">
            <div class="h-12 w-12 rounded-xl bg-gradient-to-br from-blue-400 to-purple-500 flex items-center justify-center">
              <i class="fas fa-users text-white text-xl"></i>
            </div>
            <span class="text-green-400 text-sm font-semibold flex items-center gap-1">
              <i class="fas fa-arrow-up text-xs"></i>15.3%
            </span>
          </div>
          <h3 class="text-gray-400 text-sm font-medium mb-1">Total Users</h3>
          <p class="text-3xl font-bold text-white">48,392</p>
          <p class="text-gray-500 text-xs mt-2">+6,429 this month</p>
        </div>
      </div>

      {{-- Platform Fees --}}
      <div class="group relative overflow-hidden bg-gradient-to-br from-[#0a0a0a] to-[#151515] border border-white/10 rounded-2xl p-6 hover:border-yellow-400/50 transition-all duration-300">
        <div class="absolute top-0 right-0 w-32 h-32 bg-green-400/5 rounded-full blur-3xl group-hover:bg-green-400/10 transition-all"></div>
        <div class="relative">
          <div class="flex items-center justify-between mb-4">
            <div class="h-12 w-12 rounded-xl bg-gradient-to-br from-green-400 to-emerald-500 flex items-center justify-center">
              <i class="fas fa-wallet text-white text-xl"></i>
            </div>
            <span class="text-green-400 text-sm font-semibold flex items-center gap-1">
              <i class="fas fa-arrow-up text-xs"></i>9.7%
            </span>
          </div>
          <h3 class="text-gray-400 text-sm font-medium mb-1">Platform Fees (24h)</h3>
          <p class="text-3xl font-bold text-white">$18.4K</p>
          <p class="text-gray-500 text-xs mt-2">+$1.6K from yesterday</p>
        </div>
      </div>
    </div>

    {{-- Charts Section --}}
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-5">
      {{-- Trading Volume Chart --}}
      <div class="xl:col-span-2 bg-gradient-to-br from-[#0a0a0a] to-[#151515] border border-white/10 rounded-2xl p-6">
        <div class="flex items-center justify-between mb-6">
          <div>
            <h2 class="text-xl font-bold text-white">Trading Volume</h2>
            <p class="text-gray-400 text-sm mt-1">Last 7 days performance</p>
          </div>
          <div class="flex gap-2">
            <button class="px-3 py-1.5 bg-gradient-to-r from-yellow-400 to-orange-500 rounded-lg text-black text-xs font-semibold">7D</button>
            <button class="px-3 py-1.5 bg-white/5 hover:bg-white/10 rounded-lg text-gray-400 text-xs font-semibold transition">30D</button>
            <button class="px-3 py-1.5 bg-white/5 hover:bg-white/10 rounded-lg text-gray-400 text-xs font-semibold transition">90D</button>
          </div>
        </div>
        
        {{-- Chart Canvas --}}
        <div class="h-72 relative">
          <canvas id="volumeChart"></canvas>
        </div>
      </div>

      {{-- Top Performing Coins --}}
      <div class="bg-gradient-to-br from-[#0a0a0a] to-[#151515] border border-white/10 rounded-2xl p-6">
        <h2 class="text-xl font-bold text-white mb-6">Top Performers</h2>
        <div class="space-y-4">
          @php
            $topCoins = [
              ['name' => 'MoonDoge', 'symbol' => 'MDOGE', 'change' => '+127.5%', 'volume' => '$342K', 'color' => 'yellow'],
              ['name' => 'RocketFuel', 'symbol' => 'RCKT', 'change' => '+98.3%', 'volume' => '$289K', 'color' => 'orange'],
              ['name' => 'StarToken', 'symbol' => 'STAR', 'change' => '+76.2%', 'volume' => '$215K', 'color' => 'blue'],
              ['name' => 'GalaxyInu', 'symbol' => 'GINU', 'change' => '+64.8%', 'volume' => '$198K', 'color' => 'purple'],
              ['name' => 'CosmicCoin', 'symbol' => 'CSMC', 'change' => '+52.1%', 'volume' => '$176K', 'color' => 'green'],
            ];
          @endphp

          @foreach($topCoins as $index => $coin)
            <div class="group flex items-center gap-3 p-3 rounded-xl bg-white/5 hover:bg-white/10 border border-white/5 hover:border-yellow-400/30 transition-all cursor-pointer">
              <div class="flex items-center justify-center h-10 w-10 rounded-full bg-gradient-to-br from-{{ $coin['color'] }}-400 to-{{ $coin['color'] }}-600 text-white font-bold text-sm">
                #{{ $index + 1 }}
              </div>
              <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2">
                  <h3 class="text-white font-semibold text-sm truncate">{{ $coin['name'] }}</h3>
                  <span class="text-gray-500 text-xs">{{ $coin['symbol'] }}</span>
                </div>
                <p class="text-gray-400 text-xs">{{ $coin['volume'] }}</p>
              </div>
              <div class="text-right">
                <span class="text-green-400 font-semibold text-sm">{{ $coin['change'] }}</span>
              </div>
            </div>
          @endforeach
        </div>
      </div>
    </div>

    {{-- User Activity & Recent Launches --}}
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-5">
      {{-- User Activity Chart --}}
      <div class="bg-gradient-to-br from-[#0a0a0a] to-[#151515] border border-white/10 rounded-2xl p-6">
        <div class="flex items-center justify-between mb-6">
          <div>
            <h2 class="text-xl font-bold text-white">User Activity</h2>
            <p class="text-gray-400 text-sm mt-1">Active users over time</p>
          </div>
        </div>
        <div class="h-64 relative">
          <canvas id="userActivityChart"></canvas>
        </div>
      </div>

      {{-- Recent Coin Launches --}}
      <div class="bg-gradient-to-br from-[#0a0a0a] to-[#151515] border border-white/10 rounded-2xl p-6">
        <h2 class="text-xl font-bold text-white mb-6">Recent Launches</h2>
        <div class="space-y-3">
          @php
            $recentLaunches = [
              ['name' => 'SuperMoon', 'time' => '5 mins ago', 'volume' => '$12.4K', 'status' => 'live'],
              ['name' => 'LaunchPad', 'time' => '12 mins ago', 'volume' => '$8.9K', 'status' => 'live'],
              ['name' => 'MoonRise', 'time' => '23 mins ago', 'volume' => '$15.2K', 'status' => 'live'],
              ['name' => 'StarBurst', 'time' => '1 hour ago', 'volume' => '$21.7K', 'status' => 'migrated'],
              ['name' => 'NovaCoin', 'time' => '2 hours ago', 'volume' => '$18.3K', 'status' => 'migrated'],
            ];
          @endphp

          @foreach($recentLaunches as $launch)
            <div class="flex items-center gap-4 p-4 rounded-xl bg-white/5 hover:bg-white/10 border border-white/5 transition-all group cursor-pointer">
              <div class="h-12 w-12 rounded-full bg-gradient-to-br from-yellow-400 to-orange-500 flex items-center justify-center shrink-0">
                <i class="fas fa-rocket text-black"></i>
              </div>
              <div class="flex-1 min-w-0">
                <h3 class="text-white font-semibold truncate">{{ $launch['name'] }}</h3>
                <p class="text-gray-400 text-xs">{{ $launch['time'] }}</p>
              </div>
              <div class="text-right">
                <p class="text-white font-semibold text-sm">{{ $launch['volume'] }}</p>
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium 
                  {{ $launch['status'] === 'live' ? 'bg-green-500/20 text-green-400' : 'bg-blue-500/20 text-blue-400' }}">
                  {{ ucfirst($launch['status']) }}
                </span>
              </div>
            </div>
          @endforeach
        </div>
      </div>
    </div>

    {{-- Platform Health & Alerts --}}
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-5">
      {{-- System Health --}}
      <div class="bg-gradient-to-br from-[#0a0a0a] to-[#151515] border border-white/10 rounded-2xl p-6">
        <h2 class="text-xl font-bold text-white mb-6">System Health</h2>
        <div class="space-y-4">
          <div>
            <div class="flex items-center justify-between mb-2">
              <span class="text-gray-400 text-sm">API Response Time</span>
              <span class="text-green-400 text-sm font-semibold">Excellent</span>
            </div>
            <div class="h-2 bg-white/5 rounded-full overflow-hidden">
              <div class="h-full w-[92%] bg-gradient-to-r from-green-400 to-emerald-500 rounded-full"></div>
            </div>
          </div>
          <div>
            <div class="flex items-center justify-between mb-2">
              <span class="text-gray-400 text-sm">Database Performance</span>
              <span class="text-green-400 text-sm font-semibold">Good</span>
            </div>
            <div class="h-2 bg-white/5 rounded-full overflow-hidden">
              <div class="h-full w-[85%] bg-gradient-to-r from-green-400 to-emerald-500 rounded-full"></div>
            </div>
          </div>
          <div>
            <div class="flex items-center justify-between mb-2">
              <span class="text-gray-400 text-sm">Server Load</span>
              <span class="text-yellow-400 text-sm font-semibold">Moderate</span>
            </div>
            <div class="h-2 bg-white/5 rounded-full overflow-hidden">
              <div class="h-full w-[65%] bg-gradient-to-r from-yellow-400 to-orange-500 rounded-full"></div>
            </div>
          </div>
          <div>
            <div class="flex items-center justify-between mb-2">
              <span class="text-gray-400 text-sm">Network Latency</span>
              <span class="text-green-400 text-sm font-semibold">Optimal</span>
            </div>
            <div class="h-2 bg-white/5 rounded-full overflow-hidden">
              <div class="h-full w-[95%] bg-gradient-to-r from-green-400 to-emerald-500 rounded-full"></div>
            </div>
          </div>
        </div>
      </div>

      {{-- Quick Stats --}}
      <div class="bg-gradient-to-br from-[#0a0a0a] to-[#151515] border border-white/10 rounded-2xl p-6">
        <h2 class="text-xl font-bold text-white mb-6">Quick Stats</h2>
        <div class="space-y-4">
          <div class="flex items-center justify-between p-3 rounded-lg bg-white/5">
            <div class="flex items-center gap-3">
              <div class="h-10 w-10 rounded-lg bg-purple-500/20 flex items-center justify-center">
                <i class="fas fa-exchange-alt text-purple-400"></i>
              </div>
              <span class="text-gray-300 text-sm">Total Trades</span>
            </div>
            <span class="text-white font-bold">23,847</span>
          </div>
          <div class="flex items-center justify-between p-3 rounded-lg bg-white/5">
            <div class="flex items-center gap-3">
              <div class="h-10 w-10 rounded-lg bg-blue-500/20 flex items-center justify-center">
                <i class="fas fa-fire text-blue-400"></i>
              </div>
              <span class="text-gray-300 text-sm">Trending Coins</span>
            </div>
            <span class="text-white font-bold">142</span>
          </div>
          <div class="flex items-center justify-between p-3 rounded-lg bg-white/5">
            <div class="flex items-center gap-3">
              <div class="h-10 w-10 rounded-lg bg-green-500/20 flex items-center justify-center">
                <i class="fas fa-check-circle text-green-400"></i>
              </div>
              <span class="text-gray-300 text-sm">Verified Coins</span>
            </div>
            <span class="text-white font-bold">892</span>
          </div>
          <div class="flex items-center justify-between p-3 rounded-lg bg-white/5">
            <div class="flex items-center gap-3">
              <div class="h-10 w-10 rounded-lg bg-red-500/20 flex items-center justify-center">
                <i class="fas fa-flag text-red-400"></i>
              </div>
              <span class="text-gray-300 text-sm">Flagged Items</span>
            </div>
            <span class="text-white font-bold">17</span>
          </div>
        </div>
      </div>

      {{-- Recent Alerts --}}
      <div class="bg-gradient-to-br from-[#0a0a0a] to-[#151515] border border-white/10 rounded-2xl p-6">
        <h2 class="text-xl font-bold text-white mb-6">Recent Alerts</h2>
        <div class="space-y-3">
          <div class="flex items-start gap-3 p-3 rounded-lg bg-yellow-500/10 border border-yellow-500/20">
            <i class="fas fa-exclamation-triangle text-yellow-400 mt-1"></i>
            <div class="flex-1">
              <p class="text-yellow-200 text-sm font-medium">High Trading Volume</p>
              <p class="text-yellow-400/70 text-xs mt-1">MoonDoge exceeded 500% increase</p>
            </div>
          </div>
          <div class="flex items-start gap-3 p-3 rounded-lg bg-blue-500/10 border border-blue-500/20">
            <i class="fas fa-info-circle text-blue-400 mt-1"></i>
            <div class="flex-1">
              <p class="text-blue-200 text-sm font-medium">Migration Complete</p>
              <p class="text-blue-400/70 text-xs mt-1">StarToken moved to PancakeSwap</p>
            </div>
          </div>
          <div class="flex items-start gap-3 p-3 rounded-lg bg-green-500/10 border border-green-500/20">
            <i class="fas fa-check-circle text-green-400 mt-1"></i>
            <div class="flex-1">
              <p class="text-green-200 text-sm font-medium">System Update</p>
              <p class="text-green-400/70 text-xs mt-1">All services running smoothly</p>
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>

  {{-- Chart.js Scripts --}}
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script>
    // Trading Volume Chart
    const volumeCtx = document.getElementById('volumeChart').getContext('2d');
    const volumeChart = new Chart(volumeCtx, {
      type: 'line',
      data: {
        labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
        datasets: [{
          label: 'Volume',
          data: [1.8, 2.1, 1.9, 2.3, 2.7, 2.4, 2.4],
          borderColor: '#facc15',
          backgroundColor: (context) => {
            const ctx = context.chart.ctx;
            const gradient = ctx.createLinearGradient(0, 0, 0, 300);
            gradient.addColorStop(0, 'rgba(250, 204, 21, 0.3)');
            gradient.addColorStop(1, 'rgba(250, 204, 21, 0)');
            return gradient;
          },
          borderWidth: 3,
          fill: true,
          tension: 0.4,
          pointBackgroundColor: '#facc15',
          pointBorderColor: '#000',
          pointBorderWidth: 2,
          pointRadius: 5,
          pointHoverRadius: 7,
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            display: false
          },
          tooltip: {
            backgroundColor: 'rgba(0, 0, 0, 0.8)',
            padding: 12,
            titleColor: '#facc15',
            bodyColor: '#fff',
            borderColor: '#facc15',
            borderWidth: 1,
            callbacks: {
              label: function(context) {
                return 'Volume: $' + context.parsed.y + 'M';
              }
            }
          }
        },
        scales: {
          y: {
            beginAtZero: true,
            grid: {
              color: 'rgba(255, 255, 255, 0.05)',
              drawBorder: false,
            },
            ticks: {
              color: '#9ca3af',
              callback: function(value) {
                return '$' + value + 'M';
              }
            }
          },
          x: {
            grid: {
              display: false,
            },
            ticks: {
              color: '#9ca3af'
            }
          }
        }
      }
    });

    // User Activity Chart
    const userCtx = document.getElementById('userActivityChart').getContext('2d');
    const userChart = new Chart(userCtx, {
      type: 'bar',
      data: {
        labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
        datasets: [{
          label: 'Active Users',
          data: [6200, 7100, 6800, 8300, 9100, 8400, 7900],
          backgroundColor: (context) => {
            const ctx = context.chart.ctx;
            const gradient = ctx.createLinearGradient(0, 0, 0, 300);
            gradient.addColorStop(0, 'rgba(59, 130, 246, 0.8)');
            gradient.addColorStop(1, 'rgba(147, 51, 234, 0.8)');
            return gradient;
          },
          borderRadius: 8,
          borderSkipped: false,
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            display: false
          },
          tooltip: {
            backgroundColor: 'rgba(0, 0, 0, 0.8)',
            padding: 12,
            titleColor: '#3b82f6',
            bodyColor: '#fff',
            borderColor: '#3b82f6',
            borderWidth: 1,
            callbacks: {
              label: function(context) {
                return 'Users: ' + context.parsed.y.toLocaleString();
              }
            }
          }
        },
        scales: {
          y: {
            beginAtZero: true,
            grid: {
              color: 'rgba(255, 255, 255, 0.05)',
              drawBorder: false,
            },
            ticks: {
              color: '#9ca3af',
              callback: function(value) {
                return (value / 1000) + 'K';
              }
            }
          },
          x: {
            grid: {
              display: false,
            },
            ticks: {
              color: '#9ca3af'
            }
          }
        }
      }
    });
  </script>
</x-business-layout>