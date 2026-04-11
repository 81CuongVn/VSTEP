<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@vstep.local'],
            [
                'full_name' => 'Admin',
                'password' => 'password',
                'role' => Role::Admin,
            ],
        );

        User::updateOrCreate(
            ['email' => 'instructor@vstep.local'],
            [
                'full_name' => 'Instructor Demo',
                'password' => 'password',
                'role' => Role::Instructor,
            ],
        );

        User::updateOrCreate(
            ['email' => 'learner@vstep.local'],
            [
                'full_name' => 'Learner Demo',
                'password' => 'password',
                'role' => Role::Learner,
            ],
        );

        $this->call([
            KnowledgeGraphSeeder::class,
            GradingRubricSeeder::class,
            QuestionSeeder::class,
            PracticeReviewSeeder::class,
            VocabularySeeder::class,
            SentenceSeeder::class,
            ExamSeeder::class,
        ]);
    }
}
