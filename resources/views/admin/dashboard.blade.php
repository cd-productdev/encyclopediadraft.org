<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-bold text-2xl text-gray-900 leading-tight">
                    Admin Dashboard
                </h2>
                <p class="text-sm text-gray-600 mt-1">Welcome back, {{ auth()->user()->name }}! Here's what's happening today.</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('admin.articles.index') }}" class="inline-flex items-center gap-2 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white font-semibold py-2 px-4 rounded-lg shadow-lg hover:shadow-xl transition-all duration-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Manage Articles
                </a>
                <a href="{{ route('admin.users.index') }}" class="inline-flex items-center gap-2 bg-gradient-to-r from-purple-600 to-purple-700 hover:from-purple-700 hover:to-purple-800 text-white font-semibold py-2 px-4 rounded-lg shadow-lg hover:shadow-xl transition-all duration-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    Manage Users
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Total Users Card -->
                <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition-all duration-200">
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
                    <div class="mt-4 flex items-center gap-2 text-sm">
                        <span class="bg-blue-400 bg-opacity-30 px-2 py-1 rounded">{{ $adminUsers }} Admins</span>
                        <span class="bg-blue-400 bg-opacity-30 px-2 py-1 rounded">{{ $moderatorUsers }} Moderators</span>
                        <span class="bg-blue-400 bg-opacity-30 px-2 py-1 rounded">{{ $regularUsers }} Users</span>
                    </div>
                </div>

                <!-- Total Articles Card -->
                <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition-all duration-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-purple-100 text-sm font-semibold uppercase tracking-wide">Total Articles</p>
                            <p class="text-4xl font-bold mt-2">{{ number_format($totalArticles) }}</p>
                        </div>
                        <div class="bg-purple-400 bg-opacity-30 rounded-full p-4">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4 text-sm text-purple-100">
                        Published encyclopedia entries
                    </div>
                </div>

                <!-- Pending Reviews Card -->
                <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition-all duration-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-yellow-100 text-sm font-semibold uppercase tracking-wide">Pending Review</p>
                            <p class="text-4xl font-bold mt-2">{{ number_format($pendingArticles) }}</p>
                        </div>
                        <div class="bg-yellow-400 bg-opacity-30 rounded-full p-4">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4 text-sm text-yellow-100">
                        Awaiting moderation
                    </div>
                </div>

                <!-- Published Articles Card -->
                <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition-all duration-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-green-100 text-sm font-semibold uppercase tracking-wide">Published</p>
                            <p class="text-4xl font-bold mt-2">{{ number_format($publishedArticles) }}</p>
                        </div>
                        <div class="bg-green-400 bg-opacity-30 rounded-full p-4">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4 text-sm text-green-100">
                        Live articles on platform
                    </div>
                </div>
            </div>

            <!-- Article Status Breakdown -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Article Status -->
                <div class="bg-white rounded-xl shadow-md border border-gray-100 p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        Article Status
                    </h3>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <span class="text-sm font-medium text-gray-700">Drafts</span>
                            <span class="text-lg font-bold text-gray-900">{{ $draftArticles }}</span>
                        </div>
                        <div class="flex items-center justify-between p-3 bg-yellow-50 rounded-lg">
                            <span class="text-sm font-medium text-yellow-700">Pending</span>
                            <span class="text-lg font-bold text-yellow-900">{{ $pendingArticles }}</span>
                        </div>
                        <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                            <span class="text-sm font-medium text-green-700">Published</span>
                            <span class="text-lg font-bold text-green-900">{{ $publishedArticles }}</span>
                        </div>
                        <div class="flex items-center justify-between p-3 bg-red-50 rounded-lg">
                            <span class="text-sm font-medium text-red-700">Rejected</span>
                            <span class="text-lg font-bold text-red-900">{{ $rejectedArticles }}</span>
                        </div>
                    </div>
                </div>

                <!-- Top Contributors -->
                <div class="bg-white rounded-xl shadow-md border border-gray-100 p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                        </svg>
                        Top Contributors
                    </h3>
                    <div class="space-y-2">
                        @forelse($topContributors as $contributor)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-purple-500 rounded-full flex items-center justify-center text-white font-bold text-sm">
                                        {{ substr($contributor->name, 0, 1) }}
                                    </div>
                                    <span class="text-sm font-medium text-gray-900">{{ $contributor->name }}</span>
                                </div>
                                <span class="text-sm font-bold text-blue-600">{{ $contributor->created_articles_count }} articles</span>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500 text-center py-4">No contributors yet</p>
                        @endforelse
                    </div>
                </div>

                <!-- Recent Users -->
                <div class="bg-white rounded-xl shadow-md border border-gray-100 p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                        </svg>
                        Recent Users
                    </h3>
                    <div class="space-y-2">
                        @forelse($recentUsers as $user)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $user->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $user->email }}</p>
                                </div>
                                <span class="text-xs px-2 py-1 rounded-full {{ $user->role === 'admin' ? 'bg-red-100 text-red-700' : ($user->role === 'moderator' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-700') }}">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500 text-center py-4">No users yet</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Pending Reviews -->
            @if($pendingReviews->count() > 0)
            <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
                <div class="bg-gradient-to-r from-yellow-500 to-orange-500 px-6 py-4">
                    <h3 class="text-lg font-bold text-white flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Pending Article Reviews ({{ $pendingReviews->count() }})
                    </h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        @foreach($pendingReviews as $article)
                            <div class="border border-gray-200 rounded-lg p-4 hover:border-blue-300 hover:shadow-md transition-all">
                                <div class="flex items-start justify-between gap-4">
                                    <div class="flex-1">
                                        <h4 class="font-bold text-gray-900 mb-1">{{ $article->title }}</h4>
                                        <div class="flex items-center gap-4 text-sm text-gray-600">
                                            <span class="flex items-center gap-1">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                                </svg>
                                                {{ $article->creator->name ?? 'Unknown' }}
                                            </span>
                                            <span class="flex items-center gap-1">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                </svg>
                                                Submitted {{ $article->submitted_at ? $article->submitted_at->diffForHumans() : 'N/A' }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex gap-2">
                                        <a href="{{ route('articles.show', $article->slug) }}" class="px-4 py-2 bg-blue-50 text-blue-700 text-sm font-semibold rounded-lg hover:bg-blue-100 transition-colors">
                                            Review
                                        </a>
                                        <form method="POST" action="{{ route('articles.approve', $article->slug) }}" class="inline">
                                            @csrf
                                            <button type="submit" class="px-4 py-2 bg-green-50 text-green-700 text-sm font-semibold rounded-lg hover:bg-green-100 transition-colors">
                                                Approve
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <!-- Recent Articles -->
            <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
                <div class="bg-gradient-to-r from-blue-500 to-purple-500 px-6 py-4">
                    <h3 class="text-lg font-bold text-white flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Recent Articles
                    </h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Author</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($recentArticles as $article)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4">
                                        <a href="{{ route('articles.show', $article->slug) }}" class="text-blue-600 hover:text-blue-800 font-medium">
                                            {{ Str::limit($article->title, 50) }}
                                        </a>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900">
                                        {{ $article->creator->name ?? 'Unknown' }}
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($article->status === 'draft')
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-200 text-gray-800">Draft</span>
                                        @elseif($article->status === 'pending')
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-200 text-yellow-800">Pending</span>
                                        @elseif($article->status === 'published')
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-200 text-green-800">Published</span>
                                        @elseif($article->status === 'rejected')
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-200 text-red-800">Rejected</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500">
                                        {{ $article->created_at->format('M d, Y') }}
                                    </td>
                                    <td class="px-6 py-4 text-right text-sm font-medium">
                                        <a href="{{ route('articles.show', $article->slug) }}" class="text-blue-600 hover:text-blue-900">View</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-8 text-center text-gray-500">No articles yet</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
