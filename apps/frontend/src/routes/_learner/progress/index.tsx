import {
	Book02Icon,
	Clock01Icon,
	HeadphonesIcon,
	Mic01Icon,
	PencilEdit02Icon,
	Target02Icon,
	Fire02Icon,
} from "@hugeicons/core-free-icons"
import type { IconSvgElement } from "@hugeicons/react"
import { HugeiconsIcon } from "@hugeicons/react"
import { createFileRoute, Link } from "@tanstack/react-router"
import { ActivityHeatmap } from "@/components/common/ActivityHeatmap"
import { DoughnutChart, DoughnutLegend } from "@/components/common/DoughnutChart"
import { SpiderChart } from "@/components/common/SpiderChart"
import { Avatar, AvatarFallback, AvatarImage } from "@/components/ui/avatar"
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs"
import { useActivity, useProgress, useSpiderChart } from "@/hooks/use-progress"
import { useUser } from "@/hooks/use-user"
import { user as getAuthUser } from "@/lib/auth"
import { avatarUrl, getInitials } from "@/lib/avatar"
import { cn } from "@/lib/utils"
import type { Skill, Trend } from "@/types/api"

export const Route = createFileRoute("/_learner/progress/")({
	component: ProgressOverviewPage,
})

const SKILLS: { key: Skill; label: string; icon: IconSvgElement }[] = [
	{ key: "listening", label: "Listening", icon: HeadphonesIcon },
	{ key: "reading", label: "Reading", icon: Book02Icon },
	{ key: "writing", label: "Writing", icon: PencilEdit02Icon },
	{ key: "speaking", label: "Speaking", icon: Mic01Icon },
]

const SKILL_COLORS: Record<Skill, string> = {
	listening: "var(--skill-listening)",
	reading: "var(--skill-reading)",
	writing: "var(--skill-writing)",
	speaking: "var(--skill-speaking)",
}

const skillColor: Record<Skill, string> = {
	listening: "bg-skill-listening/15 text-skill-listening",
	reading: "bg-skill-reading/15 text-skill-reading",
	writing: "bg-skill-writing/15 text-skill-writing",
	speaking: "bg-skill-speaking/15 text-skill-speaking",
}

const skillColorText: Record<Skill, string> = {
	listening: "text-skill-listening",
	reading: "text-skill-reading",
	writing: "text-skill-writing",
	speaking: "text-skill-speaking",
}

const skillBarBg: Record<Skill, string> = {
	listening: "bg-skill-listening",
	reading: "bg-skill-reading",
	writing: "bg-skill-writing",
	speaking: "bg-skill-speaking",
}

const trendDisplay: Record<Trend, { text: string; className: string }> = {
	improving: { text: "↑ Đang tiến bộ", className: "text-success" },
	stable: { text: "→ Ổn định", className: "text-muted-foreground" },
	declining: { text: "↓ Giảm", className: "text-destructive" },
	inconsistent: { text: "~ Không đều", className: "text-warning" },
	insufficient_data: { text: "— Chưa đủ dữ liệu", className: "text-muted-foreground" },
}

