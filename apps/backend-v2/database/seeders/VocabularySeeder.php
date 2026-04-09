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
            [
                'name' => 'Health and Lifestyle',
                'description' => 'Vocabulary for health habits, public well-being, and personal development topics.',
                'icon_key' => 'heart',
                'words' => [
                    [
                        'word' => 'sedentary',
                        'phonetic' => '/ˈsed.ən.tər.i/',
                        'part_of_speech' => 'adjective',
                        'definition' => 'involving little physical activity',
                        'explanation' => 'Common in essays about modern lifestyle and health risks.',
                        'examples' => [
                            'A sedentary lifestyle can increase the risk of obesity.',
                            'Office workers are often encouraged to avoid sedentary routines.',
                        ],
                    ],
                    [
                        'word' => 'well-being',
                        'phonetic' => '/ˌwelˈbiː.ɪŋ/',
                        'part_of_speech' => 'noun',
                        'definition' => 'the state of feeling healthy and happy',
                        'explanation' => 'Useful in both personal and public health discussions.',
                        'examples' => [
                            'Regular exercise contributes to mental well-being.',
                            'Work-life balance affects overall well-being.',
                        ],
                    ],
                    [
                        'word' => 'nutritious',
                        'phonetic' => '/njuːˈtrɪʃ.əs/',
                        'part_of_speech' => 'adjective',
                        'definition' => 'healthy because it contains important nutrients',
                        'explanation' => 'A key adjective for food, school meals, and public health topics.',
                        'examples' => [
                            'Children need nutritious meals to develop properly.',
                            'Nutritious food should be affordable for low-income families.',
                        ],
                    ],
                    [
                        'word' => 'preventive',
                        'phonetic' => '/prɪˈven.tɪv/',
                        'part_of_speech' => 'adjective',
                        'definition' => 'intended to stop something bad from happening',
                        'explanation' => 'Useful for healthcare systems and long-term policy writing.',
                        'examples' => [
                            'Preventive healthcare can reduce treatment costs.',
                            'Schools should promote preventive habits from an early age.',
                        ],
                    ],
                    [
                        'word' => 'resilience',
                        'phonetic' => '/rɪˈzɪl.i.əns/',
                        'part_of_speech' => 'noun',
                        'definition' => 'the ability to recover quickly after difficulties',
                        'explanation' => 'Helpful in essays about stress, education, and personal growth.',
                        'examples' => [
                            'Sport can help teenagers build resilience.',
                            'Emotional resilience is important in a fast-changing world.',
                        ],
                    ],
                ],
            ],
            [
                'name' => 'Work and Career',
                'description' => 'Target vocabulary for employment, workplace change, and professional development.',
                'icon_key' => 'briefcase',
                'words' => [
                    [
                        'word' => 'recruitment',
                        'phonetic' => '/rɪˈkruːt.mənt/',
                        'part_of_speech' => 'noun',
                        'definition' => 'the process of finding and hiring new people',
                        'explanation' => 'Useful when discussing business growth and job markets.',
                        'examples' => [
                            'Online platforms have changed recruitment practices.',
                            'The company improved its recruitment strategy this year.',
                        ],
                    ],
                    [
                        'word' => 'upskill',
                        'phonetic' => '/ʌpˈskɪl/',
                        'part_of_speech' => 'verb',
                        'definition' => 'to teach workers new skills',
                        'explanation' => 'Frequently used in discussions about AI and automation.',
                        'examples' => [
                            'Many companies need to upskill staff for digital transformation.',
                            'Governments should help workers upskill throughout their careers.',
                        ],
                    ],
                    [
                        'word' => 'productivity',
                        'phonetic' => '/ˌprɒd.ʌkˈtɪv.ə.ti/',
                        'part_of_speech' => 'noun',
                        'definition' => 'the rate at which work is completed',
                        'explanation' => 'A core term in workplace, management, and technology essays.',
                        'examples' => [
                            'Flexible schedules may improve productivity.',
                            'Technology can raise productivity when used effectively.',
                        ],
                    ],
                    [
                        'word' => 'entrepreneurial',
                        'phonetic' => '/ˌɒn.trə.prəˈnɜː.ri.əl/',
                        'part_of_speech' => 'adjective',
                        'definition' => 'related to starting and managing businesses',
                        'explanation' => 'Useful for discussions about innovation and young people.',
                        'examples' => [
                            'Universities should encourage entrepreneurial thinking.',
                            'Entrepreneurial skills can help graduates create their own jobs.',
                        ],
                    ],
                    [
                        'word' => 'retain',
                        'phonetic' => '/rɪˈteɪn/',
                        'part_of_speech' => 'verb',
                        'definition' => 'to keep someone or something',
                        'explanation' => 'Often used for staff retention, knowledge retention, and customer loyalty.',
                        'examples' => [
                            'Companies must improve working conditions to retain talent.',
                            'Good revision strategies help students retain information longer.',
                        ],
                    ],
                ],
            ],
            [
                'name' => 'Society and Culture',
                'description' => 'Language for community values, social issues, media, and cultural identity.',
                'icon_key' => 'users',
                'words' => [
                    [
                        'word' => 'inclusive',
                        'phonetic' => '/ɪnˈkluː.sɪv/',
                        'part_of_speech' => 'adjective',
                        'definition' => 'including many different kinds of people',
                        'explanation' => 'Valuable for essays about equality, policy, and education.',
                        'examples' => [
                            'Public spaces should be more inclusive for people with disabilities.',
                            'Schools need inclusive teaching materials.',
                        ],
                    ],
                    [
                        'word' => 'heritage',
                        'phonetic' => '/ˈher.ɪ.tɪdʒ/',
                        'part_of_speech' => 'noun',
                        'definition' => 'traditions, culture, and buildings from the past',
                        'explanation' => 'Useful for speaking and writing on tourism and culture.',
                        'examples' => [
                            'Young people should learn more about national heritage.',
                            'Tourism can help preserve cultural heritage.',
                        ],
                    ],
                    [
                        'word' => 'stereotype',
                        'phonetic' => '/ˈster.i.ə.taɪp/',
                        'part_of_speech' => 'noun',
                        'definition' => 'a fixed idea that many people have about a group',
                        'explanation' => 'Helpful in essays about media, gender, and social attitudes.',
                        'examples' => [
                            'Advertising can reinforce harmful stereotypes.',
                            'Education helps students challenge stereotypes.',
                        ],
                    ],
                    [
                        'word' => 'cohesion',
                        'phonetic' => '/kəʊˈhiː.ʒən/',
                        'part_of_speech' => 'noun',
                        'definition' => 'the quality of working well together',
                        'explanation' => 'Useful for community and social policy topics.',
                        'examples' => [
                            'Sport can strengthen social cohesion in local communities.',
                            'Trust is essential for team cohesion.',
                        ],
                    ],
                    [
                        'word' => 'ethical',
                        'phonetic' => '/ˈeθ.ɪ.kəl/',
                        'part_of_speech' => 'adjective',
                        'definition' => 'morally right and acceptable',
                        'explanation' => 'A key word for AI, business, science, and media questions.',
                        'examples' => [
                            'Companies should follow ethical business practices.',
                            'The use of AI raises several ethical concerns.',
                        ],
                    ],
                ],
            ],
        ];
    }
}
