import {
	ArrowDown01Icon,
	Book02Icon,
	HeadphonesIcon,
	Mic01Icon,
	PencilEdit02Icon,
} from "@hugeicons/core-free-icons"
import type { IconSvgElement } from "@hugeicons/react"
import { HugeiconsIcon } from "@hugeicons/react"
import { createFileRoute, Link, redirect } from "@tanstack/react-router"
import { useEffect, useRef, useState } from "react"
import { Logo } from "@/components/common/Logo"
import { Avatar, AvatarFallback, AvatarImage } from "@/components/ui/avatar"
import { Button } from "@/components/ui/button"
import { isAuthenticated } from "@/lib/auth"
import { cn } from "@/lib/utils"

export const Route = createFileRoute("/")({
	beforeLoad: () => {
		if (isAuthenticated()) throw redirect({ to: "/practice" })
	},
	component: LandingPage,
})

interface SkillItem {
	key: string
	label: string
	desc: string
	icon: IconSvgElement
	color: string
}

interface ExerciseExample {
	skill: string
	title: string
	examples: string[]
}

interface TrustPoint {
	title: string
	desc: string
}

const SKILLS: SkillItem[] = [
	{
		key: "listening",
		label: "Listening",
		desc: "Luyện short dialogues, longer talks, main idea và detail questions.",
		icon: HeadphonesIcon,
		color: "bg-skill-listening/12 text-skill-listening",
	},
	{
		key: "reading",
		label: "Reading",
		desc: "Luyện multiple choice, gap-fill, matching headings và inference questions.",
		icon: Book02Icon,
		color: "bg-skill-reading/12 text-skill-reading",
	},
	{
		key: "writing",
		label: "Writing",
		desc: "Viết email, letter, opinion essay và nhận feedback theo rubric.",
		icon: PencilEdit02Icon,
		color: "bg-skill-writing/12 text-skill-writing",
	},
	{
		key: "speaking",
		label: "Speaking",
		desc: "Luyện Part 1, 2, 3 với transcript, pronunciation, fluency và prosody.",
		icon: Mic01Icon,
		color: "bg-skill-speaking/12 text-skill-speaking",
	},
]

const EXERCISE_EXAMPLES: ExerciseExample[] = [
	{
		skill: "Listening",
		title: "Nghe đúng những dạng bài dễ gặp trong VSTEP",
		examples: ["Short dialogues", "Longer talks", "Note completion", "Main idea questions"],
	},
	{
		skill: "Reading",
		title: "Làm quen các kiểu câu hỏi dễ mất điểm",
		examples: ["Multiple choice", "Gap-fill", "Matching headings", "Inference questions"],
	},
	{
		skill: "Writing",
		title: "Viết theo task thật thay vì luyện chung chung",
		examples: ["Apology email", "Request letter", "Opinion essay", "Problem-solution essay"],
	},
	{
		skill: "Speaking",
		title: "Luyện trọn bộ 3 phần của bài nói",
		examples: [
			"Personal questions",
			"Situation response",
			"Follow-up discussion",
			"Topic expansion",
		],
	},
]

const STEPS = [
	{
		num: "1",
		title: "Làm bài theo đúng dạng bạn cần luyện",
		desc: "Chọn full mock test hoặc luyện riêng từng kỹ năng với các dạng bài cụ thể như email, opinion essay, Part 2 speaking hay matching headings.",
		image: "/images/buoc1.jpg",
	},
	{
		num: "2",
		title: "Nhận AI scoring có cấu trúc",
		desc: "Writing và Speaking được phân tích theo tiêu chí chấm, kèm chỉ báo rõ hơn về pronunciation, fluency, prosody và các lỗi lặp lại.",
		image: "/images/buoc2.jpg",
	},
	{
		num: "3",
		title: "Sửa bài và theo dõi tiến bộ",
		desc: "Dùng feedback để sửa từng vòng nhỏ, sau đó đối chiếu kết quả các lần làm để biết band điểm đang tăng nhờ nội dung, ngữ pháp hay phát âm.",
		image: "/images/buoc3.jpg",
	},
]