function ProgressOverviewPage() {
	const currentUser = getAuthUser()
	const { data: userData } = useUser(currentUser?.id ?? "")
	const spider = useSpiderChart()
	const progress = useProgress()
	const activity = useActivity(90)

	const isLoading = spider.isLoading || progress.isLoading || activity.isLoading
	const error = spider.error || progress.error || activity.error

	if (isLoading) {
		return <p className="py-10 text-center text-muted-foreground">Đang tải...</p>
	}

	if (error) {
		return <p className="py-10 text-center text-destructive">Lỗi: {error.message}</p>
	}

	const spiderData = spider.data
	const progressData = progress.data
	const activityData = activity.data

	const initials = getInitials(currentUser?.fullName, currentUser?.email)
	const avatarSrc = avatarUrl(userData?.avatarKey, currentUser?.fullName)

	return (
		<div className="space-y-6">
			{/* Profile Header */}
			<div className="relative overflow-hidden rounded-2xl bg-gradient-to-r from-primary to-primary/80 px-8 py-8">
				<div className="relative z-10 flex items-center gap-5">
					<Avatar className="size-16 border-2 border-white/30 shadow-lg">
						<AvatarImage src={avatarSrc} alt={currentUser?.fullName ?? "Avatar"} />
						<AvatarFallback className="bg-white/20 text-lg font-bold text-white">
							{initials}
						</AvatarFallback>
					</Avatar>
					<div>
						<h1 className="text-2xl font-bold text-white">
							Hi, {currentUser?.fullName ?? "Bạn"}
						</h1>
						<p className="mt-1 text-sm text-white/80">
							Hãy tiếp tục học mỗi ngày — nỗ lực của bạn sẽ được đền đáp!
						</p>
					</div>
				</div>
				{/* Decorative circles */}
				<div className="absolute -top-8 -right-8 size-32 rounded-full bg-white/5" />
				<div className="absolute -bottom-4 -right-4 size-20 rounded-full bg-white/5" />
			</div>

			{/* Tabs */}
			<Tabs defaultValue="overview">
				<TabsList variant="line" className="w-full">
					<TabsTrigger value="overview" className="flex-1">Tổng Quát</TabsTrigger>
					<TabsTrigger value="test-practice" className="flex-1">Test Practice</TabsTrigger>
				</TabsList>

				<TabsContent value="overview" className="mt-6 space-y-6">
					<OverviewTab
						spiderData={spiderData}
						progressData={progressData}
						activityData={activityData}
					/>
				</TabsContent>

				<TabsContent value="test-practice" className="mt-6 space-y-6">
					<TestPracticeTab spiderData={spiderData} progressData={progressData} />
				</TabsContent>
			</Tabs>
		</div>
	)
}

// ---------- Overview Tab ----------

function OverviewTab({
	spiderData,
	progressData,
	activityData,
}: {
	spiderData: ReturnType<typeof useSpiderChart>["data"]
	progressData: ReturnType<typeof useProgress>["data"]
	activityData: ReturnType<typeof useActivity>["data"]
}) {
	const totalTests = progressData?.skills.reduce((s, sk) => s + sk.attemptCount, 0) ?? 0
	const studyMinutes = activityData?.totalStudyTimeMinutes ?? 0
	const studyLabel =
		studyMinutes >= 60
			? `${Math.floor(studyMinutes / 60)} giờ ${studyMinutes % 60 > 0 ? `${studyMinutes % 60} phút` : ""}`
			: `${studyMinutes} phút`

	return (
		<>
			{/* Stats Row */}
			<div className="grid grid-cols-2 gap-4 lg:grid-cols-4">
				<StatCard
					icon={Clock01Icon}
					iconBg="bg-primary/10 text-primary"
					label="Tổng thời lượng"
					value={studyLabel}
					valueColor="text-primary"
				/>
				<StatCard
					icon={Target02Icon}
					iconBg="bg-warning/10 text-warning"
					label="Tổng bài tập"
					value={String(activityData?.totalExercises ?? 0)}
					valueColor="text-warning"
				/>
				<StatCard
					icon={PencilEdit02Icon}
					iconBg="bg-destructive/10 text-destructive"
					label="Tổng số bài test"
					value={String(totalTests)}
					valueColor="text-destructive"
				/>
				<StatCard
					icon={Fire02Icon}
					iconBg="bg-success/10 text-success"
					label="Streak"
					value={`${activityData?.streak ?? 0} ngày`}
					valueColor="text-success"
				/>
			</div>

			{/* Spider Chart + Doughnut Chart */}
			<div className="grid gap-6 md:grid-cols-2">
				<SpiderChartCard spiderData={spiderData} />
				<DoughnutChartCard progressData={progressData} />
			</div>

			{/* Activity Heatmap */}
			<ActivityHeatmap activeDays={activityData?.activeDays ?? []} />
		</>
	)
}

// ---------- Test Practice Tab ----------

