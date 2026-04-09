import {
	Add01Icon,
	AnalyticsUpIcon,
	ArrowRight01Icon,
	Book02Icon,
	Copy01Icon,
	Delete02Icon,
	DocumentValidationIcon,
	FolderLibraryIcon,
	Notification03Icon,
	Shield01Icon,
	StudentIcon,
	TeacherIcon,
	UserGroup02Icon,
} from "@hugeicons/core-free-icons"
import { HugeiconsIcon } from "@hugeicons/react"
import { createFileRoute, Link } from "@tanstack/react-router"
import type { MouseEvent } from "react"
import { useState } from "react"
import { toast } from "sonner"
import { Button } from "@/components/ui/button"
import {
	Dialog,
	DialogContent,
	DialogFooter,
	DialogHeader,
	DialogTitle,
} from "@/components/ui/dialog"
import { Input } from "@/components/ui/input"
import { Label } from "@/components/ui/label"
import { Skeleton } from "@/components/ui/skeleton"
import { Textarea } from "@/components/ui/textarea"
import { useClasses, useCreateClass, useDeleteClass, useJoinClass } from "@/hooks/use-classes"
import { usePracticeCatalog } from "@/hooks/use-practice"
import { useVocabularyTopics } from "@/hooks/use-vocabulary"
import { user } from "@/lib/auth"

export const Route = createFileRoute("/_learner/dashboard")({
	component: DashboardPage,
})

function DashboardPage() {
	const currentUser = user()

	if (currentUser?.role === "admin") return <AdminView />
	if (currentUser?.role === "instructor") return <InstructorView />
	return <LearnerView />
}