const TRUST_POINTS: TrustPoint[] = [
	{
		title: "Chấm theo rubric trước, không chấm cảm tính",
		desc: "AI scoring có giá trị nhất khi bài làm bám format và cần phản hồi nhanh theo từng tiêu chí, thay vì chỉ trả một điểm tổng khó hành động.",
	},
	{
		title: "Speech analysis không chỉ là một điểm phát âm",
		desc: "Hệ thống có thể tạo transcript, đo pronunciation accuracy, fluency, prosody và gợi ý những chỗ có khả năng nuốt âm cuối, ngắt nghỉ gấp hoặc nhấn âm chưa tự nhiên.",
	},
	{
		title: "Phù hợp cho luyện tập hằng ngày",
		desc: "Điểm mạnh của AI là tốc độ và sự nhất quán. Người học có thể làm bài, sửa bài và làm lại nhiều vòng trong cùng một ngày mà không phải chờ lâu.",
	},
	{
		title: "Instructor vẫn là lớp đánh giá quan trọng",
		desc: "Các câu trả lời nhiều sắc thái, lập luận quá mơ hồ hoặc trường hợp độ tin cậy thấp vẫn nên được hiểu là cần instructor review. AI giúp tăng tốc, không thay thế hoàn toàn giảng viên.",
	},
]

const BANDS = [
	{
		level: "B1",
		label: "Nền tảng",
		desc: "Xây chắc khả năng hiểu ý chính, trả lời rõ ràng và kiểm soát lỗi cơ bản.",
		skills: ["Short dialogues", "Basic letters", "Simple topic response"],
	},
	{
		level: "B2",
		label: "Tăng tốc",
		desc: "Mở rộng lập luận, tổ chức ý mạch lạc hơn và duy trì độ trôi chảy ổn định.",
		skills: ["Longer talks", "Opinion essay", "Part 2 decision making"],
	},
	{
		level: "C1",
		label: "Hoàn thiện",
		desc: "Kiểm soát sắc thái, phát triển luận điểm sâu hơn và dùng tiếng Anh tự nhiên hơn.",
		skills: ["Inference reading", "Academic-style writing", "Extended discussion"],
	},
]

const TESTIMONIALS = [
	{
		name: "Minh Anh",
		role: "Sinh viên Đại học Bách Khoa",
		quote:
			"Phần hữu ích nhất là mình không chỉ thấy điểm Speaking, mà còn thấy transcript và biết mình đang nói vấp ở đâu.",
		score: "B1 -> B2",
		initials: "MA",
		avatar: "https://i.pravatar.cc/150?img=32",
		stars: 5,
	},
	{
		name: "Thanh Hà",
		role: "Nhân viên văn phòng",
		quote:
			"Writing feedback dễ dùng hơn mình nghĩ. Mỗi lần sửa chỉ tập trung vào một tiêu chí nên đỡ bị quá tải.",
		score: "B2 -> C1",
		initials: "TH",
		avatar: "https://i.pravatar.cc/150?img=47",
		stars: 5,
	},
	{
		name: "Đức Huy",
		role: "Giảng viên tiếng Anh",
		quote:
			"Nếu truyền thông đúng rằng AI là lớp phản hồi nhanh trước khi instructor review, đây là một giá trị thực sự thuyết phục cho người học.",
		score: "Khuyến nghị cho lớp học",
		initials: "ĐH",
		avatar: "https://i.pravatar.cc/150?img=11",
		stars: 4,
	},
]

const fadeUp = "translate-y-8 opacity-0"
const fadeIn = "translate-y-0 opacity-100"

function useInView(threshold = 0.15) {
	const ref = useRef<HTMLDivElement>(null)
	const [visible, setVisible] = useState(false)

	useEffect(() => {
		const el = ref.current
		if (!el) return

		const observer = new IntersectionObserver(
			([entry]) => {
				if (entry.isIntersecting) setVisible(true)
			},
			{ threshold },
		)

		observer.observe(el)
		return () => observer.disconnect()
	}, [threshold])

	return { ref, visible }
}

function useTilt(intensity = 6) {
	const ref = useRef<HTMLDivElement>(null)

	useEffect(() => {
		const el = ref.current
		if (!el) return

		const onMove = (event: MouseEvent) => {
			const rect = el.getBoundingClientRect()
			const x = ((event.clientX - rect.left) / rect.width - 0.5) * 2
			const y = ((event.clientY - rect.top) / rect.height - 0.5) * 2
			el.style.transform = `perspective(700px) rotateY(${x * intensity}deg) rotateX(${-y * intensity}deg) scale(1.01)`
		}

		const onLeave = () => {
			el.style.transform = ""
		}

		el.addEventListener("mousemove", onMove)
		el.addEventListener("mouseleave", onLeave)

		return () => {
			el.removeEventListener("mousemove", onMove)
			el.removeEventListener("mouseleave", onLeave)
		}
	}, [intensity])

	return ref
}

