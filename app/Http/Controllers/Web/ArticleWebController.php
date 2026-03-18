<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\ArticleAttribute;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ArticleWebController extends Controller
{
    public function index(Request $request): View
    {
        $user = auth()->user();
        
        // Filter by status
        $statusFilter = $request->input('status', 'all');

        // Check user role - ONLY admin role can see all articles
        if ($user->role === 'admin') {
            // Admins see all articles
            $query = Article::with(['creator'])
                ->whereNull('deleted_at');
            
            if ($statusFilter !== 'all') {
                $query->where('status', $statusFilter);
            }
        } else {
            // Everyone else (including moderators) sees ONLY their own articles
            $query = Article::with(['creator'])
                ->whereNull('deleted_at')
                ->where('created_by', $user->id);
            
            if ($statusFilter !== 'all') {
                $query->where('status', $statusFilter);
            }
        }

        // Apply semantic search if search term provided
        if ($request->has('search') && !empty($request->input('search'))) {
            $searchTerm = $request->input('search');
            
            // Use full-text search for better semantic matching
            $query->search($searchTerm);
        } else {
            // Default ordering when no search
            $query->latest();
        }

        $articles = $query->paginate(20);

        return view('articles.index', compact('articles', 'statusFilter'));
    }

    public function create(): View
    {
        return view('articles.create');
    }

    public function searchOrCreate(Request $request)
    {
        $request->validate([
            'article_name' => 'required|string|max:255',
        ]);

        $articleName = $request->input('article_name');
        $slug = Str::slug($articleName);

        $article = Article::where('slug', $slug)
            ->orWhere('title', 'like', $articleName)
            ->first();

        if ($article) {
            if (auth()->check()) {
                return redirect()->route('articles.edit', $article->slug);
            }

            return redirect()->route('articles.show', $article->slug);
        }

        if (! auth()->check()) {
            return redirect()->route('login')
                ->with('info', 'Please login to create new articles.');
        }

        return view('articles.create', ['articleName' => $articleName]);
    }

    public function show(string $slug): View
    {
        $article = Article::with(['creator', 'attributes', 'reviewer'])
            ->where('slug', $slug)
            ->firstOrFail();

        return view('articles.show-wikipedia', compact('article'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'summary' => 'nullable|string|max:500',
            'status' => 'required|in:draft,pending',
            'info' => 'nullable|array',
            'info.*.key' => 'nullable|string|max:255',
            'info.*.value' => 'nullable|string|max:500',
            'infobox_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'references' => 'nullable|array',
            'references.*.title' => 'nullable|string|max:500',
            'references.*.url' => 'nullable|url|max:1000',
        ]);

        $validated['created_by'] = auth()->id();

        // Set submission timestamp if submitting for review
        if ($validated['status'] === Article::STATUS_PENDING) {
            $validated['submitted_at'] = now();
        }

        if ($request->hasFile('infobox_image')) {
            $path = $request->file('infobox_image')->store('infobox_images', 'public');
            $validated['infobox_image'] = $path;
        }

        $infoData = null;
        if (isset($validated['info'])) {
            $infoData = array_filter($validated['info'], function ($item) {
                return ! empty($item['key']) || ! empty($item['value']);
            });
            unset($validated['info']);
        }

        $referencesData = null;
        if (isset($validated['references'])) {
            $referencesData = array_filter($validated['references'], function ($item) {
                return ! empty($item['title']) || ! empty($item['url']);
            });
            // JSON encode the references or set to null if empty
            $validated['references'] = !empty($referencesData) ? json_encode(array_values($referencesData)) : null;
        }

        $article = Article::create($validated);

        if ($infoData) {
            foreach ($infoData as $item) {
                ArticleAttribute::create([
                    'article_id' => $article->id,
                    'key' => $item['key'],
                    'value' => $item['value'],
                ]);
            }
        }

        return redirect()->route('articles.show', $article->slug)
            ->with('success', 'Article created successfully!');
    }

    public function edit(string $slug): View
    {
        $article = Article::with('attributes')->where('slug', $slug)->firstOrFail();

        $user = auth()->user();
        
        // Admin and moderator can edit any article, regular users can only edit their own
        if (!in_array($user->role, ['admin', 'moderator']) && $user->id !== $article->created_by) {
            abort(403, 'You can only edit your own articles.');
        }

        return view('articles.edit', compact('article'));
    }

    public function update(Request $request, string $slug): RedirectResponse
    {
        $article = Article::where('slug', $slug)->firstOrFail();

        $user = auth()->user();
        
        // Admin and moderator can edit any article, regular users can only edit their own
        if (!in_array($user->role, ['admin', 'moderator']) && $user->id !== $article->created_by) {
            abort(403, 'You can only edit your own articles.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'summary' => 'required|string|max:500',
            'status' => 'required|in:draft,pending,published,rejected',
            'info' => 'nullable|array',
            'info.*.key' => 'nullable|string|max:255',
            'info.*.value' => 'nullable|string|max:500',
            'infobox_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'remove_image' => 'nullable|boolean',
            'references' => 'nullable|array',
            'references.*.title' => 'nullable|string|max:500',
            'references.*.url' => 'nullable|url|max:1000',
        ]);

        // Handle status change
        $oldStatus = $article->status;
        $newStatus = $validated['status'];

        // If changing from draft/rejected to pending, set submitted_at
        if (in_array($oldStatus, ['draft', 'rejected']) && $newStatus === 'pending') {
            $validated['submitted_at'] = now();
            $validated['rejection_reason'] = null;
        }

        if ($request->has('remove_image') && $article->infobox_image) {
            Storage::disk('public')->delete($article->infobox_image);
            $validated['infobox_image'] = null;
        }

        if ($request->hasFile('infobox_image')) {
            if ($article->infobox_image) {
                Storage::disk('public')->delete($article->infobox_image);
            }
            $path = $request->file('infobox_image')->store('infobox_images', 'public');
            $validated['infobox_image'] = $path;
        }

        $infoData = null;
        if (isset($validated['info'])) {
            $infoData = array_filter($validated['info'], function ($item) {
                return ! empty($item['key']) || ! empty($item['value']);
            });
            unset($validated['info']);
            unset($validated['remove_image']);
        }

        $referencesData = null;
        if (isset($validated['references'])) {
            $referencesData = array_filter($validated['references'], function ($item) {
                return ! empty($item['title']) || ! empty($item['url']);
            });
            // JSON encode the references or set to null if empty
            $validated['references'] = !empty($referencesData) ? json_encode(array_values($referencesData)) : null;
        }

        $article->update($validated);

        $article->attributes()->delete();
        if ($infoData) {
            foreach ($infoData as $item) {
                ArticleAttribute::create([
                    'article_id' => $article->id,
                    'key' => $item['key'],
                    'value' => $item['value'],
                ]);
            }
        }

        return redirect()->route('articles.show', $article->slug)
            ->with('success', 'Article updated successfully!');
    }

    public function destroy(string $slug): RedirectResponse
    {
        $article = Article::where('slug', $slug)->firstOrFail();

        if (auth()->user()->id !== $article->created_by) {
            abort(403, 'You can only delete your own articles.');
        }

        $article->deleted_by = auth()->id();
        $article->save();
        $article->delete();

        return redirect()->route('articles.index')
            ->with('success', 'Article deleted successfully!');
    }

    public function random(): RedirectResponse
    {
        $article = Article::inRandomOrder()->first();

        if (! $article) {
            return redirect()->route('articles.index')
                ->with('info', 'No articles available yet.');
        }

        return redirect()->route('articles.show', $article->slug);
    }

    public function submitForReview(string $slug): RedirectResponse
    {
        $article = Article::where('slug', $slug)->firstOrFail();

        if (! $article->canBeSubmittedBy(auth()->id())) {
            abort(403, 'You cannot submit this article for review.');
        }

        $article->update([
            'status' => Article::STATUS_PENDING,
            'submitted_at' => now(),
            'rejection_reason' => null,
        ]);

        return redirect()->route('articles.show', $article->slug)
            ->with('success', 'Article submitted for review successfully!');
    }

    public function approve(string $slug): RedirectResponse
    {
        $article = Article::where('slug', $slug)->firstOrFail();

        if (! $article->canBeReviewedBy(auth()->user()->role)) {
            abort(403, 'You do not have permission to review articles.');
        }

        $article->update([
            'status' => Article::STATUS_PUBLISHED,
            'published_at' => now(),
            'reviewed_by' => auth()->id(),
            'rejection_reason' => null,
        ]);

        return redirect()->route('articles.show', $article->slug)
            ->with('success', 'Article published successfully!');
    }

    public function reject(Request $request, string $slug): RedirectResponse
    {
        $article = Article::where('slug', $slug)->firstOrFail();

        if (! $article->canBeReviewedBy(auth()->user()->role)) {
            abort(403, 'You do not have permission to review articles.');
        }

        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ]);

        $article->update([
            'status' => Article::STATUS_REJECTED,
            'reviewed_by' => auth()->id(),
            'rejection_reason' => $validated['rejection_reason'],
            'published_at' => null,
        ]);

        return redirect()->route('articles.show', $article->slug)
            ->with('success', 'Article rejected. Author has been notified.');
    }

    public function uploadImage(Request $request): JsonResponse
    {
        $request->validate([
            'upload' => 'required|image|max:5120',
        ]);

        if ($request->hasFile('upload')) {
            $image = $request->file('upload');
            $filename = time().'_'.$image->getClientOriginalName();
            $path = $image->storeAs('article_images', $filename, 'public');

            return response()->json([
                'url' => Storage::url($path),
            ]);
        }

        return response()->json(['error' => 'No image uploaded'], 400);
    }
}
