import {
	Award02Icon,
	Book02Icon,
	CheckmarkCircle01Icon,
	Fire02Icon,
	HeadphonesIcon,
	Mic01Icon,
	PencilEdit02Icon,
	Target02Icon,
} from "@hugeicons/core-free-icons"
import type { IconSvgElement } from "@hugeicons/react"
import { HugeiconsIcon } from "@hugeicons/react"
import { createFileRoute, Link } from "@tanstack/react-router"
import { Button } from "@/components/ui/button"
import { cn } from "@/lib/utils"

export const Route = createFileRoute("/")({
	component: LandingPage,
})

/* ---------- data ---------- */

interface Skill {
	key: string
	label: string
	description: string
	icon: IconSvgElement
	color: string
}

const SKILLS: Skill[] = [
	{
		key: "listening",
		label: "Listening",
		description: "Luyện nghe với các đoạn hội thoại và bài giảng thực tế",
		icon: HeadphonesIcon,
		color: "bg-skill-listening/12 text-skill-listening",
	},
	{
		key: "reading",
		label: "Reading",
		description: "Phân tích đoạn văn, tìm ý chính và chi tiết quan trọng",
		icon: Book02Icon,
		color: "bg-skill-reading/12 text-skill-reading",
	},
	{
		key: "writing",
		label: "Writing",
		description: "Viết thư, luận điểm với phản hồi chi tiết từ AI",
		icon: PencilEdit02Icon,
		color: "bg-skill-writing/12 text-skill-writing",
	},
	{
		key: "speaking",
		label: "Speaking",
		description: "Luyện nói theo chủ đề, nhận đánh giá phát âm tức thì",
		icon: Mic01Icon,
		color: "bg-skill-speaking/12 text-skill-speaking",
	},
]

interface Feature {
	icon: IconSvgElement
	title: string
	description: string
}

const FEATURES: Feature[] = [
	{
		icon: Target02Icon,
		title: "Học tập tương tác",
		description: "Bài học được chia nhỏ, dễ tiếp thu. Mỗi ngày chỉ cần 15 phút.",
	},
	{
		icon: CheckmarkCircle01Icon,
		title: "Thi thử sát thực tế",
		description: "Đề thi mô phỏng đầy đủ 4 kỹ năng theo chuẩn định dạng VSTEP.",
	},
	{
		icon: Fire02Icon,
		title: "Streak & điểm XP",
		description: "Hệ thống streak và điểm kinh nghiệm giúp bạn duy trì động lực.",
	},
]

interface Band {
	level: string
	label: string
	description: string
}

const BANDS: Band[] = [
	{ level: "B1", label: "Trung cấp", description: "Giao tiếp cơ bản trong công việc và đời sống" },
	{
		level: "B2",
		label: "Trung cấp cao",
		description: "Tự tin trong môi trường học thuật và nghề nghiệp",
	},
	{
		level: "C1",
		label: "Nâng cao",
		description: "Sử dụng tiếng Anh linh hoạt, chính xác và hiệu quả",
	},
]

const STATS = [
	{ value: "10,000+", label: "Học viên" },
	{ value: "500+", label: "Bài thi" },
	{ value: "4", label: "Kỹ năng" },
	{ value: "3", label: "Cấp độ" },
]

/* ---------- page ---------- */

function LandingPage() {
	return (
		<div className="min-h-screen text-foreground">
			<Header />
			<Hero />
			<SkillsSection />
			<FeaturesSection />
			<RoadmapSection />
			<StatsSection />
			<CtaSection />
			<Footer />
		</div>
	)
}

/* ---------- header ---------- */

