<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\ArticleAttribute;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ArticleWebController extends Controller
{
    public function index(Request $request): View
    {
        $user = auth()->user();

        // Filter by status
        $statusFilter = $request->input('status', 'all');

        // Check user role - Admin and moderator can see all articles
        if (in_array($user->role, ['admin', 'moderator'])) {
            // Admins and moderators see all articles
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
        if ($request->has('search') && ! empty($request->input('search'))) {
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

        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'content' => 'required|string',
                'summary' => 'nullable|string|max:500',
                'status' => 'required|in:draft,pending,rejected',
                'rejection_reason' => 'nullable|array',
                'rejection_reason.*' => 'nullable|string|max:500',
                'rejection_active' => 'nullable|array',
                'rejection_active.*' => 'nullable|in:1',
                'draft_reason_preset' => ['nullable', 'string', Rule::in(array_keys(config('article.draft_reason_presets', [])))],
                'show_lock_icon' => 'nullable|boolean',
                'info' => 'nullable|array',
                'info.*.key' => 'nullable|string|max:255',
                'info.*.value' => 'nullable|string|max:500',
                'infobox_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'references' => 'nullable|array',
                'references.*.title' => 'nullable|string|max:500',
                'references.*.url' => 'nullable|url|max:1000',
            ]);

            $validated['created_by'] = auth()->id();

            unset($validated['rejection_active']);

            if ($validated['status'] === 'pending') {
                $validated['submitted_at'] = now();
                $validated['rejection_reason'] = null;
            } else {
                $validated['rejection_reason'] = $this->mergeRejectionReasonsFromRequest($request);
            }

            // Draft reason
            if ($validated['status'] === 'draft') {
                $validated['draft_reason'] = $this->resolveDraftReasonFromPreset(
                    $validated['status'],
                    $validated['draft_reason_preset'] ?? null
                );
            }
            unset($validated['draft_reason_preset']);

            // File upload
            if ($request->hasFile('infobox_image') && $request->file('infobox_image')->isValid()) {
                try {
                    $validated['infobox_image'] = $request->file('infobox_image')
                        ->store('infobox_images', 'public');
                } catch (\Exception $e) {
                    Log::error('Image upload failed', ['error' => $e->getMessage()]);

                    return back()
                        ->withInput()
                        ->withErrors(['infobox_image' => 'Image upload failed. Please try again.']);
                }
            }

            // Info data
            $infoData = null;
            if (isset($validated['info'])) {
                $infoData = array_filter($validated['info'], function ($item) {
                    return ! empty($item['key']) && ! empty($item['value']);
                });
                unset($validated['info']);
            }

            // References
            if (isset($validated['references'])) {
                $referencesData = array_filter($validated['references'], function ($item) {
                    return ! empty($item['title']) && ! empty($item['url']);
                });

                $validated['references'] = ! empty($referencesData)
                    ? array_values($referencesData)
                    : null;
            }

            DB::beginTransaction();

            $article = Article::create($validated);

            // Insert attributes
            if ($infoData) {
                $attributes = [];

                foreach ($infoData as $item) {
                    $attributes[] = [
                        'article_id' => $article->id,
                        'key' => $item['key'],
                        'value' => $item['value'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }

                ArticleAttribute::insert($attributes);
            }

            DB::commit();

            return redirect()
                ->route('articles.show', $article->slug)
                ->with('success', 'Article created successfully!');

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Laravel automatically handles this, but keeping for clarity
            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Article creation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()
                ->withInput()
                ->with('error', 'Something went wrong while creating the article.');
        }
    }

    public function edit(string $slug): View
    {
        $article = Article::with('attributes')->where('slug', $slug)->firstOrFail();

        $user = auth()->user();

        // Admin and moderator can edit any article, regular users can only edit their own
        if (! in_array($user->role, ['admin', 'moderator']) && $user->id !== $article->created_by) {
            abort(403, 'You can only edit your own articles.');
        }

        return view('articles.edit', compact('article'));
    }

    public function update(Request $request, string $slug): RedirectResponse
    {

        $article = Article::where('slug', $slug)->firstOrFail();
        $user = auth()->user();

        if (! in_array($user->role, ['admin', 'moderator']) && $user->id !== $article->created_by) {
            abort(403, 'You can only edit your own articles.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'summary' => 'required|string|max:500',
            'status' => 'required|in:draft,pending,published,rejected',
            'rejection_reason' => 'nullable|array',
            'rejection_reason.*' => 'nullable|string|max:500',
            'rejection_active' => 'nullable|array',
            'rejection_active.*' => 'nullable|in:1',
            'draft_reason_preset' => ['nullable', 'string', Rule::in(array_keys(config('article.draft_reason_presets', [])))],
            'show_lock_icon' => 'nullable|boolean',
            'info' => 'nullable|array',
            'info.*.key' => 'nullable|string|max:255',
            'info.*.value' => 'nullable|string|max:500',
            'infobox_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'remove_image' => 'nullable|boolean',
            'references' => 'nullable|array',
            'references.*.title' => 'nullable|string|max:500',
            'references.*.url' => 'nullable|url|max:1000',
        ]);

        $oldStatus = $article->status;
        $newStatus = $validated['status'];

        unset($validated['rejection_active']);

        if (in_array($oldStatus, ['draft', 'rejected']) && $newStatus === 'pending') {
            $validated['submitted_at'] = now();
            $validated['rejection_reason'] = null;
        } else {
            $validated['rejection_reason'] = $this->mergeRejectionReasonsFromRequest($request);
        }

        $validated['draft_reason'] = $this->resolveDraftReasonFromPreset(
            $validated['status'],
            $validated['draft_reason_preset'] ?? null
        );
        unset($validated['draft_reason_preset']);

        // Image logic
        if ($request->has('remove_image') && $article->infobox_image) {
            Storage::disk('public')->delete($article->infobox_image);
            $validated['infobox_image'] = null;
        }
        unset($validated['remove_image']);

        if ($request->hasFile('infobox_image')) {
            if ($article->infobox_image) {
                Storage::disk('public')->delete($article->infobox_image);
            }
            $path = $request->file('infobox_image')->store('infobox_images', 'public');
            $validated['infobox_image'] = $path;
        }

        // Info Data
        $infoData = null;
        if (isset($validated['info'])) {
            $infoData = array_filter($validated['info'], function ($item) {
                return ! empty($item['key']) || ! empty($item['value']);
            });
            unset($validated['info']);
        }

        // References
        if (isset($validated['references'])) {
            $referencesData = array_filter($validated['references'], function ($item) {
                return ! empty($item['title']) || ! empty($item['url']);
            });
            $validated['references'] = ! empty($referencesData) ? json_encode(array_values($referencesData)) : null;
        }

        $article->update($validated);

        // Sync attributes
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
            'rejection_reason' => [trim($validated['rejection_reason'])],
            'published_at' => null,
        ]);

        return redirect()->route('articles.show', $article->slug)
            ->with('success', 'Article rejected. Author has been notified.');
    }

    public function preview(Request $request): View
    {
        // Create a temporary article object from request data
        $article = new Article;
        $article->title = $request->input('title');
        $article->content = $request->input('content');
        $article->summary = $request->input('summary');
        $article->status = $request->input('status', 'draft'); // Dynamic status for preview

        // Draft reason logic
        $article->draft_reason = $this->resolveDraftReasonFromPreset(
            $article->status,
            $request->input('draft_reason_preset')
        );

        $article->rejection_reason = $this->mergeRejectionReasonsFromRequest($request) ?? [];

        $article->show_lock_icon = $request->input('show_lock_icon', false);
        $article->created_by = auth()->id();
        $article->created_at = now();

        // Handle infobox image upload for preview
        if ($request->hasFile('infobox_image')) {
            $path = $request->file('infobox_image')->store('infobox_images', 'public');
            $article->infobox_image = $path;
        } elseif ($request->input('existing_infobox_image')) {
            $article->infobox_image = $request->input('existing_infobox_image');
        }

        // Parse references
        $references = $request->input('references');
        if (is_string($references)) {
            $references = json_decode($references, true);
        }
        $article->references = $references ?: [];

        // Set relations
        $article->setRelation('creator', auth()->user());

        // Parse and set attributes (info fields)
        $infoFields = $request->input('info', []);
        $attributes = collect();

        foreach ($infoFields as $field) {
            if (! empty($field['key']) && ! empty($field['value'])) {
                $attr = new ArticleAttribute;
                $attr->key = $field['key'];
                $attr->value = $field['value'];
                $attributes->push($attr);
            }
        }

        $article->setRelation('attributes', $attributes);
        $article->setRelation('reviewer', null);

        // Return the Wikipedia view with preview badge
        return view('articles.show-wikipedia', [
            'article' => $article,
            'isPreview' => true,
        ]);
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

    /**
     * Build stored rejection reasons: only rows where rejection_active[i] is checked ("1") and text is non-empty.
     *
     * @return array<int, string>|null
     */
    protected function mergeRejectionReasonsFromRequest(Request $request): ?array
    {
        $reasons = $request->input('rejection_reason', []);
        if (! is_array($reasons)) {
            $reasons = [];
        }
        $active = $request->input('rejection_active', []);
        if (! is_array($active)) {
            $active = [];
        }

        $merged = [];
        for ($i = 0; $i < 4; $i++) {
            if (! isset($active[$i]) || (string) $active[$i] !== '1') {
                continue;
            }
            $text = isset($reasons[$i]) ? trim((string) $reasons[$i]) : '';
            if ($text !== '') {
                $merged[] = $text;
            }
        }

        return $merged === [] ? null : $merged;
    }

    protected function resolveDraftReasonFromPreset(?string $status, ?string $presetKey): ?string
    {
        if ($status !== Article::STATUS_DRAFT) {
            return null;
        }

        $map = config('article.draft_reason_presets', []);
        if ($presetKey !== null && $presetKey !== '' && isset($map[$presetKey])) {
            return $map[$presetKey];
        }

        return null;
    }
}
