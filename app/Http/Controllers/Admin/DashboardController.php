<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        if (!in_array(auth()->user()->role, ['admin', 'moderator'])) {
            abort(403, 'Unauthorized access.');
        }

        // Get platform statistics
        $totalUsers = User::count();
        $totalArticles = Article::count();
        $pendingArticles = Article::where('status', 'pending')->count();
        $draftArticles = Article::where('status', 'draft')->count();
        $publishedArticles = Article::where('status', 'published')->count();
        $rejectedArticles = Article::where('status', 'rejected')->count();

        // User breakdown by role
        $adminUsers = User::where('role', 'admin')->count();
        $moderatorUsers = User::where('role', 'moderator')->count();
        $regularUsers = User::where('role', 'user')->count();

        // Recent articles
        $recentArticles = Article::with('creator')
            ->latest()
            ->take(5)
            ->get();

        // Recent users
        $recentUsers = User::latest()
            ->take(5)
            ->get();

        // Top contributors
        $topContributors = User::withCount('createdArticles')
            ->having('created_articles_count', '>', 0)
            ->orderBy('created_articles_count', 'desc')
            ->take(5)
            ->get();

        // Recent pending reviews
        $pendingReviews = Article::with('creator')
            ->where('status', 'pending')
            ->latest('submitted_at')
            ->take(10)
            ->get();

        return view('admin.dashboard', compact(
            'totalUsers',
            'totalArticles',
            'pendingArticles',
            'draftArticles',
            'publishedArticles',
            'rejectedArticles',
            'adminUsers',
            'moderatorUsers',
            'regularUsers',
            'recentArticles',
            'recentUsers',
            'topContributors',
            'pendingReviews'
        ));
    }
}
