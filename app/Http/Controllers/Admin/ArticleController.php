<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ArticleController extends Controller
{
    public function index(Request $request): View
    {
        if (!in_array(auth()->user()->role, ['admin', 'moderator'])) {
            abort(403, 'Unauthorized access.');
        }

        $query = Article::with(['creator', 'attributes']);

        // Filter by status
        if ($request->has('status') && $request->get('status') !== 'all') {
            $query->where('status', $request->get('status'));
        }

        // Filter by trashed
        if ($request->get('trashed') === 'true') {
            $query->onlyTrashed();
        }

        // Search with semantic full-text search
        if ($request->has('search') && !empty($request->get('search'))) {
            $searchTerm = $request->get('search');
            $query->search($searchTerm);
        } else {
            // Default ordering when no search
            $query->orderBy('created_at', 'desc');
        }

        $articles = $query->paginate(20);

        // Statistics
        $totalArticles = Article::count();
        $draftArticles = Article::where('status', 'draft')->count();
        $pendingArticles = Article::where('status', 'pending')->count();
        $publishedArticles = Article::where('status', 'published')->count();
        $rejectedArticles = Article::where('status', 'rejected')->count();
        $trashedArticles = Article::onlyTrashed()->count();

        return view('admin.articles.index', compact(
            'articles',
            'totalArticles',
            'draftArticles',
            'pendingArticles',
            'publishedArticles',
            'rejectedArticles',
            'trashedArticles'
        ));
    }

    public function approve(string $slug): RedirectResponse
    {
        if (!in_array(auth()->user()->role, ['admin', 'moderator'])) {
            abort(403, 'Unauthorized access.');
        }

        $article = Article::where('slug', $slug)->firstOrFail();

        $article->update([
            'status' => Article::STATUS_PUBLISHED,
            'published_at' => now(),
            'reviewed_by' => auth()->id(),
            'rejection_reason' => null,
        ]);

        return redirect()->back()->with('success', 'Article approved and published successfully!');
    }

    public function reject(Request $request, string $slug): RedirectResponse
    {
        if (!in_array(auth()->user()->role, ['admin', 'moderator'])) {
            abort(403, 'Unauthorized access.');
        }

        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ]);

        $article = Article::where('slug', $slug)->firstOrFail();

        $article->update([
            'status' => Article::STATUS_REJECTED,
            'reviewed_by' => auth()->id(),
            'rejection_reason' => $validated['rejection_reason'],
        ]);

        return redirect()->back()->with('success', 'Article rejected successfully!');
    }

    public function destroy(string $slug): RedirectResponse
    {
        if (!in_array(auth()->user()->role, ['admin', 'moderator'])) {
            abort(403, 'Unauthorized access.');
        }

        $article = Article::where('slug', $slug)->firstOrFail();
        $article->delete();

        return redirect()->back()->with('success', 'Article moved to trash!');
    }

    public function restore($id): RedirectResponse
    {
        if (!in_array(auth()->user()->role, ['admin', 'moderator'])) {
            abort(403, 'Unauthorized access.');
        }

        $article = Article::onlyTrashed()->findOrFail($id);
        $article->restore();

        return redirect()->back()->with('success', 'Article restored successfully!');
    }

    public function forceDelete($id): RedirectResponse
    {
        if (!in_array(auth()->user()->role, ['admin', 'moderator'])) {
            abort(403, 'Unauthorized access.');
        }

        $article = Article::onlyTrashed()->findOrFail($id);
        $article->forceDelete();

        return redirect()->back()->with('success', 'Article permanently deleted!');
    }
}