function InstructorView() {
	const { data, isLoading } = useClasses()
	const createClass = useCreateClass()
	const deleteClass = useDeleteClass()

	const classes = data?.data ?? []

	const [showCreate, setShowCreate] = useState(false)
	const [name, setName] = useState("")
	const [description, setDescription] = useState("")

	function handleCreate() {
		if (!name.trim()) {
			toast.error("Vui lòng nhập tên lớp")
			return
		}

		createClass.mutate(
			{ name: name.trim(), description: description.trim() || undefined },
			{
				onSuccess: () => {
					setShowCreate(false)
					setName("")
					setDescription("")
					toast.success("Tạo lớp học thành công")
				},
				onError: () => toast.error("Không thể tạo lớp học. Vui lòng thử lại."),
			},
		)
	}

	function handleDelete(e: MouseEvent, id: string) {
		e.preventDefault()

		if (!confirm("Bạn có chắc muốn xóa lớp học này?")) return

		deleteClass.mutate(id, {
			onSuccess: () => toast.success("Đã xóa lớp học"),
			onError: () => toast.error("Không thể xóa lớp học"),
		})
	}

	function handleCopyCode(e: MouseEvent, code: string) {
		e.preventDefault()
		navigator.clipboard
			.writeText(code)
			.then(() => toast.success("Đã sao chép mã mời"))
			.catch(() => toast.error("Không thể sao chép"))
	}

	return (
		<div className="space-y-6">
			<RoleHero
				icon={TeacherIcon}
				title="Instructor workspace"
				description="Quản lý lớp học, giao bài ôn tập, và chuẩn bị review packs cho học viên trước các bài thi thử."
				stats={[
					{ label: "Lớp học", value: String(classes.length) },
					{ label: "Mã mời", value: String(classes.filter((item) => item.inviteCode).length) },
				]}
				actions={[
					{ label: "Tạo lớp mới", onClick: () => setShowCreate(true) },
					{ label: "Mở practice", to: "/practice" },
				]}
			/>

			<QuickPanel
				title="Teaching focus"
				items={[
					{
						title: "Review packs",
						description: "Dùng bộ practice theo kỹ năng và part để ôn nhanh trước buổi học.",
						icon: Book02Icon,
						to: "/practice",
					},
					{
						title: "Vocabulary bank",
						description: "Cho học viên luyện topic vocabulary trước khi làm bài speaking và writing.",
						icon: FolderLibraryIcon,
						to: "/vocabulary",
					},
					{
						title: "Class dashboard",
						description: "Theo dõi lớp đầu tiên để xem tiến độ và nhóm học viên có nguy cơ tụt nhịp.",
						icon: AnalyticsUpIcon,
						to: classes[0] ? "/dashboard/$classId" : undefined,
						params: classes[0] ? { classId: classes[0].id } : undefined,
					},
				]}
			/>

			<div className="flex items-center justify-between">
				<div>
					<h2 className="text-2xl font-bold">Lớp học của tôi</h2>
					<p className="mt-1 text-muted-foreground">Quản lý các lớp học và mã mời của bạn</p>
				</div>
				<Button className="gap-1.5" onClick={() => setShowCreate(true)}>
					<HugeiconsIcon icon={Add01Icon} className="size-4" />
					Tạo lớp mới
				</Button>
			</div>

			{isLoading ? (
				<CardSkeletonGrid />
			) : classes.length === 0 ? (
				<EmptyState
					title="Chưa có lớp học nào"
					description="Tạo lớp đầu tiên để bắt đầu giao bài ôn tập và theo dõi học viên."
					actionLabel="Tạo lớp đầu tiên"
					onAction={() => setShowCreate(true)}
				/>
			) : (
				<div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
					{classes.map((cls) => (
						<Link
							key={cls.id}
							to="/dashboard/$classId"
							params={{ classId: cls.id }}
							className="flex flex-col rounded-2xl border bg-background p-5 transition-colors hover:bg-muted/40"
						>
							<div className="flex items-start gap-3">
								<div className="flex size-10 items-center justify-center rounded-xl bg-primary/10 text-primary">
									<HugeiconsIcon icon={UserGroup02Icon} className="size-5" />
								</div>
								<div className="flex-1">
									<p className="font-semibold">{cls.name}</p>
									{cls.description && (
										<p className="mt-0.5 text-sm text-muted-foreground line-clamp-2">
											{cls.description}
										</p>
									)}
								</div>
							</div>

							<div className="mt-3 flex items-center gap-2 text-xs text-muted-foreground">
								<span className="rounded bg-muted px-2 py-0.5 font-mono">{cls.inviteCode}</span>
								<button
									type="button"
									className="text-muted-foreground hover:text-foreground"
									onClick={(e) => handleCopyCode(e, cls.inviteCode)}
								>
									<HugeiconsIcon icon={Copy01Icon} className="size-3.5" />
								</button>
							</div>

							<div className="mt-auto flex items-center gap-2 pt-4">
								<span className="flex-1 text-sm font-medium text-muted-foreground">
									Chi tiết
									<HugeiconsIcon icon={ArrowRight01Icon} className="ml-1 inline size-3.5" />
								</span>
								<Button
									size="sm"
									variant="ghost"
									className="text-destructive hover:text-destructive"
									onClick={(e) => handleDelete(e, cls.id)}
								>
									<HugeiconsIcon icon={Delete02Icon} className="size-4" />
								</Button>
							</div>
						</Link>
					))}
				</div>
			)}

			<Dialog open={showCreate} onOpenChange={setShowCreate}>
				<DialogContent>
					<DialogHeader>
						<DialogTitle>Tạo lớp mới</DialogTitle>
					</DialogHeader>
					<div className="space-y-4">
						<div className="space-y-1.5">
							<Label htmlFor="className">Tên lớp</Label>
							<Input
								id="className"
								placeholder="Ví dụ: VSTEP B2 - Lớp 1"
								value={name}
								onChange={(e) => setName(e.target.value)}
							/>
						</div>
						<div className="space-y-1.5">
							<Label htmlFor="classDesc">Mô tả</Label>
							<Textarea
								id="classDesc"
								placeholder="Mô tả ngắn về lớp học..."
								value={description}
								onChange={(e) => setDescription(e.target.value)}
								rows={3}
							/>
						</div>
					</div>
					<DialogFooter>
						<Button variant="outline" onClick={() => setShowCreate(false)}>
							Hủy
						</Button>
						<Button onClick={handleCreate} disabled={!name.trim() || createClass.isPending}>
							{createClass.isPending ? "Đang tạo..." : "Tạo lớp"}
						</Button>
					</DialogFooter>
				</DialogContent>
			</Dialog>
		</div>
	)
}

