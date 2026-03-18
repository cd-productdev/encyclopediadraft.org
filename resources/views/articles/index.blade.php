<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-bold text-2xl text-gray-900 leading-tight">
                    My Articles
                </h2>
                <p class="text-sm text-gray-600 mt-1">Manage and organize your encyclopedia entries</p>
            </div>
            <a href="{{ route('articles.create') }}" class="inline-flex items-center gap-2 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-semibold py-3 px-6 rounded-lg shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-105">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Create Article
            </a>
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

            <!-- Filter Section -->
            <div class="bg-white rounded-xl shadow-md mb-6 border border-gray-100">
                <div class="p-6">
                    <form method="GET" action="{{ route('articles.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label for="status" class="block text-sm font-semibold text-gray-700 mb-2">
                                <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                                </svg>
                                Status Filter
                            </label>
                            <select name="status" id="status" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all">
                                <option value="all" {{ $statusFilter === 'all' ? 'selected' : '' }}>All Articles</option>
                                <option value="draft" {{ $statusFilter === 'draft' ? 'selected' : '' }}>📝 Drafts</option>
                                <option value="pending" {{ $statusFilter === 'pending' ? 'selected' : '' }}>⏳ Pending Review</option>
                                <option value="published" {{ $statusFilter === 'published' ? 'selected' : '' }}>✅ Published</option>
                                <option value="rejected" {{ $statusFilter === 'rejected' ? 'selected' : '' }}>❌ Rejected</option>
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <label for="search" class="block text-sm font-semibold text-gray-700 mb-2">
                                <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                                Search Articles
                            </label>
                            <div class="flex gap-3">
                                <input type="text" name="search" id="search" value="{{ request('search') }}" 
                                    placeholder="Search by title, author, or content..." 
                                    class="flex-1 rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all">
                                <button type="submit" class="bg-gradient-to-r from-gray-700 to-gray-800 hover:from-gray-800 hover:to-gray-900 text-white font-semibold py-2 px-8 rounded-lg shadow-md hover:shadow-lg transition-all duration-200 transform hover:scale-105">
                                    Apply
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Articles Grid -->
            @if($articles->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                    @foreach($articles as $article)
                        <div class="group bg-white rounded-xl shadow-md hover:shadow-2xl border border-gray-100 overflow-hidden transition-all duration-300 transform hover:-translate-y-1">
                            <!-- Article Content -->
                            <div class="p-5">
                                <!-- Title with Status Badge -->
                                <div class="flex items-start justify-between gap-3 mb-3">
                                    <h3 class="text-lg font-bold text-gray-900 line-clamp-2 group-hover:text-blue-600 transition-colors flex-1">
                                        <a href="{{ route('articles.show', $article->slug) }}">
                                            {{ $article->title }}
                                        </a>
                                    </h3>
                                    
                                    <!-- Status Badge -->
                                    @if($article->status === 'draft')
                                        <span class="px-3 py-1 text-xs font-bold rounded-full bg-gray-200 text-gray-800 shrink-0">
                                            Draft
                                        </span>
                                    @elseif($article->status === 'pending')
                                        <span class="px-3 py-1 text-xs font-bold rounded-full bg-yellow-200 text-yellow-800 shrink-0">
                                            Pending
                                        </span>
                                    @elseif($article->status === 'published')
                                        <span class="px-3 py-1 text-xs font-bold rounded-full bg-green-200 text-green-800 shrink-0">
                                            Published
                                        </span>
                                    @elseif($article->status === 'rejected')
                                        <span class="px-3 py-1 text-xs font-bold rounded-full bg-red-200 text-red-800 shrink-0">
                                            Rejected
                                        </span>
                                    @endif
                                </div>
                                
                                @if($article->summary)
                                    <p class="text-sm text-gray-600 mb-3 line-clamp-2">{{ $article->summary }}</p>
                                @endif

                                <!-- Meta Information -->
                                <div class="flex items-center gap-4 text-xs text-gray-500 mb-4 pb-4 border-b border-gray-100">
                                    <div class="flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                        <span class="font-medium">{{ $article->creator->name ?? 'Unknown' }}</span>
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        <span>{{ $article->created_at->format('M d, Y') }}</span>
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="flex flex-wrap gap-2">
                                    <a href="{{ route('articles.show', $article->slug) }}" class="flex-1 text-center px-3 py-2 bg-blue-50 text-blue-700 text-xs font-semibold rounded-lg hover:bg-blue-100 transition-colors">
                                        View
                                    </a>
                                    
                                    @if(auth()->user()->id === $article->created_by || in_array(auth()->user()->role, ['admin', 'moderator']))
                                        <a href="{{ route('articles.edit', $article->slug) }}" class="flex-1 text-center px-3 py-2 bg-indigo-50 text-indigo-700 text-xs font-semibold rounded-lg hover:bg-indigo-100 transition-colors">
                                            Edit
                                        </a>
                                    @endif
                                    
                                    @if(auth()->user()->id === $article->created_by)
                                        <form method="POST" action="{{ route('articles.destroy', $article->slug) }}" 
                                              onsubmit="return confirm('Are you sure you want to delete this article?');" 
                                              class="flex-1">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="w-full px-3 py-2 bg-red-50 text-red-700 text-xs font-semibold rounded-lg hover:bg-red-100 transition-colors">
                                                Delete
                                            </button>
                                        </form>
                                    @endif

                                    @if(in_array(auth()->user()->role, ['admin', 'moderator']) && $article->status === 'pending')
                                        <form method="POST" action="{{ route('articles.approve', $article->slug) }}" class="flex-1">
                                            @csrf
                                            <button type="submit" class="w-full px-3 py-2 bg-green-50 text-green-700 text-xs font-semibold rounded-lg hover:bg-green-100 transition-colors">
                                                Approve
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="bg-white rounded-xl shadow-md border border-gray-100 px-6 py-4">
                    {{ $articles->links() }}
                </div>
            @else
                <!-- Empty State -->
                <div class="bg-white rounded-xl shadow-md border border-gray-100 p-12">
                    <div class="text-center">
                        <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-blue-100 to-purple-100 rounded-full mb-6">
                            <svg class="w-10 h-10 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">No articles found</h3>
                        <p class="text-gray-600 mb-8 max-w-md mx-auto">Start building your encyclopedia by creating your first article. Share your knowledge with the world!</p>
                        <a href="{{ route('articles.create') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-semibold rounded-lg shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-105">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Create Your First Article
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
