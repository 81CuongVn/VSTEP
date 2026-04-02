<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VSTEP E2E Playground</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/vue@3/dist/vue.global.prod.js"></script>
    <style>
        /* ─── Design Tokens ─────────────────────────────────────────── */
        :root {
            --bg:         #070b12;
            --surface:     #0d1420;
            --card:       #111827;
            --border:      rgba(51,65,85,0.55);
            --border-sub:  rgba(51,65,85,0.35);
            --text-1:     #f1f5f9;
            --text-2:     #cbd5e1;
            --text-3:     #94a3b8;
            --text-4:     #64748b;
            --brand:      #6366f1;
            --brand-hover: #4f46e5;
            --emerald:     #22c55e;
            --amber:       #f59e0b;
            --rose:        #f43f5e;
            --sky:         #38bdf8;
            --radius-btn:  8px;
            --radius-card: 12px;
            --radius-dot:  9999px;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }
        html, body { height: 100%; background: var(--bg); color: var(--text-2); font-family: Inter,ui-sans-serif,system-ui,-apple-system,sans-serif; font-size: 14px; line-height: 1.5; -webkit-font-smoothing: antialiased; }
        .mono { font-family: ui-monospace,SFMono-Regular,Menlo,Monaco,Consolas,monospace; }

        /* ─── Scrollbar ────────────────────────────────────────────── */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #1c2a42; border-radius: 999px; }
        ::-webkit-scrollbar-thumb:hover { background: #243350; }

        /* ─── Selection ─────────────────────────────────────────────── */
        ::selection { background: rgba(99,102,241,0.25); color: #fff; }

        /* ─── Animations ───────────────────────────────────────────── */
        @keyframes fadeUp { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes scoreReveal { 0% { transform: scale(0.7); opacity: 0; } 60% { transform: scale(1.08); } 100% { transform: scale(1); opacity: 1; } }
        @keyframes radarFill { from { stroke-dashoffset: 400; fill-opacity: 0; } to { stroke-dashoffset: 0; fill-opacity: 0.18; } }
        @keyframes dotGlow { 0%,100% { box-shadow: 0 0 0 0 transparent; } 50% { box-shadow: 0 0 0 4px currentColor; } }

        .animate-fade-up  { animation: fadeUp 0.3s ease-out forwards; }
        .animate-score   { animation: scoreReveal 0.5s cubic-bezier(0.34,1.56,0.64,1) forwards; }
        .animate-radar    { stroke-dasharray: 400; stroke-dashoffset: 400; animation: radarFill 0.7s ease-out 0.2s forwards; }

        /* ─── Focus ───────────────────────────────────────────────── */
        :focus-visible { outline: 2px solid var(--brand); outline-offset: 2px; border-radius: 4px; }

        /* ─── JSON ────────────────────────────────────────────────── */
        .jk { color: #7dd3fc; }
        .js { color: #86efac; }
        .jn { color: #fbbf24; }
        .jb { color: #c4b5fd; }
        .jx { color: #64748b; }

        /* ─── Audio ───────────────────────────────────────────────── */
        audio { width: 100%; border-radius: 8px; }
        audio::-webkit-media-controls-panel { background: #141e30; }

        /* ─── Vue cloak ──────────────────────────────────────────── */
        [v-cloak] { display: none; }
    </style>
</head>
<body>
<div id="app" v-cloak>
@verbatim

    <!-- ─── App Shell ──────────────────────────────────────────── -->
    <div class="flex flex-col h-screen overflow-hidden">

        <!-- Header -->
        <header class="flex items-center justify-between px-6 h-16 shrink-0 border-b border-[var(--border)] bg-[var(--surface)]" style="backdrop-filter:blur(12px);-webkit-backdrop-filter:blur(12px);">
            <div class="flex items-center gap-5">
                <!-- Logo -->
                <div class="flex items-center gap-3">
                    <div class="relative">
                        <div class="w-9 h-9 rounded-xl flex items-center justify-center" style="background:rgba(99,102,241,0.15);box-shadow:0 0 0 1px rgba(99,102,241,0.25),0 0 20px rgba(99,102,241,0.1)">
                            <div class="w-3.5 h-3.5 rounded-md" style="background:var(--brand)"></div>
                        </div>
                    </div>
                    <div>
                        <p class="text-[10px] uppercase tracking-[0.3em] text-[var(--text-4)] font-medium">Internal QA</p>
                        <h1 class="text-sm font-semibold text-[var(--text-1)] leading-tight">VSTEP E2E Playground</h1>
                    </div>
                </div>
                <div class="w-px h-6 bg-[var(--border)]"></div>
                <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-[10px] font-semibold uppercase tracking-[0.2em]" style="background:rgba(34,197,94,0.08);border:1px solid rgba(34,197,94,0.2);color:#4ade80">
                    <span class="w-1.5 h-1.5 rounded-full" style="background:#4ade80"></span>localhost
                </span>
            </div>

            <div class="flex items-center gap-2">
                <!-- Mode toggle -->
                <div class="flex rounded-lg p-1" style="background:rgba(20,30,48,0.8);border:1px solid var(--border)">
                    <button v-for="m in ['summary','debug']" :key="m"
                        @click="mode=m"
                        class="px-4 py-1.5 rounded-md text-[11px] font-semibold uppercase tracking-[0.15em] transition-all duration-150"
                        :style="mode===m ? 'background:var(--brand);color:#fff' : 'color:var(--text-4)'"
                        :class="mode===m ? 'shadow-[0_0_12px_rgba(99,102,241,0.3)]' : ''">
                        {{ m }}
                    </button>
                </div>
                <div class="w-px h-6 bg-[var(--border)]"></div>
                <!-- Run -->
                <button @click="running ? stopRun() : startRun()"
                    :disabled="loading"
                    class="flex items-center gap-2 rounded-lg px-5 py-2 text-[11px] font-bold transition-all duration-150"
                    style="background:var(--brand);color:#fff"
                    :disabled="loading"
                    :style="loading ? 'opacity:0.5;cursor:not-allowed' : ''">
                    <svg v-if="!running" class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                    <svg v-else class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M6 4h4v16H6zM14 4h4v16h-4z"/></svg>
                    <span>{{ running ? 'Running...' : 'Run full flow' }}</span>
                </button>
                <!-- Export -->
                <button @click="exportRun"
                    :disabled="!runLogs.length"
                    class="rounded-lg px-4 py-2 text-[11px] font-semibold transition-all duration-150"
                    style="border:1px solid var(--border);color:var(--text-3);background:rgba(20,30,48,0.8)"
                    :style="!runLogs.length ? 'opacity:0.4;cursor:not-allowed' : ''">
                    Export
                </button>
            </div>
        </header>

        <!-- Progress strip -->
        <div class="flex items-center gap-3 px-6 h-11 shrink-0 border-b border-[var(--border-sub)] bg-[var(--bg)]">
            <div class="flex items-center gap-2 overflow-x-auto flex-1">
                <template v-for="(step, i) in steps" :key="step.key">
                    <div class="flex items-center gap-2">
                        <button @click="selectStep(step.key)" class="relative w-2.5 h-2.5 rounded-full border-2 transition-all duration-150 shrink-0"
                            :style="stepDotStyle(step.key)"
                            :title="step.label">
                        </button>
                        <div v-if="i < steps.length - 1" class="w-5 h-px shrink-0 transition-all duration-300"
                            :style="stepLineStyle(i)">
                        </div>
                    </div>
                </template>
            </div>
            <div class="flex items-center gap-4 text-[11px] mono shrink-0" style="color:var(--text-4)">
                <span>{{ completedCount }}/{{ steps.length }}</span>
                <span class="opacity-40">|</span>
                <span class="opacity-60">{{ timerDisplay }}</span>
                <span class="opacity-40">|</span>
                <span :style="statusColor">{{ statusText }}</span>
            </div>
        </div>

        <!-- Main grid -->
        <div class="grid flex-1 min-h-0 gap-4 p-4" style="grid-template-columns:220px 1fr 260px">

            <!-- ─── Sidebar ─────────────────────────────────────── -->
            <aside class="flex flex-col rounded-xl overflow-hidden min-h-0" style="background:var(--surface);border:1px solid var(--border)">
                <div class="flex items-center justify-between px-4 py-3 border-b border-[var(--border-sub)]">
                    <p class="text-[10px] font-semibold uppercase tracking-[0.25em]" style="color:var(--text-4)">Pipeline</p>
                    <button @click="resetAll" class="text-[10px] font-semibold uppercase tracking-[0.2em] transition-colors duration-100" style="color:var(--text-4)">Clear</button>
                </div>
                <div class="flex-1 overflow-y-auto px-3 py-3 space-y-5">
                    <div v-for="group in stepGroups" :key="group.label">
                        <p class="text-[9px] font-semibold uppercase tracking-[0.25em] px-2 mb-2" style="color:#334155">{{ group.label }}</p>
                        <div class="space-y-0.5">
                            <button v-for="step in group.steps" :key="step.key"
                                @click="selectStep(step.key)"
                                class="group flex items-start gap-3 w-full rounded-lg px-3 py-2.5 text-left transition-all duration-100"
                                :style="sidebarItemStyle(step.key)"
                                :class="selectedStep===step.key ? 'ring-1' : 'hover:bg-[rgba(20,30,48,0.6)]'">
                                <!-- Number badge -->
                                <div class="mt-0.5 w-5 h-5 rounded-full flex items-center justify-center text-[9px] font-bold shrink-0 border-2 transition-all duration-150"
                                    :style="sidebarBadgeStyle(step.key)">
                                    {{ stepIndex(step.key) + 1 }}
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center justify-between gap-2">
                                        <p class="text-[11px] font-semibold truncate leading-tight" style="color:var(--text-2)">{{ step.label }}</p>
                                        <span class="mono text-[9px] shrink-0" style="color:var(--text-4)">{{ stepDuration(step.key) }}</span>
                                    </div>
                                    <p class="text-[10px] mt-0.5 truncate leading-relaxed" style="color:var(--text-4)">{{ stepMessage(step.key) }}</p>
                                </div>
                            </button>
                        </div>
                    </div>
                </div>
            </aside>

            <!-- ─── Center panel ───────────────────────────────── -->
            <main class="flex flex-col min-h-0 overflow-hidden">

                <!-- SUMMARY MODE -->
                <div v-if="mode==='summary'" class="flex-1 overflow-y-auto space-y-4">

                    <!-- Writing -->
                    <section class="rounded-xl overflow-hidden animate-fade-up" style="background:var(--surface);border:1px solid var(--border)">
                        <div class="flex items-center justify-between px-5 py-4 border-b border-[var(--border-sub)]">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-xl flex items-center justify-center text-[11px] font-bold" style="background:rgba(99,102,241,0.1);color:#a5b4fc">W</div>
                                <div>
                                    <p class="text-[10px] font-semibold uppercase tracking-[0.25em]" style="color:var(--text-4)">Writing Result</p>
                                    <p class="text-[12px] mt-0.5" style="color:var(--text-3)">{{ writing.question?.topic || 'Awaiting question' }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-4">
                                <div class="text-right">
                                    <p class="text-2xl font-bold leading-none" style="color:var(--text-1)">{{ writing.score ?? '—' }}</p>
                                    <p class="text-[11px] mt-0.5" style="color:var(--text-4)">Band {{ writing.band || '—' }}</p>
                                </div>
                                <span class="rounded-full px-3 py-1 text-[10px] font-semibold uppercase tracking-[0.2em]" :style="statusBadgeStyle(writing.status)">
                                    {{ writing.status || 'idle' }}
                                </span>
                            </div>
                        </div>

                        <!-- Content grid -->
                        <div class="grid gap-4 p-5" :style="writing.status==='completed' ? 'grid-template-columns:1fr 1fr' : ''">

                            <!-- Left: Question + Answer -->
                            <div class="space-y-4">
                                <!-- Question -->
                                <div v-if="writing.question" class="rounded-xl p-4" style="background:var(--card);border:1px solid var(--border-sub)">
                                    <div class="flex items-center gap-2 mb-3">
                                        <span class="text-[10px] font-semibold uppercase tracking-[0.2em]" style="color:var(--brand)">Question</span>
                                        <span class="rounded px-2 py-0.5 text-[9px] font-medium" style="background:rgba(20,30,48,0.8);color:var(--text-4)">{{ writing.question.level }}</span>
                                        <span v-if="writing.question.content && writing.question.content.taskType" class="rounded px-2 py-0.5 text-[9px] font-medium" style="background:rgba(20,30,48,0.8);color:var(--text-4)">{{ writing.question.content.taskType }}</span>
                                    </div>
                                    <p class="text-[13px] leading-relaxed" style="color:var(--text-2)">{{ writing.question.content?.prompt }}</p>
                                    <div v-if="writing.question.content && writing.question.content.requiredPoints && writing.question.content.requiredPoints.length" class="mt-3 space-y-1.5">
                                        <p class="text-[10px] font-semibold uppercase tracking-[0.15em]" style="color:var(--text-4)">Required</p>
                                        <div v-for="pt in writing.question.content.requiredPoints" :key="pt" class="flex items-start gap-2 text-[12px]" style="color:var(--text-3)">
                                            <span style="color:var(--brand)">›</span><span>{{ pt }}</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Answer -->
                                <div v-if="writing.answerText" class="rounded-xl p-4" style="background:var(--card);border:1px solid var(--border-sub)">
                                    <p class="text-[10px] font-semibold uppercase tracking-[0.2em] mb-3" style="color:var(--text-4)">Your answer</p>
                                    <pre class="mono text-[11px] leading-6 overflow-auto max-h-48 whitespace-pre-wrap" style="color:var(--text-2)">{{ writing.answerText }}</pre>
                                </div>

                                <!-- Feedback -->
                                <div v-if="writing.feedback" class="rounded-xl p-4" style="background:var(--card);border:1px solid var(--border-sub)">
                                    <button @click="writing.feedbackOpen = !writing.feedbackOpen" class="flex items-center justify-between w-full text-left">
                                        <p class="text-[10px] font-semibold uppercase tracking-[0.2em]" style="color:var(--text-4)">Feedback</p>
                                        <svg class="w-3.5 h-3.5 transition-transform duration-150" :style="writing.feedbackOpen ? 'transform:rotate(90deg)' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                    </button>
                                    <div v-show="writing.feedbackOpen" class="mt-3 max-h-56 overflow-auto rounded-lg p-3 text-[12px] leading-relaxed whitespace-pre-wrap" style="background:rgba(20,30,48,0.5);color:var(--text-3)">{{ writing.feedback }}</div>
                                </div>
                            </div>

                            <!-- Right: Scores (only when completed) -->
                            <div v-if="writing.status==='completed'" class="space-y-4">
                                <!-- Radar + score -->
                                <div class="rounded-xl p-4" style="background:var(--card);border:1px solid var(--border-sub)">
                                    <div class="flex items-center justify-between mb-3">
                                        <p class="text-[10px] font-semibold uppercase tracking-[0.2em]" style="color:var(--text-4)">Criteria breakdown</p>
                                        <span class="mono text-[11px]" style="color:var(--text-3)">{{ writing.score }} / Band {{ writing.band }}</span>
                                    </div>
                                    <!-- Radar SVG -->
                                    <svg viewBox="0 0 220 180" class="w-full max-w-[260px] mx-auto" v-html="writing.radarSvg"></svg>
                                    <!-- Criteria bars -->
                                    <div class="mt-4 space-y-3">
                                        <div v-for="c in writing.criteria" :key="c.name" class="flex items-center gap-3">
                                            <span class="w-24 text-[11px] truncate" style="color:var(--text-3)">{{ c.name }}</span>
                                            <div class="flex-1 h-1.5 rounded-full overflow-hidden" style="background:rgba(30,41,59,0.8)">
                                                <div class="h-full rounded-full transition-all duration-700" style="width:{{ c.score * 10 }}%;background:var(--brand)"></div>
                                            </div>
                                            <span class="mono w-7 text-right text-[11px] font-semibold" style="color:var(--text-1)">{{ c.score }}</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Knowledge gaps -->
                                <div v-if="writing.knowledgeGaps.length" class="rounded-xl p-4" style="background:var(--card);border:1px solid var(--border-sub)">
                                    <button @click="writing.gapsOpen = !writing.gapsOpen" class="flex items-center justify-between w-full text-left">
                                        <p class="text-[10px] font-semibold uppercase tracking-[0.2em]" style="color:var(--text-4)">Knowledge gaps ({{ writing.knowledgeGaps.length }})</p>
                                        <svg class="w-3.5 h-3.5 transition-transform duration-150" :style="writing.gapsOpen ? 'transform:rotate(90deg)' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                    </button>
                                    <div v-show="writing.gapsOpen" class="mt-3 space-y-2">
                                        <div v-for="g in writing.knowledgeGaps" :key="g.name" class="rounded-lg p-3" style="background:rgba(20,30,48,0.5);border:1px solid var(--border-sub)">
                                            <p class="text-[11px] font-semibold" style="color:var(--text-2)">{{ g.name }}</p>
                                            <p class="text-[10px] mt-1 leading-relaxed" style="color:var(--text-4)">{{ g.description }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- Speaking -->
                    <section class="rounded-xl overflow-hidden animate-fade-up" style="background:var(--surface);border:1px solid var(--border)">
                        <div class="flex items-center justify-between px-5 py-4 border-b border-[var(--border-sub)]">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-xl flex items-center justify-center text-[11px] font-bold" style="background:rgba(56,189,248,0.1);color:#7dd3fc">S</div>
                                <div>
                                    <p class="text-[10px] font-semibold uppercase tracking-[0.25em]" style="color:var(--text-4)">Speaking Result</p>
                                    <p class="text-[12px] mt-0.5" style="color:var(--text-3)">{{ speaking.question?.topic || 'Awaiting question' }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-4">
                                <div class="text-right">
                                    <p class="text-2xl font-bold leading-none" :class="speaking.score !== null ? 'animate-score' : ''" style="color:var(--text-1)">{{ speaking.score ?? '—' }}</p>
                                    <p class="text-[11px] mt-0.5" style="color:var(--text-4)">{{ speaking.resultType || '—' }}</p>
                                </div>
                                <span class="rounded-full px-3 py-1 text-[10px] font-semibold uppercase tracking-[0.2em]" :style="statusBadgeStyle(speaking.status)">
                                    {{ speaking.status || 'idle' }}
                                </span>
                            </div>
                        </div>

                        <div class="grid gap-4 p-5" :style="speaking.status==='completed' ? 'grid-template-columns:1fr 1fr' : ''">

                            <!-- Left: Question + Audio + Transcript -->
                            <div class="space-y-4">
                                <!-- Question -->
                                <div v-if="speaking.question" class="rounded-xl p-4" style="background:var(--card);border:1px solid var(--border-sub)">
                                    <div class="flex items-center gap-2 mb-3">
                                        <span class="text-[10px] font-semibold uppercase tracking-[0.2em]" style="color:var(--brand)">Question</span>
                                        <span class="rounded px-2 py-0.5 text-[9px] font-medium" style="background:rgba(20,30,48,0.8);color:var(--text-4)">{{ speaking.question.level }}</span>
                                        <span v-if="speaking.question.content && speaking.question.content.preparationSeconds" class="rounded px-2 py-0.5 text-[9px] font-medium" style="background:rgba(20,30,48,0.8);color:var(--text-4)">{{ speaking.question.content.preparationSeconds }}s prep</span>
                                        <span v-if="speaking.question.content && speaking.question.content.speakingSeconds" class="rounded px-2 py-0.5 text-[9px] font-medium" style="background:rgba(20,30,48,0.8);color:var(--text-4)">{{ speaking.question.content.speakingSeconds }}s speak</span>
                                    </div>
                                    <div class="space-y-2">
                                        <template v-if="speaking.question.content && speaking.question.content.topics && speaking.question.content.topics[0] && speaking.question.content.topics[0].questions && speaking.question.content.topics[0].questions.length">
                                            <div v-for="(q,idx) in speaking.question.content.topics[0].questions" :key="idx" class="flex items-start gap-2 text-[12px]" style="color:var(--text-2)">
                                                <span style="color:var(--brand)">›</span><span>{{ q }}</span>
                                            </div>
                                        </template>
                                        <div v-else-if="speaking.question.content && speaking.question.content.situation" class="text-[12px] italic" style="color:var(--text-3)">{{ speaking.question.content.situation }}</div>
                                    </div>
                                </div>

                                <!-- Audio player -->
                                <div v-if="speaking.audioUrl" class="rounded-xl p-4" style="background:var(--card);border:1px solid var(--border-sub)">
                                    <p class="text-[10px] font-semibold uppercase tracking-[0.2em] mb-3" style="color:var(--text-4)">Your recording</p>
                                    <audio class="w-full" controls preload="metadata" :src="speaking.audioUrl"></audio>
                                </div>

                                <!-- Transcript -->
                                <div v-if="speaking.transcriptHtml" class="rounded-xl p-4" style="background:var(--card);border:1px solid var(--border-sub)">
                                    <p class="text-[10px] font-semibold uppercase tracking-[0.2em] mb-3" style="color:var(--text-4)">Transcript</p>
                                    <div class="rounded-lg p-4 text-[13px] leading-7" style="background:rgba(20,30,48,0.5)" v-html="speaking.transcriptHtml"></div>
                                </div>
                            </div>

                            <!-- Right: Scores + Errors -->
                            <div v-if="speaking.status==='completed'" class="space-y-4">
                                <!-- Pronunciation metrics -->
                                <div class="rounded-xl p-4" style="background:var(--card);border:1px solid var(--border-sub)">
                                    <p class="text-[10px] font-semibold uppercase tracking-[0.2em] mb-4" style="color:var(--text-4)">Pronunciation</p>
                                    <div class="space-y-4">
                                        <div v-for="m in speakingMetrics" :key="m.key">
                                            <div class="flex items-center justify-between mb-1.5">
                                                <span class="text-[11px]" :style="'color:'+m.color">{{ m.label }}</span>
                                                <span class="mono text-[12px] font-bold" :style="'color:'+m.color">{{ m.value }}</span>
                                            </div>
                                            <div class="h-2 rounded-full overflow-hidden" style="background:rgba(30,41,59,0.8)">
                                                <div class="h-full rounded-full transition-all duration-700" :style="'width:'+m.pct+'%;background:'+m.color"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Word errors -->
                                <div v-if="speaking.wordErrors.length" class="rounded-xl p-4" style="background:var(--card);border:1px solid rgba(244,63,94,0.15)">
                                    <p class="text-[10px] font-semibold uppercase tracking-[0.2em] mb-3" style="color:#fb7185">Pronunciation errors ({{ speaking.wordErrors.length }})</p>
                                    <div class="space-y-2">
                                        <div v-for="err in speaking.wordErrors" :key="err.word+err.accuracy_score"
                                            class="flex items-center justify-between rounded-lg px-3 py-2"
                                            style="background:rgba(244,63,94,0.06);border:1px solid rgba(244,63,94,0.15)">
                                            <div>
                                                <p class="text-[12px] font-semibold" style="color:#fda4af">{{ err.word }}</p>
                                                <p class="text-[10px]" style="color:#fb923c">{{ err.error_type }}</p>
                                            </div>
                                            <span class="mono text-[11px]" style="color:var(--text-4)">{{ err.accuracy_score }}/100</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                </div>

                <!-- DEBUG MODE -->
                <div v-else class="flex-1 flex flex-col min-h-0 rounded-xl overflow-hidden" style="background:var(--surface);border:1px solid var(--border)">
                    <!-- Debug header -->
                    <div class="flex items-center justify-between px-5 py-3 border-b border-[var(--border-sub)] shrink-0">
                        <div>
                            <p class="text-[10px] font-semibold uppercase tracking-[0.25em]" style="color:var(--text-4)">Debug Inspector</p>
                            <p class="text-sm font-semibold mt-0.5" style="color:var(--text-1)">{{ selectedMeta?.label || 'Select a step' }}</p>
                        </div>
                        <div class="flex items-center gap-3 mono text-[11px]" style="color:var(--text-4)">
                            <span v-if="selectedState && selectedState.latency">{{ selectedState.latency }}ms</span>
                            <span v-if="selectedState && selectedState.duration">{{ (selectedState.duration/1000).toFixed(1) }}s</span>
                        </div>
                    </div>
                    <!-- Tabs -->
                    <div class="flex border-b border-[var(--border-sub)] shrink-0">
                        <button v-for="t in ['request','response','log']" :key="t"
                            @click="debugTab=t"
                            class="px-5 py-3 text-[11px] font-semibold uppercase tracking-[0.2em] border-b-2 transition-all duration-150"
                            :style="debugTab===t ? 'border-color:var(--brand);color:var(--text-1)' : 'border-color:transparent;color:var(--text-4)'">
                            {{ t }}
                        </button>
                    </div>
                    <!-- Tab content -->
                    <div class="flex-1 overflow-auto p-5">
                        <template v-if="selectedStep">
                            <!-- Request -->
                            <div v-if="debugTab==='request'">
                                <div class="flex items-center gap-3 mb-4">
                                    <span class="rounded-lg px-2.5 py-1 text-[10px] font-bold uppercase" style="background:rgba(99,102,241,0.15);color:var(--brand)">{{ selectedState?.request?.method || '—' }}</span>
                                    <span class="mono text-[13px]" style="color:var(--text-2)">{{ selectedState?.request?.path || '—' }}</span>
                                </div>
                                <pre class="rounded-xl p-4 text-[11px] leading-relaxed overflow-auto mono" style="background:var(--card);border:1px solid var(--border-sub);color:var(--text-2)" v-html="highlightJson(selectedState?.request?.body)"></pre>
                            </div>
                            <!-- Response -->
                            <div v-else-if="debugTab==='response'">
                                <div class="flex items-center gap-3 mb-4">
                                    <span v-if="selectedState && selectedState.statusCode"
                                        class="rounded-lg px-2.5 py-1 text-[10px] font-bold uppercase"
                                        :style="selectedState.statusCode >= 200 && selectedState.statusCode < 300 ? 'background:rgba(34,197,94,0.1);color:#4ade80' : 'background:rgba(244,63,94,0.1);color:#fb7185'">
                                        {{ selectedState.statusCode }}
                                    </span>
                                    <span class="mono text-[11px]" style="color:var(--text-4)">{{ selectedState?.response ? 'Response' : 'No response' }}</span>
                                </div>
                                <pre class="rounded-xl p-4 text-[11px] leading-relaxed overflow-auto mono" style="background:var(--card);border:1px solid var(--border-sub);color:var(--text-2)" v-html="highlightJson(selectedState?.response)"></pre>
                            </div>
                            <!-- Log -->
                            <div v-else class="space-y-3">
                                <div v-for="entry in selectedLogs" :key="entry.time+entry.title"
                                    class="rounded-xl p-4" style="background:var(--card);border:1px solid var(--border-sub)">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-[10px] font-semibold uppercase tracking-[0.2em]" style="color:var(--brand)">{{ entry.title }}</span>
                                        <span class="mono text-[10px]" style="color:var(--text-4)">{{ formatClock(entry.time) }}</span>
                                    </div>
                                    <pre class="text-[11px] leading-relaxed overflow-auto mono" style="color:var(--text-3)" v-html="highlightJson(entry.payload)"></pre>
                                </div>
                                <p v-if="!selectedLogs.length" class="text-center text-[12px]" style="color:var(--text-4)">No log entries</p>
                            </div>
                        </template>
                        <div v-else class="flex items-center justify-center h-full">
                            <p class="text-[13px]" style="color:var(--text-4)">Select a step from the sidebar</p>
                        </div>
                    </div>
                </div>
            </main>

            <!-- ─── Right panel: Exam ─────────────────────────────── -->
            <aside class="flex flex-col rounded-xl overflow-hidden min-h-0" style="background:var(--surface);border:1px solid var(--border)">
                <div class="px-4 py-3 border-b border-[var(--border-sub)]">
                    <p class="text-[10px] font-semibold uppercase tracking-[0.25em]" style="color:var(--text-4)">Exam Session</p>
                    <p class="text-[13px] font-semibold mt-1" style="color:var(--text-1)">{{ exam.title || 'No exam started' }}</p>
                    <div class="flex items-center justify-between gap-2 mt-0.5">
                        <p class="mono text-[10px]" style="color:var(--text-4)">{{ exam.sessionId ? '#'+exam.sessionId.slice(0,8) : '—' }}</p>
                        <span v-if="exam.totalQuestions" class="rounded-full px-2.5 py-1 text-[9px] font-semibold uppercase tracking-[0.15em]" style="background:rgba(99,102,241,0.12);color:#a5b4fc">
                            {{ exam.answeredQuestions }}/{{ exam.totalQuestions }} answered
                        </span>
                    </div>
                    <div v-if="exam.overallScore !== null" class="flex items-center gap-4 mt-3 pt-3 border-t border-[var(--border-sub)]">
                        <div class="text-center">
                            <p class="text-[9px] font-semibold uppercase tracking-[0.2em]" style="color:var(--text-4)">Overall</p>
                            <p class="text-xl font-bold leading-none" style="color:var(--text-1)">{{ exam.overallScore }}</p>
                            <p class="text-[10px] mt-0.5" style="color:var(--text-3)">Band {{ exam.overallBand }}</p>
                        </div>
                        <div class="flex-1 grid grid-cols-2 gap-x-3 gap-y-1.5 text-[10px]">
                            <div class="flex items-center justify-between">
                                <span style="color:var(--text-4)">Listening</span>
                                <span class="mono font-semibold" style="color:var(--text-1)">{{ exam.listeningScore ?? '—' }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span style="color:var(--text-4)">Reading</span>
                                <span class="mono font-semibold" style="color:var(--text-1)">{{ exam.readingScore ?? '—' }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span style="color:var(--text-4)">Writing</span>
                                <span class="mono font-semibold" style="color:var(--text-1)">{{ exam.writingScore ?? '—' }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span style="color:var(--text-4)">Speaking</span>
                                <span class="mono font-semibold" style="color:var(--text-1)">{{ exam.speakingScore ?? '—' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex-1 overflow-y-auto p-4 space-y-3">
                    <div v-for="section in exam.sections" :key="section.skill+'-'+section.part"
                        class="rounded-xl p-4" style="background:var(--card);border:1px solid var(--border-sub)">
                        <div class="flex items-center justify-between gap-2 mb-2">
                            <div class="flex items-center gap-2">
                                <span class="rounded-full px-2.5 py-0.5 text-[9px] font-bold uppercase tracking-[0.15em]" :style="skillBadgeStyle(section.skill)">{{ section.skill }}</span>
                                <span class="text-[11px]" style="color:var(--text-4)">Part {{ section.part }}</span>
                            </div>
                            <span class="mono text-[10px]" :style="section.answeredCount === section.questionCount && section.questionCount ? 'color:#4ade80' : 'color:var(--text-4)'">
                                {{ section.answeredCount }}/{{ section.questionCount || 0 }}
                            </span>
                        </div>
                        <p class="text-[12px] font-semibold" style="color:var(--text-2)">{{ section.topic || '—' }}</p>
                        <div v-if="section.questions && section.questions.length" class="mt-2 space-y-1.5">
                            <div v-for="(q,idx) in section.questions" :key="idx" class="flex items-start gap-2 text-[11px]" style="color:var(--text-3)">
                                <span style="color:var(--brand)">›</span><span>{{ q }}</span>
                            </div>
                        </div>
                        <p v-if="section.prompt" class="mt-2 text-[11px] leading-relaxed italic" style="color:var(--text-4)">{{ section.prompt }}</p>
                        <div class="flex flex-wrap gap-2 mt-3">
                            <span v-if="section.preparationSeconds" class="rounded px-2 py-0.5 text-[9px]" style="background:rgba(20,30,48,0.8);color:var(--text-4)">{{ section.preparationSeconds }}s prep</span>
                            <span v-if="section.speakingSeconds" class="rounded px-2 py-0.5 text-[9px]" style="background:rgba(20,30,48,0.8);color:var(--text-4)">{{ section.speakingSeconds }}s speak</span>
                            <span v-if="section.minWords" class="rounded px-2 py-0.5 text-[9px]" style="background:rgba(20,30,48,0.8);color:var(--text-4)">{{ section.minWords }} words</span>
                        </div>
                    </div>
                    <p v-if="!exam.sections.length" class="text-center text-[11px]" style="color:var(--text-4)">No exam started yet</p>
                </div>
            </aside>
        </div>
    </div>
</div>
@endverbatim

<script>
const { createApp } = Vue

createApp({
    data() {
        return {
            apiBase: `${location.origin}/api/v1`,
            mode: 'summary',
            debugTab: 'request',
            running: false,
            loading: false,
            selectedStep: null,
            startAt: null,
            timerId: null,
            elapsed: 0,
            steps: [
                { key: 'register',       label: 'Register user',            group: 'Auth'     },
                { key: 'login',          label: 'Login',                    group: 'Auth'     },
                { key: 'onboarding',     label: 'Self-assess onboarding', group: 'Onboard'  },
                { key: 'writingStart',   label: 'Start writing',          group: 'Writing'  },
                { key: 'writingSubmit',  label: 'Submit writing',         group: 'Writing'  },
                { key: 'writingPoll',    label: 'Poll writing result',    group: 'Writing'  },
                { key: 'speakingStart',  label: 'Start speaking',         group: 'Speaking' },
                { key: 'speakingUpload', label: 'Upload speaking audio',  group: 'Speaking' },
                { key: 'speakingSubmit', label: 'Submit speaking',        group: 'Speaking' },
                { key: 'speakingPoll',   label: 'Poll speaking result',   group: 'Speaking' },
                { key: 'examStart',      label: 'Start exam',             group: 'Exam'     },
                { key: 'examAnswer',     label: 'Save full exam answers', group: 'Exam'     },
                { key: 'examSubmit',     label: 'Submit & grade exam',    group: 'Exam'     },
            ],
            stepStates: {},
            stepLogs: [],
            ctx: { token: null, email: null, password: 'secret123',
                   writingSessionId: null, writingSubmissionId: null,
                   speakingSessionId: null, speakingSubmissionId: null,
                   examSessionId: null },
            writing: {
                status: null, question: null, answerText: '', score: null, band: null,
                criteria: [], feedback: '', feedbackOpen: true, knowledgeGaps: [], gapsOpen: false, radarSvg: '',
            },
            speaking: {
                status: null, question: null, audioPath: '', audioUrl: '',
                resultType: '', score: null,
                pronunciation: { accuracy_score: null, fluency_score: null, prosody_score: null },
                transcript: '', transcriptHtml: '', wordErrors: [],
            },
            exam: { title: '', sessionId: '', sections: [], totalQuestions: 0, answeredQuestions: 0,
                status: null, overallScore: null, overallBand: null,
                listeningScore: null, readingScore: null, writingScore: null, speakingScore: null },
            runLogs: [],
        }
    },
    computed: {
        stepGroups() {
            const groups = {}
            for (const s of this.steps) {
                if (!groups[s.group]) groups[s.group] = []
                groups[s.group].push(s)
            }
            return Object.entries(groups).map(([label, steps]) => ({ label, steps }))
        },
        completedCount() {
            return this.steps.filter(s => ['success','error'].includes(this.stepStates[s.key]?.status)).length
        },
        statusText() {
            if (this.running) return 'Running'
            if (!this.completedCount) return 'Idle'
            return Object.values(this.stepStates).some(s => s?.status === 'error') ? 'Failed' : 'Success'
        },
        statusColor() {
            if (this.running) return 'color:#fbbf24'
            if (!this.completedCount) return 'color:var(--text-4)'
            return Object.values(this.stepStates).some(s => s?.status === 'error') ? 'color:#fb7185' : 'color:#4ade80'
        },
        timerDisplay() {
            const m = String(Math.floor(this.elapsed/60)).padStart(2,'0')
            const s = String(this.elapsed%60).padStart(2,'0')
            return `${m}:${s}`
        },
        selectedMeta() {
            return this.steps.find(s => s.key === this.selectedStep) || null
        },
        selectedState() {
            return this.selectedStep ? this.stepStates[this.selectedStep] || null : null
        },
        selectedLogs() {
            return this.selectedStep ? this.stepLogs.filter(l => l.step === this.selectedStep) : []
        },
        speakingMetrics() {
            const p = this.speaking.pronunciation
            return [
                { key: 'accuracy', label: 'Accuracy', value: p.accuracy_score ?? '—', pct: p.accuracy_score ?? 0, color: '#fbbf24' },
                { key: 'fluency',  label: 'Fluency',  value: p.fluency_score  ?? '—', pct: p.fluency_score  ?? 0, color: '#4ade80' },
                { key: 'prosody',  label: 'Prosody',  value: p.prosody_score  ? (Math.round(p.prosody_score*10)/10) : '—', pct: p.prosody_score ?? 0, color: '#38bdf8' },
            ]
        },
    },
    created() {
        this.resetStates()
    },
    methods: {
        resetStates() {
            for (const s of this.steps) {
                this.stepStates[s.key] = { status: null, message: 'Pending', latency: null, duration: null, statusCode: null, request: null, response: null }
            }
        },
        resetAll() {
            this.running = false; this.loading = false; this.selectedStep = null
            clearInterval(this.timerId); this.elapsed = 0; this.startAt = null
            this.stepLogs = []; this.runLogs = []
            this.ctx = { token: null, email: null, password: 'secret123', writingSessionId: null, writingSubmissionId: null, speakingSessionId: null, speakingSubmissionId: null, examSessionId: null }
            this.writing = { status: null, question: null, answerText: '', score: null, band: null, criteria: [], feedback: '', feedbackOpen: true, knowledgeGaps: [], gapsOpen: false, radarSvg: '' }
            this.speaking = { status: null, question: null, audioPath: '', audioUrl: '', resultType: '', score: null, pronunciation: { accuracy_score: null, fluency_score: null, prosody_score: null }, transcript: '', transcriptHtml: '', wordErrors: [] }
            this.exam = { title: '', sessionId: '', sections: [], totalQuestions: 0, answeredQuestions: 0,
                status: null, overallScore: null, overallBand: null,
                listeningScore: null, readingScore: null, writingScore: null, speakingScore: null }
            this.resetStates()
        },
        async startRun() {
            this.resetAll()
            this.running = true
            this.startAt = Date.now()
            this.timerId = setInterval(() => { this.elapsed = Math.floor((Date.now() - this.startAt) / 1000) }, 1000)
            for (const step of this.steps) {
                if (!this.running) break
                try { await this.runStep(step.key) }
                catch { break }
            }
            this.running = false
            clearInterval(this.timerId)
        },
        stopRun() { this.running = false },
        selectStep(key) {
            this.selectedStep = key
            this.mode = 'debug'
        },
        stepIndex(key) { return this.steps.findIndex(s => s.key === key) },
        stepDuration(key) {
            const d = this.stepStates[key]?.duration
            return d ? `${(d/1000).toFixed(1)}s` : '—'
        },
        stepMessage(key) { return this.stepStates[key]?.message || 'Pending' },
        stepDotStyle(key) {
            const s = this.stepStates[key]?.status
            if (s === 'running') return 'border-color:#f59e0b;background:#f59e0b;box-shadow:0 0 0 3px rgba(245,158,11,0.2),0 0 12px rgba(245,158,11,0.4);color:#f59e0b'
            if (s === 'success') return 'border-color:#22c55e;background:#22c55e;box-shadow:0 0 0 3px rgba(34,197,94,0.15),0 0 10px rgba(34,197,94,0.3);color:#22c55e'
            if (s === 'error') return 'border-color:#f43f5e;background:#f43f5e;box-shadow:0 0 0 3px rgba(244,63,94,0.15),0 0 10px rgba(244,63,94,0.3);color:#f43f5e'
            return 'border-color:#334155;background:transparent;color:#334155'
        },
        stepLineStyle(i) {
            const curr = this.steps[i].key
            const next = this.steps[i+1]?.key
            const cs = this.stepStates[curr]?.status
            const ns = this.stepStates[next]?.status
            if (cs === 'success' || ns === 'success' || cs === 'error' || ns === 'error') return 'background:#475569'
            if (cs === 'running' || ns === 'running') return 'background:rgba(245,158,11,0.5)'
            return 'background:#1e293b'
        },
        sidebarItemStyle(key) {
            const sel = this.selectedStep === key
            if (sel) return 'background:rgba(20,30,48,0.9);box-shadow:0 0 0 1px rgba(99,102,241,0.3)'
            return ''
        },
        sidebarBadgeStyle(key) {
            const s = this.stepStates[key]?.status
            if (s === 'running') return 'border-color:#f59e0b;background:#f59e0b;color:#070b12'
            if (s === 'success') return 'border-color:#22c55e;background:#22c55e;color:#070b12'
            if (s === 'error') return 'border-color:#f43f5e;background:#f43f5e;color:#fff'
            return 'border-color:#334155;background:transparent;color:#475569'
        },
        statusBadgeStyle(status) {
            if (status === 'completed' || status === 'success') return 'background:rgba(34,197,94,0.08);border:1px solid rgba(34,197,94,0.2);color:#4ade80'
            if (status === 'processing' || status === 'running') return 'background:rgba(245,158,11,0.08);border:1px solid rgba(245,158,11,0.2);color:#fbbf24'
            if (status === 'failed' || status === 'error') return 'background:rgba(244,63,94,0.08);border:1px solid rgba(244,63,94,0.2);color:#fb7185'
            return 'background:rgba(20,30,48,0.8);border:1px solid rgba(51,65,85,0.5);color:#64748b'
        },
        skillBadgeStyle(skill) {
            if (skill === 'writing') return 'background:rgba(99,102,241,0.1);color:#a5b4fc'
            if (skill === 'speaking') return 'background:rgba(56,189,248,0.1);color:#7dd3fc'
            if (skill === 'reading') return 'background:rgba(34,197,94,0.1);color:#86efac'
            if (skill === 'listening') return 'background:rgba(245,158,11,0.1);color:#fbbf24'
            return 'background:rgba(51,65,85,0.3);color:#94a3b8'
        },
        async runStep(key) {
            const t0 = performance.now()
            this.selectStep(key)
            this.stepStates[key] = { status: 'running', message: '...', latency: null, duration: null, statusCode: null, request: null, response: null }
            try {
                switch (key) {
                    case 'register':       await this.sRegister(); break
                    case 'login':           await this.sLogin(); break
                    case 'onboarding':      await this.sOnboarding(); break
                    case 'writingStart':    await this.sWritingStart(); break
                    case 'writingSubmit':   await this.sWritingSubmit(); break
                    case 'writingPoll':     await this.sWritingPoll(); break
                    case 'speakingStart':   await this.sSpeakingStart(); break
                    case 'speakingUpload':  await this.sSpeakingUpload(); break
                    case 'speakingSubmit':  await this.sSpeakingSubmit(); break
                    case 'speakingPoll':    await this.sSpeakingPoll(); break
                    case 'examStart':       await this.sExamStart(); break
                    case 'examAnswer':      await this.sExamAnswer(); break
                    case 'examSubmit':      await this.sExamSubmit(); break
                }
                this.stepStates[key].status = 'success'
                this.stepStates[key].duration = Math.round(performance.now() - t0)
            } catch(e) {
                this.stepStates[key].status = 'error'
                this.stepStates[key].message = e.message
                this.stepStates[key].duration = Math.round(performance.now() - t0)
                this.pushLog(key, 'error', { message: e.message })
                throw e
            }
        },
        async apiCall(key, method, path, body = null, auth = true) {
            const h = { Accept: 'application/json' }
            if (!(body instanceof FormData)) h['Content-Type'] = 'application/json'
            if (auth && this.ctx.token) h['Authorization'] = `Bearer ${this.ctx.token}`
            this.stepStates[key].request = { method, path, body }
            const t0 = performance.now()
            const r = await fetch(`${this.apiBase}${path}`, { method, headers: h, body: body instanceof FormData ? body : body ? JSON.stringify(body) : undefined })
            const latency = Math.round(performance.now() - t0)
            let raw = await r.text(), json = {}
            try { json = JSON.parse(raw) } catch { json = { raw } }
            this.stepStates[key].latency = latency
            this.stepStates[key].statusCode = r.status
            const data = json.data ?? json
            this.stepStates[key].response = data
            this.pushLog(key, `${method} ${path}`, data)
            if (!r.ok) throw Object.assign(new Error(data?.message || `HTTP ${r.status}`), { response: data })
            return data
        },
        async poll(key, id) {
            for (let i = 0; i < 20; i++) {
                const d = await this.apiCall(key, 'GET', `/submissions/${id}`)
                if (['completed','review_pending','failed'].includes(d.status)) return d
                await new Promise(r => setTimeout(r, 3000))
            }
            throw new Error('Polling timed out')
        },
        async getAudioUrl(path) {
            try {
                const d = await this.apiCall('speakingPoll', 'GET', `/audio/presign?path=${encodeURIComponent(path)}`)
                return d.url || ''
            } catch { return '' }
        },
        async uploadAudio(key) {
            const blob = await (await fetch('/e2e-speaking-sample.wav')).blob()
            const presign = await this.apiCall(key, 'POST', '/uploads/presign', { content_type: 'audio/wav', file_size: blob.size })
            await fetch(presign.upload_url, { method: 'PUT', headers: { ...presign.headers, 'Content-Type': 'audio/wav' }, body: blob })
            return presign.audio_path
        },
        pushLog(step, title, payload) {
            this.stepLogs.unshift({ step, title, payload, time: new Date().toISOString() })
            this.runLogs.push({ step, title, payload, time: new Date().toISOString() })
        },
        renderRadar(criteria) {
            if (!criteria?.length) return ''
            const cx = 110, cy = 90, maxR = 64, n = criteria.length, step = (Math.PI * 2) / n
            const short = { 'Hoàn thành yêu cầu': 'Task', 'Tổ chức bài viết': 'Org', 'Từ vựng': 'Vocab', 'Ngữ pháp': 'Grammar' }
            const rings = [0.25, 0.5, 0.75, 1].map(r => {
                const pts = Array.from({length:n},(_,i) => {
                    const a = i*step - Math.PI/2
                    return `${cx+r*maxR*Math.cos(a)},${cy+r*maxR*Math.sin(a)}`
                }).join(' ')
                return `<polygon points="${pts}" fill="none" stroke="#1e293b" stroke-width="1"/>`
            }).join('')
            const axes = criteria.map((_,i) => {
                const a = i*step - Math.PI/2
                return `<line x1="${cx}" y1="${cy}" x2="${cx+maxR*Math.cos(a)}" y2="${cy+maxR*Math.sin(a)}" stroke="#1e293b" stroke-width="1"/>`
            }).join('')
            const lbls = criteria.map((c,i) => {
                const a = i*step - Math.PI/2
                const lx = cx+(maxR+18)*Math.cos(a), ly = cy+(maxR+18)*Math.sin(a)
                return `<text x="${lx}" y="${ly}" text-anchor="middle" dominant-baseline="middle" font-size="10" fill="#64748b">${short[c.name]||c.name}</text>`
            }).join('')
            const pts = criteria.map((c,i) => {
                const a = i*step - Math.PI/2, r = maxR*(c.score/10)
                return `${cx+r*Math.cos(a)},${cy+r*Math.sin(a)}`
            }).join(' ')
            const dots = criteria.map((c,i) => {
                const a = i*step - Math.PI/2, r = maxR*(c.score/10)
                return `<circle cx="${cx+r*Math.cos(a)}" cy="${cy+r*Math.sin(a)}" r="3" fill="#818cf8"/>`
            }).join('')
            return `${rings}${axes}<polygon points="${pts}" fill="rgba(99,102,241,0.18)" stroke="#6366f1" stroke-width="2" class="animate-radar"/>${dots}${lbls}`
        },
        renderTranscript(text, errors) {
            if (!text) return ''
            let h = this.esc(text)
            for (const e of errors||[]) h = h.replace(new RegExp(`\\b${this.escRe(e.word)}\\b`, 'g'),
                `<mark style="background:rgba(244,63,94,0.15);color:#fda4af;border-radius:3px;padding:0 2px">${this.esc(e.word)}</mark>`)
            return h
        },
        highlightJson(v) {
            let j = typeof v === 'string' ? v : JSON.stringify(v ?? {}, null, 2)
            return j.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(
                /("(\\u[a-zA-Z0-9]{4}|\\[^u]|[^\\"])*"(\s*:)?|\b(true|false|null)\b|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?)/g,
                m => {
                    if (/^"/.test(m)) return /:$/.test(m) ? `<span class=jk>${m}</span>` : `<span class=js>${m}</span>`
                    if (/true|false/.test(m)) return `<span class=jb>${m}</span>`
                    if (/null/.test(m)) return `<span class=jx>${m}</span>`
                    return `<span class=jn>${m}</span>`
                })
        },
        buildExamAnswer(question, sharedAudioPath) {
            const skill = typeof question.skill === 'string' ? question.skill : question.skill?.value
            if (skill === 'speaking') return { audio_path: sharedAudioPath }
            if (skill === 'writing') {
                const prompt = question.content?.prompt || question.topic || 'the assigned topic'
                return {
                    text: `This is a sample E2E writing response for ${prompt}. It provides a clear introduction, supporting ideas, and a short conclusion so the exam session is fully populated for QA review.`
                }
            }

            const correctAnswers = question.answer_key?.correctAnswers
            if (correctAnswers) return { answers: Array.isArray(correctAnswers) ? correctAnswers : Object.values(correctAnswers) }

            const blanks = question.content?.questions?.length || question.content?.items?.length || 1
            return { answers: Array.from({ length: blanks }, () => 'A') }
        },
        syncExamSections(detail) {
            const questions = detail.questions || []
            const answers = detail.answers || []
            this.exam.totalQuestions = questions.length
            this.exam.answeredQuestions = answers.length
            this.exam.sections = (detail.exam?.sections || []).map(sec => {
                const sectionQuestionIds = sec.question_ids || []
                const sectionQuestions = questions.filter(q => sectionQuestionIds.includes(q.id))
                const first = sectionQuestions[0] || null
                return {
                    skill: sec.skill,
                    part: sec.part,
                    level: first?.level || '',
                    topic: first?.topic || '',
                    prompt: first?.content?.prompt || '',
                    questions: first?.content?.topics?.[0]?.questions || [],
                    preparationSeconds: first?.content?.preparationSeconds || null,
                    speakingSeconds: first?.content?.speakingSeconds || null,
                    minWords: first?.content?.minWords || null,
                    questionCount: sectionQuestions.length,
                    answeredCount: sectionQuestions.filter(q => answers.some(a => a.question_id === q.id)).length,
                }
            })
        },
        formatClock(iso) {
            return new Date(iso).toLocaleTimeString('en-GB',{hour12:false})
        },
        esc(s) { return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;') },
        escRe(s) { return String(s).replace(/[.*+?^${}()|[\]\\]/g,'\\$&') },
        exportRun() {
            const blob = new Blob([JSON.stringify({ exportedAt: new Date().toISOString(), elapsed: this.elapsed, steps: this.stepStates, writing: this.writing, speaking: this.speaking, exam: this.exam, logs: this.runLogs }, null, 2)], {type:'application/json'})
            const a = document.createElement('a'); a.href = URL.createObjectURL(blob); a.download = `e2e-${Date.now()}.json`; a.click(); URL.revokeObjectURL(a.href)
        },
        // ─── Steps ───────────────────────────────────────────────
        async sRegister() {
            const email = `demo_${Date.now()}_${Math.random().toString(16).slice(2,6)}@example.com`
            this.ctx.email = email
            const d = await this.apiCall('register', 'POST', '/auth/register', { email, password: this.ctx.password, full_name: 'Blade Demo User' }, false)
            this.ctx.token = d.access_token
            this.stepStates.register.message = d.user?.id?.slice(0,8) || 'OK'
        },
        async sLogin() {
            const d = await this.apiCall('login', 'POST', '/auth/login', { email: this.ctx.email, password: this.ctx.password }, false)
            this.ctx.token = d.access_token
            this.stepStates.login.message = d.user?.full_name || 'OK'
        },
        async sOnboarding() {
            const d = await this.apiCall('onboarding', 'POST', '/onboarding/self-assess', { listening:'B1', reading:'B1', writing:'A2', speaking:'A2', target_band:'B1', daily_study_time_minutes:45, deadline:'2026-12-31' })
            this.stepStates.onboarding.message = `Band ${d.estimated_band}`
        },
        async sWritingStart() {
            const d = await this.apiCall('writingStart', 'POST', '/practice/sessions', { skill:'writing', mode:'guided', items_count:1, part:1 })
            this.ctx.writingSessionId = d.session.id
            this.writing.question = d.current_item?.question || null
            this.stepStates.writingStart.message = d.current_item?.writing_scaffold?.type || 'Started'
        },
        async sWritingSubmit() {
            const text = 'Dear Lan,\n\nI am happy to invite you to visit my hometown next Saturday. You can come by bus and stay at my house. We will eat local food, walk around the lake, and visit the night market together.\n\nBest regards,\nLinh'
            this.writing.answerText = text
            const d = await this.apiCall('writingSubmit', 'POST', `/practice/sessions/${this.ctx.writingSessionId}/submit`, { answer: { text } })
            this.ctx.writingSubmissionId = d.submission_id
            this.stepStates.writingSubmit.message = d.submission_id?.slice(0,8) || 'Submitted'
        },
        async sWritingPoll() {
            const d = await this.poll('writingPoll', this.ctx.writingSubmissionId)
            this.writing.status = d.status
            this.writing.score = d.score
            this.writing.band = d.band
            this.writing.criteria = d.result?.criteria || []
            this.writing.feedback = d.feedback || ''
            this.writing.knowledgeGaps = d.result?.knowledge_gaps || []
            this.writing.radarSvg = this.renderRadar(this.writing.criteria)
            this.stepStates.writingPoll.message = `${d.score} (${d.band})`
        },
        async sSpeakingStart() {
            const d = await this.apiCall('speakingStart', 'POST', '/practice/sessions', { skill:'speaking', mode:'shadowing', items_count:1 })
            this.ctx.speakingSessionId = d.session.id
            this.speaking.question = d.current_item?.question || null
            this.stepStates.speakingStart.message = this.speaking.question?.topic || 'Started'
        },
        async sSpeakingUpload() {
            const audioPath = await this.uploadAudio('speakingUpload')
            this.ctx.speakingAudioPath = audioPath
            this.stepStates.speakingUpload.message = audioPath.split('/').pop()
        },
        async sSpeakingSubmit() {
            const d = await this.apiCall('speakingSubmit', 'POST', `/practice/sessions/${this.ctx.speakingSessionId}/submit`, { answer: { audio_path: this.ctx.speakingAudioPath } })
            this.ctx.speakingSubmissionId = d.submission_id
            this.stepStates.speakingSubmit.message = d.submission_id?.slice(0,8) || 'Submitted'
        },
        async sSpeakingPoll() {
            const d = await this.poll('speakingPoll', this.ctx.speakingSubmissionId)
            this.speaking.status = d.status
            this.speaking.audioPath = d.answer?.audio_path || ''
            this.speaking.audioUrl = await this.getAudioUrl(this.speaking.audioPath)
            this.speaking.resultType = d.result?.type || ''
            this.speaking.score = d.score
            this.speaking.pronunciation = d.result?.pronunciation || { accuracy_score:null, fluency_score:null, prosody_score:null }
            this.speaking.transcript = d.result?.pronunciation?.transcript || ''
            this.speaking.wordErrors = d.result?.pronunciation?.word_errors || []
            this.speaking.transcriptHtml = this.renderTranscript(this.speaking.transcript, this.speaking.wordErrors)
            this.stepStates.speakingPoll.message = `Score ${d.score}`
        },
        async sExamStart() {
            const exams = await this.apiCall('examStart', 'GET', '/exams')
            const exam = exams.find(e => e.title === 'VSTEP Mock B1')
            if (!exam) throw new Error('VSTEP Mock B1 not found')
            const start = await this.apiCall('examStart', 'POST', `/exams/${exam.id}/start`)
            const detail = await this.apiCall('examStart', 'GET', `/sessions/${start.session.id}`)
            this.ctx.examSessionId = start.session.id
            this.exam.title = exam.title
            this.exam.sessionId = start.session.id
            this.syncExamSections(detail)
            this.stepStates.examStart.message = exam.title
        },
        async sExamAnswer() {
            const detail = await this.apiCall('examAnswer', 'GET', `/sessions/${this.ctx.examSessionId}`)
            const audioPath = await this.uploadAudio('examAnswer')
            const answers = (detail.questions || []).map(question => ({
                question_id: question.id,
                answer: this.buildExamAnswer(question, audioPath),
            }))
            if (!answers.length) throw new Error('No exam questions found')
            const saved = await this.apiCall('examAnswer', 'PUT', `/sessions/${this.ctx.examSessionId}`, { answers })
            const refreshed = await this.apiCall('examAnswer', 'GET', `/sessions/${this.ctx.examSessionId}`)
            this.syncExamSections(refreshed)
            this.stepStates.examAnswer.message = `Saved ${saved.saved || answers.length} answers`
        },
        async sExamSubmit() {
            const result = await this.apiCall('examSubmit', 'POST', `/sessions/${this.ctx.examSessionId}/submit`)
            const data = result.data || result
            this.exam.status = data.status || 'completed'
            this.exam.overallScore = data.overall_score ?? null
            this.exam.overallBand = data.overall_band ?? null
            this.exam.listeningScore = data.listening_score ?? null
            this.exam.readingScore = data.reading_score ?? null
            this.exam.writingScore = data.writing_score ?? null
            this.exam.speakingScore = data.speaking_score ?? null
            this.stepStates.examSubmit.message = data.status === 'completed'
                ? `Done · Score ${data.overall_score ?? '—'} · Band ${data.overall_band ?? '—'}`
                : `Submitted · Pending AI grading`
        },
    },
}).mount('#app')
</script>
</body>
</html>