function AnimSection({
	children,
	className,
	delay = 0,
}: {
	children: React.ReactNode
	className?: string
	delay?: number
}) {
	const { ref, visible } = useInView()

	return (
		<div
			ref={ref}
			className={cn("transition-all duration-700 ease-out", visible ? fadeIn : fadeUp, className)}
			style={{ transitionDelay: `${delay}ms` }}
		>
			{children}
		</div>
	)
}

function Heading({ title, sub }: { title: string; sub: string }) {
	return (
		<div className="text-center">
			<h2 className="text-2xl font-bold lg:text-3xl">{title}</h2>
			<p className="mx-auto mt-3 max-w-2xl text-muted-foreground">{sub}</p>
		</div>
	)
}

function LandingPage() {
	return (
		<div className="min-h-screen text-foreground">
			<Header />
			<Hero />
			<SkillsSection />
			<ExerciseTypesSection />
			<HowItWorksSection />
			<ScoringTrustSection />
			<GuideSection />
			<RoadmapSection />
			<TestimonialsSection />
			<CtaSection />
			<Footer />
		</div>
	)
}

function Header() {
	return (
		<header className="absolute inset-x-0 top-0 z-50">
			<div className="mx-auto flex h-16 max-w-6xl items-center justify-between px-6">
				<Link to="/">
					<Logo className="text-white" />
				</Link>
				<div className="flex items-center gap-2">
					<Button
						variant="ghost"
						size="sm"
						className="text-white/80 hover:bg-white/10 hover:text-white"
						asChild
					>
						<Link to="/login">Đăng nhập</Link>
					</Button>
					<Button
						size="sm"
						className="rounded-full bg-white px-6 font-bold text-[oklch(0.35_0.18_258)] hover:bg-white/90"
						asChild
					>
						<Link to="/register">Bắt đầu</Link>
					</Button>
				</div>
			</div>
		</header>
	)
}

function Hero() {
	return (
		<section className="relative overflow-hidden rounded-b-3xl bg-gradient-to-b from-[oklch(0.35_0.18_258)] via-[oklch(0.45_0.2_258)] to-[oklch(0.5_0.2_258)]">
			<div className="pointer-events-none absolute inset-0">
				<div className="absolute left-1/2 top-1/2 size-[840px] -translate-x-1/2 -translate-y-1/2 rounded-full bg-white/[0.04]" />
				<div className="absolute left-1/2 top-1/2 size-[560px] -translate-x-1/2 -translate-y-1/2 rounded-full border border-white/[0.07]" />
				<div className="absolute right-[18%] top-16 size-10 rounded-full bg-amber-400/80 blur-[1px]" />
				<div className="absolute bottom-20 left-[12%] size-5 rounded-full bg-white/15" />
			</div>

			<div className="relative mx-auto flex max-w-5xl flex-col items-center px-6 pb-24 pt-24 text-center sm:pt-32">
				<AnimSection>
					<h1 className="text-4xl font-bold leading-[1.08] tracking-tight text-white sm:text-6xl lg:text-7xl">
						Luyện VSTEP với
						<br />
						<span className="text-amber-300">AI scoring dễ hiểu</span>
					</h1>
				</AnimSection>

				<AnimSection delay={80}>
					<p className="mx-auto mt-5 max-w-3xl text-base leading-relaxed text-white/78 sm:text-lg">
						Không chỉ nói chung là “có AI”. VSTEP Practice cho người học thấy rõ mình đang luyện
						dạng bài nào, AI đang chấm gì trong Writing và Speaking, và khi nào nên tin vào
						instructor review.
					</p>
				</AnimSection>

				<AnimSection delay={140}>
					<div className="mt-5 flex flex-wrap items-center justify-center gap-2">
						{["Email", "Opinion essay", "Part 2 speaking", "Pronunciation + prosody"].map(
							(item) => (
								<span
									key={item}
									className="rounded-full border border-white/20 bg-white/10 px-4 py-1.5 text-sm text-white/88 backdrop-blur"
								>
									{item}
								</span>
							),
						)}
					</div>
				</AnimSection>

				<AnimSection delay={220}>
					<div className="mt-8 flex w-full flex-col items-center gap-3 sm:w-auto sm:flex-row">
						<Button
							size="lg"
							className="w-full rounded-full bg-white px-10 text-base font-bold text-[oklch(0.35_0.18_258)] shadow-lg shadow-black/20 hover:bg-white/90 sm:w-auto"
							asChild
						>
							<Link to="/register">Bắt đầu ngay</Link>
						</Button>
						<Button
							variant="ghost"
							size="lg"
							className="w-full rounded-full border border-white/30 bg-transparent px-10 text-base font-bold text-white hover:bg-white/10 hover:text-white sm:w-auto"
							asChild
						>
							<a href="#how-it-works">Xem cách hoạt động</a>
						</Button>
					</div>
				</AnimSection>

				<AnimSection delay={280}>
					<Link
						to="/ai-scoring-guide"
						className="mt-5 inline-flex items-center gap-2 text-sm font-medium text-amber-200 underline-offset-4 transition hover:text-white hover:underline"
					>
						Đọc hướng dẫn chi tiết về AI scoring
						<HugeiconsIcon icon={ArrowDown01Icon} className="size-4 rotate-[-90deg]" />
					</Link>
				</AnimSection>
			</div>
		</section>
	)
}