function Header() {
	return (
		<header className="sticky top-0 z-50 border-b border-border/50 bg-background/95 backdrop-blur-sm">
			<div className="mx-auto flex h-14 max-w-5xl items-center justify-between px-6">
				{/* <!-- Logo placeholder --> */}
				<Link to="/" className="text-lg font-bold tracking-tight">
					VSTEP
				</Link>
				<div className="flex items-center gap-2">
					<Button variant="ghost" size="sm" asChild>
						<Link to="/login">Đăng nhập</Link>
					</Button>
					<Button size="sm" asChild>
						<Link to="/register">Bắt đầu</Link>
					</Button>
				</div>
			</div>
		</header>
	)
}

/* ---------- hero ---------- */

function Hero() {
	return (
		<section className="relative overflow-hidden bg-background">
			<div className="mx-auto grid min-h-[85vh] max-w-5xl items-center gap-8 px-6 py-20 lg:grid-cols-2 lg:gap-0">
				<div className="space-y-6">
					<h1 className="text-4xl font-bold leading-tight tracking-tight lg:text-5xl">
						Chinh phục VSTEP
						<br />
						<span className="text-primary">theo cách hoàn toàn mới</span>
					</h1>
					<p className="max-w-md text-lg text-muted-foreground">
						Luyện thi hiệu quả với bài học tương tác, đề thi thử sát thực tế và phản hồi tức thì từ
						AI.
					</p>
					<div className="flex flex-wrap gap-3">
						<Button size="lg" className="rounded-xl px-8 text-base" asChild>
							<Link to="/register">Bắt đầu ngay</Link>
						</Button>
						<Button variant="outline" size="lg" className="rounded-xl px-8 text-base" asChild>
							<Link to="/login">Tôi đã có tài khoản</Link>
						</Button>
					</div>
				</div>

				{/* <!-- Hero illustration placeholder --> */}
				<div className="hidden aspect-square w-full max-w-md items-center justify-center rounded-3xl bg-muted/30 lg:flex">
					<span className="text-sm text-muted-foreground">Illustration</span>
				</div>
			</div>
		</section>
	)
}

/* ---------- skills ---------- */

function SkillsSection() {
	return (
		<section className="bg-primary/[0.03]">
			<div className="mx-auto max-w-5xl px-6 py-20">
				<SectionHeading
					title="4 kỹ năng, một nền tảng"
					subtitle="Luyện tập toàn diện Nghe – Đọc – Viết – Nói trên cùng một hệ thống"
				/>
				<div className="mt-12 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
					{SKILLS.map((skill) => (
						<div
							key={skill.key}
							className="rounded-2xl border border-border/50 bg-background p-6 transition-colors hover:border-border"
						>
							<div className={cn("mb-4 inline-flex rounded-xl p-3", skill.color)}>
								<HugeiconsIcon icon={skill.icon} className="size-6" />
							</div>
							<h3 className="text-base font-bold">{skill.label}</h3>
							<p className="mt-2 text-sm leading-relaxed text-muted-foreground">
								{skill.description}
							</p>
						</div>
					))}
				</div>
			</div>
		</section>
	)
}

/* ---------- features ---------- */

function FeaturesSection() {
	return (
		<section>
			<SectionHeading
				title="Học thông minh hơn, không phải nhiều hơn"
				subtitle="Phương pháp học được thiết kế dựa trên nghiên cứu khoa học"
			/>
			<div className="mt-12 grid gap-6 sm:grid-cols-3">
				{FEATURES.map((feature) => (
					<div key={feature.title} className="space-y-3 rounded-2xl border border-border/50 p-6">
						<div className="inline-flex rounded-xl bg-primary/10 p-3 text-primary">
							<HugeiconsIcon icon={feature.icon} className="size-6" />
						</div>
						<h3 className="text-base font-bold">{feature.title}</h3>
						<p className="text-sm leading-relaxed text-muted-foreground">{feature.description}</p>
					</div>
				))}
			</div>
		</section>
	)
}

/* ---------- roadmap / bands ---------- */

