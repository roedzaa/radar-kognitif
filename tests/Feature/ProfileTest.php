<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_page_is_displayed(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get('/profile');

        $response->assertOk();
    }

    public function test_shared_tester_cannot_open_the_profile_page(): void
    {
        $tester = User::factory()->create(['email' => User::SHARED_TESTER_EMAIL]);

        $this->actingAs($tester)
            ->get('/profile')
            ->assertForbidden();
    }

    public function test_profile_information_can_be_updated(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => 'Test User',
                'email' => 'test@example.com',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $user->refresh();

        $this->assertSame('Test User', $user->name);
        $this->assertSame('test@example.com', $user->email);
        $this->assertNull($user->email_verified_at);
    }

    public function test_shared_tester_cannot_change_its_profile(): void
    {
        $tester = User::factory()->create(['email' => User::SHARED_TESTER_EMAIL]);

        $this->actingAs($tester)
            ->patch('/profile', [
                'name' => 'Changed Tester',
                'email' => 'changed@example.test',
            ])
            ->assertForbidden();

        $this->assertDatabaseHas('users', [
            'id' => $tester->id,
            'name' => $tester->name,
            'email' => User::SHARED_TESTER_EMAIL,
        ]);
    }

    public function test_email_verification_status_is_unchanged_when_the_email_address_is_unchanged(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => 'Test User',
                'email' => $user->email,
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $this->assertNotNull($user->refresh()->email_verified_at);
    }

    public function test_user_can_delete_their_account(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->delete('/profile', [
                'password' => 'password',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/');

        $this->assertGuest();
        $this->assertNull($user->fresh());
    }

    public function test_shared_tester_cannot_delete_its_account(): void
    {
        $tester = User::factory()->create(['email' => User::SHARED_TESTER_EMAIL]);

        $this->actingAs($tester)
            ->delete('/profile', ['password' => 'password'])
            ->assertForbidden();

        $this->assertModelExists($tester);
    }

    public function test_correct_password_must_be_provided_to_delete_account(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from('/profile')
            ->delete('/profile', [
                'password' => 'wrong-password',
            ]);

        $response
            ->assertSessionHasErrorsIn('userDeletion', 'password')
            ->assertRedirect('/profile');

        $this->assertNotNull($user->fresh());
    }
}