function SkillsSection() {
	return (
		<section className="bg-muted/20">
			<div className="mx-auto max-w-5xl px-6 py-20">
				<AnimSection>
					<Heading
						title="4 kỹ năng, nhưng thông điệp cụ thể hơn"
						sub="Mỗi kỹ năng đều được mô tả bằng dạng bài và kiểu phản hồi thực tế để người học hiểu ngay giá trị sản phẩm."
					/>
				</AnimSection>
				<div className="mt-12 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
					{SKILLS.map((skill, index) => (
						<AnimSection key={skill.key} delay={index * 100}>
							<TiltCard className="rounded-2xl bg-background p-6 shadow-sm ring-1 ring-border/60">
								<div className={cn("mb-4 inline-flex rounded-xl p-3", skill.color)}>
									<HugeiconsIcon icon={skill.icon} className="size-6" />
								</div>
								<h3 className="font-bold">{skill.label}</h3>
								<p className="mt-2 text-sm leading-relaxed text-muted-foreground">{skill.desc}</p>
							</TiltCard>
						</AnimSection>
					))}
				</div>
			</div>
		</section>
	)
}

function ExerciseTypesSection() {
	return (
		<section className="bg-background">
			<div className="mx-auto max-w-5xl px-6 py-20">
				<AnimSection>
					<Heading
						title="Cụ thể bạn sẽ luyện gì?"
						sub="Đây là phần giúp landing page chuyển từ mô tả mơ hồ sang giá trị dễ hình dung ngay cho người học."
					/>
				</AnimSection>
				<div className="mt-12 grid gap-4 lg:grid-cols-2">
					{EXERCISE_EXAMPLES.map((item, index) => (
						<AnimSection key={item.skill} delay={index * 120}>
							<div className="rounded-2xl border bg-card p-6">
								<p className="text-sm font-semibold text-primary">{item.skill}</p>
								<h3 className="mt-2 text-lg font-bold">{item.title}</h3>
								<div className="mt-4 flex flex-wrap gap-2">
									{item.examples.map((example) => (
										<span
											key={example}
											className="rounded-full border px-3 py-1 text-xs text-muted-foreground"
										>
											{example}
										</span>
									))}
								</div>
							</div>
						</AnimSection>
					))}
				</div>
			</div>
		</section>
	)
}

function HowItWorksSection() {
	return (
		<section id="how-it-works" className="bg-muted/20 py-20">
			<div className="mx-auto max-w-5xl px-6">
				<AnimSection>
					<Heading
						title="AI scoring nên được giải thích như thế nào?"
						sub="Không hứa quá mức. Chỉ nói rõ chu trình thật mà người học sẽ trải nghiệm khi dùng sản phẩm."
					/>
				</AnimSection>
				<div className="mt-12 grid gap-4 lg:grid-cols-3">
					{STEPS.map((step, index) => (
						<AnimSection key={step.num} delay={index * 120}>
							<div className="flex h-full flex-col overflow-hidden rounded-3xl bg-gradient-to-b from-[#001656] to-[#0172FA] p-5 text-white">
								<p className="text-sm font-semibold text-white/60">Bước {step.num}</p>
								<h3 className="mt-3 text-xl font-bold">{step.title}</h3>
								<p className="mt-3 text-sm leading-relaxed text-white/72">{step.desc}</p>
								<div className="mt-5 overflow-hidden rounded-2xl bg-white/8">
									<img
										src={step.image}
										alt={step.title}
										className="aspect-video w-full object-cover"
									/>
								</div>
							</div>
						</AnimSection>
					))}
				</div>
			</div>
		</section>
	)
}

