<x-business-layout title="CloudWatch Monitoring">
  <div class="space-y-6">
    {{-- Header Section --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
      <div>
        <p class="text-gray-400 mt-1">Real-time system health and performance monitoring</p>
      </div>
      <div class="flex items-center gap-3">
        <button class="px-4 py-2 bg-white/5 hover:bg-white/10 border border-white/10 rounded-lg text-white text-sm transition">
          <i class="fas fa-sync-alt mr-2"></i>Refresh
        </button>
        <button class="px-4 py-2 bg-gradient-to-r from-yellow-400 to-orange-500 hover:from-yellow-500 hover:to-orange-600 rounded-lg text-black font-semibold text-sm transition">
          <i class="fas fa-bell mr-2"></i>Alerts
        </button>
      </div>
    </div>

    {{-- System Health Stats --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-5">
      {{-- API Errors --}}
      <div class="group relative overflow-hidden bg-gradient-to-br from-[#0a0a0a] to-[#151515] border border-white/10 rounded-2xl p-6 hover:border-red-400/50 transition-all duration-300">
        <div class="absolute top-0 right-0 w-32 h-32 bg-red-400/5 rounded-full blur-3xl group-hover:bg-red-400/10 transition-all"></div>
        <div class="relative">
          <div class="flex items-center justify-between mb-4">
            <div class="h-12 w-12 rounded-xl bg-gradient-to-br from-red-400 to-red-600 flex items-center justify-center">
              <i class="fas fa-exclamation-triangle text-white text-xl"></i>
            </div>
            <span class="text-red-400 text-sm font-semibold flex items-center gap-1">
              <i class="fas fa-arrow-up text-xs"></i>3.2%
            </span>
          </div>
          <h3 class="text-gray-400 text-sm font-medium mb-1">API Errors (24h)</h3>
          <p class="text-3xl font-bold text-white">127</p>
          <p class="text-gray-500 text-xs mt-2">4 critical errors</p>
        </div>
      </div>

      {{-- Backend Uptime --}}
      <div class="group relative overflow-hidden bg-gradient-to-br from-[#0a0a0a] to-[#151515] border border-white/10 rounded-2xl p-6 hover:border-green-400/50 transition-all duration-300">
        <div class="absolute top-0 right-0 w-32 h-32 bg-green-400/5 rounded-full blur-3xl group-hover:bg-green-400/10 transition-all"></div>
        <div class="relative">
          <div class="flex items-center justify-between mb-4">
            <div class="h-12 w-12 rounded-xl bg-gradient-to-br from-green-400 to-emerald-500 flex items-center justify-center">
              <i class="fas fa-server text-white text-xl"></i>
            </div>
            <span class="text-green-400 text-sm font-semibold flex items-center gap-1">
              <i class="fas fa-check text-xs"></i>Healthy
            </span>
          </div>
          <h3 class="text-gray-400 text-sm font-medium mb-1">Backend Uptime</h3>
          <p class="text-3xl font-bold text-white">99.98%</p>
          <p class="text-gray-500 text-xs mt-2">3 mins downtime (30d)</p>
        </div>
      </div>

      {{-- Budget Status --}}
      <div class="group relative overflow-hidden bg-gradient-to-br from-[#0a0a0a] to-[#151515] border border-white/10 rounded-2xl p-6 hover:border-yellow-400/50 transition-all duration-300">
        <div class="absolute top-0 right-0 w-32 h-32 bg-yellow-400/5 rounded-full blur-3xl group-hover:bg-yellow-400/10 transition-all"></div>
        <div class="relative">
          <div class="flex items-center justify-between mb-4">
            <div class="h-12 w-12 rounded-xl bg-gradient-to-br from-yellow-400 to-orange-500 flex items-center justify-center">
              <i class="fas fa-dollar-sign text-black text-xl"></i>
            </div>
            <span class="text-yellow-400 text-sm font-semibold flex items-center gap-1">
              <i class="fas fa-minus text-xs"></i>Normal
            </span>
          </div>
          <h3 class="text-gray-400 text-sm font-medium mb-1">Monthly Budget</h3>
          <p class="text-3xl font-bold text-white">$4,287</p>
          <p class="text-gray-500 text-xs mt-2">67% of $6,400 limit</p>
        </div>
      </div>

      {{-- Job Failures --}}
      <div class="group relative overflow-hidden bg-gradient-to-br from-[#0a0a0a] to-[#151515] border border-white/10 rounded-2xl p-6 hover:border-orange-400/50 transition-all duration-300">
        <div class="absolute top-0 right-0 w-32 h-32 bg-orange-400/5 rounded-full blur-3xl group-hover:bg-orange-400/10 transition-all"></div>
        <div class="relative">
          <div class="flex items-center justify-between mb-4">
            <div class="h-12 w-12 rounded-xl bg-gradient-to-br from-orange-400 to-red-500 flex items-center justify-center">
              <i class="fas fa-times-circle text-white text-xl"></i>
            </div>
            <span class="text-orange-400 text-sm font-semibold flex items-center gap-1">
              <i class="fas fa-arrow-down text-xs"></i>1.8%
            </span>
          </div>
          <h3 class="text-gray-400 text-sm font-medium mb-1">Job Failures (24h)</h3>
          <p class="text-3xl font-bold text-white">23</p>
          <p class="text-gray-500 text-xs mt-2">12 retried successfully</p>
        </div>
      </div>
    </div>

    {{-- Main Monitoring Grid --}}
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-5">
      {{-- Recent API Errors --}}
      <div class="xl:col-span-2 bg-gradient-to-br from-[#0a0a0a] to-[#151515] border border-white/10 rounded-2xl p-6">
        <div class="flex items-center justify-between mb-6">
          <div>
            <h2 class="text-xl font-bold text-white">Recent API Errors</h2>
            <p class="text-gray-400 text-sm mt-1">Latest errors and exceptions</p>
          </div>
          <a href="/admin/api-errors" class="text-yellow-400 hover:text-yellow-300 text-sm font-semibold flex items-center gap-2 transition">
            View All <i class="fas fa-arrow-right text-xs"></i>
          </a>
        </div>

        <div class="space-y-3">
          @php
            $apiErrors = [
              ['type' => '500 Internal Server Error', 'endpoint' => '/api/v1/coins/create', 'message' => 'Database connection timeout', 'time' => '2 mins ago', 'status' => 'critical'],
              ['type' => '429 Too Many Requests', 'endpoint' => '/api/v1/trades/execute', 'message' => 'Rate limit exceeded', 'time' => '8 mins ago', 'status' => 'warning'],
              ['type' => '504 Gateway Timeout', 'endpoint' => '/api/v1/wallet/balance', 'message' => 'Upstream service timeout', 'time' => '15 mins ago', 'status' => 'critical'],
              ['type' => '400 Bad Request', 'endpoint' => '/api/v1/user/update', 'message' => 'Invalid payload format', 'time' => '23 mins ago', 'status' => 'error'],
              ['type' => '503 Service Unavailable', 'endpoint' => '/api/v1/market/data', 'message' => 'Service temporarily unavailable', 'time' => '35 mins ago', 'status' => 'warning'],
            ];
          @endphp

          @foreach($apiErrors as $error)
            <div class="group flex items-start gap-4 p-4 rounded-xl bg-white/5 hover:bg-white/10 border border-white/5 hover:border-red-400/30 transition-all cursor-pointer">
              <div class="mt-1 h-10 w-10 rounded-lg 
                {{ $error['status'] === 'critical' ? 'bg-red-500/20' : ($error['status'] === 'warning' ? 'bg-yellow-500/20' : 'bg-orange-500/20') }} 
                flex items-center justify-center shrink-0">
                <i class="fas fa-exclamation-circle 
                  {{ $error['status'] === 'critical' ? 'text-red-400' : ($error['status'] === 'warning' ? 'text-yellow-400' : 'text-orange-400') }}"></i>
              </div>
              <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 mb-1">
                  <h3 class="text-white font-semibold">{{ $error['type'] }}</h3>
                  <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium 
                    {{ $error['status'] === 'critical' ? 'bg-red-500/20 text-red-400' : ($error['status'] === 'warning' ? 'bg-yellow-500/20 text-yellow-400' : 'bg-orange-500/20 text-orange-400') }}">
                    {{ ucfirst($error['status']) }}
                  </span>
                </div>
                <p class="text-gray-400 text-xs font-mono mb-1">{{ $error['endpoint'] }}</p>
                <p class="text-gray-500 text-sm">{{ $error['message'] }}</p>
                <p class="text-gray-600 text-xs mt-2">{{ $error['time'] }}</p>
              </div>
            </div>
          @endforeach
        </div>
      </div>

      {{-- Error Rate Chart --}}
      <div class="bg-gradient-to-br from-[#0a0a0a] to-[#151515] border border-white/10 rounded-2xl p-6">
        <h2 class="text-xl font-bold text-white mb-6">Error Rate Trend</h2>
        <div class="h-64 relative mb-6">
          <canvas id="errorRateChart"></canvas>
        </div>
        
        <div class="space-y-3">
          <div class="flex items-center justify-between p-3 rounded-lg bg-red-500/10">
            <div class="flex items-center gap-3">
              <div class="h-10 w-10 rounded-lg bg-red-500/20 flex items-center justify-center">
                <i class="fas fa-bug text-red-400"></i>
              </div>
              <span class="text-gray-300 text-sm">Critical</span>
            </div>
            <span class="text-white font-bold">4</span>
          </div>
          <div class="flex items-center justify-between p-3 rounded-lg bg-orange-500/10">
            <div class="flex items-center gap-3">
              <div class="h-10 w-10 rounded-lg bg-orange-500/20 flex items-center justify-center">
                <i class="fas fa-exclamation text-orange-400"></i>
              </div>
              <span class="text-gray-300 text-sm">Warnings</span>
            </div>
            <span class="text-white font-bold">89</span>
          </div>
        </div>
      </div>
    </div>

    {{-- Backend & Jobs Grid --}}
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-5">
      {{-- Backend Downtime --}}
      <div class="bg-gradient-to-br from-[#0a0a0a] to-[#151515] border border-white/10 rounded-2xl p-6">
        <div class="flex items-center justify-between mb-6">
          <div>
            <h2 class="text-xl font-bold text-white">Backend Status</h2>
            <p class="text-gray-400 text-sm mt-1">Service availability monitoring</p>
          </div>
          <a href="/admin/backend-downtime" class="text-yellow-400 hover:text-yellow-300 text-sm font-semibold flex items-center gap-2 transition">
            Details <i class="fas fa-arrow-right text-xs"></i>
          </a>
        </div>

        <div class="space-y-4 mb-6">
          @php
            $services = [
              ['name' => 'API Gateway', 'status' => 'operational', 'uptime' => '99.99%', 'latency' => '45ms'],
              ['name' => 'Database Cluster', 'status' => 'operational', 'uptime' => '99.98%', 'latency' => '12ms'],
              ['name' => 'Cache Layer', 'status' => 'operational', 'uptime' => '100%', 'latency' => '3ms'],
              ['name' => 'Job Queue', 'status' => 'degraded', 'uptime' => '98.76%', 'latency' => '120ms'],
              ['name' => 'Blockchain Node', 'status' => 'operational', 'uptime' => '99.95%', 'latency' => '89ms'],
            ];
          @endphp

          @foreach($services as $service)
            <div class="flex items-center justify-between p-3 rounded-lg bg-white/5 hover:bg-white/10 transition-all">
              <div class="flex items-center gap-3">
                <div class="h-3 w-3 rounded-full {{ $service['status'] === 'operational' ? 'bg-green-400' : 'bg-yellow-400' }} animate-pulse"></div>
                <span class="text-white font-medium">{{ $service['name'] }}</span>
              </div>
              <div class="flex items-center gap-4 text-sm">
                <span class="text-gray-400">{{ $service['uptime'] }}</span>
                <span class="text-gray-500">{{ $service['latency'] }}</span>
              </div>
            </div>
          @endforeach
        </div>

        <div class="p-4 rounded-lg bg-green-500/10 border border-green-500/20">
          <div class="flex items-center gap-3">
            <i class="fas fa-check-circle text-green-400 text-xl"></i>
            <div>
              <p class="text-green-200 font-medium">All Systems Operational</p>
              <p class="text-green-400/70 text-xs mt-1">Last incident: 7 days ago</p>
            </div>
          </div>
        </div>
      </div>

      {{-- Recent Job Failures --}}
      <div class="bg-gradient-to-br from-[#0a0a0a] to-[#151515] border border-white/10 rounded-2xl p-6">
        <div class="flex items-center justify-between mb-6">
          <div>
            <h2 class="text-xl font-bold text-white">Recent Job Failures</h2>
            <p class="text-gray-400 text-sm mt-1">Failed background jobs</p>
          </div>
          <a href="/admin/job-failures" class="text-yellow-400 hover:text-yellow-300 text-sm font-semibold flex items-center gap-2 transition">
            View All <i class="fas fa-arrow-right text-xs"></i>
          </a>
        </div>

        <div class="space-y-3">
          @php
            $jobFailures = [
              ['job' => 'ProcessRewards', 'error' => 'Insufficient gas', 'attempts' => '3/3', 'time' => '12 mins ago'],
              ['job' => 'SyncPriceData', 'error' => 'API rate limit', 'attempts' => '2/3', 'time' => '25 mins ago'],
              ['job' => 'SendEmailNotification', 'error' => 'SMTP timeout', 'attempts' => '1/3', 'time' => '41 mins ago'],
              ['job' => 'UpdateCoinMetadata', 'error' => 'Network timeout', 'attempts' => '3/3', 'time' => '1 hour ago'],
              ['job' => 'GenerateReport', 'error' => 'Memory limit exceeded', 'attempts' => '2/3', 'time' => '2 hours ago'],
            ];
          @endphp

          @foreach($jobFailures as $job)
            <div class="flex items-start gap-4 p-4 rounded-xl bg-white/5 hover:bg-white/10 border border-white/5 hover:border-orange-400/30 transition-all cursor-pointer">
              <div class="mt-1 h-10 w-10 rounded-lg bg-orange-500/20 flex items-center justify-center shrink-0">
                <i class="fas fa-cog text-orange-400"></i>
              </div>
              <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 mb-1">
                  <h3 class="text-white font-semibold">{{ $job['job'] }}</h3>
                  <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-500/20 text-red-400">
                    Failed
                  </span>
                </div>
                <p class="text-gray-400 text-sm">{{ $job['error'] }}</p>
                <div class="flex items-center gap-3 mt-2">
                  <span class="text-gray-500 text-xs">Attempts: {{ $job['attempts'] }}</span>
                  <span class="text-gray-600 text-xs">{{ $job['time'] }}</span>
                </div>
              </div>
            </div>
          @endforeach
        </div>
      </div>
    </div>

    {{-- Budget Alerts --}}
    <div class="bg-gradient-to-br from-[#0a0a0a] to-[#151515] border border-white/10 rounded-2xl p-6">
      <div class="flex items-center justify-between mb-6">
        <div>
          <h2 class="text-xl font-bold text-white">Budget Monitoring</h2>
          <p class="text-gray-400 text-sm mt-1">AWS cost tracking and alerts</p>
        </div>
        <a href="/admin/budget-alerts" class="text-yellow-400 hover:text-yellow-300 text-sm font-semibold flex items-center gap-2 transition">
          View Details <i class="fas fa-arrow-right text-xs"></i>
        </a>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
        @php
          $budgetItems = [
            ['service' => 'EC2 Instances', 'spent' => '$1,847', 'limit' => '$2,000', 'percentage' => 92],
            ['service' => 'RDS Database', 'spent' => '$1,234', 'limit' => '$1,500', 'percentage' => 82],
            ['service' => 'S3 Storage', 'spent' => '$456', 'limit' => '$800', 'percentage' => 57],
            ['service' => 'CloudWatch', 'spent' => '$750', 'limit' => '$1,100', 'percentage' => 68],
          ];
        @endphp

        @foreach($budgetItems as $item)
          <div class="p-4 rounded-xl bg-white/5 border border-white/5 hover:border-yellow-400/30 transition-all">
            <h3 class="text-white font-semibold mb-3">{{ $item['service'] }}</h3>
            <div class="flex items-end justify-between mb-2">
              <span class="text-2xl font-bold text-white">{{ $item['spent'] }}</span>
              <span class="text-gray-400 text-sm">/ {{ $item['limit'] }}</span>
            </div>
            <div class="h-2 bg-white/5 rounded-full overflow-hidden">
              <div class="h-full rounded-full transition-all duration-300
                {{ $item['percentage'] >= 90 ? 'bg-gradient-to-r from-red-400 to-red-600' : ($item['percentage'] >= 75 ? 'bg-gradient-to-r from-yellow-400 to-orange-500' : 'bg-gradient-to-r from-green-400 to-emerald-500') }}" 
                style="width: {{ $item['percentage'] }}%">
              </div>
            </div>
            <p class="text-gray-500 text-xs mt-2">{{ $item['percentage'] }}% utilized</p>
          </div>
        @endforeach
      </div>
    </div>

  </div>

  {{-- Chart.js Script --}}
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script>
    // Error Rate Chart
    const errorRateCtx = document.getElementById('errorRateChart').getContext('2d');
    const errorRateChart = new Chart(errorRateCtx, {
      type: 'line',
      data: {
        labels: ['00:00', '04:00', '08:00', '12:00', '16:00', '20:00', '24:00'],
        datasets: [{
          label: 'Errors',
          data: [45, 67, 89, 123, 156, 134, 127],
          borderColor: '#ef4444',
          backgroundColor: (context) => {
            const ctx = context.chart.ctx;
            const gradient = ctx.createLinearGradient(0, 0, 0, 250);
            gradient.addColorStop(0, 'rgba(239, 68, 68, 0.3)');
            gradient.addColorStop(1, 'rgba(239, 68, 68, 0)');
            return gradient;
          },
          borderWidth: 3,
          fill: true,
          tension: 0.4,
          pointBackgroundColor: '#ef4444',
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
            titleColor: '#ef4444',
            bodyColor: '#fff',
            borderColor: '#ef4444',
            borderWidth: 1,
            callbacks: {
              label: function(context) {
                return 'Errors: ' + context.parsed.y;
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
              color: '#9ca3af'
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