import {
	CheckmarkCircle01Icon,
	Mic01Icon,
	PencilEdit02Icon,
	Target02Icon,
} from "@hugeicons/core-free-icons"
import { HugeiconsIcon } from "@hugeicons/react"
import { createFileRoute, Link } from "@tanstack/react-router"
import { Logo } from "@/components/common/Logo"
import { Button } from "@/components/ui/button"

export const Route = createFileRoute("/ai-scoring-guide")({
	component: AiScoringGuidePage,
})

const exerciseExamples = [
	"Writing Task 1: apology email, request letter, message with a clear communicative purpose.",
	"Writing Task 2: opinion essay, discussion essay, problem-solution essay on education, society or work.",
	"Speaking Part 1: personal questions about study, work, habits and daily life.",
	"Speaking Part 2: situation-based response where the learner must choose and justify.",
	"Speaking Part 3: follow-up discussion, comparison, and deeper explanation.",
]

const writingCriteria = [
	"Task Achievement: trả lời đúng yêu cầu đề, đủ ý, đúng mục tiêu giao tiếp.",
	"Coherence & Cohesion: bố cục rõ, liên kết ý mạch lạc, không đứt đoạn.",
	"Lexical Resource: từ vựng phù hợp chủ đề, tránh lặp từ đơn điệu.",
	"Grammatical Range & Accuracy: đa dạng cấu trúc và kiểm soát lỗi ngữ pháp.",
]

const speakingCriteria = [
	"Fluency & Coherence: nói đủ ý, ít ngập ngừng, biết phát triển câu trả lời.",
	"Pronunciation: độ rõ âm, phụ âm cuối, nguyên âm, nhấn âm và mức dễ hiểu tổng thể.",
	"Content & Relevance: bám đề, trả lời đúng phần được hỏi, có ví dụ hoặc lý do hỗ trợ.",
	"Vocabulary & Grammar: dùng từ phù hợp và kiểm soát cấu trúc khi nói tự nhiên.",
]

const speechSignals = [
	"Transcript để đối chiếu giữa điều người học định nói và điều hệ thống thực sự nghe được.",
	"Pronunciation accuracy để phát hiện từ hoặc cụm từ phát âm chưa rõ.",
	"Fluency score để nhận ra nhịp nói bị đứt, quá chậm hoặc quá gấp.",
	"Prosody score để phản ánh ngữ điệu, nhấn âm và độ tự nhiên tương đối của câu nói.",
	"Word-level issues để chỉ ra những vị trí có khả năng đang nuốt âm cuối hoặc đọc sai âm chính.",
]

const trustNotes = [
	"AI phù hợp nhất để chấm nháp nhanh, phát hiện lỗi lặp lại và cho người học thêm nhiều vòng luyện trong cùng một ngày.",
	"AI thường nhất quán hơn khi áp một rubric cố định cho nhiều bài tương tự, nên hữu ích để theo dõi xu hướng tiến bộ theo thời gian.",
	"Instructor vẫn mạnh hơn ở các bài nhiều sắc thái, ý tưởng mơ hồ nhưng có tiềm năng, hoặc trường hợp cần cân nhắc bối cảnh học thuật.",
	"Nếu AI và instructor lệch nhau, nên ưu tiên nhận xét của instructor cho quyết định học thuật cuối cùng.",
]