function ScoringTrustSection() {
	return (
		<section className="bg-background py-20">
			<div className="mx-auto max-w-5xl px-6">
				<AnimSection>
					<Heading
						title="Giải thích rõ về độ tin cậy"
						sub="Người học cần biết AI mạnh ở đâu, giới hạn ở đâu, và vì sao điều đó vẫn tạo ra giá trị lớn cho quá trình luyện thi."
					/>
				</AnimSection>
				<div className="mt-12 grid gap-4 md:grid-cols-2">
					{TRUST_POINTS.map((item, index) => (
						<AnimSection key={item.title} delay={index * 100}>
							<div className="rounded-2xl bg-muted/20 p-6">
								<h3 className="text-base font-bold">{item.title}</h3>
								<p className="mt-3 text-sm leading-relaxed text-muted-foreground">{item.desc}</p>
							</div>
						</AnimSection>
					))}
				</div>
			</div>
		</section>
	)
}

function GuideSection() {
	return (
		<section className="bg-muted/20 py-20">
			<div className="mx-auto max-w-4xl px-6">
				<AnimSection>
					<div className="rounded-3xl border bg-card p-8 text-center shadow-sm sm:p-10">
						<p className="text-sm font-semibold tracking-[0.2em] text-primary uppercase">Guide</p>
						<h2 className="mt-4 text-2xl font-bold lg:text-3xl">
							Hướng dẫn riêng về cách dùng AI scoring
						</h2>
						<p className="mx-auto mt-4 max-w-2xl text-sm leading-relaxed text-muted-foreground sm:text-base">
							Trang hướng dẫn mới tập trung vào Writing và Speaking: cách đọc feedback, ví dụ
							exercise types, speech analysis đang đo gì, và vì sao kết quả AI nên được hiểu là phản
							hồi nhanh chứ không phải thay thế hoàn toàn instructor.
						</p>
						<Button size="lg" className="mt-6 rounded-full px-8" asChild>
							<Link to="/ai-scoring-guide">Mở hướng dẫn AI scoring</Link>
						</Button>
					</div>
				</AnimSection>
			</div>
		</section>
	)
}

function RoadmapSection() {
	return (
		<section className="overflow-hidden bg-background">
			<div className="mx-auto max-w-2xl px-6 py-20">
				<AnimSection>
					<Heading
						title="Lộ trình từ B1 đến C1"
						sub="Các level card giờ cũng cụ thể hơn ở loại kỹ năng và task mà người học cần chinh phục."
					/>
				</AnimSection>
				<div className="relative mt-16">
					<div className="absolute bottom-0 left-5 top-0 w-0.5 bg-border" />
					<div className="flex flex-col gap-0">
						{BANDS.map((band, index) => (
							<AnimSection key={band.level} delay={index * 140}>
								<div className="relative flex items-stretch">
									<div className="relative z-10 flex w-10 shrink-0 flex-col items-center">
										<div className="mt-6 flex size-10 items-center justify-center rounded-full border-2 border-foreground/20 bg-background text-sm font-bold">
											{band.level}
										</div>
										{index < BANDS.length - 1 && <div className="flex-1" />}
									</div>
									<div className="mt-10 w-6 border-t-2 border-dashed border-foreground/15" />
									<div className="mb-6 flex-1 rounded-xl border bg-card p-5">
										<p className="font-bold">{band.label}</p>
										<p className="mt-1 text-sm leading-relaxed text-muted-foreground">
											{band.desc}
										</p>
										<div className="mt-3 flex flex-wrap gap-1.5">
											{band.skills.map((skill) => (
												<span
													key={skill}
													className="rounded-md border px-2.5 py-1 text-xs text-muted-foreground"
												>
													{skill}
												</span>
											))}
										</div>
									</div>
								</div>
							</AnimSection>
						))}
					</div>
				</div>
			</div>
		</section>
	)
}

