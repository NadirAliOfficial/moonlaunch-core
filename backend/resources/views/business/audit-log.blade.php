<x-business-layout title="Audit Log">
    <div class="p-6">
        
        {{-- Header --}}
        <div class="mb-8 flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-white mb-2">Audit Log</h1>
                <p class="text-gray-400">Track all admin actions and changes</p>
            </div>
            <a href="{{ route('admin.audit-log.export') }}" 
               class="px-6 py-3 bg-gradient-to-r from-yellow-400 to-orange-500 text-white font-bold rounded-lg hover:brightness-110 transition">
                <i class="fas fa-download mr-2"></i> Export CSV
            </a>
        </div>

        {{-- Filters --}}
        <div class="mb-6 bg-gray-800/50 rounded-xl border border-gray-700 p-6">
            <form action="{{ route('admin.audit-log.search') }}" method="GET" class="space-y-4">
                
                {{-- Search & Action Filter --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">Search</label>
                        <input type="text" name="search" value="{{ $search ?? '' }}" 
                               placeholder="Search by description, admin name, or email..." 
                               class="w-full px-4 py-3 bg-gray-800 border border-gray-700 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:border-yellow-400">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">Action Type</label>
                        <select name="action" 
                                class="w-full px-4 py-3 bg-gray-800 border border-gray-700 rounded-lg text-white focus:outline-none focus:border-yellow-400">
                            <option value="">All Actions</option>
                            <option value="admin_login" {{ (isset($action) && $action === 'admin_login') ? 'selected' : '' }}>Admin Login</option>
                            <option value="admin_logout" {{ (isset($action) && $action === 'admin_logout') ? 'selected' : '' }}>Admin Logout</option>
                            <option value="user_deleted" {{ (isset($action) && $action === 'user_deleted') ? 'selected' : '' }}>User Deleted</option>
                            <option value="token_flagged" {{ (isset($action) && $action === 'token_flagged') ? 'selected' : '' }}>Token Flagged</option>
                            <option value="fee_updated" {{ (isset($action) && $action === 'fee_updated') ? 'selected' : '' }}>Fee Updated</option>
                            <option value="payout_executed" {{ (isset($action) && $action === 'payout_executed') ? 'selected' : '' }}>Payout Executed</option>
                        </select>
                    </div>
                </div>

                {{-- Date Range --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">From Date</label>
                        <input type="date" name="date_from" value="{{ $dateFrom ?? '' }}" 
                               class="w-full px-4 py-3 bg-gray-800 border border-gray-700 rounded-lg text-white focus:outline-none focus:border-yellow-400">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">To Date</label>
                        <input type="date" name="date_to" value="{{ $dateTo ?? '' }}" 
                               class="w-full px-4 py-3 bg-gray-800 border border-gray-700 rounded-lg text-white focus:outline-none focus:border-yellow-400">
                    </div>
                </div>

                {{-- Buttons --}}
                <div class="flex gap-3">
                    <button type="submit" 
                            class="px-6 py-3 bg-gradient-to-r from-yellow-400 to-orange-500 text-white font-bold rounded-lg hover:brightness-110 transition">
                        <i class="fas fa-filter mr-2"></i> Apply Filters
                    </button>
                    @if(isset($search) || isset($action) || isset($dateFrom) || isset($dateTo))
                        <a href="{{ route('admin.audit-log') }}" 
                           class="px-6 py-3 bg-gray-700 text-white font-bold rounded-lg hover:bg-gray-600 transition">
                            <i class="fas fa-times mr-2"></i> Clear
                        </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Audit Logs Table --}}
        <div class="bg-gray-800/50 rounded-xl border border-gray-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gradient-to-r from-yellow-400/20 to-orange-500/20 border-b border-gray-700">
                            <th class="px-6 py-4 text-left text-xs font-bold text-yellow-400 uppercase tracking-wider">
                                ID
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-yellow-400 uppercase tracking-wider">
                                Admin
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-yellow-400 uppercase tracking-wider">
                                Action
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-yellow-400 uppercase tracking-wider">
                                Description
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-yellow-400 uppercase tracking-wider">
                                IP Address
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-yellow-400 uppercase tracking-wider">
                                Date & Time
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700">
                        @forelse($logs as $log)
                            <tr class="hover:bg-gray-700/30 transition">
                                {{-- ID --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-400">#{{ $log->id }}</div>
                                </td>

                                {{-- Admin --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-white">{{ $log->admin_name }}</div>
                                    <div class="text-xs text-gray-500">{{ $log->admin_email }}</div>
                                    <div class="inline-block mt-1">
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full
                                            @if($log->admin_role === 'owner') bg-purple-500/20 text-purple-400
                                            @elseif($log->admin_role === 'admin') bg-blue-500/20 text-blue-400
                                            @elseif($log->admin_role === 'ops') bg-green-500/20 text-green-400
                                            @elseif($log->admin_role === 'finance') bg-yellow-500/20 text-yellow-400
                                            @else bg-gray-500/20 text-gray-400
                                            @endif">
                                            {{ ucfirst($log->admin_role) }}
                                        </span>
                                    </div>
                                </td>

                                {{-- Action --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-3 py-1 text-xs font-bold rounded-full
                                        @if(str_contains($log->action, 'login')) bg-green-500/20 text-green-400
                                        @elseif(str_contains($log->action, 'logout')) bg-orange-500/20 text-orange-400
                                        @elseif(str_contains($log->action, 'delete')) bg-red-500/20 text-red-400
                                        @elseif(str_contains($log->action, 'update')) bg-blue-500/20 text-blue-400
                                        @else bg-gray-500/20 text-gray-400
                                        @endif">
                                        {{ str_replace('_', ' ', ucfirst($log->action)) }}
                                    </span>
                                </td>

                                {{-- Description --}}
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-300 max-w-md">{{ $log->description }}</div>
                                </td>

                                {{-- IP Address --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-400 font-mono">{{ $log->ip_address }}</div>
                                </td>

                                {{-- Date & Time --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-400">
                                        {{ \Carbon\Carbon::parse($log->created_at)->format('M d, Y') }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        {{ \Carbon\Carbon::parse($log->created_at)->format('h:i:s A') }}
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <div class="text-gray-500">
                                        <i class="fas fa-clipboard-list text-4xl mb-3"></i>
                                        <p class="text-lg">No audit logs found</p>
                                        @if(isset($search) || isset($action) || isset($dateFrom) || isset($dateTo))
                                            <p class="text-sm mt-2">Try adjusting your filters</p>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($logs->hasPages())
                <div class="px-6 py-4 bg-gray-800/30 border-t border-gray-700">
                    {{ $logs->links() }}
                </div>
            @endif
        </div>

        {{-- Stats --}}
        <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-gray-800/50 rounded-lg border border-gray-700 p-4">
                <p class="text-gray-400 text-sm">Total Logs</p>
                <p class="text-white text-2xl font-bold">{{ $logs->total() }}</p>
            </div>
            <div class="bg-gray-800/50 rounded-lg border border-gray-700 p-4">
                <p class="text-gray-400 text-sm">Current Page</p>
                <p class="text-white text-2xl font-bold">{{ $logs->currentPage() }}</p>
            </div>
            <div class="bg-gray-800/50 rounded-lg border border-gray-700 p-4">
                <p class="text-gray-400 text-sm">Per Page</p>
                <p class="text-white text-2xl font-bold">{{ $logs->perPage() }}</p>
            </div>
        </div>

    </div>
</x-business-layout>