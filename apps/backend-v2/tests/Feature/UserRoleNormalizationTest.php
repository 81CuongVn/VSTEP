<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UserRoleNormalizationTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_returns_instructor_for_legacy_teacher_rows(): void
    {
        $user = User::create([
            'full_name' => 'Legacy Teacher',
            'email' => 'legacy-teacher@example.com',
            'password' => 'password',
            'role' => 'learner',
        ]);

        DB::table('users')->where('id', $user->id)->update(['role' => 'teacher']);
        $user->refresh();

        $this->actingAs($user, 'api')
            ->getJson("/api/v1/users/{$user->id}")
            ->assertOk()
            ->assertJsonPath('data.role', 'instructor');
    }

    #[Test]
    public function it_normalizes_teacher_role_updates_to_instructor(): void
    {
        $admin = User::create([
            'full_name' => 'Admin',
            'email' => 'admin-role@example.com',
            'password' => 'password',
            'role' => 'admin',
        ]);

        $user = User::create([
            'full_name' => 'Learner',
            'email' => 'learner-role@example.com',
            'password' => 'password',
            'role' => 'learner',
        ]);

        $this->actingAs($admin, 'api')
            ->patchJson("/api/v1/users/{$user->id}", [
                'role' => 'teacher',
            ])
            ->assertOk()
            ->assertJsonPath('data.role', 'instructor');
    }

    #[Test]
    public function it_forbids_non_admin_users_from_changing_roles(): void
    {
        $user = User::create([
            'full_name' => 'Learner',
            'email' => 'self-role@example.com',
            'password' => 'password',
            'role' => 'learner',
        ]);

        $this->actingAs($user, 'api')
            ->patchJson("/api/v1/users/{$user->id}", [
                'role' => 'teacher',
            ])
            ->assertForbidden()
            ->assertJson([
                'message' => 'Only admins can change user roles',
            ]);
    }
}