function TestimonialsSection() {
	return (
		<section className="mx-auto max-w-5xl px-6 py-20">
			<AnimSection>
				<Heading
					title="Người học và giảng viên cần nghe điều gì?"
					sub="Thông điệp tin cậy hơn khi testimonial nói về giá trị thật: nhanh, rõ, dễ dùng và không thổi phồng vai trò AI."
				/>
			</AnimSection>
			<div className="mt-12 grid gap-4 sm:grid-cols-3">
				{TESTIMONIALS.map((item, index) => (
					<AnimSection key={item.name} delay={index * 120}>
						<div className="rounded-2xl bg-muted/30 p-6">
							<div className="flex items-center gap-3">
								<Avatar size="lg">
									<AvatarImage src={item.avatar} alt={item.name} />
									<AvatarFallback className="bg-primary/10 text-primary">
										{item.initials}
									</AvatarFallback>
								</Avatar>
								<div>
									<p className="text-sm font-bold">{item.name}</p>
									<p className="text-xs text-muted-foreground">{item.role}</p>
									<div className="mt-0.5 flex gap-0.5">
										{Array.from({ length: 5 }).map((_, starIndex) => (
											<svg
												key={`${item.name}-${starIndex.toString()}`}
												className={cn(
													"size-3",
													starIndex < item.stars ? "text-amber-400" : "text-muted-foreground/25",
												)}
												viewBox="0 0 20 20"
												fill="currentColor"
												aria-hidden="true"
											>
												<path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
											</svg>
										))}
									</div>
								</div>
							</div>
							<p className="mt-4 text-sm leading-relaxed text-muted-foreground italic">
								"{item.quote}"
							</p>
							<div className="mt-4">
								<span className="rounded-full bg-success/10 px-3 py-1 text-xs font-bold text-success">
									{item.score}
								</span>
							</div>
						</div>
					</AnimSection>
				))}
			</div>
		</section>
	)
}

function CtaSection() {
	return (
		<section className="relative overflow-hidden rounded-t-3xl bg-muted/30 pt-8">
			<div className="mx-auto max-w-5xl px-6 text-center">
				<AnimSection className="mx-auto max-w-3xl">
					<h2 className="text-2xl font-bold tracking-tight lg:text-4xl">
						Bắt đầu luyện ngay, nhưng với kỳ vọng rõ ràng hơn
					</h2>
					<p className="mx-auto mt-4 max-w-2xl text-sm leading-relaxed text-foreground/75 sm:text-base">
						VSTEP Practice có thể tạo ra giá trị rất mạnh nếu sản phẩm nói rõ cho người học: bạn sẽ
						luyện những dạng bài gì, AI đang phân tích điều gì, và instructor vẫn giữ vai trò nào
						trong quá trình đánh giá.
					</p>
					<div className="mt-6 flex flex-col items-center justify-center gap-3 sm:flex-row">
						<Button size="lg" className="rounded-xl px-8 text-base" asChild>
							<Link to="/register">Tạo tài khoản miễn phí</Link>
						</Button>
						<Button size="lg" variant="outline" className="rounded-xl px-8 text-base" asChild>
							<Link to="/ai-scoring-guide">Xem hướng dẫn AI scoring</Link>
						</Button>
					</div>
				</AnimSection>

				<AnimSection delay={120} className="mt-10">
					<img
						src="/images/home-mascot.png"
						alt="Mascot VSTEP đang luyện đề cùng sách và máy chấm bài"
						className="mx-auto w-full max-w-4xl object-contain"
					/>
				</AnimSection>
			</div>
		</section>
	)
}

function Footer() {
	return (
		<footer className="mt-8">
			<div className="mx-auto flex max-w-5xl flex-col items-center gap-4 px-6 py-8 text-sm text-muted-foreground sm:flex-row sm:justify-between">
				<p>© 2026 VSTEP Practice</p>
				<nav className="flex gap-6">
					<Link to="/ai-scoring-guide" className="transition-colors hover:text-foreground">
						Hướng dẫn AI scoring
					</Link>
					<Link to="/" className="transition-colors hover:text-foreground">
						Về chúng tôi
					</Link>
				</nav>
			</div>
		</footer>
	)
}

function TiltCard({ children, className }: { children: React.ReactNode; className?: string }) {
	const ref = useTilt()

	return (
		<div ref={ref} className={cn("transition-transform duration-200 ease-out", className)}>
			{children}
		</div>
	)
}
