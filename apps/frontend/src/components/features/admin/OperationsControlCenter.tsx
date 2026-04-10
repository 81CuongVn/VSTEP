import { ArrowRight01Icon } from "@hugeicons/core-free-icons"
import type { IconSvgElement } from "@hugeicons/react"
import { HugeiconsIcon } from "@hugeicons/react"
import { Link } from "@tanstack/react-router"
import { Badge } from "@/components/ui/badge"
import { Button } from "@/components/ui/button"
import { cn } from "@/lib/utils"

interface OverviewStat {
	label: string
	value: string
	note?: string
}

interface OverviewSection {
	title: string
	description: string
	metric: string
	icon: IconSvgElement
	href?: string
	badge?: string
	toneClass: string
}

interface OverviewSetting {
	title: string
	description: string
	icon: IconSvgElement
	items: string[]
	href?: string
	badge?: string
	toneClass: string
}

interface OperationsControlCenterProps {
	eyebrow: string
	title: string
	description: string
	stats: OverviewStat[]
	sections: OverviewSection[]
	settings: OverviewSetting[]
}

export function OperationsControlCenter({
	eyebrow,
	title,
	description,
	stats,
	sections,
	settings,
}: OperationsControlCenterProps) {
	return (
		<div className="space-y-8">
			<section className="overflow-hidden rounded-3xl border bg-gradient-to-br from-primary/10 via-background to-muted/60">
				<div className="grid gap-6 p-6 lg:grid-cols-[minmax(0,1.6fr)_minmax(320px,1fr)] lg:p-8">
					<div className="space-y-4">
						<Badge variant="outline" className="rounded-full px-3 py-1 text-xs font-medium">
							{eyebrow}
						</Badge>
						<div className="space-y-2">
							<h1 className="text-3xl font-bold tracking-tight">{title}</h1>
							<p className="max-w-3xl text-sm leading-6 text-muted-foreground">{description}</p>
						</div>
					</div>

					<div className="grid gap-3 sm:grid-cols-3 lg:grid-cols-2">
						{stats.map((stat) => (
							<div key={stat.label} className="rounded-2xl border bg-background/90 p-4">
								<p className="text-[11px] uppercase tracking-[0.14em] text-muted-foreground">
									{stat.label}
								</p>
								<p className="mt-2 text-2xl font-bold">{stat.value}</p>
								{stat.note ? (
									<p className="mt-1 text-xs leading-5 text-muted-foreground">{stat.note}</p>
								) : null}
							</div>
						))}
					</div>
				</div>
			</section>

			<section className="space-y-3">
				<div className="flex items-end justify-between gap-4">
					<div>
						<h2 className="text-lg font-semibold">Section Overview</h2>
						<p className="text-sm text-muted-foreground">
							Các khu vực chính để theo dõi vận hành và đi nhanh vào đúng màn quản trị.
						</p>
					</div>
				</div>

				<div className="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
					{sections.map((section) => (
						<div key={section.title} className="rounded-3xl border bg-background p-5 shadow-sm">
							<div className="flex items-start justify-between gap-3">
								<div
									className={cn(
										"flex size-11 items-center justify-center rounded-2xl border",
										section.toneClass,
									)}
								>
									<HugeiconsIcon icon={section.icon} className="size-5" />
								</div>
								<div className="text-right">
									<p className="text-xs uppercase tracking-[0.14em] text-muted-foreground">Count</p>
									<p className="text-lg font-bold">{section.metric}</p>
								</div>
							</div>

							<div className="mt-4 space-y-2">
								<div className="flex items-center gap-2">
									<h3 className="font-semibold">{section.title}</h3>
									{section.badge ? <Badge variant="secondary">{section.badge}</Badge> : null}
								</div>
								<p className="text-sm leading-6 text-muted-foreground">{section.description}</p>
							</div>

							<div className="mt-5">
								{section.href ? (
									<Button asChild variant="outline" size="sm" className="gap-1.5 rounded-full">
										<Link to={section.href as never}>
											Open section
											<HugeiconsIcon icon={ArrowRight01Icon} className="size-4" />
										</Link>
									</Button>
								) : (
									<div className="text-xs text-muted-foreground">Tổng quan hiện chỉ để theo dõi.</div>
								)}
							</div>
						</div>
					))}
				</div>
			</section>

			<section className="space-y-3">
				<div>
					<h2 className="text-lg font-semibold">Operational Settings</h2>
					<p className="text-sm text-muted-foreground">
						Các cấu hình và nguyên tắc vận hành quan trọng để instructor và admin nắm ngay trên một màn.
					</p>
				</div>

				<div className="grid gap-4 lg:grid-cols-2">
					{settings.map((setting) => (
						<div key={setting.title} className="rounded-3xl border bg-background p-5 shadow-sm">
							<div className="flex items-start justify-between gap-3">
								<div className="flex items-start gap-3">
									<div
										className={cn(
											"mt-0.5 flex size-10 items-center justify-center rounded-2xl border",
											setting.toneClass,
										)}
									>
										<HugeiconsIcon icon={setting.icon} className="size-5" />
									</div>
									<div>
										<div className="flex items-center gap-2">
											<h3 className="font-semibold">{setting.title}</h3>
											{setting.badge ? <Badge variant="outline">{setting.badge}</Badge> : null}
										</div>
										<p className="mt-1 text-sm leading-6 text-muted-foreground">
											{setting.description}
										</p>
									</div>
								</div>
							</div>

							<div className="mt-4 space-y-2">
								{setting.items.map((item) => (
									<div key={item} className="flex items-start gap-2 text-sm text-muted-foreground">
										<span className="mt-2 size-1.5 rounded-full bg-foreground/40" />
										<span className="leading-6">{item}</span>
									</div>
								))}
							</div>

							{setting.href ? (
								<div className="mt-5">
									<Button asChild variant="ghost" size="sm" className="gap-1.5 px-0">
										<Link to={setting.href as never}>
											Go to related section
											<HugeiconsIcon icon={ArrowRight01Icon} className="size-4" />
										</Link>
									</Button>
								</div>
							) : null}
						</div>
					))}
				</div>
			</section>
		</div>
	)
}
