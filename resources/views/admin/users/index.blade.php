<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-bold text-2xl text-gray-900 leading-tight">
                    User Management
                </h2>
                <p class="text-sm text-gray-600 mt-1">Manage platform users, roles, and permissions</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-2 bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold py-2 px-4 rounded-lg transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to Dashboard
                </a>
                <a href="{{ route('admin.users.create') }}" class="inline-flex items-center gap-2 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-semibold py-2 px-4 rounded-lg shadow-lg hover:shadow-xl transition-all duration-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Add New User
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="mb-6 bg-gradient-to-r from-green-50 to-emerald-50 border-l-4 border-green-500 text-green-800 px-6 py-4 rounded-r-lg shadow-sm">
                    <div class="flex items-center gap-3">
                        <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span class="font-medium">{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 bg-gradient-to-r from-red-50 to-pink-50 border-l-4 border-red-500 text-red-800 px-6 py-4 rounded-r-lg shadow-sm">
                    <div class="flex items-center gap-3">
                        <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span class="font-medium">{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-blue-100 text-sm font-semibold uppercase tracking-wide">Total Users</p>
                            <p class="text-4xl font-bold mt-2">{{ number_format($totalUsers) }}</p>
                        </div>
                        <div class="bg-blue-400 bg-opacity-30 rounded-full p-4">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-xl shadow-lg p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-red-100 text-sm font-semibold uppercase tracking-wide">Trashed Users</p>
                            <p class="text-4xl font-bold mt-2">{{ number_format($trashedUsers) }}</p>
                        </div>
                        <div class="bg-red-400 bg-opacity-30 rounded-full p-4">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filter Section -->
            <div class="bg-white rounded-xl shadow-md mb-6 border border-gray-100">
                <div class="p-6">
                    <form method="GET" action="{{ route('admin.users.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="md:col-span-2">
                            <label for="search" class="block text-sm font-semibold text-gray-700 mb-2">
                                <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                                Search Users
                            </label>
                            <input type="text" name="search" id="search" value="{{ request('search') }}" 
                                placeholder="Search by name or email..." 
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all">
                        </div>
                        <div class="flex items-end gap-3">
                            <button type="submit" class="flex-1 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-semibold py-2 px-6 rounded-lg shadow-md hover:shadow-lg transition-all">
                                Search
                            </button>
                            @if(request('trashed') === 'true')
                                <a href="{{ route('admin.users.index') }}" class="flex-1 text-center bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold py-2 px-6 rounded-lg transition-colors">
                                    Show Active
                                </a>
                            @else
                                <a href="{{ route('admin.users.index', ['trashed' => 'true']) }}" class="flex-1 text-center bg-red-100 hover:bg-red-200 text-red-700 font-semibold py-2 px-6 rounded-lg transition-colors">
                                    Show Trashed
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>

            <!-- Users Table -->
            <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">User</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Email</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Role</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Joined</th>
                                <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($users as $user)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-500 rounded-full flex items-center justify-center text-white font-bold">
                                                {{ substr($user->name, 0, 1) }}
                                            </div>
                                            <div>
                                                <p class="font-semibold text-gray-900">{{ $user->name }}</p>
                                                @if($user->trashed())
                                                    <span class="text-xs text-red-600 font-medium">Trashed</span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        {{ $user->email }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="px-3 py-1 text-xs font-bold rounded-full {{ 
                                            $user->role === 'admin' ? 'bg-red-100 text-red-700' : 
                                            ($user->role === 'moderator' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-700') 
                                        }}">
                                            {{ ucfirst($user->role) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500">
                                        {{ $user->created_at->format('M d, Y') }}
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex justify-end gap-2">
                                            @if($user->trashed())
                                                <form method="POST" action="{{ route('admin.users.restore', $user->id) }}" class="inline">
                                                    @csrf
                                                    <button type="submit" class="px-3 py-1 bg-green-100 text-green-700 text-xs font-semibold rounded hover:bg-green-200 transition-colors">
                                                        Restore
                                                    </button>
                                                </form>
                                                <form method="POST" action="{{ route('admin.users.forceDelete', $user->id) }}" 
                                                      onsubmit="return confirm('Are you sure? This action cannot be undone!');" 
                                                      class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="px-3 py-1 bg-red-100 text-red-700 text-xs font-semibold rounded hover:bg-red-200 transition-colors">
                                                        Delete Forever
                                                    </button>
                                                </form>
                                            @else
                                                <a href="{{ route('admin.users.edit', $user) }}" class="px-3 py-1 bg-blue-100 text-blue-700 text-xs font-semibold rounded hover:bg-blue-200 transition-colors">
                                                    Edit
                                                </a>
                                                <button onclick="openPasswordModal({{ $user->id }}, '{{ $user->name }}')" class="px-3 py-1 bg-purple-100 text-purple-700 text-xs font-semibold rounded hover:bg-purple-200 transition-colors">
                                                    Password
                                                </button>
                                                @if($user->id !== auth()->id())
                                                    <form method="POST" action="{{ route('admin.users.destroy', $user) }}" 
                                                          onsubmit="return confirm('Are you sure you want to trash this user?');" 
                                                          class="inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="px-3 py-1 bg-red-100 text-red-700 text-xs font-semibold rounded hover:bg-red-200 transition-colors">
                                                            Trash
                                                        </button>
                                                    </form>
                                                @endif
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center">
                                        <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                        </svg>
                                        <p class="text-gray-600 font-medium">No users found</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($users->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                        {{ $users->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Password Change Modal -->
    <div id="passwordModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-2xl max-w-md w-full p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-gray-900">Change Password</h3>
                <button onclick="closePasswordModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            
            <div id="passwordError" class="hidden mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded"></div>
            <div id="passwordSuccess" class="hidden mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded"></div>

            <form id="passwordForm" onsubmit="changePassword(event)">
                <input type="hidden" id="userId" value="">
                <p class="text-sm text-gray-600 mb-4">Changing password for: <span id="userName" class="font-semibold text-gray-900"></span></p>
                
                <div class="space-y-4">
                    <div>
                        <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">New Password</label>
                        <input type="password" id="password" name="password" required minlength="8"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200">
                        <p class="text-xs text-gray-500 mt-1">Minimum 8 characters</p>
                    </div>
                    
                    <div>
                        <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-2">Confirm Password</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" required minlength="8"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200">
                    </div>
                </div>

                <div class="mt-6 flex gap-3">
                    <button type="button" onclick="closePasswordModal()" class="flex-1 px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold rounded-lg transition-colors">
                        Cancel
                    </button>
                    <button type="submit" class="flex-1 px-4 py-2 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-semibold rounded-lg shadow-lg transition-all">
                        Change Password
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openPasswordModal(userId, userName) {
            document.getElementById('userId').value = userId;
            document.getElementById('userName').textContent = userName;
            document.getElementById('passwordModal').classList.remove('hidden');
            document.getElementById('passwordError').classList.add('hidden');
            document.getElementById('passwordSuccess').classList.add('hidden');
            document.getElementById('passwordForm').reset();
        }

        function closePasswordModal() {
            document.getElementById('passwordModal').classList.add('hidden');
        }

        async function changePassword(event) {
            event.preventDefault();
            
            const userId = document.getElementById('userId').value;
            const password = document.getElementById('password').value;
            const passwordConfirmation = document.getElementById('password_confirmation').value;
            
            const errorDiv = document.getElementById('passwordError');
            const successDiv = document.getElementById('passwordSuccess');
            
            errorDiv.classList.add('hidden');
            successDiv.classList.add('hidden');

            try {
                const response = await fetch(`/admin/users/${userId}/change-password`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        password: password,
                        password_confirmation: passwordConfirmation
                    })
                });

                const data = await response.json();

                if (response.ok) {
                    successDiv.textContent = data.message;
                    successDiv.classList.remove('hidden');
                    setTimeout(() => {
                        closePasswordModal();
                    }, 2000);
                } else {
                    errorDiv.textContent = data.message || 'Failed to change password';
                    errorDiv.classList.remove('hidden');
                }
            } catch (error) {
                errorDiv.textContent = 'An error occurred. Please try again.';
                errorDiv.classList.remove('hidden');
            }
        }

        // Close modal on escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closePasswordModal();
            }
        });
    </script>
</x-app-layout>
