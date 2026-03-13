<x-business-layout title="User Management">
    <div class="p-6">
        
        {{-- Header --}}
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-white mb-2">User Management</h1>
            <p class="text-gray-400">Manage all registered users</p>
        </div>

        {{-- Success/Error Messages with Auto-Dismiss --}}
        @if(session('success'))
            <div id="successAlert" class="mb-6 p-4 bg-green-500/20 border border-green-500 rounded-lg flex items-center justify-between">
                <p class="text-green-400 text-sm">{{ session('success') }}</p>
                <button onclick="closeAlert('successAlert')" class="text-green-400 hover:text-green-300">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        @endif

        @if(session('error'))
            <div id="errorAlert" class="mb-6 p-4 bg-red-500/20 border border-red-500 rounded-lg flex items-center justify-between">
                <p class="text-red-400 text-sm">{{ session('error') }}</p>
                <button onclick="closeAlert('errorAlert')" class="text-red-400 hover:text-red-300">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        @endif

        {{-- Search Bar --}}
        <div class="mb-6">
            <form action="{{ route('admin.users.search') }}" method="GET" class="flex gap-3">
                <input type="text" name="search" value="{{ $search ?? '' }}" 
                       placeholder="Search by name or email..." 
                       class="flex-1 px-4 py-3 bg-gray-800 border border-gray-700 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:border-yellow-400">
                <button type="submit" 
                        class="px-6 py-3 bg-gradient-to-r from-yellow-400 to-orange-500 text-white font-bold rounded-lg hover:brightness-110 transition">
                    Search
                </button>
                @if(isset($search))
                    <a href="{{ route('admin.user') }}" 
                       class="px-6 py-3 bg-gray-700 text-white font-bold rounded-lg hover:bg-gray-600 transition">
                        Clear
                    </a>
                @endif
            </form>
        </div>

        {{-- Users Table --}}
        <div class="bg-gray-800/50 rounded-xl border border-gray-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gradient-to-r from-yellow-400/20 to-orange-500/20 border-b border-gray-700">
                            <th class="px-6 py-4 text-left text-xs font-bold text-yellow-400 uppercase tracking-wider">
                                Profile
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-yellow-400 uppercase tracking-wider">
                                Name
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-yellow-400 uppercase tracking-wider">
                                Email
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-yellow-400 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-yellow-400 uppercase tracking-wider">
                                Joined
                            </th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-yellow-400 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700">
                        @forelse($users as $user)
                            <tr class="hover:bg-gray-700/30 transition">
                                {{-- Profile Picture --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($user->profile_pick)
                                        <img src="{{ $user->profile_pick }}" 
                                             alt="{{ $user->name }}" 
                                             class="h-10 w-10 rounded-full object-cover border-2 border-yellow-400">
                                    @else
                                        <div class="h-10 w-10 rounded-full bg-gradient-to-r from-yellow-400 to-orange-500 flex items-center justify-center">
                                            <span class="text-white font-bold text-sm">
                                                {{ strtoupper(substr($user->name, 0, 1)) }}
                                            </span>
                                        </div>
                                    @endif
                                </td>

                                {{-- Name --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-white">{{ $user->name }}</div>
                                </td>

                                {{-- Email --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-400">{{ $user->email }}</div>
                                </td>

                                {{-- Status --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($user->status == 'Active')
                                        <span class="px-3 py-1 text-xs font-bold rounded-full bg-green-500/20 text-green-400 border border-green-500">
                                            Active
                                        </span>
                                    @elseif($user->status == 'Blocked')
                                        <span class="px-3 py-1 text-xs font-bold rounded-full bg-red-500/20 text-red-400 border border-red-500">
                                            Blocked
                                        </span>
                                    @else
                                        <span class="px-3 py-1 text-xs font-bold rounded-full bg-gray-500/20 text-gray-400 border border-gray-500">
                                            Inactive
                                        </span>
                                    @endif
                                </td>

                                {{-- Created At --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-400">
                                        {{ \Carbon\Carbon::parse($user->created_at)->format('M d, Y') }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        {{ \Carbon\Carbon::parse($user->created_at)->format('h:i A') }}
                                    </div>
                                </td>

                                {{-- Actions --}}
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <div class="flex gap-2 justify-center">
                                        {{-- Block/Unblock User --}}
                                        @if($user->status == 'Blocked')
                                            <button onclick="confirmUnblock({{ $user->id }}, '{{ $user->name }}')" 
                                                    class="px-3 py-2 bg-green-500 hover:bg-green-600 text-white text-xs font-bold rounded-lg transition"
                                                    title="Unblock User">
                                                <i class="fas fa-unlock mr-1"></i> Unblock
                                            </button>
                                        @else
                                            <button onclick="confirmBlock({{ $user->id }}, '{{ $user->name }}')" 
                                                    class="px-3 py-2 bg-orange-500 hover:bg-orange-600 text-white text-xs font-bold rounded-lg transition"
                                                    title="Block User">
                                                <i class="fas fa-ban mr-1"></i> Block
                                            </button>
                                        @endif

                                        {{-- Reset Password --}}
                                        <button onclick="openResetPasswordModal({{ $user->id }}, '{{ $user->name }}')" 
                                                class="px-3 py-2 bg-blue-500 hover:bg-blue-600 text-white text-xs font-bold rounded-lg transition"
                                                title="Reset Password">
                                            <i class="fas fa-key mr-1"></i> Reset
                                        </button>

                                        {{-- Delete User --}}
                                        <button onclick="confirmDelete({{ $user->id }}, '{{ $user->name }}')" 
                                                class="px-3 py-2 bg-red-500 hover:bg-red-600 text-white text-xs font-bold rounded-lg transition"
                                                title="Delete User">
                                            <i class="fas fa-trash mr-1"></i> Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <div class="text-gray-500">
                                        <i class="fas fa-users text-4xl mb-3"></i>
                                        <p class="text-lg">No users found</p>
                                        @if(isset($search))
                                            <p class="text-sm mt-2">Try a different search term</p>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($users->hasPages())
                <div class="px-6 py-4 bg-gray-800/30 border-t border-gray-700">
                    {{ $users->links() }}
                </div>
            @endif
        </div>

        {{-- Stats --}}
        <div class="mt-6 grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-gray-800/50 rounded-lg border border-gray-700 p-4">
                <p class="text-gray-400 text-sm">Total Users</p>
                <p class="text-white text-2xl font-bold">{{ $users->total() }}</p>
            </div>
            <div class="bg-gray-800/50 rounded-lg border border-gray-700 p-4">
                <p class="text-gray-400 text-sm">Active Users</p>
                <p class="text-green-400 text-2xl font-bold">{{ $users->where('status', 'Active')->count() }}</p>
            </div>
            <div class="bg-gray-800/50 rounded-lg border border-gray-700 p-4">
                <p class="text-gray-400 text-sm">Blocked Users</p>
                <p class="text-red-400 text-2xl font-bold">{{ $users->where('status', 'Blocked')->count() }}</p>
            </div>
            <div class="bg-gray-800/50 rounded-lg border border-gray-700 p-4">
                <p class="text-gray-400 text-sm">Current Page</p>
                <p class="text-white text-2xl font-bold">{{ $users->currentPage() }}</p>
            </div>
        </div>

    </div>

    {{-- Delete Confirmation Modal --}}
    <div id="deleteModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50">
        <div class="bg-gray-800 rounded-xl border border-gray-700 p-6 max-w-md w-full mx-4">
            <h3 class="text-xl font-bold text-white mb-4">Confirm Delete</h3>
            <p class="text-gray-400 mb-6">
                Are you sure you want to delete <span id="deleteUserName" class="text-yellow-400 font-bold"></span>? 
                This action cannot be undone.
            </p>
            <div class="flex gap-3">
                <form id="deleteForm" method="POST" class="flex-1">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="w-full px-4 py-2 bg-red-500 hover:bg-red-600 text-white font-bold rounded-lg transition">
                        Yes, Delete User
                    </button>
                </form>
                <button onclick="closeDeleteModal()" 
                        class="flex-1 px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white font-bold rounded-lg transition">
                    Cancel
                </button>
            </div>
        </div>
    </div>

    {{-- Block Confirmation Modal --}}
    <div id="blockModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50">
        <div class="bg-gray-800 rounded-xl border border-gray-700 p-6 max-w-md w-full mx-4">
            <h3 class="text-xl font-bold text-white mb-4">Confirm Block</h3>
            <p class="text-gray-400 mb-6">
                Are you sure you want to block <span id="blockUserName" class="text-yellow-400 font-bold"></span>? 
                This user will not be able to access the platform.
            </p>
            <div class="flex gap-3">
                <form id="blockForm" method="POST" class="flex-1">
                    @csrf
                    @method('PUT')
                    <button type="submit" 
                            class="w-full px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white font-bold rounded-lg transition">
                        Yes, Block User
                    </button>
                </form>
                <button onclick="closeBlockModal()" 
                        class="flex-1 px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white font-bold rounded-lg transition">
                    Cancel
                </button>
            </div>
        </div>
    </div>

    {{-- Unblock Confirmation Modal --}}
    <div id="unblockModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50">
        <div class="bg-gray-800 rounded-xl border border-gray-700 p-6 max-w-md w-full mx-4">
            <h3 class="text-xl font-bold text-white mb-4">Confirm Unblock</h3>
            <p class="text-gray-400 mb-6">
                Are you sure you want to unblock <span id="unblockUserName" class="text-yellow-400 font-bold"></span>? 
                This user will regain access to the platform.
            </p>
            <div class="flex gap-3">
                <form id="unblockForm" method="POST" class="flex-1">
                    @csrf
                    @method('PUT')
                    <button type="submit" 
                            class="w-full px-4 py-2 bg-green-500 hover:bg-green-600 text-white font-bold rounded-lg transition">
                        Yes, Unblock User
                    </button>
                </form>
                <button onclick="closeUnblockModal()" 
                        class="flex-1 px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white font-bold rounded-lg transition">
                    Cancel
                </button>
            </div>
        </div>
    </div>

    {{-- Reset Password Modal --}}
    <div id="resetPasswordModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50">
        <div class="bg-gray-800 rounded-xl border border-gray-700 p-6 max-w-md w-full mx-4">
            <h3 class="text-xl font-bold text-white mb-4">Reset Password</h3>
            <p class="text-gray-400 mb-4">
                Reset password for <span id="resetPasswordUserName" class="text-yellow-400 font-bold"></span>
            </p>

            <form id="resetPasswordForm" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                
                <div>
                    <label class="block text-sm font-bold text-gray-300 mb-2">New Password</label>
                    <div class="relative">
                        <input id="new_password" 
                               type="password" 
                               name="password" 
                               placeholder="Enter new password" 
                               required 
                               minlength="6"
                               class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:border-yellow-400 pr-10">
                        <button type="button" 
                                onclick="togglePasswordVisibility('new_password')"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-white">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Minimum 6 characters</p>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-300 mb-2">Confirm Password</label>
                    <div class="relative">
                        <input id="confirm_password" 
                               type="password" 
                               name="password_confirmation" 
                               placeholder="Confirm new password" 
                               required 
                               minlength="6"
                               class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:border-yellow-400 pr-10">
                        <button type="button" 
                                onclick="togglePasswordVisibility('confirm_password')"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-white">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="flex gap-3 mt-6">
                    <button type="submit" 
                            class="flex-1 px-4 py-3 bg-blue-500 hover:bg-blue-600 text-white font-bold rounded-lg transition">
                        Reset Password
                    </button>
                    <button type="button" 
                            onclick="closeResetPasswordModal()" 
                            class="flex-1 px-4 py-3 bg-gray-700 hover:bg-gray-600 text-white font-bold rounded-lg transition">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Auto-dismiss alerts after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            const successAlert = document.getElementById('successAlert');
            const errorAlert = document.getElementById('errorAlert');

            if (successAlert) {
                setTimeout(() => {
                    fadeOutAlert('successAlert');
                }, 5000); // 5 seconds
            }

            if (errorAlert) {
                setTimeout(() => {
                    fadeOutAlert('errorAlert');
                }, 5000); // 5 seconds
            }
        });

        // Fade out and remove alert
        function fadeOutAlert(alertId) {
            const alert = document.getElementById(alertId);
            if (alert) {
                alert.style.transition = 'opacity 0.5s ease-out';
                alert.style.opacity = '0';
                setTimeout(() => {
                    alert.remove();
                }, 500);
            }
        }

        // Close alert manually
        function closeAlert(alertId) {
            fadeOutAlert(alertId);
        }

        // Delete Modal Functions
        function confirmDelete(userId, userName) {
            document.getElementById('deleteUserName').textContent = userName;
            document.getElementById('deleteForm').action = '/admin/users/' + userId;
            document.getElementById('deleteModal').classList.remove('hidden');
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');
        }

        // Block Modal Functions
        function confirmBlock(userId, userName) {
            document.getElementById('blockUserName').textContent = userName;
            document.getElementById('blockForm').action = '/admin/users/' + userId + '/block';
            document.getElementById('blockModal').classList.remove('hidden');
        }

        function closeBlockModal() {
            document.getElementById('blockModal').classList.add('hidden');
        }

        // Unblock Modal Functions
        function confirmUnblock(userId, userName) {
            document.getElementById('unblockUserName').textContent = userName;
            document.getElementById('unblockForm').action = '/admin/users/' + userId + '/unblock';
            document.getElementById('unblockModal').classList.remove('hidden');
        }

        function closeUnblockModal() {
            document.getElementById('unblockModal').classList.add('hidden');
        }

        // Reset Password Modal Functions
        function openResetPasswordModal(userId, userName) {
            document.getElementById('resetPasswordUserName').textContent = userName;
            document.getElementById('resetPasswordForm').action = '/admin/users/' + userId + '/reset-password';
            document.getElementById('resetPasswordModal').classList.remove('hidden');
            // Clear previous input
            document.getElementById('new_password').value = '';
            document.getElementById('confirm_password').value = '';
        }

        function closeResetPasswordModal() {
            document.getElementById('resetPasswordModal').classList.add('hidden');
        }

        // Toggle Password Visibility
        function togglePasswordVisibility(inputId) {
            const input = document.getElementById(inputId);
            const icon = input.nextElementSibling.querySelector('i');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        // Close modals on ESC key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeDeleteModal();
                closeBlockModal();
                closeUnblockModal();
                closeResetPasswordModal();
            }
        });
    </script>

</x-business-layout>