function LearnerView() {
	const { data, isLoading } = useClasses()
	const { data: catalog } = usePracticeCatalog()
	const { data: vocabTopics } = useVocabularyTopics(1, 6)
	const joinClass = useJoinClass()

	const classes = data?.data ?? []
	const practiceSkills = catalog?.skills ?? []
	const topicCount = vocabTopics?.data.length ?? 0

	const [showJoin, setShowJoin] = useState(false)
	const [inviteCode, setInviteCode] = useState("")
	const [joinError, setJoinError] = useState("")

	function handleJoin() {
		if (!inviteCode.trim()) {
			toast.error("Vui lòng nhập mã mời")
			return
		}

		setJoinError("")
		joinClass.mutate(
			{ inviteCode: inviteCode.trim() },
			{
				onSuccess: () => {
					setShowJoin(false)
					setInviteCode("")
					toast.success("Tham gia lớp học thành công")
				},
				onError: (err) => {
					const message = err instanceof Error ? err.message : "Mã mời không hợp lệ"
					setJoinError(message)
					toast.error(message)
				},
			},
		)
	}

	return (
		<div className="space-y-6">
			<RoleHero
				icon={StudentIcon}
				title="Learner hub"
				description="Ôn từ vựng theo chủ đề, luyện sample tests theo kỹ năng, và chuyển nhanh sang thi thử để chuẩn bị cho kỳ thi VSTEP."
				stats={[
					{ label: "Skill packs", value: String(practiceSkills.length) },
					{ label: "Vocabulary topics", value: String(topicCount) },
					{ label: "Joined classes", value: String(classes.length) },
				]}
				actions={[
					{ label: "Luyện practice", to: "/practice" },
					{ label: "Ôn từ vựng", to: "/vocabulary" },
					{ label: "Thi thử", to: "/exams" },
				]}
			/>

			<QuickPanel
				title="Review and exam preparation"
				items={[
					{
						title: "Vocabulary review database",
						description: "Các topic Education, Technology, Environment đã sẵn sàng để bạn ôn theo chủ đề.",
						icon: FolderLibraryIcon,
						to: "/vocabulary",
					},
					{
						title: "Sample tests by skill",
						description: "Bộ reading, listening, writing, speaking mẫu giúp bạn luyện theo part và level.",
						icon: Book02Icon,
						to: "/practice",
					},
					{
						title: "Mock exam preparation",
						description: "Chuyển sang thi thử sau khi review để làm quen áp lực thời gian thật.",
						icon: DocumentValidationIcon,
						to: "/exams",
					},
				]}
			/>

			<div className="flex items-center justify-between">
				<div>
					<h2 className="text-2xl font-bold">Lớp học của tôi</h2>
					<p className="mt-1 text-muted-foreground">Xem các lớp bạn đã tham gia và nhận bài ôn tập</p>
				</div>
				<Button className="gap-1.5" onClick={() => setShowJoin(true)}>
					<HugeiconsIcon icon={Add01Icon} className="size-4" />
					Tham gia lớp
				</Button>
			</div>

			{isLoading ? (
				<CardSkeletonGrid />
			) : classes.length === 0 ? (
				<EmptyState
					title="Bạn chưa tham gia lớp học nào"
					description="Nhập mã mời từ giảng viên để nhận bài ôn tập và hướng dẫn học tập."
				/>
			) : (
				<div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
					{classes.map((cls) => (
						<div
							key={cls.id}
							className="flex flex-col rounded-2xl border bg-background p-5 transition-colors hover:bg-muted/40"
						>
							<div className="flex items-start gap-3">
								<div className="flex size-10 items-center justify-center rounded-xl bg-primary/10 text-primary">
									<HugeiconsIcon icon={UserGroup02Icon} className="size-5" />
								</div>
								<div className="flex-1">
									<p className="font-semibold">{cls.name}</p>
									{cls.description && (
										<p className="mt-0.5 text-sm text-muted-foreground line-clamp-2">
											{cls.description}
										</p>
									)}
								</div>
							</div>

							<div className="mt-auto flex items-center gap-2 pt-4">
								<Button size="sm" variant="outline" className="flex-1 gap-1.5" asChild>
									<Link to="/classes/$classId" params={{ classId: cls.id }}>
										Chi tiết
										<HugeiconsIcon icon={ArrowRight01Icon} className="size-3.5" />
									</Link>
								</Button>
							</div>
						</div>
					))}
				</div>
			)}

			<Dialog open={showJoin} onOpenChange={setShowJoin}>
				<DialogContent>
					<DialogHeader>
						<DialogTitle>Tham gia lớp học</DialogTitle>
					</DialogHeader>
					<div className="space-y-4">
						<div className="space-y-1.5">
							<Label htmlFor="inviteCode">Mã mời</Label>
							<Input
								id="inviteCode"
								placeholder="Nhập mã mời từ giảng viên"
								value={inviteCode}
								onChange={(e) => setInviteCode(e.target.value)}
								onKeyDown={(e) => e.key === "Enter" && handleJoin()}
							/>
						</div>
						{joinError && <p className="text-sm text-destructive">{joinError}</p>}
					</div>
					<DialogFooter>
						<Button variant="outline" onClick={() => setShowJoin(false)}>
							Hủy
						</Button>
						<Button onClick={handleJoin} disabled={!inviteCode.trim() || joinClass.isPending}>
							{joinClass.isPending ? "Đang tham gia..." : "Tham gia"}
						</Button>
					</DialogFooter>
				</DialogContent>
			</Dialog>
		</div>
	)
}

