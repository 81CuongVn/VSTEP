import { useQuery } from "@tanstack/react-query"
import { api } from "@/lib/api"
import type { PaginatedResponse, Question, Skill } from "@/types/api"

interface UsePracticeQuestionsParams {
	skill: Skill
	level?: string
	part?: number
	topic?: string
	search?: string
}

function usePracticeQuestions(params: UsePracticeQuestionsParams) {
	const qs = new URLSearchParams({ skill: params.skill, per_page: "100" })
	if (params.level) qs.set("level", params.level)
	if (params.part != null) qs.set("part", String(params.part))
	if (params.topic) qs.set("topic", params.topic)
	if (params.search) qs.set("search", params.search)

	return useQuery({
		queryKey: ["practice", "questions", params],
		queryFn: () => api.get<PaginatedResponse<Question>>(`/api/practice/questions?${qs}`),
	})
}

export { usePracticeQuestions }
