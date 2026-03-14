<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class RegisteredUserControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function registration_screen_can_be_rendered(): void
    {
        $this->get('/register')->assertOk();
    }

    #[Test]
    public function new_users_can_register(): void
    {
        $response = $this->post(route('register'), [
            'name' => 'Test User',
            'username' => 'testuser',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ])->assertValid();

        $response->assertRedirect('http://testuser.'.config('app.domain').'/login?status=verify-email');

        $this->assertAuthenticated();
    }

    #[Test]
    public function email_verified_at_is_set_when_allow_without_email_verification_is_true(): void
    {
        config()->set('app.single_db_per_app', true);
        config()->set('app.allow_without_email_verification', true);

        $this->post(route('register'), [
            'name' => 'Test User',
            'username' => 'testuser',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ])->assertValid();

        $this->assertNotNull(User::first()->email_verified_at);
    }

    #[Test]
    public function registration_redirects_to_dashboard_when_allow_without_email_verification_is_true(): void
    {
        config()->set('app.single_db_per_app', true);
        config()->set('app.allow_without_email_verification', true);

        $response = $this->post(route('register'), [
            'name' => 'Test User',
            'username' => 'testuser',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertRedirect('/dashboard');
    }

    #[Test]
    public function registration_redirects_to_same_domain_login(): void
    {
        config()->set('app.single_db_per_app', true);

        $response = $this->post(route('register'), [
            'name' => 'Test User',
            'username' => 'testuser',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ])->assertValid();

        $response->assertRedirect('/login?status=verify-email');
    }

    #[Test]
    public function user_is_created_in_default_database(): void
    {
        config()->set('app.single_db_per_app', true);

        $this->post(route('register'), [
            'name' => 'Test User',
            'username' => 'testuser',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ])->assertValid();

        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'username' => 'testuser',
        ]);
    }

    #[Test]
    public function subdomain_preview_is_hidden_on_register_page(): void
    {
        config()->set('app.single_db_per_app', true);

        $this->get('/register')
            ->assertOk()
            ->assertDontSee('Your URL will be:');
    }

    #[Test]
    public function registration_page_redirects_to_login_when_user_exists(): void
    {
        config()->set('app.single_db_per_app', true);

        User::factory()->create();

        $this->get('/register')->assertRedirect(route('login'));
    }

    #[Test]
    public function registration_post_redirects_to_login_when_user_exists(): void
    {
        config()->set('app.single_db_per_app', true);

        User::factory()->create();

        $this->post(route('register'), [
            'name' => 'Test User',
            'username' => 'testuser',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ])->assertRedirect(route('login'));
    }

    #[Test]
    public function email_verified_at_is_null_when_allow_without_email_verification_is_false(): void
    {
        config()->set('app.single_db_per_app', true);

        $this->post(route('register'), [
            'name' => 'Test User',
            'username' => 'testuser',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ])->assertValid();

        $this->assertNull(User::first()->email_verified_at);
    }
}
