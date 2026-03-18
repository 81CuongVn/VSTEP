import { SpiderChart } from "@/components/common/SpiderChart"
import type { useSpiderChart } from "@/hooks/use-progress"
import { SKILLS, skillColorText } from "./progress-constants"

export function SpiderChartCard({
	spiderData,
}: {
	spiderData: ReturnType<typeof useSpiderChart>["data"]
}) {
	const spiderSkills = spiderData
		? SKILLS.map(({ key, label }) => ({
				label,
				value: spiderData.skills[key]?.current ?? 0,
				color: skillColorText[key],
			}))
		: []

	if (spiderSkills.length === 0) return null

	return (
		<div className="rounded-2xl bg-muted/50 p-5 shadow-sm">
			<h3 className="text-lg font-semibold">Điểm trung bình theo kỹ năng</h3>
			<p className="mb-4 text-sm text-muted-foreground">trong Test Practice</p>
			<div className="flex justify-center">
				<SpiderChart skills={spiderSkills} className="size-64" />
			</div>
		</div>
	)
}
