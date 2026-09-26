<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Testing\TestCase;

class ImpersonationTest extends TestCase
{
    public function createApplication()
    {
        $app = require __DIR__ . '/../../bootstrap/app.php';
        $app->make(Kernel::class)->bootstrap();

        return $app;
    }

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('database.default', 'sqlite');
        config()->set('database.connections.sqlite.database', ':memory:');
        config()->set('session.driver', 'array');
        config()->set('app.url', 'http://localhost');
        url()->forceRootUrl('http://localhost');
        $this->artisan('migrate:fresh', ['--force' => true]);
        $this->withoutExceptionHandling();
    }

    public function test_super_admin_can_impersonate_and_return(): void
    {
        $admin = $this->user('super_admin');
        $target = $this->user('admin');

        $this->actingAs($admin)
            ->post(route('users.impersonate', $target))
            ->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($target);
        $this->assertSame($admin->id, session('impersonation.original_user_id'));

        $this->post(route('impersonation.stop'))
            ->assertRedirect(route('users.index'));

        $this->assertAuthenticatedAs($admin);
        $this->assertNull(session('impersonation'));
    }

    public function test_company_admin_cannot_impersonate(): void
    {
        $admin = $this->user('admin');
        $target = $this->user('admin');

        $this->actingAs($admin)
            ->post(route('users.impersonate', $target))
            ->assertForbidden();

        $this->assertAuthenticatedAs($admin);
    }

    public function test_inactive_target_and_nested_switch_are_rejected(): void
    {
        $admin = $this->user('super_admin');
        $inactive = $this->user('admin', false);
        $target = $this->user('super_admin');
        $other = $this->user('admin');

        $this->actingAs($admin)
            ->post(route('users.impersonate', $inactive))
            ->assertStatus(422);

        $this->post(route('users.impersonate', $target))
            ->assertRedirect(route('dashboard'));

        $this->post(route('users.impersonate', $other))
            ->assertForbidden();

        $this->assertAuthenticatedAs($target);
    }

    public function test_return_requires_an_impersonation_session(): void
    {
        $admin = $this->user('super_admin');

        $this->actingAs($admin)
            ->post(route('impersonation.stop'))
            ->assertForbidden();
    }

    private function user(string $type, bool $active = true): User
    {
        return User::create([
            'name' => 'Test ' . $type,
            'email' => uniqid('user-', true) . '@example.test',
            'password' => 'password123',
            'role' => $type,
            'user_type' => $type,
            'is_active' => $active,
        ]);
    }
}
