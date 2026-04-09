<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\Level;
use App\Enums\Skill;
use App\Models\Question;
use Illuminate\Database\Seeder;

class PracticeReviewSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->questions() as $question) {
            Question::updateOrCreate(
                [
                    'skill' => Skill::from($question['skill']),
                    'level' => Level::from($question['level']),
                    'part' => $question['part'],
                    'topic' => $question['topic'],
                ],
                [
                    'content' => $question['content'],
                    'answer_key' => $question['answer_key'],
                    'bloom_level' => null,
                    'is_active' => true,
                    'verified_at' => now(),
                ],
            );
        }
    }

    private function questions(): array
    {
        return [
            [
                'skill' => 'reading',
                'level' => 'B1',
                'part' => 2,
                'topic' => 'Exam Review Pack - Study Habits',
                'content' => [
                    'title' => 'Study Habits Before Important Exams',
                    'passage' => "Students often prepare for important exams in very different ways. Some prefer to study alone in a quiet place, while others work better in small groups where they can discuss difficult ideas. Research suggests that the most effective learners do not simply spend long hours reading notes. Instead, they review key concepts regularly, test themselves, and take short breaks to maintain concentration.\n\nAnother useful strategy is to simulate real exam conditions. By completing practice tests within a fixed time limit, learners become more familiar with pressure and can identify weak areas more accurately. Teachers also advise students to focus on sleep and nutrition before the exam, since poor physical health can reduce memory and attention.\n\nAlthough last-minute revision is common, it is usually less effective than a long-term plan. Students who spread their revision over several weeks are more likely to remember information and feel confident on test day.",
                    'items' => [
                        ['stem' => 'What is the passage mainly about?', 'options' => ['Problems with school uniforms', 'Ways to prepare effectively for exams', 'How to become a teacher', 'Why students dislike homework']],
                        ['stem' => 'According to the passage, effective learners often...', 'options' => ['study without breaks', 'avoid practice tests', 'review and test themselves regularly', 'memorize everything the night before']],
                        ['stem' => 'Why are practice tests useful?', 'options' => ['They remove the need for sleep', 'They help learners get used to exam pressure', 'They replace all classroom learning', 'They make exams shorter']],
                        ['stem' => 'The word "simulate" is closest in meaning to...', 'options' => ['copy or recreate', 'cancel', 'improve', 'ignore']],
                    ],
                ],
                'answer_key' => ['correctAnswers' => ['1' => 'B', '2' => 'C', '3' => 'B', '4' => 'A']],
            ],
            [
                'skill' => 'listening',
                'level' => 'B2',
                'part' => 3,
                'topic' => 'Exam Prep Mini Test - Campus Orientation',
                'content' => [
                    'audioUrl' => 'listening/b2_exam_prep_orientation.wav',
                    'transcript' => 'Welcome to the university orientation session. Over the next two weeks, new students are encouraged to attend workshops on academic writing, digital research, and time management. Although attendance is optional, students who join these sessions usually adapt more quickly to university life. The library will also extend its opening hours during the first month of the semester so that students can explore the new study spaces. Finally, please note that the student support office has moved to Building C, where advisers are available from Monday to Friday.',
                    'items' => [
                        ['stem' => 'What is the main purpose of the talk?', 'options' => ['To advertise a sports event', 'To introduce support services for new students', 'To explain graduation rules', 'To announce exam results']],
                        ['stem' => 'What do the workshops focus on?', 'options' => ['Transport and housing', 'Writing, research, and time management', 'Scholarships only', 'Career interviews']],
                        ['stem' => 'What is true about the workshops?', 'options' => ['They are compulsory', 'They are available only online', 'They are optional but useful', 'They are for teachers only']],
                        ['stem' => 'Where is the student support office now?', 'options' => ['Building A', 'Building B', 'Building C', 'The library']],
                    ],
                ],
                'answer_key' => ['correctAnswers' => ['1' => 'B', '2' => 'B', '3' => 'C', '4' => 'C']],
            ],
            [
                'skill' => 'writing',
                'level' => 'B2',
                'part' => 2,
                'topic' => 'Exam Review Essay - Online Learning',
                'content' => [
                    'prompt' => 'Many universities now offer blended or fully online courses. Write an essay discussing the advantages and disadvantages of online learning and give your opinion on whether it should replace face-to-face classes.',
                    'taskType' => 'essay',
                    'minWords' => 220,
                    'instructions' => [
                        'Organize your answer clearly with an introduction, body paragraphs, and a conclusion.',
                        'Support your ideas with specific reasons or examples.',
                    ],
                    'requiredPoints' => [
                        'advantages of online learning',
                        'disadvantages of online learning',
                        'your opinion on replacing face-to-face classes',
                    ],
                ],
                'answer_key' => null,
            ],
            [
                'skill' => 'speaking',
                'level' => 'C1',
                'part' => 3,
                'topic' => 'Exam Prep Speaking - Urban Transport',
                'content' => [
                    'centralIdea' => 'Large cities should reduce the number of private cars in the city centre.',
                    'suggestions' => [
                        'expand public transport',
                        'increase parking fees',
                        'create more cycling lanes',
                    ],
                    'followUpQuestion' => 'Do you think these policies would be accepted by the public? Why or why not?',
                    'preparationSeconds' => 60,
                    'speakingSeconds' => 120,
                ],
                'answer_key' => null,
            ],
        ];
    }
}