function AiScoringGuidePage() {
	return (
		<div className="min-h-screen bg-background text-foreground">
			<header className="border-b bg-background/90 backdrop-blur">
				<div className="mx-auto flex max-w-5xl items-center justify-between px-6 py-4">
					<Link to="/" aria-label="Về trang chủ">
						<Logo />
					</Link>
					<Button variant="outline" asChild>
						<Link to="/">Về trang chủ</Link>
					</Button>
				</div>
			</header>

			<main className="mx-auto max-w-5xl px-6 py-12 sm:py-16">
				<section className="rounded-3xl bg-muted/25 p-8 sm:p-10">
					<p className="text-sm font-semibold tracking-[0.2em] text-primary uppercase">Guide</p>
					<h1 className="mt-4 text-3xl font-bold tracking-tight sm:text-5xl">
						Cách dùng AI scoring để làm nổi bật giá trị thật của sản phẩm
					</h1>
					<p className="mt-4 max-w-3xl text-sm leading-relaxed text-muted-foreground sm:text-base">
						Người học không chỉ cần nghe rằng nền tảng có AI. Họ cần biết AI giúp gì trong quá trình
						ôn VSTEP, nó mạnh ở đâu, và giới hạn ở đâu so với instructor. Trang này giải thích điều
						đó theo ngôn ngữ cụ thể, không thổi phồng.
					</p>
				</section>

				<section className="mt-12 grid gap-4 md:grid-cols-3">
					<GuideCard
						icon={Target02Icon}
						title="Nhanh"
						desc="Giúp người học có phản hồi sớm để sửa bài ngay thay vì phải đợi lâu."
					/>
					<GuideCard
						icon={PencilEdit02Icon}
						title="Có cấu trúc"
						desc="Writing và Speaking được tách theo tiêu chí thay vì chỉ trả một band điểm tổng."
					/>
					<GuideCard
						icon={Mic01Icon}
						title="Dễ hiểu"
						desc="Speech analysis được diễn giải bằng transcript, pronunciation, fluency và prosody."
					/>
				</section>

				<section className="mt-14 grid gap-6 lg:grid-cols-2">
					<ArticleBlock title="1. Ví dụ cụ thể về exercise types" items={exerciseExamples} />
					<ArticleBlock title="2. Writing được AI chấm như thế nào" items={writingCriteria} />
				</section>

				<section className="mt-6 grid gap-6 lg:grid-cols-2">
					<ArticleBlock title="3. Speaking được AI chấm như thế nào" items={speakingCriteria} />
					<ArticleBlock title="4. Speech analysis hiện giúp được gì" items={speechSignals} />
				</section>

				<section className="mt-14 rounded-3xl border bg-card p-8 sm:p-10">
					<h2 className="text-2xl font-bold">
						5. Độ chính xác so với instructor nên được diễn đạt ra sao?
					</h2>
					<div className="mt-5 space-y-4 text-sm leading-relaxed text-muted-foreground sm:text-base">
						<p>
							Cách diễn đạt an toàn và thuyết phục nhất là: AI scoring rất hữu ích cho phản hồi
							nhanh, nhất quán và bám rubric, đặc biệt ở giai đoạn luyện tập hằng ngày. Đây là công
							cụ giúp người học sửa nhiều vòng hơn trong thời gian ngắn.
						</p>
						<p>
							Tuy nhiên, instructor vẫn có lợi thế rõ ràng ở những bài nói hoặc bài viết nhiều sắc
							thái, trường hợp ý tưởng chưa rõ nhưng có tiềm năng, hoặc tình huống cần phán đoán sâu
							hơn theo ngữ cảnh học thuật. Vì vậy, AI không nên được mô tả là thay thế hoàn toàn
							instructor.
						</p>
						<p>
							Nói ngắn gọn: AI giúp tăng tốc quá trình luyện tập; instructor giúp xác nhận những
							quyết định đánh giá quan trọng hơn. Truyền thông như vậy sẽ đáng tin hơn với sinh viên
							lẫn giảng viên.
						</p>
					</div>
				</section>

				<section className="mt-14">
					<ArticleBlock
						title="6. Cách người học nên dùng feedback"
						items={[
							"Nhìn vào tiêu chí thấp nhất trước vì đó thường là điểm bứt phá nhanh nhất.",
							"Với Writing, sửa theo từng nhóm lỗi thay vì cố sửa mọi thứ trong một lượt.",
							"Với Speaking, nghe lại audio và đối chiếu transcript để xem hệ thống nghe sai ở đâu.",
							"Nếu transcript lệch nhiều, ưu tiên sửa độ rõ âm, phụ âm cuối và tốc độ nói.",
							"So sánh các lần chấm liên tiếp để biết điểm tăng nhờ nội dung, ngữ pháp hay phát âm.",
						]}
					/>
				</section>

				<section className="mt-14 rounded-2xl border bg-background p-6">
					<h2 className="text-lg font-bold">Những thông điệp nên giữ để xây trust</h2>
					<ul className="mt-4 space-y-3 text-sm leading-relaxed text-muted-foreground">
						{trustNotes.map((note) => (
							<li key={note} className="flex gap-3">
								<HugeiconsIcon
									icon={CheckmarkCircle01Icon}
									className="mt-0.5 size-5 shrink-0 text-primary"
								/>
								<span>{note}</span>
							</li>
						))}
					</ul>
				</section>
			</main>
		</div>
	)
}

function GuideCard({
	icon,
	title,
	desc,
}: {
	icon: Parameters<typeof HugeiconsIcon>[0]["icon"]
	title: string
	desc: string
}) {
	return (
		<div className="rounded-2xl border bg-card p-6">
			<HugeiconsIcon icon={icon} className="size-6 text-primary" />
			<h2 className="mt-4 text-lg font-bold">{title}</h2>
			<p className="mt-2 text-sm leading-relaxed text-muted-foreground">{desc}</p>
		</div>
	)
}

function ArticleBlock({ title, items }: { title: string; items: string[] }) {
	return (
		<div className="rounded-2xl border bg-card p-6 sm:p-8">
			<h2 className="text-xl font-bold">{title}</h2>
			<ul className="mt-4 space-y-3 text-sm leading-relaxed text-muted-foreground sm:text-base">
				{items.map((item) => (
					<li key={item} className="flex gap-3">
						<HugeiconsIcon
							icon={CheckmarkCircle01Icon}
							className="mt-0.5 size-5 shrink-0 text-primary"
						/>
						<span>{item}</span>
					</li>
				))}
			</ul>
		</div>
	)
}
