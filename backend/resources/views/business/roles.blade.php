<x-business-layout title="Role Management">
    <div class="p-6">
        
        {{-- Header --}}
        <div class="mb-8 flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-white mb-2">Role Management</h1>
                <p class="text-gray-400">Manage all admin roles and permissions</p>
            </div>
            <button onclick="openAddModal()" 
                    class="px-6 py-3 bg-gradient-to-r from-yellow-400 to-orange-500 text-white font-bold rounded-lg hover:brightness-110 transition">
                <i class="fas fa-plus mr-2"></i> Add Role
            </button>
        </div>

        {{-- Success/Error Messages --}}
        @if(session('success'))
            <div class="mb-6 p-4 bg-green-500/20 border border-green-500 rounded-lg">
                <p class="text-green-400 text-sm">{{ session('success') }}</p>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 p-4 bg-red-500/20 border border-red-500 rounded-lg">
                <p class="text-red-400 text-sm">{{ session('error') }}</p>
            </div>
        @endif

        @if($errors->any())
            <div class="mb-6 p-4 bg-red-500/20 border border-red-500 rounded-lg">
                <ul class="text-red-400 text-sm">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Search Bar --}}
        <div class="mb-6">
            <form action="{{ route('admin.roles.search') }}" method="GET" class="flex gap-3">
                <input type="text" name="search" value="{{ $search ?? '' }}" 
                       placeholder="Search by name or email..." 
                       class="flex-1 px-4 py-3 bg-gray-800 border border-gray-700 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:border-yellow-400">
                <button type="submit" 
                        class="px-6 py-3 bg-gradient-to-r from-yellow-400 to-orange-500 text-white font-bold rounded-lg hover:brightness-110 transition">
                    Search
                </button>
                @if(isset($search))
                    <a href="{{ route('admin.roles') }}" 
                       class="px-6 py-3 bg-gray-700 text-white font-bold rounded-lg hover:bg-gray-600 transition">
                        Clear
                    </a>
                @endif
            </form>
        </div>

        {{-- Roles Table --}}
        <div class="bg-gray-800/50 rounded-xl border border-gray-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gradient-to-r from-yellow-400/20 to-orange-500/20 border-b border-gray-700">
                            <th class="px-6 py-4 text-left text-xs font-bold text-yellow-400 uppercase tracking-wider">
                                ID
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-yellow-400 uppercase tracking-wider">
                                Name
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-yellow-400 uppercase tracking-wider">
                                Email
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-yellow-400 uppercase tracking-wider">
                                Role Type
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-yellow-400 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-yellow-400 uppercase tracking-wider">
                                Last Login
                            </th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-yellow-400 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700">
                        @forelse($roles as $role)
                            <tr class="hover:bg-gray-700/30 transition">
                                {{-- ID --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-white">#{{ $role->roles_id }}</div>
                                </td>

                                {{-- Name --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-white">{{ $role->name }}</div>
                                </td>

                                {{-- Email --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-400">{{ $role->email }}</div>
                                </td>

                                {{-- Role Type --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-3 py-1 rounded-full text-xs font-bold
                                        @if($role->role_type == 'Owner') bg-purple-500/20 text-purple-400 border border-purple-500
                                        @elseif($role->role_type == 'Admin') bg-blue-500/20 text-blue-400 border border-blue-500
                                        @elseif($role->role_type == 'Ops') bg-green-500/20 text-green-400 border border-green-500
                                        @elseif($role->role_type == 'Finance') bg-yellow-500/20 text-yellow-400 border border-yellow-500
                                        @else bg-gray-500/20 text-gray-400 border border-gray-500
                                        @endif">
                                        {{ $role->role_type }}
                                    </span>
                                </td>

                                {{-- Status --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-3 py-1 rounded-full text-xs font-bold
                                        @if($role->status == 'Active') bg-green-500/20 text-green-400 border border-green-500
                                        @else bg-red-500/20 text-red-400 border border-red-500
                                        @endif">
                                        {{ $role->status }}
                                    </span>
                                </td>

                                {{-- Last Login --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($role->last_login_at)
                                        <div class="text-sm text-gray-400">
                                            {{ \Carbon\Carbon::parse($role->last_login_at)->format('M d, Y') }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{ \Carbon\Carbon::parse($role->last_login_at)->format('h:i A') }}
                                        </div>
                                    @else
                                        <span class="text-sm text-gray-500">Never</span>
                                    @endif
                                </td>

                                {{-- Actions --}}
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <div class="flex gap-2 justify-center">
                                        <button onclick='openEditModal(@json($role))' 
                                                class="px-3 py-2 bg-blue-500 hover:bg-blue-600 text-white text-sm font-bold rounded-lg transition">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button onclick="confirmDelete({{ $role->roles_id }}, '{{ $role->name }}')" 
                                                class="px-3 py-2 bg-red-500 hover:bg-red-600 text-white text-sm font-bold rounded-lg transition">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center">
                                    <div class="text-gray-500">
                                        <i class="fas fa-user-shield text-4xl mb-3"></i>
                                        <p class="text-lg">No roles found</p>
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
            @if($roles->hasPages())
                <div class="px-6 py-4 bg-gray-800/30 border-t border-gray-700">
                    {{ $roles->links() }}
                </div>
            @endif
        </div>

        {{-- Stats --}}
        <div class="mt-6 grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-gray-800/50 rounded-lg border border-gray-700 p-4">
                <p class="text-gray-400 text-sm">Total Roles</p>
                <p class="text-white text-2xl font-bold">{{ $roles->total() }}</p>
            </div>
            <div class="bg-gray-800/50 rounded-lg border border-gray-700 p-4">
                <p class="text-gray-400 text-sm">Active Roles</p>
                <p class="text-green-400 text-2xl font-bold">{{ $activeCount ?? 0 }}</p>
            </div>
            <div class="bg-gray-800/50 rounded-lg border border-gray-700 p-4">
                <p class="text-gray-400 text-sm">Inactive Roles</p>
                <p class="text-red-400 text-2xl font-bold">{{ $inactiveCount ?? 0 }}</p>
            </div>
            <div class="bg-gray-800/50 rounded-lg border border-gray-700 p-4">
                <p class="text-gray-400 text-sm">Per Page</p>
                <p class="text-white text-2xl font-bold">{{ $roles->perPage() }}</p>
            </div>
        </div>

    </div>

    {{-- Add Role Modal --}}
    <div id="addModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50 overflow-y-auto">
        <div class="bg-gray-800 rounded-xl border border-gray-700 p-6 max-w-2xl w-full mx-4 my-8">
            <h3 class="text-2xl font-bold text-white mb-6">Add New Role</h3>
            <form action="{{ route('admin.roles.store') }}" method="POST">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    {{-- Name --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-400 mb-2">Name *</label>
                        <input type="text" name="name" required 
                               class="w-full px-4 py-3 bg-gray-900 border border-gray-700 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:border-yellow-400">
                    </div>

                    {{-- Email --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-400 mb-2">Email *</label>
                        <input type="email" name="email" required 
                               class="w-full px-4 py-3 bg-gray-900 border border-gray-700 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:border-yellow-400">
                    </div>

                    {{-- Role Type --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-400 mb-2">Role Type *</label>
                        <select name="role_type" required 
                                class="w-full px-4 py-3 bg-gray-900 border border-gray-700 rounded-lg text-white focus:outline-none focus:border-yellow-400">
                            <option value="">Select Role Type</option>
                            <option value="Owner">Owner</option>
                            <option value="Admin">Admin</option>
                            <option value="Ops">Ops</option>
                            <option value="Finance">Finance</option>
                            <option value="Support">Support</option>
                        </select>
                    </div>

                    {{-- Status --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-400 mb-2">Status *</label>
                        <select name="status" required 
                                class="w-full px-4 py-3 bg-gray-900 border border-gray-700 rounded-lg text-white focus:outline-none focus:border-yellow-400">
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                        </select>
                    </div>

                    {{-- Password --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-400 mb-2">Password *</label>
                        <input type="password" name="password" required 
                               class="w-full px-4 py-3 bg-gray-900 border border-gray-700 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:border-yellow-400">
                    </div>

                    {{-- Confirm Password --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-400 mb-2">Confirm Password *</label>
                        <input type="password" name="password_confirmation" required 
                               class="w-full px-4 py-3 bg-gray-900 border border-gray-700 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:border-yellow-400">
                    </div>
                </div>

                <div class="flex gap-3 mt-6">
                    <button type="submit" 
                            class="flex-1 px-4 py-3 bg-gradient-to-r from-yellow-400 to-orange-500 text-white font-bold rounded-lg hover:brightness-110 transition">
                        <i class="fas fa-plus mr-2"></i> Add Role
                    </button>
                    <button type="button" onclick="closeAddModal()" 
                            class="flex-1 px-4 py-3 bg-gray-700 hover:bg-gray-600 text-white font-bold rounded-lg transition">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Edit Role Modal --}}
    <div id="editModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50 overflow-y-auto">
        <div class="bg-gray-800 rounded-xl border border-gray-700 p-6 max-w-2xl w-full mx-4 my-8">
            <h3 class="text-2xl font-bold text-white mb-6">Edit Role</h3>
            <form id="editForm" method="POST">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    {{-- Name --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-400 mb-2">Name *</label>
                        <input type="text" name="name" id="edit_name" required 
                               class="w-full px-4 py-3 bg-gray-900 border border-gray-700 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:border-yellow-400">
                    </div>

                    {{-- Email --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-400 mb-2">Email *</label>
                        <input type="email" name="email" id="edit_email" required 
                               class="w-full px-4 py-3 bg-gray-900 border border-gray-700 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:border-yellow-400">
                    </div>

                    {{-- Role Type --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-400 mb-2">Role Type *</label>
                        <select name="role_type" id="edit_role_type" required 
                                class="w-full px-4 py-3 bg-gray-900 border border-gray-700 rounded-lg text-white focus:outline-none focus:border-yellow-400">
                            <option value="">Select Role Type</option>
                            <option value="Owner">Owner</option>
                            <option value="Admin">Admin</option>
                            <option value="Ops">Ops</option>
                            <option value="Finance">Finance</option>
                            <option value="Support">Support</option>
                        </select>
                    </div>

                    {{-- Status --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-400 mb-2">Status *</label>
                        <select name="status" id="edit_status" required 
                                class="w-full px-4 py-3 bg-gray-900 border border-gray-700 rounded-lg text-white focus:outline-none focus:border-yellow-400">
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                        </select>
                    </div>

                    {{-- Password (Optional) --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-400 mb-2">New Password (Optional)</label>
                        <input type="password" name="password" id="edit_password"
                               class="w-full px-4 py-3 bg-gray-900 border border-gray-700 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:border-yellow-400">
                        <p class="text-xs text-gray-500 mt-1">Leave blank to keep current password</p>
                    </div>

                    {{-- Confirm Password --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-400 mb-2">Confirm New Password</label>
                        <input type="password" name="password_confirmation" id="edit_password_confirmation"
                               class="w-full px-4 py-3 bg-gray-900 border border-gray-700 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:border-yellow-400">
                    </div>
                </div>

                <div class="flex gap-3 mt-6">
                    <button type="submit" 
                            class="flex-1 px-4 py-3 bg-gradient-to-r from-yellow-400 to-orange-500 text-white font-bold rounded-lg hover:brightness-110 transition">
                        <i class="fas fa-save mr-2"></i> Update Role
                    </button>
                    <button type="button" onclick="closeEditModal()" 
                            class="flex-1 px-4 py-3 bg-gray-700 hover:bg-gray-600 text-white font-bold rounded-lg transition">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Delete Confirmation Modal --}}
    <div id="deleteModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50">
        <div class="bg-gray-800 rounded-xl border border-gray-700 p-6 max-w-md w-full mx-4">
            <h3 class="text-xl font-bold text-white mb-4">Confirm Delete</h3>
            <p class="text-gray-400 mb-6">
                Are you sure you want to delete <span id="roleName" class="text-yellow-400 font-bold"></span>? 
                This action cannot be undone.
            </p>
            <div class="flex gap-3">
                <form id="deleteForm" method="POST" class="flex-1">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="w-full px-4 py-2 bg-red-500 hover:bg-red-600 text-white font-bold rounded-lg transition">
                        Yes, Delete Role
                    </button>
                </form>
                <button onclick="closeDeleteModal()" 
                        class="flex-1 px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white font-bold rounded-lg transition">
                    Cancel
                </button>
            </div>
        </div>
    </div>

    <script>
        // Add Modal Functions
        function openAddModal() {
            document.getElementById('addModal').classList.remove('hidden');
        }

        function closeAddModal() {
            document.getElementById('addModal').classList.add('hidden');
        }

        // Edit Modal Functions
        function openEditModal(role) {
            document.getElementById('edit_name').value = role.name;
            document.getElementById('edit_email').value = role.email;
            document.getElementById('edit_role_type').value = role.role_type;
            document.getElementById('edit_status').value = role.status;
            document.getElementById('edit_password').value = '';
            document.getElementById('edit_password_confirmation').value = '';
            document.getElementById('editForm').action = '/admin/roles/' + role.roles_id;
            document.getElementById('editModal').classList.remove('hidden');
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.add('hidden');
        }

        // Delete Modal Functions
        function confirmDelete(roleId, roleName) {
            document.getElementById('roleName').textContent = roleName;
            document.getElementById('deleteForm').action = '/admin/roles/' + roleId;
            document.getElementById('deleteModal').classList.remove('hidden');
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');
        }

        // Close modals on ESC key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeAddModal();
                closeEditModal();
                closeDeleteModal();
            }
        });

        // Close modal when clicking outside
        document.getElementById('addModal').addEventListener('click', function(e) {
            if (e.target === this) closeAddModal();
        });

        document.getElementById('editModal').addEventListener('click', function(e) {
            if (e.target === this) closeEditModal();
        });

        document.getElementById('deleteModal').addEventListener('click', function(e) {
            if (e.target === this) closeDeleteModal();
        });
    </script>

</x-business-layout>