function TestPracticeTab({
	spiderData,
	progressData,
}: {
	spiderData: ReturnType<typeof useSpiderChart>["data"]
	progressData: ReturnType<typeof useProgress>["data"]
}) {
	return (
		<>
			<div className="grid gap-6 md:grid-cols-2">
				<SpiderChartCard spiderData={spiderData} />
				<DoughnutChartCard progressData={progressData} />
			</div>
			<SkillBars
				skills={spiderData?.skills ?? ({} as Record<Skill, { current: number; trend: Trend }>)}
			/>
		</>
	)
}

// ---------- Shared Components ----------

function StatCard({
	icon,
	iconBg,
	label,
	value,
	valueColor,
}: {
	icon: IconSvgElement
	iconBg: string
	label: string
	value: string
	valueColor: string
}) {
	return (
		<div className="rounded-xl border bg-card p-4">
			<div className="flex items-center gap-3">
				<div className={cn("flex size-10 items-center justify-center rounded-xl", iconBg)}>
					<HugeiconsIcon icon={icon} className="size-5" />
				</div>
				<div>
					<p className="text-sm text-muted-foreground">{label}</p>
					<p className={cn("text-lg font-bold", valueColor)}>{value}</p>
				</div>
			</div>
		</div>
	)
}

function SpiderChartCard({
	spiderData,
}: { spiderData: ReturnType<typeof useSpiderChart>["data"] }) {
	const spiderSkills = spiderData
		? SKILLS.map(({ key, label }) => ({
				label,
				value: spiderData.skills[key]?.current ?? 0,
				color: skillColorText[key],
			}))
		: []

	if (spiderSkills.length === 0) return null

	return (
		<div className="rounded-xl border bg-card p-5">
			<h3 className="text-lg font-semibold">Điểm trung bình theo kỹ năng</h3>
			<p className="mb-4 text-sm text-muted-foreground">trong Test Practice</p>
			<div className="flex justify-center">
				<SpiderChart skills={spiderSkills} className="size-64" />
			</div>
		</div>
	)
}

function DoughnutChartCard({
	progressData,
}: { progressData: ReturnType<typeof useProgress>["data"] }) {
	const segments = SKILLS.map(({ key, label }) => {
		const sk = progressData?.skills.find((s) => s.skill === key)
		return {
			label,
			value: sk?.attemptCount ?? 0,
			color: SKILL_COLORS[key],
		}
	})
	const total = segments.reduce((s, seg) => s + seg.value, 0)

	return (
		<div className="rounded-xl border bg-card p-5">
			<h3 className="text-lg font-semibold">Tổng số bài test đã hoàn thành</h3>
			<p className="mb-4 text-sm text-muted-foreground">trong Test Practice</p>
			<DoughnutChart
				segments={segments}
				centerLabel="Tổng số bài test"
				centerValue={total}
			/>
			<DoughnutLegend segments={segments} className="mt-4 justify-center" />
		</div>
	)
}

function SkillBars({
	skills,
}: { skills: Record<Skill, { current: number; trend: Trend }> }) {
	return (
		<div className="space-y-4">
			{SKILLS.map(({ key, label, icon }) => {
				const data = skills[key]
				if (!data) return null
				const pct = Math.min(100, (data.current / 10) * 100)
				const trend = trendDisplay[data.trend]

				return (
					<Link
						key={key}
						to="/progress/$skill"
						params={{ skill: key }}
						className="block rounded-xl border bg-card p-4 transition-colors hover:bg-muted/50"
					>
						<div className="mb-2 flex items-center justify-between">
							<div className="flex items-center gap-3">
								<div
									className={cn(
										"flex size-9 items-center justify-center rounded-lg",
										skillColor[key],
									)}
								>
									<HugeiconsIcon icon={icon} className="size-5" />
								</div>
								<span className="font-medium">{label}</span>
							</div>
							<div className="flex items-center gap-3">
								<span className={cn("text-sm", trend.className)}>{trend.text}</span>
								<span className="font-bold tabular-nums">{data.current.toFixed(1)}</span>
							</div>
						</div>
						<div className="h-2 overflow-hidden rounded-full bg-muted">
							<div
								className={cn("h-full rounded-full transition-all", skillBarBg[key])}
								style={{ width: `${pct}%` }}
							/>
						</div>
					</Link>
				)
			})}
		</div>
	)
}
