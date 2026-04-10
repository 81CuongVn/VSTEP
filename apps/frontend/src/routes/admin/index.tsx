import {
	AnalyticsUpIcon,
	Book01Icon,
	BubbleChatIcon,
	DocumentValidationIcon,
	FolderLibraryIcon,
	Notification03Icon,
	PencilEdit01Icon,
	Shield01Icon,
	UserGroup02Icon,
} from "@hugeicons/core-free-icons"
import { createFileRoute } from "@tanstack/react-router"
import { OperationsControlCenter } from "@/components/features/admin/OperationsControlCenter"
import { useKnowledgePoints } from "@/hooks/use-admin-knowledge-points"
import { useAdminQuestions } from "@/hooks/use-admin-questions"
import { useAdminUsers } from "@/hooks/use-admin-users"
import { useClasses } from "@/hooks/use-classes"
import { useExams } from "@/hooks/use-exams"
import { usePracticeCatalog } from "@/hooks/use-practice"
import { useSubmissions } from "@/hooks/use-submissions"
import { useVocabularyTopics } from "@/hooks/use-vocabulary"

export const Route = createFileRoute("/admin/")({
	component: AdminDashboard,
})

function AdminDashboard() {
	const users = useAdminUsers({ page: 1 })
	const exams = useExams({ limit: 100 })
	const questions = useAdminQuestions({ page: 1 })
	const knowledgePoints = useKnowledgePoints({ page: 1 })
	const submissions = useSubmissions({ page: 1 })
	const reviewPending = useSubmissions({ page: 1, status: "review_pending" })
	const classes = useClasses(1, 100)
	const practiceCatalog = usePracticeCatalog()
	const vocabularyTopics = useVocabularyTopics(1, 100)

	const totalUsers = users.data?.meta?.total ?? users.data?.data.length ?? 0
	const totalExams = exams.data?.meta?.total ?? exams.data?.data.length ?? 0
	const activeExams = exams.data?.data.filter((item) => item.isActive).length ?? 0
	const totalQuestions = questions.data?.meta?.total ?? questions.data?.data.length ?? 0
	const totalKnowledgePoints =
		knowledgePoints.data?.meta?.total ?? knowledgePoints.data?.data.length ?? 0
	const totalSubmissions = submissions.data?.meta?.total ?? submissions.data?.data.length ?? 0
	const reviewQueue = reviewPending.data?.meta?.total ?? reviewPending.data?.data.length ?? 0
	const totalClasses = classes.data?.meta?.total ?? classes.data?.data.length ?? 0
	const totalVocabularyTopics =
		vocabularyTopics.data?.meta?.total ?? vocabularyTopics.data?.data.length ?? 0
	const practiceSkills = practiceCatalog.data?.skills.length ?? 0

	return (
		<OperationsControlCenter
			eyebrow="Administration"
			title="Control Center for Admin"
			description="Một màn tổng quan để theo dõi toàn bộ khu vực vận hành, đi nhanh vào đúng section quản trị, và nắm các thiết lập quan trọng của hệ thống VSTEP."
			stats={[
				{
					label: "Users",
					value: String(totalUsers),
					note: "Tài khoản đang hiện diện trong hệ thống.",
				},
				{
					label: "Review Queue",
					value: String(reviewQueue),
					note: "Bài viết hoặc nói đang chờ xử lý thêm.",
				},
				{
					label: "Exams",
					value: `${activeExams}/${totalExams}`,
					note: "Đề thi đang bật trên tổng số đề hiện có.",
				},
				{
					label: "Classes",
					value: String(totalClasses),
					note: "Lớp học để instructor vận hành và giao bài.",
				},
			]}
			sections={[
				{
					title: "Users",
					description: "Quản lý vai trò admin, instructor, learner và rà soát sức khỏe truy cập của hệ thống.",
					metric: String(totalUsers),
					icon: UserGroup02Icon,
					href: "/admin/users",
					badge: "Full access",
					toneClass: "border-rose-200 bg-rose-50 text-rose-700 dark:border-rose-900/60 dark:bg-rose-950/40 dark:text-rose-300",
				},
				{
					title: "Exam Bank",
					description: "Theo dõi số lượng đề thi, trạng thái active và đi vào màn cấu hình blueprint.",
					metric: String(totalExams),
					icon: DocumentValidationIcon,
					href: "/admin/exams",
					badge: activeExams > 0 ? `${activeExams} active` : "Inactive",
					toneClass: "border-sky-200 bg-sky-50 text-sky-700 dark:border-sky-900/60 dark:bg-sky-950/40 dark:text-sky-300",
				},
				{
					title: "Question Bank",
					description: "Bao phủ tất cả kỹ năng với bộ câu hỏi dùng cho practice, placement và mock test.",
					metric: String(totalQuestions),
					icon: Book01Icon,
					href: "/admin/questions",
					toneClass: "border-amber-200 bg-amber-50 text-amber-700 dark:border-amber-900/60 dark:bg-amber-950/40 dark:text-amber-300",
				},
				{
					title: "Knowledge Points",
					description: "Phân lớp grammar, vocabulary, strategy để giữ cấu trúc học liệu thống nhất.",
					metric: String(totalKnowledgePoints),
					icon: BubbleChatIcon,
					href: "/admin/knowledge-points",
					toneClass: "border-violet-200 bg-violet-50 text-violet-700 dark:border-violet-900/60 dark:bg-violet-950/40 dark:text-violet-300",
				},
				{
					title: "Submission Flow",
					description: "Theo dõi toàn bộ bài nộp và phát hiện sớm các cụm bài đang cần review hoặc auto-grade.",
					metric: String(totalSubmissions),
					icon: PencilEdit01Icon,
					href: "/admin/submissions",
					badge: reviewQueue > 0 ? `${reviewQueue} waiting` : "Clear",
					toneClass: "border-emerald-200 bg-emerald-50 text-emerald-700 dark:border-emerald-900/60 dark:bg-emerald-950/40 dark:text-emerald-300",
				},
				{
					title: "Learning Surfaces",
					description: "Tổng hợp các khu vực learner-facing như practice, vocabulary và classroom để kiểm tra độ phủ nội dung.",
					metric: `${practiceSkills + totalVocabularyTopics + totalClasses}`,
					icon: FolderLibraryIcon,
					toneClass: "border-slate-200 bg-slate-50 text-slate-700 dark:border-slate-800 dark:bg-slate-950/40 dark:text-slate-300",
				},
			]}
			settings={[
				{
					title: "Access & Roles",
					description: "Quy tắc phân quyền hiện tại mà đội vận hành cần nắm nhanh.",
					icon: Shield01Icon,
					href: "/admin/users",
					toneClass: "border-rose-200 bg-rose-50 text-rose-700 dark:border-rose-900/60 dark:bg-rose-950/40 dark:text-rose-300",
					items: [
						"Admin có quyền quản trị tất cả section và thiết lập nội dung.",
						"Instructor tập trung vào lớp học, theo dõi tiến độ và hoạt động vận hành học viên.",
						"Learner chỉ thấy các luồng học, luyện tập, thi thử và tiến độ cá nhân.",
					],
				},
				{
					title: "Grading Workflow",
					description: "Toàn cảnh luồng chấm điểm để quyết định nơi cần can thiệp.",
					icon: AnalyticsUpIcon,
					href: "/admin/submissions",
					toneClass: "border-emerald-200 bg-emerald-50 text-emerald-700 dark:border-emerald-900/60 dark:bg-emerald-950/40 dark:text-emerald-300",
					items: [
						"Listening và Reading có thể đi qua auto-grade nhanh.",
						"Writing và Speaking cần theo dõi review queue khi AI chưa đủ chắc chắn.",
						"Số bài review_pending hiện tại là chỉ báo tốt nhất cho chất lượng vận hành grading.",
					],
				},
				{
					title: "Content Coverage",
					description: "Tóm tắt độ phủ nội dung học liệu đang có trong hệ thống.",
					icon: FolderLibraryIcon,
					toneClass: "border-amber-200 bg-amber-50 text-amber-700 dark:border-amber-900/60 dark:bg-amber-950/40 dark:text-amber-300",
					items: [
						`${practiceSkills} kỹ năng practice đang có mặt trong catalog.`,
						`${totalVocabularyTopics} topic vocabulary giúp learner ôn theo chủ đề.`,
						`${totalKnowledgePoints} knowledge point đang đóng vai trò phân loại câu hỏi và lộ trình.`,
					],
				},
				{
					title: "Operational Watchlist",
					description: "Các tín hiệu cần nhìn mỗi ngày từ màn tổng quan.",
					icon: Notification03Icon,
					toneClass: "border-sky-200 bg-sky-50 text-sky-700 dark:border-sky-900/60 dark:bg-sky-950/40 dark:text-sky-300",
					items: [
						`Review queue hiện có ${reviewQueue} bài cần quan tâm.`,
						`${totalClasses} lớp học đang tạo áp lực vận hành cho instructor dashboard.`,
						`Tổng số bài nộp đã ghi nhận là ${totalSubmissions}, dùng để đánh giá mức độ sử dụng thực tế.`,
					],
				},
			]}
		/>
	)
}
