<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-bold text-2xl text-gray-900 leading-tight">
                    Edit User
                </h2>
                <p class="text-sm text-gray-600 mt-1">Update user information and role</p>
            </div>
            <a href="{{ route('admin.users.index') }}" class="inline-flex items-center gap-2 bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold py-2 px-4 rounded-lg transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Users
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            
            @if($errors->any())
                <div class="mb-6 bg-gradient-to-r from-red-50 to-pink-50 border-l-4 border-red-500 text-red-800 px-6 py-4 rounded-r-lg shadow-sm">
                    <div class="flex items-start gap-3">
                        <svg class="w-6 h-6 text-red-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div>
                            <p class="font-semibold mb-2">Please fix the following errors:</p>
                            <ul class="list-disc list-inside space-y-1">
                                @foreach($errors->all() as $error)
                                    <li class="text-sm">{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
                <div class="bg-gradient-to-r from-purple-500 to-pink-500 px-6 py-4">
                    <h3 class="text-lg font-bold text-white">Edit User: {{ $user->name }}</h3>
                </div>

                <form method="POST" action="{{ route('admin.users.update', $user) }}" class="p-6 space-y-6">
                    @csrf
                    @method('PUT')

                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">
                            Full Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all"
                            placeholder="John Doe">
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                            Email Address <span class="text-red-500">*</span>
                        </label>
                        <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all"
                            placeholder="john@example.com">
                    </div>

                    <!-- Role -->
                    <div>
                        <label for="role" class="block text-sm font-semibold text-gray-700 mb-2">
                            User Role <span class="text-red-500">*</span>
                        </label>
                        <select name="role" id="role" required
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all">
                            <option value="user" {{ old('role', $user->role) === 'user' ? 'selected' : '' }}>User (Regular)</option>
                            <option value="moderator" {{ old('role', $user->role) === 'moderator' ? 'selected' : '' }}>Moderator</option>
                            <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Administrator</option>
                        </select>
                        <p class="text-xs text-gray-500 mt-1">
                            Admins have full access, moderators can manage content, users can create articles.
                        </p>
                    </div>

                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-blue-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <div class="text-sm text-blue-800">
                                <p class="font-semibold mb-1">Password Note</p>
                                <p>To change the user's password, use the "Password" button on the users list page. This form only updates basic information.</p>
                            </div>
                        </div>
                    </div>

                    <!-- User Stats -->
                    <div class="grid grid-cols-2 gap-4 pt-4 border-t border-gray-200">
                        <div class="bg-gray-50 rounded-lg p-4">
                            <p class="text-xs text-gray-600 mb-1">Member Since</p>
                            <p class="text-lg font-bold text-gray-900">{{ $user->created_at->format('M d, Y') }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <p class="text-xs text-gray-600 mb-1">Last Updated</p>
                            <p class="text-lg font-bold text-gray-900">{{ $user->updated_at->format('M d, Y') }}</p>
                        </div>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex gap-3 pt-4">
                        <a href="{{ route('admin.users.index') }}" class="flex-1 text-center px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold rounded-lg transition-colors">
                            Cancel
                        </a>
                        <button type="submit" class="flex-1 px-6 py-3 bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white font-semibold rounded-lg shadow-lg hover:shadow-xl transition-all">
                            Update User
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
