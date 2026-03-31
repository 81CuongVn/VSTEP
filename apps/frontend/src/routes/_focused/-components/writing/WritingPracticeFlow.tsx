import { useCallback, useEffect, useRef, useState } from "react"
import { Button } from "@/components/ui/button"
import { useStartPractice, useSubmission, useSubmitPracticeAnswer } from "@/hooks/use-practice"
import type {
	PracticeItem,
	PracticeSession,
	PracticeStartResponse,
	QuestionLevel,
	WritingContent,
	WritingHints,
} from "@/types/api"
import { WritingGradingResult } from "./WritingGradingResult"

// ═══════════════════════════════════════════════════
// Type guards
// ═══════════════════════════════════════════════════

function isWritingContent(c: unknown): c is WritingContent {
	return c !== null && typeof c === "object" && "prompt" in (c as Record<string, unknown>)
}

// ═══════════════════════════════════════════════════
// Props
// ═══════════════════════════════════════════════════

interface WritingPracticeFlowProps {
	questionId?: string
	questionLevel: QuestionLevel
}

// ═══════════════════════════════════════════════════
// Main component
// ═══════════════════════════════════════════════════

export function WritingPracticeFlow({ questionLevel }: WritingPracticeFlowProps) {
	const [session, setSession] = useState<PracticeSession | null>(null)
	const [item, setItem] = useState<PracticeItem | null>(null)
	const [writingText, setWritingText] = useState("")
	const [submissionId, setSubmissionId] = useState<string | null>(null)
	const [phase, setPhase] = useState<"loading" | "writing" | "submitting" | "grading" | "result">(
		"loading",
	)
	const [error, setError] = useState<string | null>(null)

	const startMutation = useStartPractice()
	const hasStartedRef = useRef(false)

	// Auto-start practice session on mount
	useEffect(() => {
		if (hasStartedRef.current) return
		hasStartedRef.current = true

		startMutation.mutate(
			{ skill: "writing", mode: "guided", level: questionLevel, itemsCount: 1 },
			{
				onSuccess: (data: PracticeStartResponse) => {
					setSession(data.session)
					setItem(data.currentItem)
					setPhase("writing")
				},
				onError: (err) => {
					setError(err.message)
					setPhase("loading")
				},
			},
		)
	}, [questionLevel, startMutation])

	const content = item
		? isWritingContent(item.question.content)
			? item.question.content
			: null
		: null
	const hints = item?.writingHints ?? null

	return (
		<div className="flex h-full flex-col overflow-hidden">
			{phase === "loading" && !error && (
				<div className="flex flex-1 items-center justify-center">
					<p className="text-sm text-muted-foreground">Đang tạo phiên luyện tập...</p>
				</div>
			)}

			{error && (
				<div className="flex flex-1 flex-col items-center justify-center gap-3">
					<p className="text-sm text-destructive">{error}</p>
					<Button
						variant="outline"
						onClick={() => {
							setError(null)
							hasStartedRef.current = false
						}}
					>
						Thử lại
					</Button>
				</div>
			)}

			{phase === "writing" && content && session && item && (
				<WritingEditor
					content={content}
					hints={hints}
					text={writingText}
					onTextChange={setWritingText}
					sessionId={session.id}
					questionId={item.question.id}
					onSubmitted={(subId) => {
						setSubmissionId(subId)
						setPhase("grading")
					}}
					onSubmitting={() => setPhase("submitting")}
					onError={(msg) => setError(msg)}
				/>
			)}

			{phase === "submitting" && (
				<div className="flex flex-1 items-center justify-center">
					<p className="text-sm text-muted-foreground">Đang nộp bài...</p>
				</div>
			)}

			{(phase === "grading" || phase === "result") && submissionId && (
				<GradingPoller
					submissionId={submissionId}
					submittedText={writingText}
					content={content}
					onCompleted={() => setPhase("result")}
				/>
			)}
		</div>
	)
}

// ═══════════════════════════════════════════════════
// Writing editor sub-component
// ═══════════════════════════════════════════════════

interface WritingEditorProps {
	content: WritingContent
	hints: WritingHints | null
	text: string
	onTextChange: (text: string) => void
	sessionId: string
	questionId: string
	onSubmitted: (submissionId: string) => void
	onSubmitting: () => void
	onError: (msg: string) => void
}