function AdminView() {
	const { data: classesData } = useClasses()
	const { data: catalog } = usePracticeCatalog()
	const { data: vocabTopics } = useVocabularyTopics(1, 6)

	const classes = classesData?.data ?? []
	const practiceSkills = catalog?.skills ?? []

	return (
		<div className="space-y-6">
			<RoleHero
				icon={Shield01Icon}
				title="Admin control center"
				description="Quản trị người dùng, theo dõi tài nguyên ôn tập, và đảm bảo learner lẫn instructor đều có đủ dữ liệu để review và thi thử."
				stats={[
					{ label: "Practice skills", value: String(practiceSkills.length) },
					{ label: "Vocabulary topics", value: String(vocabTopics?.data.length ?? 0) },
					{ label: "Classes", value: String(classes.length) },
				]}
				actions={[
					{ label: "Người dùng", to: "/admin/users" },
					{ label: "Câu hỏi", to: "/admin/questions" },
					{ label: "Đề thi", to: "/admin/exams" },
				]}
			/>

			<QuickPanel
				title="System areas"
				items={[
					{
						title: "Question bank",
						description: "Bổ sung và rà soát sample tests cho review và exam preparation.",
						icon: Book02Icon,
						to: "/admin/questions",
					},
					{
						title: "Knowledge map",
						description: "Quản lý cấu trúc kiến thức để nội dung grammar, vocabulary, strategy đi đúng hướng.",
						icon: FolderLibraryIcon,
						to: "/admin/knowledge-points",
					},
					{
						title: "Submission review",
						description: "Theo dõi bài nộp để kiểm tra chất lượng grading và trải nghiệm người học.",
						icon: Notification03Icon,
						to: "/admin/submissions",
					},
				]}
			/>

			<div className="grid gap-4 md:grid-cols-3">
				<InfoCard
					title="Vocabulary database"
					value={`${vocabTopics?.data.length ?? 0} topics`}
					description="Seeder now includes curated review packs for exam preparation topics."
					icon={FolderLibraryIcon}
				/>
				<InfoCard
					title="Practice coverage"
					value={`${practiceSkills.length} skills`}
					description="Catalog-backed sample tests can now be surfaced for review before mock exams."
					icon={DocumentValidationIcon}
				/>
				<InfoCard
					title="Class activity"
					value={`${classes.length} classes`}
					description="Instructor and learner dashboards are now split for clearer role-based workflows."
					icon={UserGroup02Icon}
				/>
			</div>
		</div>
	)
}

