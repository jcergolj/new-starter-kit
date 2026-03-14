<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class RegistrationWithoutEmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('app.single_user_mode', true);
        config()->set('app.allow_without_email_verification', true);
    }

    #[Test]
    public function email_verified_at_is_set_when_allow_without_email_verification_is_true(): void
    {
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
        $response = $this->post(route('register'), [
            'name' => 'Test User',
            'username' => 'testuser',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertRedirect('/dashboard');
    }
}