function WritingEditor({
	content,
	hints,
	text,
	onTextChange,
	sessionId,
	questionId,
	onSubmitted,
	onSubmitting,
	onError,
}: WritingEditorProps) {
	const submitMutation = useSubmitPracticeAnswer(sessionId)
	const wordCount = text.trim() ? text.trim().split(/\s+/).length : 0

	const handleSubmit = useCallback(() => {
		if (!text.trim()) return
		onSubmitting()
		submitMutation.mutate(
			{ questionId, answer: { text } },
			{
				onSuccess: (data) => {
					onSubmitted(data.submissionId)
				},
				onError: (err) => {
					onError(err.message)
				},
			},
		)
	}, [text, questionId, submitMutation, onSubmitted, onSubmitting, onError])

	return (
		<div className="flex flex-1 flex-col overflow-hidden lg:flex-row">
			{/* Left — Prompt + hints */}
			<div className="w-full shrink-0 overflow-y-auto border-b p-6 lg:w-[420px] lg:border-b-0 lg:border-r">
				<div className="space-y-4">
					<div>
						<span className="inline-block rounded-lg bg-primary/10 px-2.5 py-1 text-xs font-semibold text-primary">
							{content.taskType === "letter" ? "Viết thư" : "Viết luận"}
						</span>
					</div>

					<div className="whitespace-pre-wrap rounded-xl bg-muted/30 p-4 text-sm leading-relaxed">
						{content.prompt}
					</div>

					{content.requiredPoints && content.requiredPoints.length > 0 && (
						<div className="space-y-2">
							<p className="text-xs font-semibold text-muted-foreground">Yêu cầu:</p>
							<ul className="space-y-1 pl-4 text-sm">
								{content.requiredPoints.map((point) => (
									<li key={point} className="list-disc text-muted-foreground">
										{point}
									</li>
								))}
							</ul>
						</div>
					)}

					{content.instructions && content.instructions.length > 0 && (
						<div className="space-y-1">
							<p className="text-xs font-semibold text-muted-foreground">Hướng dẫn:</p>
							{content.instructions.map((instr) => (
								<p key={instr} className="text-sm text-muted-foreground">
									{instr}
								</p>
							))}
						</div>
					)}

					{hints && (
						<div className="space-y-3 rounded-xl border border-dashed border-primary/30 bg-primary/5 p-4">
							<p className="text-xs font-semibold text-primary">Gợi ý dàn bài</p>
							<ol className="space-y-1 pl-4 text-sm">
								{hints.outline.map((line) => (
									<li key={line} className="list-decimal text-muted-foreground">
										{line}
									</li>
								))}
							</ol>

							<p className="text-xs font-semibold text-primary">Mẫu câu gợi ý</p>
							<div className="flex flex-wrap gap-1.5">
								{hints.starters.map((s) => (
									<span
										key={s}
										className="inline-block rounded-md bg-background px-2 py-0.5 text-xs text-muted-foreground"
									>
										{s}
									</span>
								))}
							</div>

							<p className="text-xs text-muted-foreground">
								Số từ yêu cầu: <strong>{hints.wordCount}</strong>
							</p>
						</div>
					)}
				</div>
			</div>

			{/* Right — Textarea */}
			<div className="flex flex-1 flex-col p-6">
				<textarea
					className="min-h-[300px] flex-1 resize-none rounded-xl border bg-background p-4 text-sm leading-relaxed focus:outline-none focus:ring-2 focus:ring-primary/30"
					placeholder="Nhập bài viết của bạn..."
					value={text}
					onChange={(ev) => onTextChange(ev.target.value)}
				/>
				<div className="mt-3 flex items-center justify-between">
					<p className="text-sm text-muted-foreground">
						{wordCount} từ
						{content.minWords > 0 && wordCount < content.minWords && (
							<span className="ml-1 text-orange-500">(cần tối thiểu {content.minWords} từ)</span>
						)}
					</p>
					<Button onClick={handleSubmit} disabled={!text.trim() || submitMutation.isPending}>
						{submitMutation.isPending ? "Đang nộp..." : "Nộp bài"}
					</Button>
				</div>
			</div>
		</div>
	)
}

// ═══════════════════════════════════════════════════
// Grading poller sub-component
// ═══════════════════════════════════════════════════

interface GradingPollerProps {
	submissionId: string
	submittedText: string
	content: WritingContent | null
	onCompleted: () => void
}

function GradingPoller({ submissionId, submittedText, content, onCompleted }: GradingPollerProps) {
	const { data: submission } = useSubmission(submissionId)
	const notifiedRef = useRef(false)

	const isTerminal =
		submission?.status === "completed" ||
		submission?.status === "review_pending" ||
		submission?.status === "failed"

	useEffect(() => {
		if (isTerminal && !notifiedRef.current) {
			notifiedRef.current = true
			onCompleted()
		}
	}, [isTerminal, onCompleted])

	if (!isTerminal) {
		return <GradingPending />
	}

	return (
		<WritingGradingResult submission={submission} submittedText={submittedText} content={content} />
	)
}

function GradingPending() {
	return (
		<div className="flex flex-1 flex-col items-center justify-center gap-4">
			<div className="flex items-center gap-3">
				<span className="relative flex size-3">
					<span className="absolute inline-flex h-full w-full animate-ping rounded-full bg-amber-500 opacity-75" />
					<span className="relative inline-flex size-3 rounded-full bg-amber-500" />
				</span>
				<p className="font-semibold text-amber-700 dark:text-amber-400">Đang chấm bài...</p>
			</div>
			<p className="max-w-sm text-center text-sm text-muted-foreground">
				AI đang phân tích bài viết của bạn. Quá trình này thường mất 30-60 giây.
			</p>
		</div>
	)
}
