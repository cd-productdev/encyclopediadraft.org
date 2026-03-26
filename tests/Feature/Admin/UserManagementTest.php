<?php

namespace Tests\Feature\Admin;

use App\Mail\AccountCreatedMail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_creating_user_sends_credentials_email_and_assigns_role(): void
    {
        Mail::fake();

        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->post(route('admin.users.store'), [
            'name' => 'New Staff',
            'email' => 'newstaff@example.com',
            'password' => 'TempPass99',
            'password_confirmation' => 'TempPass99',
            'role' => 'moderator',
        ]);

        $response->assertRedirect(route('admin.users.index'));

        $created = User::query()->where('email', 'newstaff@example.com')->first();
        $this->assertNotNull($created);
        $this->assertSame('moderator', $created->role);

        Mail::assertSent(AccountCreatedMail::class, function (AccountCreatedMail $mail) use ($created): bool {
            return $mail->user->is($created)
                && $mail->plainPassword === 'TempPass99';
        });
    }

    public function test_moderator_creating_user_forces_regular_user_role_and_sends_email(): void
    {
        Mail::fake();

        $moderator = User::factory()->create(['role' => 'moderator']);

        $response = $this->actingAs($moderator)->post(route('admin.users.store'), [
            'name' => 'Regular Person',
            'email' => 'regular@example.com',
            'password' => 'AnotherPass88',
            'password_confirmation' => 'AnotherPass88',
            'role' => 'admin',
        ]);

        $response->assertRedirect(route('admin.users.index'));

        $created = User::query()->where('email', 'regular@example.com')->first();
        $this->assertNotNull($created);
        $this->assertSame('user', $created->role);

        Mail::assertSent(AccountCreatedMail::class, function (AccountCreatedMail $mail): bool {
            return $mail->user->email === 'regular@example.com'
                && $mail->plainPassword === 'AnotherPass88';
        });
    }

    public function test_moderator_updating_user_cannot_change_role(): void
    {
        $moderator = User::factory()->create(['role' => 'moderator']);
        $target = User::factory()->create(['role' => 'user', 'name' => 'Original Name']);

        $response = $this->actingAs($moderator)->put(route('admin.users.update', $target), [
            'name' => 'Updated Name',
            'email' => $target->email,
            'role' => 'admin',
        ]);

        $response->assertRedirect(route('admin.users.index'));

        $target->refresh();
        $this->assertSame('user', $target->role);
        $this->assertSame('Updated Name', $target->name);
    }
}
