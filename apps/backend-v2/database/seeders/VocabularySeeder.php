<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\VocabularyTopic;
use App\Models\VocabularyWord;
use Illuminate\Database\Seeder;

class VocabularySeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->topics() as $index => $topicData) {
            $topic = VocabularyTopic::updateOrCreate(
                ['name' => $topicData['name']],
                [
                    'description' => $topicData['description'],
                    'icon_key' => $topicData['icon_key'],
                    'sort_order' => $index + 1,
                ],
            );

            foreach ($topicData['words'] as $wordIndex => $wordData) {
                VocabularyWord::updateOrCreate(
                    [
                        'topic_id' => $topic->id,
                        'word' => $wordData['word'],
                    ],
                    [
                        'phonetic' => $wordData['phonetic'],
                        'audio_url' => null,
                        'part_of_speech' => $wordData['part_of_speech'],
                        'definition' => $wordData['definition'],
                        'explanation' => $wordData['explanation'],
                        'examples' => $wordData['examples'],
                        'sort_order' => $wordIndex + 1,
                    ],
                );
            }
        }
    }

    private function topics(): array
    {
        return [
            [
                'name' => 'Education Review Pack',
                'description' => 'Core vocabulary for school systems, self-study, and academic discussion.',
                'icon_key' => 'book-open',
                'words' => [
                    [
                        'word' => 'curriculum',
                        'phonetic' => '/kəˈrɪk.jə.ləm/',
                        'part_of_speech' => 'noun',
                        'definition' => 'the subjects included in a course of study',
                        'explanation' => 'Useful for writing and speaking tasks about schools, universities, and reforms.',
                        'examples' => [
                            'The national curriculum now includes more project-based learning.',
                            'Students said the curriculum should focus more on practical skills.',
                        ],
                    ],
                    [
                        'word' => 'tuition',
                        'phonetic' => '/tʃuˈɪʃ.ən/',
                        'part_of_speech' => 'noun',
                        'definition' => 'the teaching received at school; also the money paid for teaching',
                        'explanation' => 'Common in opinion essays about the cost of higher education.',
                        'examples' => [
                            'Many families worry about rising tuition fees.',
                            'She received extra tuition before the exam.',
                        ],
                    ],
                    [
                        'word' => 'allocate',
                        'phonetic' => '/ˈæl.ə.keɪt/',
                        'part_of_speech' => 'verb',
                        'definition' => 'to give something officially to someone for a particular purpose',
                        'explanation' => 'Helpful in problem-solution essays and classroom management topics.',
                        'examples' => [
                            'Schools should allocate more time to speaking practice.',
                            'The budget was allocated to teacher training.',
                        ],
                    ],
                    [
                        'word' => 'compulsory',
                        'phonetic' => '/kəmˈpʌl.sər.i/',
                        'part_of_speech' => 'adjective',
                        'definition' => 'required by law or rule',
                        'explanation' => 'Frequently appears in debates about subjects, attendance, and exams.',
                        'examples' => [
                            'Physical education is compulsory in many schools.',
                            'Attendance at the orientation session was compulsory.',
                        ],
                    ],
                    [
                        'word' => 'scholarship',
                        'phonetic' => '/ˈskɒl.ə.ʃɪp/',
                        'part_of_speech' => 'noun',
                        'definition' => 'money given to support a student’s education',
                        'explanation' => 'Useful for education equity and opportunity topics.',
                        'examples' => [
                            'She won a scholarship to study abroad.',
                            'Scholarships can reduce the financial burden on students.',
                        ],
                    ],
                ],
            ],
            [
                'name' => 'Technology Exam Prep',
                'description' => 'High-frequency vocabulary for digital life, AI, and online learning topics.',
                'icon_key' => 'chip',
                'words' => [
                    [
                        'word' => 'automation',
                        'phonetic' => '/ˌɔː.təˈmeɪ.ʃən/',
                        'part_of_speech' => 'noun',
                        'definition' => 'the use of machines or computers instead of people',
                        'explanation' => 'Strong keyword for essays about employment and productivity.',
                        'examples' => [
                            'Automation can improve efficiency in manufacturing.',
                            'Some workers fear automation will replace routine jobs.',
                        ],
                    ],
                    [
                        'word' => 'cybersecurity',
                        'phonetic' => '/ˌsaɪ.bə.sɪˈkjʊə.rə.ti/',
                        'part_of_speech' => 'noun',
                        'definition' => 'ways of protecting computer systems and information',
                        'explanation' => 'Important for discussion questions about internet safety.',
                        'examples' => [
                            'Companies now invest heavily in cybersecurity.',
                            'Students need basic cybersecurity awareness when studying online.',
                        ],
                    ],
                    [
                        'word' => 'innovative',
                        'phonetic' => '/ˈɪn.ə.veɪ.tɪv/',
                        'part_of_speech' => 'adjective',
                        'definition' => 'using new methods or ideas',
                        'explanation' => 'A flexible adjective for describing products, lessons, and solutions.',
                        'examples' => [
                            'The app offers an innovative way to practise pronunciation.',
                            'Teachers should be encouraged to use innovative methods.',
                        ],
                    ],
                    [
                        'word' => 'accessible',
                        'phonetic' => '/əkˈses.ə.bəl/',
                        'part_of_speech' => 'adjective',
                        'definition' => 'easy to reach, obtain, or use',
                        'explanation' => 'Useful for arguments about fairness and digital inclusion.',
                        'examples' => [
                            'Online resources make learning more accessible to rural students.',
                            'Public services should be accessible to older people.',
                        ],
                    ],
                    [
                        'word' => 'data-driven',
                        'phonetic' => '/ˈdeɪ.tə ˌdrɪv.ən/',
                        'part_of_speech' => 'adjective',
                        'definition' => 'based on information that has been collected and analysed',
                        'explanation' => 'Good phrase for higher-level speaking and writing tasks.',
                        'examples' => [
                            'The school adopted a data-driven approach to learner support.',
                            'Businesses increasingly rely on data-driven decisions.',
                        ],
                    ],
                ],
            ],
            [
                'name' => 'Environment Review Set',
                'description' => 'Topic-specific language for sustainability, transport, and community action.',
                'icon_key' => 'leaf',
                'words' => [
                    [
                        'word' => 'sustainable',
                        'phonetic' => '/səˈsteɪ.nə.bəl/',
                        'part_of_speech' => 'adjective',
                        'definition' => 'able to continue over time without harming the environment',
                        'explanation' => 'Key word for essays about development and lifestyle change.',
                        'examples' => [
                            'Cities need more sustainable transport systems.',
                            'Consumers are paying more attention to sustainable products.',
                        ],
                    ],
                    [
                        'word' => 'conservation',
                        'phonetic' => '/ˌkɒn.səˈveɪ.ʃən/',
                        'part_of_speech' => 'noun',
                        'definition' => 'the protection of natural resources and wildlife',
                        'explanation' => 'Useful in reading and speaking tasks about the environment.',
                        'examples' => [
                            'Conservation projects depend on local community support.',
                            'Young people can help with water conservation at home.',
                        ],
                    ],
                    [
                        'word' => 'emission',
                        'phonetic' => '/iˈmɪʃ.ən/',
                        'part_of_speech' => 'noun',
                        'definition' => 'gas or heat sent out into the air',
                        'explanation' => 'Common when discussing climate change and transport policy.',
                        'examples' => [
                            'Governments aim to reduce carbon emissions.',
                            'Electric buses can help cut urban emissions.',
                        ],
                    ],
                    [
                        'word' => 'degrade',
                        'phonetic' => '/dɪˈɡreɪd/',
                        'part_of_speech' => 'verb',
                        'definition' => 'to damage something and make it worse',
                        'explanation' => 'Good for describing environmental harm in formal tasks.',
                        'examples' => [
                            'Plastic waste can degrade marine ecosystems.',
                            'Unchecked tourism may degrade natural sites.',
                        ],
                    ],
                    [
                        'word' => 'incentive',
                        'phonetic' => '/ɪnˈsen.tɪv/',
                        'part_of_speech' => 'noun',
                        'definition' => 'something that encourages a person to do something',
                        'explanation' => 'Useful in policy and solution-based speaking tasks.',
                        'examples' => [
                            'Tax incentives can encourage people to buy electric vehicles.',
                            'The campaign offered small incentives for recycling.',
                        ],
                    ],
                ],
            ],
        ];
    }
}
