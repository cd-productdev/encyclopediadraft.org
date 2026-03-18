<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-bold text-2xl text-gray-900 leading-tight">
                    Article Management
                </h2>
                <p class="text-sm text-gray-600 mt-1">Manage all articles on the platform</p>
            </div>
            <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-2 bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold py-2 px-4 rounded-lg transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Dashboard
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

            <!-- Statistics Cards -->
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-6">
                <div class="bg-white rounded-lg shadow-md border border-gray-200 p-4">
                    <p class="text-xs text-gray-600 font-semibold uppercase">Total</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $totalArticles }}</p>
                </div>
                <div class="bg-gradient-to-br from-gray-500 to-gray-600 rounded-lg shadow-md p-4 text-white">
                    <p class="text-xs font-semibold uppercase opacity-90">Drafts</p>
                    <p class="text-2xl font-bold mt-1">{{ $draftArticles }}</p>
                </div>
                <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-lg shadow-md p-4 text-white">
                    <p class="text-xs font-semibold uppercase opacity-90">Pending</p>
                    <p class="text-2xl font-bold mt-1">{{ $pendingArticles }}</p>
                </div>
                <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg shadow-md p-4 text-white">
                    <p class="text-xs font-semibold uppercase opacity-90">Published</p>
                    <p class="text-2xl font-bold mt-1">{{ $publishedArticles }}</p>
                </div>
                <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-lg shadow-md p-4 text-white">
                    <p class="text-xs font-semibold uppercase opacity-90">Rejected</p>
                    <p class="text-2xl font-bold mt-1">{{ $rejectedArticles }}</p>
                </div>
                <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg shadow-md p-4 text-white">
                    <p class="text-xs font-semibold uppercase opacity-90">Trashed</p>
                    <p class="text-2xl font-bold mt-1">{{ $trashedArticles }}</p>
                </div>
            </div>

            <!-- Filter Section -->
            <div class="bg-white rounded-xl shadow-md mb-6 border border-gray-100">
                <div class="p-6">
                    <form method="GET" action="{{ route('admin.articles.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label for="status" class="block text-sm font-semibold text-gray-700 mb-2">Status</label>
                            <select name="status" id="status" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200">
                                <option value="all" {{ request('status') === 'all' ? 'selected' : '' }}>All</option>
                                <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Drafts</option>
                                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Published</option>
                                <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <label for="search" class="block text-sm font-semibold text-gray-700 mb-2">Search</label>
                            <input type="text" name="search" id="search" value="{{ request('search') }}" 
                                placeholder="Search articles..." 
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200">
                        </div>
                        <div class="flex items-end gap-2">
                            <button type="submit" class="flex-1 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-semibold py-2 px-6 rounded-lg shadow-md transition-all">
                                Apply
                            </button>
                            @if(request('trashed') === 'true')
                                <a href="{{ route('admin.articles.index') }}" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold rounded-lg transition-colors">
                                    Show Active
                                </a>
                            @else
                                <a href="{{ route('admin.articles.index', ['trashed' => 'true']) }}" class="px-4 py-2 bg-red-100 hover:bg-red-200 text-red-700 font-semibold rounded-lg transition-colors">
                                    Trashed
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>

            <!-- Articles Grid -->
            @if($articles->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                    @foreach($articles as $article)
                        <div class="group bg-white rounded-xl shadow-md hover:shadow-2xl border border-gray-100 overflow-hidden transition-all duration-300">
                            <!-- Article Header -->
                            <div class="h-32 bg-gradient-to-br from-blue-500 via-purple-500 to-pink-500 relative overflow-hidden">
                                @if($article->infobox_image)
                                    <img src="{{ Storage::url($article->infobox_image) }}" alt="{{ $article->title }}" class="w-full h-full object-cover opacity-90">
                                @else
                                    <div class="absolute inset-0 flex items-center justify-center">
                                        <svg class="w-12 h-12 text-white opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                    </div>
                                @endif
                                
                                <!-- Status Badge -->
                                <div class="absolute top-2 right-2">
                                    @if($article->status === 'draft')
                                        <span class="px-2 py-1 text-xs font-bold rounded-full bg-gray-800 text-white">Draft</span>
                                    @elseif($article->status === 'pending')
                                        <span class="px-2 py-1 text-xs font-bold rounded-full bg-yellow-500 text-white">Pending</span>
                                    @elseif($article->status === 'published')
                                        <span class="px-2 py-1 text-xs font-bold rounded-full bg-green-500 text-white">Published</span>
                                    @elseif($article->status === 'rejected')
                                        <span class="px-2 py-1 text-xs font-bold rounded-full bg-red-500 text-white">Rejected</span>
                                    @endif
                                </div>

                                @if($article->trashed())
                                    <div class="absolute top-2 left-2">
                                        <span class="px-2 py-1 text-xs font-bold rounded-full bg-black bg-opacity-70 text-white">Trashed</span>
                                    </div>
                                @endif
                            </div>

                            <!-- Article Content -->
                            <div class="p-4">
                                <h3 class="text-base font-bold text-gray-900 mb-2 line-clamp-2">
                                    {{ $article->title }}
                                </h3>
                                
                                @if($article->summary)
                                    <p class="text-xs text-gray-600 mb-3 line-clamp-2">{{ $article->summary }}</p>
                                @endif

                                <!-- Meta Information -->
                                <div class="flex items-center gap-3 text-xs text-gray-500 mb-3 pb-3 border-b border-gray-100">
                                    <div class="flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                        <span class="font-medium">{{ $article->creator->name ?? 'Unknown' }}</span>
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        <span>{{ $article->created_at->format('M d') }}</span>
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                @if($article->trashed())
                                    <div class="flex gap-2">
                                        <form method="POST" action="{{ route('admin.articles.restore', $article->id) }}" class="flex-1">
                                            @csrf
                                            <button type="submit" class="w-full px-3 py-1.5 bg-green-50 text-green-700 text-xs font-semibold rounded hover:bg-green-100 transition-colors">
                                                Restore
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.articles.forceDelete', $article->id) }}" 
                                              onsubmit="return confirm('Permanently delete? This cannot be undone!');" 
                                              class="flex-1">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="w-full px-3 py-1.5 bg-red-50 text-red-700 text-xs font-semibold rounded hover:bg-red-100 transition-colors">
                                                Delete Forever
                                            </button>
                                        </form>
                                    </div>
                                @else
                                    <div class="flex flex-wrap gap-2">
                                        <a href="{{ route('articles.show', $article->slug) }}" class="flex-1 text-center px-2 py-1.5 bg-blue-50 text-blue-700 text-xs font-semibold rounded hover:bg-blue-100 transition-colors">
                                            View
                                        </a>
                                        @if($article->status === 'pending')
                                            <form method="POST" action="{{ route('admin.articles.approve', $article->slug) }}" class="flex-1">
                                                @csrf
                                                <button type="submit" class="w-full px-2 py-1.5 bg-green-50 text-green-700 text-xs font-semibold rounded hover:bg-green-100 transition-colors">
                                                    Approve
                                                </button>
                                            </form>
                                            <button onclick="openRejectModal('{{ $article->slug }}', '{{ $article->title }}')" class="flex-1 px-2 py-1.5 bg-orange-50 text-orange-700 text-xs font-semibold rounded hover:bg-orange-100 transition-colors">
                                                Reject
                                            </button>
                                        @endif
                                        <form method="POST" action="{{ route('admin.articles.destroy', $article->slug) }}" 
                                              onsubmit="return confirm('Move to trash?');" 
                                              class="flex-1">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="w-full px-2 py-1.5 bg-red-50 text-red-700 text-xs font-semibold rounded hover:bg-red-100 transition-colors">
                                                Trash
                                            </button>
                                        </form>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="bg-white rounded-xl shadow-md border border-gray-100 px-6 py-4">
                    {{ $articles->links() }}
                </div>
            @else
                <div class="bg-white rounded-xl shadow-md border border-gray-100 p-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <p class="text-gray-600 font-medium">No articles found</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Reject Modal -->
    <div id="rejectModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-2xl max-w-lg w-full p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-gray-900">Reject Article</h3>
                <button onclick="closeRejectModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            
            <form id="rejectForm" method="POST">
                @csrf
                <p class="text-sm text-gray-600 mb-4">Article: <span id="rejectArticleTitle" class="font-semibold text-gray-900"></span></p>
                
                <div class="mb-6">
                    <label for="rejection_reason" class="block text-sm font-semibold text-gray-700 mb-2">Rejection Reason *</label>
                    <textarea id="rejection_reason" name="rejection_reason" required rows="4"
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-2 focus:ring-red-200"
                        placeholder="Please provide a detailed reason for rejection..."></textarea>
                </div>

                <div class="flex gap-3">
                    <button type="button" onclick="closeRejectModal()" class="flex-1 px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold rounded-lg transition-colors">
                        Cancel
                    </button>
                    <button type="submit" class="flex-1 px-4 py-2 bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white font-semibold rounded-lg shadow-lg transition-all">
                        Reject Article
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openRejectModal(slug, title) {
            document.getElementById('rejectForm').action = `/admin/articles/${slug}/reject`;
            document.getElementById('rejectArticleTitle').textContent = title;
            document.getElementById('rejectModal').classList.remove('hidden');
        }

        function closeRejectModal() {
            document.getElementById('rejectModal').classList.add('hidden');
            document.getElementById('rejection_reason').value = '';
        }

        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeRejectModal();
            }
        });
    </script>
</x-app-layout>