function RoadmapSection() {
	return (
		<section className="bg-muted/30">
			<div className="mx-auto max-w-5xl px-6 py-20">
				<SectionHeading
					title="Lộ trình rõ ràng"
					subtitle="Từ B1 đến C1 — mỗi cấp độ là một bước tiến cụ thể"
				/>
				<div className="relative mx-auto mt-16 max-w-2xl">
					{/* vertical connector line */}
					<div className="absolute top-0 bottom-0 left-1/2 w-px -translate-x-1/2 bg-border" />

					<div className="relative flex flex-col gap-16">
						{BANDS.map((band, i) => {
							const alignRight = i % 2 !== 0
							return (
								<div key={band.level} className="relative flex items-center">
									{/* left side */}
									<div className={cn("flex-1 pr-10 text-right", alignRight && "invisible")}>
										<p className="font-bold">{band.label}</p>
										<p className="text-sm text-muted-foreground">{band.description}</p>
									</div>

									{/* center node */}
									<div className="relative z-10 flex size-14 shrink-0 items-center justify-center rounded-2xl border-2 border-primary bg-background">
										<span className="text-lg font-bold text-primary">{band.level}</span>
									</div>

									{/* right side */}
									<div className={cn("flex-1 pl-10", !alignRight && "invisible")}>
										<p className="font-bold">{band.label}</p>
										<p className="text-sm text-muted-foreground">{band.description}</p>
									</div>
								</div>
							)
						})}
					</div>
				</div>
			</div>
		</section>
	)
}

/* ---------- stats ---------- */

function StatsSection() {
	return (
		<section className="bg-primary/[0.03]">
			<div className="grid grid-cols-2 gap-6 sm:grid-cols-4">
				{STATS.map((stat) => (
					<div key={stat.label} className="text-center">
						<p className="text-3xl font-bold tabular-nums text-primary">{stat.value}</p>
						<p className="mt-1 text-sm text-muted-foreground">{stat.label}</p>
					</div>
				))}
			</div>
		</section>
	)
}

/* ---------- final CTA ---------- */

function CtaSection() {
	return (
		<section className="bg-muted/30">
			<div className="mx-auto max-w-5xl px-6 py-20 text-center">
				<HugeiconsIcon icon={Award02Icon} className="mx-auto mb-4 size-10 text-primary" />
				<h2 className="text-2xl font-bold lg:text-3xl">Sẵn sàng chinh phục VSTEP?</h2>
				<p className="mx-auto mt-3 max-w-md text-muted-foreground">
					Tham gia cùng hàng nghìn học viên đang luyện thi mỗi ngày. Hoàn toàn miễn phí.
				</p>
				<Button size="lg" className="mt-8 rounded-xl px-10 text-base" asChild>
					<Link to="/register">Bắt đầu học miễn phí</Link>
				</Button>
			</div>
		</section>
	)
}

/* ---------- footer ---------- */

function Footer() {
	return (
		<footer className="border-t border-border/50">
			<div className="mx-auto flex max-w-5xl flex-col items-center gap-4 px-6 py-8 sm:flex-row sm:justify-between">
				<p className="text-sm text-muted-foreground">© 2026 VSTEP Practice</p>
				<nav className="flex gap-6 text-sm text-muted-foreground">
					<Link to="/" className="transition-colors hover:text-foreground">
						Về chúng tôi
					</Link>
					<Link to="/" className="transition-colors hover:text-foreground">
						Hướng dẫn
					</Link>
					<Link to="/" className="transition-colors hover:text-foreground">
						Điều khoản
					</Link>
				</nav>
			</div>
		</footer>
	)
}

/* ---------- shared ---------- */

interface SectionHeadingProps {
	title: string
	subtitle: string
}

function SectionHeading({ title, subtitle }: SectionHeadingProps) {
	return (
		<div className="text-center">
			<h2 className="text-2xl font-bold lg:text-3xl">{title}</h2>
			<p className="mx-auto mt-3 max-w-lg text-muted-foreground">{subtitle}</p>
		</div>
	)
}
