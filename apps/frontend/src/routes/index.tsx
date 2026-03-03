import {
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
import { useEffect, useRef, useState } from "react"
import { Logo } from "@/components/common/Logo"
import { SpiderChart } from "@/components/common/SpiderChart"
import { Button } from "@/components/ui/button"
import { cn } from "@/lib/utils"

export const Route = createFileRoute("/")({
	component: LandingPage,
})

/* ── hooks ── */

function useInView(threshold = 0.15) {
	const ref = useRef<HTMLDivElement>(null)
	const [visible, setVisible] = useState(false)
	useEffect(() => {
		const el = ref.current
		if (!el) return
		const obs = new IntersectionObserver(([e]) => e.isIntersecting && setVisible(true), {
			threshold,
		})
		obs.observe(el)
		return () => obs.disconnect()
	}, [threshold])
	return { ref, visible }
}

function useTilt(intensity = 8) {
	const ref = useRef<HTMLDivElement>(null)
	useEffect(() => {
		const el = ref.current
		if (!el) return
		const onMove = (e: MouseEvent) => {
			const rect = el.getBoundingClientRect()
			const x = ((e.clientX - rect.left) / rect.width - 0.5) * 2
			const y = ((e.clientY - rect.top) / rect.height - 0.5) * 2
			el.style.transform = `perspective(600px) rotateY(${x * intensity}deg) rotateX(${-y * intensity}deg) scale(1.02)`
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

/* ── data ── */

interface SkillItem {
	key: string
	label: string
	desc: string
	icon: IconSvgElement
	color: string
	chartColor: string
}

const SKILLS: SkillItem[] = [
	{
		key: "listening",
		label: "Listening",
		desc: "Luyện nghe hội thoại và bài giảng thực tế",
		icon: HeadphonesIcon,
		color: "bg-skill-listening/12 text-skill-listening",
		chartColor: "text-skill-listening",
	},
	{
		key: "reading",
		label: "Reading",
		desc: "Phân tích đoạn văn, tìm ý chính",
		icon: Book02Icon,
		color: "bg-skill-reading/12 text-skill-reading",
		chartColor: "text-skill-reading",
	},
	{
		key: "writing",
		label: "Writing",
		desc: "Viết luận và thư với phản hồi từ AI",
		icon: PencilEdit02Icon,
		color: "bg-skill-writing/12 text-skill-writing",
		chartColor: "text-skill-writing",
	},
	{
		key: "speaking",
		label: "Speaking",
		desc: "Luyện nói theo chủ đề, đánh giá phát âm",
		icon: Mic01Icon,
		color: "bg-skill-speaking/12 text-skill-speaking",
		chartColor: "text-skill-speaking",
	},
]

const DEMO_SCORES = [
	{ label: "Listening", value: 7.5, color: "text-skill-listening" },
	{ label: "Reading", value: 8, color: "text-skill-reading" },
	{ label: "Writing", value: 6, color: "text-skill-writing" },
	{ label: "Speaking", value: 6.5, color: "text-skill-speaking" },
]

const FEATURES = [
	{
		icon: Target02Icon,
		title: "Học tập tương tác",
		desc: "Bài học chia nhỏ, mỗi ngày chỉ cần 15 phút.",
	},
	{
		icon: CheckmarkCircle01Icon,
		title: "Thi thử sát thực tế",
		desc: "Đề thi đầy đủ 4 kỹ năng theo chuẩn VSTEP.",
	},
	{
		icon: Fire02Icon,
		title: "Streak & XP",
		desc: "Hệ thống streak giúp bạn duy trì động lực mỗi ngày.",
	},
]

const BANDS = [
	{ level: "B1", label: "Trung cấp", desc: "Giao tiếp trong công việc và đời sống" },
	{ level: "B2", label: "Trung cấp cao", desc: "Tự tin trong môi trường học thuật" },
	{ level: "C1", label: "Nâng cao", desc: "Sử dụng tiếng Anh linh hoạt, chính xác" },
]

const STATS = [
	{ value: "10,000+", label: "Học viên" },
	{ value: "500+", label: "Bài thi" },
	{ value: "4", label: "Kỹ năng" },
	{ value: "3", label: "Cấp độ" },
]

/* ── shared ── */

const fadeUp = "translate-y-8 opacity-0"
const fadeIn = "translate-y-0 opacity-100"

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
			<p className="mx-auto mt-3 max-w-lg text-muted-foreground">{sub}</p>
		</div>
	)
}

/* ── page ── */

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

/* ── header ── */

function Header() {
	return (
		<header className="sticky top-0 z-50 border-b border-border/40 bg-background/90 backdrop-blur-md">
			<div className="mx-auto flex h-14 max-w-5xl items-center justify-between px-6">
				<Link to="/">
					<Logo />
				</Link>
				<div className="flex items-center gap-2">
					<Button variant="ghost" size="sm" asChild>
						<Link to="/login">Đăng nhập</Link>
					</Button>
					<Button size="sm" className="rounded-xl" asChild>
						<Link to="/register">Bắt đầu</Link>
					</Button>
				</div>
			</div>
		</header>
	)
}

/* ── hero ── */

function Hero() {
	const chartRef = useTilt(6)

	return (
		<section className="relative overflow-hidden">
			<div className="mx-auto grid max-w-5xl items-center gap-12 px-6 py-24 lg:grid-cols-2 lg:gap-8">
				<AnimSection>
					<div className="space-y-6">
						<div className="inline-flex items-center gap-2 rounded-full bg-primary/8 px-4 py-1.5 text-sm font-medium text-primary">
							Miễn phí hoàn toàn
						</div>
						<h1 className="text-4xl font-bold leading-[1.15] tracking-tight lg:text-5xl">
							Chinh phục VSTEP
							<br />
							<span className="text-primary">theo cách hoàn toàn mới</span>
						</h1>
						<p className="max-w-md text-lg leading-relaxed text-muted-foreground">
							Luyện thi hiệu quả với bài học tương tác, đề thi thử sát thực tế và phản hồi tức thì
							từ AI.
						</p>
						<div className="flex flex-wrap gap-3 pt-2">
							<Button size="lg" className="rounded-xl px-8 text-base" asChild>
								<Link to="/register">Bắt đầu ngay</Link>
							</Button>
							<Button variant="outline" size="lg" className="rounded-xl px-8 text-base" asChild>
								<Link to="/login">Tôi đã có tài khoản</Link>
							</Button>
						</div>
					</div>
				</AnimSection>

				<AnimSection delay={200}>
					<div
						ref={chartRef}
						className="flex items-center justify-center transition-transform duration-200 ease-out"
					>
						<SpiderChart skills={DEMO_SCORES} className="size-72 lg:size-80" />
					</div>
				</AnimSection>
			</div>
		</section>
	)
}

/* ── skills ── */

function SkillsSection() {
	return (
		<section className="border-t border-border/40 bg-muted/20">
			<div className="mx-auto max-w-5xl px-6 py-20">
				<AnimSection>
					<Heading
						title="4 kỹ năng, một nền tảng"
						sub="Luyện tập toàn diện Nghe – Đọc – Viết – Nói"
					/>
				</AnimSection>
				<div className="mt-12 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
					{SKILLS.map((s, i) => (
						<AnimSection key={s.key} delay={i * 100}>
							<TiltCard className="rounded-2xl border border-border/40 bg-background p-6 transition-colors hover:border-border">
								<div className={cn("mb-4 inline-flex rounded-xl p-3", s.color)}>
									<HugeiconsIcon icon={s.icon} className="size-6" />
								</div>
								<h3 className="font-bold">{s.label}</h3>
								<p className="mt-2 text-sm leading-relaxed text-muted-foreground">{s.desc}</p>
							</TiltCard>
						</AnimSection>
					))}
				</div>
			</div>
		</section>
	)
}

/* ── features ── */

function FeaturesSection() {
	return (
		<section className="mx-auto max-w-5xl px-6 py-20">
			<AnimSection>
				<Heading
					title="Học thông minh hơn"
					sub="Phương pháp học được thiết kế dựa trên nghiên cứu khoa học"
				/>
			</AnimSection>
			<div className="mt-12 grid gap-5 sm:grid-cols-3">
				{FEATURES.map((f, i) => (
					<AnimSection key={f.title} delay={i * 120}>
						<TiltCard className="space-y-3 rounded-2xl border border-border/40 p-6">
							<div className="inline-flex rounded-xl bg-primary/10 p-3 text-primary">
								<HugeiconsIcon icon={f.icon} className="size-6" />
							</div>
							<h3 className="font-bold">{f.title}</h3>
							<p className="text-sm leading-relaxed text-muted-foreground">{f.desc}</p>
						</TiltCard>
					</AnimSection>
				))}
			</div>
		</section>
	)
}

/* ── roadmap ── */

function RoadmapSection() {
	return (
		<section className="border-t border-border/40 bg-muted/20">
			<div className="mx-auto max-w-5xl px-6 py-20">
				<AnimSection>
					<Heading
						title="Lộ trình rõ ràng"
						sub="Từ B1 đến C1 — mỗi cấp độ là một bước tiến cụ thể"
					/>
				</AnimSection>
				<div className="relative mx-auto mt-16 max-w-xl">
					<div className="absolute top-0 bottom-0 left-1/2 w-px -translate-x-1/2 bg-border" />
					<div className="flex flex-col gap-16">
						{BANDS.map((b, i) => {
							const right = i % 2 !== 0
							return (
								<AnimSection key={b.level} delay={i * 150}>
									<div className="relative flex items-center">
										<div className={cn("flex-1 pr-10 text-right", right && "invisible")}>
											<p className="font-bold">{b.label}</p>
											<p className="text-sm text-muted-foreground">{b.desc}</p>
										</div>
										<div className="relative z-10 flex size-14 shrink-0 items-center justify-center rounded-2xl border-2 border-primary bg-background transition-transform duration-300 hover:scale-110">
											<span className="text-lg font-bold text-primary">{b.level}</span>
										</div>
										<div className={cn("flex-1 pl-10", !right && "invisible")}>
											<p className="font-bold">{b.label}</p>
											<p className="text-sm text-muted-foreground">{b.desc}</p>
										</div>
									</div>
								</AnimSection>
							)
						})}
					</div>
				</div>
			</div>
		</section>
	)
}

/* ── stats ── */

function StatsSection() {
	return (
		<section className="mx-auto max-w-5xl px-6 py-16">
			<div className="grid grid-cols-2 gap-8 sm:grid-cols-4">
				{STATS.map((s, i) => (
					<AnimSection key={s.label} delay={i * 80}>
						<div className="text-center transition-transform duration-300 hover:scale-105">
							<p className="text-3xl font-bold tabular-nums text-primary">{s.value}</p>
							<p className="mt-1 text-sm text-muted-foreground">{s.label}</p>
						</div>
					</AnimSection>
				))}
			</div>
		</section>
	)
}

/* ── cta ── */

function CtaSection() {
	return (
		<section className="border-t border-border/40 bg-muted/20">
			<div className="mx-auto max-w-5xl px-6 py-20 text-center">
				<AnimSection>
					<h2 className="text-2xl font-bold lg:text-3xl">Sẵn sàng chinh phục VSTEP?</h2>
					<p className="mx-auto mt-3 max-w-md text-muted-foreground">
						Tham gia cùng hàng nghìn học viên đang luyện thi mỗi ngày.
					</p>
					<Button size="lg" className="mt-8 rounded-xl px-10 text-base" asChild>
						<Link to="/register">Bắt đầu học miễn phí</Link>
					</Button>
				</AnimSection>
			</div>
		</section>
	)
}

/* ── footer ── */

function Footer() {
	return (
		<footer className="border-t border-border/40">
			<div className="mx-auto flex max-w-5xl flex-col items-center gap-4 px-6 py-8 sm:flex-row sm:justify-between">
				<p className="text-sm text-muted-foreground">© 2026 VSTEP Practice</p>
				<nav className="flex gap-6 text-sm text-muted-foreground">
					<Link to="/" className="transition-colors hover:text-foreground">
						Về chúng tôi
					</Link>
					<Link to="/" className="transition-colors hover:text-foreground">
						Điều khoản
					</Link>
				</nav>
			</div>
		</footer>
	)
}

/* ── tilt card ── */

function TiltCard({ children, className }: { children: React.ReactNode; className?: string }) {
	const ref = useTilt(5)
	return (
		<div ref={ref} className={cn("transition-transform duration-200 ease-out", className)}>
			{children}
		</div>
	)
}
