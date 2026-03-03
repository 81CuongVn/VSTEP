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
import { useQuery } from "@tanstack/react-query"
import { createFileRoute, Link } from "@tanstack/react-router"
import { Button } from "@/components/ui/button"
import { api } from "@/lib/api"
import { cn } from "@/lib/utils"
import type { Exam, PaginatedResponse, ProgressOverview, Skill } from "@/types/api"

export const Route = createFileRoute("/_learner/dashboard")({
	component: LearnerDashboard,
})

interface BlueprintSection {
	questionIds: string[]
}

type ExamBlueprint = Partial<Record<Skill, BlueprintSection>> & {
	durationMinutes?: number
}

const skillMeta: Record<Skill, { label: string; icon: IconSvgElement }> = {
	listening: { label: "Listening", icon: HeadphonesIcon },
	reading: { label: "Reading", icon: Book02Icon },
	writing: { label: "Writing", icon: PencilEdit02Icon },
	speaking: { label: "Speaking", icon: Mic01Icon },
}

const skillColor: Record<Skill, string> = {
	listening: "bg-skill-listening/15 text-skill-listening",
	reading: "bg-skill-reading/15 text-skill-reading",
	writing: "bg-skill-writing/15 text-skill-writing",
	speaking: "bg-skill-speaking/15 text-skill-speaking",
}

const SKILL_ORDER: Skill[] = ["listening", "reading", "writing", "speaking"]

function useProgress() {
	return useQuery({
		queryKey: ["progress"],
		queryFn: () => api.get<ProgressOverview>("/api/progress"),
	})
}

function useExams() {
	return useQuery({
		queryKey: ["exams"],
		queryFn: () => api.get<PaginatedResponse<Exam>>("/api/exams"),
	})
}

function getBlueprint(exam: Exam): ExamBlueprint {
	return exam.blueprint as ExamBlueprint
}

function ExamCard({ exam, skill }: { exam: Exam; skill: Skill }) {
	const bp = getBlueprint(exam)
	const section = bp[skill]
	const questionCount = section?.questionIds.length ?? 0
	const duration = bp.durationMinutes

	return (
		<Link
			to="/exams/$examId"
			params={{ examId: exam.id }}
			className={cn(
				"group relative rounded-xl p-4 transition-colors",
				"bg-muted/30 hover:bg-muted/50",
			)}
		>
			<p className="font-medium">{exam.level} — Đề thi</p>
			<p className="mt-1 text-sm text-muted-foreground">
				{questionCount} câu{duration ? ` • ${duration} phút` : ""}
			</p>
		</Link>
	)
}

function LearnerDashboard() {
	const progress = useProgress()
	const exams = useExams()

	const isLoading = progress.isLoading || exams.isLoading
	const error = progress.error || exams.error

	if (isLoading) {
		return <p className="py-10 text-center text-muted-foreground">Đang tải...</p>
	}

	if (error) {
		return <p className="py-10 text-center text-destructive">Lỗi: {error.message}</p>
	}

	const examList = exams.data?.data ?? []
	const skills = progress.data?.skills ?? []
	const goal = progress.data?.goal

	const totalAttempts = skills.reduce((sum, s) => sum + s.attemptCount, 0)
	const dailyGoalMinutes = goal?.dailyStudyTimeMinutes ?? 45

	function examsForSkill(skill: Skill) {
		return examList.filter((e) => {
			const bp = getBlueprint(e)
			const section = bp[skill]
			return section && section.questionIds.length > 0
		})
	}

	return (
		<div className="grid gap-10 lg:grid-cols-[1fr_300px]">
			{/* Left — content */}
			<div className="space-y-10">
				{SKILL_ORDER.map((skill) => {
					const meta = skillMeta[skill]
					const skillExams = examsForSkill(skill)
					if (skillExams.length === 0) return null

					return (
						<div key={skill}>
							<div className="mb-4 flex items-center gap-3">
								<div
									className={cn(
										"flex size-9 items-center justify-center rounded-lg",
										skillColor[skill],
									)}
								>
									<HugeiconsIcon icon={meta.icon} className="size-5" />
								</div>
								<h2 className="text-lg font-bold">{meta.label}</h2>
							</div>
							<div className="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
								{skillExams.map((exam) => (
									<ExamCard key={exam.id} exam={exam} skill={skill} />
								))}
							</div>
						</div>
					)
				})}
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
							<span className="text-sm text-muted-foreground">{dailyGoalMinutes} phút</span>
						</div>
						{goal && (
							<p className="text-xs text-muted-foreground">
								Mục tiêu: {goal.targetBand}
								{goal.deadline && ` — Hạn: ${new Date(goal.deadline).toLocaleDateString("vi-VN")}`}
							</p>
						)}
					</div>

					{/* Quick stats */}
					<div className="space-y-4 px-1">
						<div className="flex items-center gap-3">
							<div className="flex size-8 items-center justify-center rounded-lg bg-muted">
								<HugeiconsIcon icon={TestTube01Icon} className="size-4 text-muted-foreground" />
							</div>
							<p className="text-sm font-medium">{totalAttempts} bài đã làm</p>
						</div>
						<div className="flex items-center gap-3">
							<div className="flex size-8 items-center justify-center rounded-lg bg-muted">
								<HugeiconsIcon icon={Clock01Icon} className="size-4 text-muted-foreground" />
							</div>
							<p className="text-sm font-medium">{skills.length} kỹ năng đang luyện</p>
						</div>
					</div>

					{/* Quick actions */}
					<div className="space-y-2">
						<Button variant="outline" className="w-full justify-start gap-2" asChild>
							<Link to="/exams">
								<HugeiconsIcon icon={DocumentValidationIcon} className="size-4" />
								Bắt đầu bài mới
							</Link>
						</Button>
						<Button variant="outline" className="w-full justify-start gap-2" asChild>
							<Link to="/progress">
								<HugeiconsIcon icon={AnalyticsUpIcon} className="size-4" />
								Xem tiến độ
							</Link>
						</Button>
					</div>
				</div>
			</div>
		</div>
	)
}
