<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('users')
            ->where('role', 'teacher')
            ->update(['role' => 'instructor']);
    }

    public function down(): void
    {
        // No-op: once normalized, we do not want to corrupt valid instructor rows on rollback.
    }
};
