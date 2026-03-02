import {
	AnalyticsUpIcon,
	Book02Icon,
	Clock01Icon,
	DocumentValidationIcon,
	HeadphonesIcon,
	Mic01Icon,
	PencilEdit02Icon,
	Target02Icon,
	TestTube01Icon,
} from "@hugeicons/core-free-icons"
import type { IconSvgElement } from "@hugeicons/react"
import { HugeiconsIcon } from "@hugeicons/react"
import { createFileRoute, Link } from "@tanstack/react-router"
import { Button } from "@/components/ui/button"
import { cn } from "@/lib/utils"

export const Route = createFileRoute("/_learner/dashboard")({
	component: LearnerDashboard,
})

type SkillKey = "listening" | "reading" | "writing" | "speaking"

interface SkillSection {
	key: SkillKey
	label: string
	icon: IconSvgElement
	tests: { id: string; name: string; subtitle: string }[]
}

const skillColor: Record<SkillKey, string> = {
	listening: "bg-skill-listening/15 text-skill-listening",
	reading: "bg-skill-reading/15 text-skill-reading",
	writing: "bg-skill-writing/15 text-skill-writing",
	speaking: "bg-skill-speaking/15 text-skill-speaking",
}

const CONTINUE_TEST = { skill: "listening" as SkillKey, testId: "l2" }

const SKILLS: SkillSection[] = [
	{
		key: "listening",
		label: "Listening",
		icon: HeadphonesIcon,
		tests: [
			{ id: "l1", name: "Practice #11", subtitle: "45 câu • 35 phút" },
			{ id: "l2", name: "Practice #12", subtitle: "45 câu • 35 phút" },
			{ id: "l3", name: "Practice #13", subtitle: "45 câu • 35 phút" },
		],
	},
	{
		key: "reading",
		label: "Reading",
		icon: Book02Icon,
		tests: [
			{ id: "r1", name: "Đề 7", subtitle: "40 câu • 60 phút" },
			{ id: "r2", name: "Đề 8", subtitle: "40 câu • 60 phút" },
		],
	},
	{
		key: "writing",
		label: "Writing",
		icon: PencilEdit02Icon,
		tests: [
			{ id: "w1", name: "Task 1 - Thư", subtitle: "1 bài • 20 phút" },
			{ id: "w2", name: "Task 2 - Luận điểm", subtitle: "1 bài • 40 phút" },
		],
	},
	{
		key: "speaking",
		label: "Speaking",
		icon: Mic01Icon,
		tests: [
			{ id: "s1", name: "Part 1 - Giới thiệu", subtitle: "3 câu • 5 phút" },
			{ id: "s2", name: "Part 2 - Thảo luận", subtitle: "1 chủ đề • 10 phút" },
		],
	},
]

function LearnerDashboard() {
	const dailyGoal = 3
	const dailyDone = 2

	return (
		<div className="grid gap-10 lg:grid-cols-[1fr_300px]">
			{/* Left — content */}
			<div className="space-y-10">
				{/* Skill sections */}
				{SKILLS.map((section) => (
					<div key={section.key}>
						<div className="mb-4 flex items-center gap-3">
							<div
								className={cn(
									"flex size-9 items-center justify-center rounded-lg",
									skillColor[section.key],
								)}
							>
								<HugeiconsIcon icon={section.icon} className="size-5" />
							</div>
							<h2 className="text-lg font-bold">{section.label}</h2>
						</div>
						<div className="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
							{section.tests.map((test) => {
								const isContinue =
									section.key === CONTINUE_TEST.skill && test.id === CONTINUE_TEST.testId
								return (
									<Link
										key={test.id}
										to="/dashboard"
										className={cn(
											"group relative rounded-xl p-4 transition-colors",
											isContinue
												? "bg-primary/5 ring-1 ring-primary/20 hover:bg-primary/10"
												: "bg-muted/30 hover:bg-muted/50",
										)}
									>
										<p className="font-medium">{test.name}</p>
										<p className="mt-1 text-sm text-muted-foreground">{test.subtitle}</p>
										{isContinue && (
											<div className="mt-3 flex items-center justify-between">
												<div className="h-1.5 flex-1 rounded-full bg-primary/15">
													<div className="h-1.5 rounded-full bg-primary" style={{ width: "67%" }} />
												</div>
												<span className="ml-3 shrink-0 text-xs font-medium text-primary">
													Tiếp tục
												</span>
											</div>
										)}
									</Link>
								)
							})}
						</div>
					</div>
				))}
			</div>

			{/* Right — sticky sidebar */}
			<div className="hidden space-y-6 lg:block">
				<div className="sticky top-24 space-y-6">
					{/* Daily goal */}
					<div className="rounded-2xl bg-muted/30 p-5">
						<div className="mb-3 flex items-center justify-between">
							<div className="flex items-center gap-2">
								<HugeiconsIcon icon={Target02Icon} className="size-4 text-primary" />
								<span className="text-sm font-medium">Mục tiêu hôm nay</span>
							</div>
							<span className="text-sm tabular-nums text-muted-foreground">
								{dailyDone}/{dailyGoal}
							</span>
						</div>
						<div className="h-2 rounded-full bg-background">
							<div
								className="h-2 rounded-full bg-primary transition-all"
								style={{ width: `${(dailyDone / dailyGoal) * 100}%` }}
							/>
						</div>
					</div>

					{/* Quick stats */}
					<div className="space-y-4 px-1">
						<div className="flex items-center gap-3">
							<div className="flex size-8 items-center justify-center rounded-lg bg-muted">
								<HugeiconsIcon icon={TestTube01Icon} className="size-4 text-muted-foreground" />
							</div>
							<p className="text-sm font-medium">47 bài đã làm</p>
						</div>
						<div className="flex items-center gap-3">
							<div className="flex size-8 items-center justify-center rounded-lg bg-muted">
								<HugeiconsIcon icon={Clock01Icon} className="size-4 text-muted-foreground" />
							</div>
							<p className="text-sm font-medium">23h tổng thời gian</p>
						</div>
					</div>

					{/* Quick actions */}
					<div className="space-y-2">
						<Button variant="outline" className="w-full justify-start gap-2">
							<HugeiconsIcon icon={DocumentValidationIcon} className="size-4" />
							Bắt đầu bài mới
						</Button>
						<Button variant="outline" className="w-full justify-start gap-2">
							<HugeiconsIcon icon={AnalyticsUpIcon} className="size-4" />
							Xem tiến độ
						</Button>
					</div>
				</div>
			</div>
		</div>
	)
}
