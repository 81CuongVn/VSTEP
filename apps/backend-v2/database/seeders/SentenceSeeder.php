<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\SentenceDifficulty;
use App\Models\SentenceItem;
use App\Models\SentenceTopic;
use Illuminate\Database\Seeder;

class SentenceSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->topics() as $index => $topicData) {
            $topic = SentenceTopic::updateOrCreate(
                ['name' => $topicData['name']],
                [
                    'description' => $topicData['description'],
                    'icon_key' => $topicData['icon_key'],
                    'sort_order' => $index + 1,
                ],
            );

            foreach ($topicData['items'] as $itemIndex => $itemData) {
                SentenceItem::updateOrCreate(
                    [
                        'topic_id' => $topic->id,
                        'sentence' => $itemData['sentence'],
                    ],
                    [
                        'audio_url' => null,
                        'translation' => $itemData['translation'],
                        'explanation' => $itemData['explanation'],
                        'writing_usage' => $itemData['writing_usage'],
                        'difficulty' => SentenceDifficulty::from($itemData['difficulty']),
                        'sort_order' => $itemIndex + 1,
                    ],
                );
            }
        }
    }

    private function topics(): array
    {
        return [
            [
                'name' => 'Opinion Expressions',
                'description' => 'Sentence models for presenting personal viewpoints in VSTEP writing and speaking.',
                'icon_key' => 'bulb',
                'items' => [
                    [
                        'sentence' => 'In my opinion, vocational education should receive more financial support.',
                        'translation' => 'Theo tôi, giáo dục nghề nghiệp nên nhận được nhiều hỗ trợ tài chính hơn.',
                        'explanation' => 'Use "In my opinion" to introduce a clear personal stance in opinion essays.',
                        'writing_usage' => 'Useful for essay introductions and speaking part 3 answers when you need to state your position directly.',
                        'difficulty' => 'easy',
                    ],
                    [
                        'sentence' => 'From my perspective, online learning is most effective when combined with face-to-face support.',
                        'translation' => 'Theo góc nhìn của tôi, học trực tuyến hiệu quả nhất khi kết hợp với hỗ trợ trực tiếp.',
                        'explanation' => '"From my perspective" is a more formal alternative to "In my opinion".',
                        'writing_usage' => 'Good for higher-band essays because it adds variety and sounds more academic.',
                        'difficulty' => 'medium',
                    ],
                    [
                        'sentence' => 'I am firmly convinced that early language exposure benefits children in the long run.',
                        'translation' => 'Tôi tin chắc rằng việc tiếp xúc ngôn ngữ sớm mang lại lợi ích lâu dài cho trẻ em.',
                        'explanation' => '"I am firmly convinced" emphasizes a strong, confident opinion.',
                        'writing_usage' => 'Works well in argumentative paragraphs when you want to strengthen your claim.',
                        'difficulty' => 'hard',
                    ],
                    [
                        'sentence' => 'There is little doubt that public libraries still play a vital role in modern education.',
                        'translation' => 'Hầu như không còn nghi ngờ gì rằng thư viện công cộng vẫn đóng vai trò thiết yếu trong giáo dục hiện đại.',
                        'explanation' => 'The pattern "There is little doubt that..." is used to express a widely accepted belief.',
                        'writing_usage' => 'Useful for topic sentences in body paragraphs about education or community development.',
                        'difficulty' => 'medium',
                    ],
                ],
            ],
            [
                'name' => 'Cause and Effect',
                'description' => 'Patterns for explaining reasons, results, and consequences in essays.',
                'icon_key' => 'arrow-right',
                'items' => [
                    [
                        'sentence' => 'Because of rising tuition fees, many students are forced to work part-time.',
                        'translation' => 'Do học phí tăng cao, nhiều sinh viên buộc phải làm thêm bán thời gian.',
                        'explanation' => 'Use "Because of" before a noun phrase to show cause.',
                        'writing_usage' => 'Helpful in problem-cause-solution essays about higher education and finance.',
                        'difficulty' => 'easy',
                    ],
                    [
                        'sentence' => 'As a result, rural communities often experience a shortage of qualified teachers.',
                        'translation' => 'Kết quả là, các cộng đồng nông thôn thường thiếu giáo viên có trình độ.',
                        'explanation' => '"As a result" is a transition marker introducing a consequence.',
                        'writing_usage' => 'Use it to connect two ideas smoothly and improve coherence.',
                        'difficulty' => 'easy',
                    ],
                    [
                        'sentence' => 'This trend has led to a noticeable decline in students’ reading habits.',
                        'translation' => 'Xu hướng này đã dẫn đến sự suy giảm rõ rệt trong thói quen đọc của học sinh.',
                        'explanation' => '"Lead to" is a core collocation for describing a result.',
                        'writing_usage' => 'Suitable for body paragraphs that analyse negative outcomes of modern lifestyle changes.',
                        'difficulty' => 'medium',
                    ],
                    [
                        'sentence' => 'Consequently, governments are under increasing pressure to reform the public transport system.',
                        'translation' => 'Do đó, chính phủ chịu áp lực ngày càng lớn trong việc cải cách hệ thống giao thông công cộng.',
                        'explanation' => '"Consequently" is a formal connector showing logical consequence.',
                        'writing_usage' => 'Strong choice for B2-C1 writing because it sounds precise and academic.',
                        'difficulty' => 'hard',
                    ],
                ],
            ],
            [
                'name' => 'Comparison and Contrast',
                'description' => 'Useful sentence frames for comparing views, systems, and trends.',
                'icon_key' => 'repeat',
                'items' => [
                    [
                        'sentence' => 'While some students thrive in competitive environments, others perform better in collaborative settings.',
                        'translation' => 'Trong khi một số học sinh phát triển tốt trong môi trường cạnh tranh, những người khác lại thể hiện tốt hơn trong môi trường hợp tác.',
                        'explanation' => 'The structure "While..., others..." is useful for balanced comparison.',
                        'writing_usage' => 'Ideal for discuss-both-views essays and speaking tasks that require comparing options.',
                        'difficulty' => 'medium',
                    ],
                    [
                        'sentence' => 'Compared with private vehicles, public transport is far more sustainable in crowded cities.',
                        'translation' => 'So với phương tiện cá nhân, giao thông công cộng bền vững hơn nhiều ở các thành phố đông đúc.',
                        'explanation' => '"Compared with" introduces a direct comparison between two things.',
                        'writing_usage' => 'Useful in environment and transport topics where advantages need to be contrasted clearly.',
                        'difficulty' => 'easy',
                    ],
                    [
                        'sentence' => 'In contrast to traditional classrooms, hybrid courses offer greater flexibility but demand stronger self-discipline.',
                        'translation' => 'Trái với lớp học truyền thống, các khóa học kết hợp linh hoạt hơn nhưng đòi hỏi tính tự giác cao hơn.',
                        'explanation' => '"In contrast to" is a formal phrase for presenting differences.',
                        'writing_usage' => 'Good for B2 and C1 responses on education and technology.',
                        'difficulty' => 'hard',
                    ],
                    [
                        'sentence' => 'Although urban life provides more opportunities, it also exposes residents to higher living costs.',
                        'translation' => 'Mặc dù cuộc sống đô thị mang lại nhiều cơ hội hơn, nó cũng khiến cư dân phải đối mặt với chi phí sinh hoạt cao hơn.',
                        'explanation' => '"Although" is used to acknowledge one side before presenting another.',
                        'writing_usage' => 'Strong sentence opener for balanced body paragraphs in compare-and-contrast essays.',
                        'difficulty' => 'medium',
                    ],
                ],
            ],
            [
                'name' => 'Academic Grammar Patterns',
                'description' => 'Grammar-focused sentence types frequently used in VSTEP writing.',
                'icon_key' => 'notebook',
                'items' => [
                    [
                        'sentence' => 'It is essential that students be given more opportunities to practise speaking in class.',
                        'translation' => 'Điều thiết yếu là học sinh cần được tạo thêm cơ hội luyện nói trong lớp.',
                        'explanation' => 'This sentence uses the mandative subjunctive: "that + subject + base verb".',
                        'writing_usage' => 'Useful for formal recommendations and advanced grammar range in writing task 2.',
                        'difficulty' => 'hard',
                    ],
                    [
                        'sentence' => 'Not only does public transport reduce pollution, but it also improves social accessibility.',
                        'translation' => 'Giao thông công cộng không chỉ làm giảm ô nhiễm mà còn cải thiện khả năng tiếp cận xã hội.',
                        'explanation' => '"Not only ... but also ..." with inversion is a strong contrast-and-addition pattern.',
                        'writing_usage' => 'Excellent for emphasizing multiple benefits in body paragraphs.',
                        'difficulty' => 'medium',
                    ],
                    [
                        'sentence' => 'Were more resources allocated to rural schools, educational inequality could be reduced significantly.',
                        'translation' => 'Nếu có thêm nguồn lực được phân bổ cho trường học vùng nông thôn, bất bình đẳng giáo dục có thể giảm đáng kể.',
                        'explanation' => 'This is an inverted conditional structure replacing "If more resources were allocated...".',
                        'writing_usage' => 'Useful for C1-level hypothetical recommendations and policy discussion.',
                        'difficulty' => 'hard',
                    ],
                    [
                        'sentence' => 'Hardly had the policy been introduced when public debate began to intensify.',
                        'translation' => 'Chính sách vừa mới được ban hành thì tranh luận công khai đã bắt đầu gia tăng.',
                        'explanation' => 'This is an inverted structure used after "Hardly" for advanced narrative emphasis.',
                        'writing_usage' => 'Good for high-level writing when you want to demonstrate grammatical variety.',
                        'difficulty' => 'hard',
                    ],
                ],
            ],
        ];
    }
}