function RoleHero({
	icon,
	title,
	description,
	stats,
	actions,
}: {
	icon: typeof TeacherIcon
	title: string
	description: string
	stats: { label: string; value: string }[]
	actions: Array<
		| { label: string; to: "/practice" | "/vocabulary" | "/exams" | "/admin/users" | "/admin/questions" | "/admin/exams"; params?: never }
		| { label: string; onClick: () => void }
	>
}) {
	return (
		<div className="rounded-3xl border bg-gradient-to-br from-primary/10 via-background to-muted/50 p-6">
			<div className="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
				<div className="max-w-2xl space-y-3">
					<div className="flex size-12 items-center justify-center rounded-2xl bg-primary text-primary-foreground">
						<HugeiconsIcon icon={icon} className="size-6" />
					</div>
					<div>
						<h1 className="text-2xl font-bold">{title}</h1>
						<p className="mt-2 text-sm leading-6 text-muted-foreground">{description}</p>
					</div>
					<div className="flex flex-wrap gap-3 pt-1">
						{actions.map((action) =>
							"to" in action ? (
								<Button key={action.label} asChild>
									<Link to={action.to}>{action.label}</Link>
								</Button>
							) : (
								<Button key={action.label} onClick={action.onClick}>
									{action.label}
								</Button>
							),
						)}
					</div>
				</div>

				<div className="grid gap-3 sm:grid-cols-3">
					{stats.map((stat) => (
						<div key={stat.label} className="min-w-32 rounded-2xl border bg-background/80 p-4">
							<p className="text-xs uppercase tracking-[0.12em] text-muted-foreground">{stat.label}</p>
							<p className="mt-2 text-2xl font-bold">{stat.value}</p>
						</div>
					))}
				</div>
			</div>
		</div>
	)
}

function QuickPanel({
	title,
	items,
}: {
	title: string
	items: {
		title: string
		description: string
		icon: typeof Book02Icon
		to?: string
		params?: Record<string, string>
	}[]
}) {
	return (
		<div className="space-y-3">
			<h2 className="text-lg font-semibold">{title}</h2>
			<div className="grid gap-4 md:grid-cols-3">
				{items.map((item) => {
					const content = (
						<div className="rounded-2xl border bg-background p-5 transition-colors hover:bg-muted/40">
							<div className="flex size-10 items-center justify-center rounded-xl bg-primary/10 text-primary">
								<HugeiconsIcon icon={item.icon} className="size-5" />
							</div>
							<h3 className="mt-4 font-semibold">{item.title}</h3>
							<p className="mt-2 text-sm leading-6 text-muted-foreground">{item.description}</p>
						</div>
					)

					if (!item.to) {
						return <div key={item.title}>{content}</div>
					}

					return (
						<Link key={item.title} to={item.to} params={item.params} className="block">
							{content}
						</Link>
					)
				})}
			</div>
		</div>
	)
}

function InfoCard({
	title,
	value,
	description,
	icon,
}: {
	title: string
	value: string
	description: string
	icon: typeof Book02Icon
}) {
	return (
		<div className="rounded-2xl border bg-background p-5">
			<div className="flex size-10 items-center justify-center rounded-xl bg-primary/10 text-primary">
				<HugeiconsIcon icon={icon} className="size-5" />
			</div>
			<p className="mt-4 text-sm text-muted-foreground">{title}</p>
			<p className="mt-1 text-2xl font-bold">{value}</p>
			<p className="mt-2 text-sm leading-6 text-muted-foreground">{description}</p>
		</div>
	)
}

function EmptyState({
	title,
	description,
	actionLabel,
	onAction,
}: {
	title: string
	description: string
	actionLabel?: string
	onAction?: () => void
}) {
	return (
		<div className="flex flex-col items-center gap-4 rounded-2xl border bg-background py-16">
			<div className="flex size-16 items-center justify-center rounded-2xl bg-muted">
				<HugeiconsIcon icon={UserGroup02Icon} className="size-8 text-muted-foreground" />
			</div>
			<div className="space-y-1 text-center">
				<p className="font-medium">{title}</p>
				<p className="text-sm text-muted-foreground">{description}</p>
			</div>
			{actionLabel && onAction ? (
				<Button variant="outline" onClick={onAction}>
					{actionLabel}
				</Button>
			) : null}
		</div>
	)
}

function CardSkeletonGrid() {
	return (
		<div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
			{Array.from({ length: 3 }).map((_, i) => (
				<Skeleton key={i} className="h-36 rounded-2xl" />
			))}
		</div>
	)
}
