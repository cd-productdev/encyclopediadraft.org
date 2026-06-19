<?php

namespace Tests\Feature\Admin;

use App\Models\Article;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BulkDeleteManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_bulk_trash_articles(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $author = User::factory()->create(['role' => 'user']);

        $articles = collect(range(1, 3))->map(fn (int $number) => Article::create([
            'title' => "Article {$number}",
            'content' => 'Article content',
            'status' => Article::STATUS_DRAFT,
            'created_by' => $author->id,
        ]));

        $response = $this->actingAs($admin)->post(route('admin.articles.bulk-destroy'), [
            'ids' => $articles->take(2)->pluck('id')->all(),
            'permanent' => false,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertSame(1, Article::count());
        $this->assertSame(2, Article::onlyTrashed()->count());
    }

    public function test_admin_can_bulk_force_delete_trashed_articles(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $author = User::factory()->create(['role' => 'user']);

        $articles = collect(range(1, 2))->map(fn (int $number) => Article::create([
            'title' => "Trashed Article {$number}",
            'content' => 'Article content',
            'status' => Article::STATUS_DRAFT,
            'created_by' => $author->id,
        ]));

        $articles->each->delete();

        $response = $this->actingAs($admin)->post(route('admin.articles.bulk-destroy'), [
            'ids' => $articles->pluck('id')->all(),
            'permanent' => true,
        ]);

        $response->assertRedirect();
        $this->assertSame(0, Article::withTrashed()->count());
    }

    public function test_admin_can_bulk_trash_users_except_self(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $users = User::factory()->count(2)->create(['role' => 'user']);

        $response = $this->actingAs($admin)->post(route('admin.users.bulk-destroy'), [
            'ids' => [$admin->id, $users[0]->id, $users[1]->id],
            'permanent' => false,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertNull($admin->fresh()->deleted_at);
        $this->assertSame(1, User::count());
        $this->assertSame(2, User::onlyTrashed()->count());
    }

    public function test_regular_user_cannot_bulk_delete_articles(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $article = Article::create([
            'title' => 'Protected Article',
            'content' => 'Article content',
            'status' => Article::STATUS_DRAFT,
            'created_by' => $user->id,
        ]);

        $response = $this->actingAs($user)->post(route('admin.articles.bulk-destroy'), [
            'ids' => [$article->id],
            'permanent' => false,
        ]);

        $response->assertForbidden();
        $this->assertSame(1, Article::count());
    }
}
