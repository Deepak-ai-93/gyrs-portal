<div class="auth-visual-inner">
    <div class="auth-visual-art">
        <svg viewBox="0 0 560 560" role="img" aria-hidden="true" focusable="false">
            <defs>
                <radialGradient id="auth-halo" cx="50%" cy="50%" r="50%">
                    <stop offset="0%" stop-color="var(--color-focus)" stop-opacity="0.2" />
                    <stop offset="55%" stop-color="var(--color-focus)" stop-opacity="0.06" />
                    <stop offset="100%" stop-color="var(--color-focus)" stop-opacity="0" />
                </radialGradient>
                <radialGradient id="auth-halo-info" cx="50%" cy="50%" r="50%">
                    <stop offset="0%" stop-color="var(--color-info)" stop-opacity="0.14" />
                    <stop offset="100%" stop-color="var(--color-info)" stop-opacity="0" />
                </radialGradient>
                <linearGradient id="auth-scan-grad" x1="0%" y1="0%" x2="100%" y2="0%">
                    <stop offset="0%" stop-color="var(--color-focus)" stop-opacity="0" />
                    <stop offset="50%" stop-color="var(--color-focus)" stop-opacity="0.9" />
                    <stop offset="100%" stop-color="var(--color-focus)" stop-opacity="0" />
                </linearGradient>
                <filter id="auth-card-shadow" x="-20%" y="-15%" width="140%" height="135%">
                    <feDropShadow dx="0" dy="10" stdDeviation="14" flood-color="#4f504f" flood-opacity="0.1" />
                </filter>
                <filter id="auth-soft-shadow" x="-25%" y="-25%" width="150%" height="155%">
                    <feDropShadow dx="0" dy="6" stdDeviation="9" flood-color="#4f504f" flood-opacity="0.08" />
                </filter>
                <pattern id="auth-dots" width="26" height="26" patternUnits="userSpaceOnUse">
                    <circle cx="2" cy="2" r="1.5" fill="var(--color-border-strong)" opacity="0.45" />
                </pattern>
                <radialGradient id="auth-dots-fade" cx="50%" cy="50%" r="50%">
                    <stop offset="0%" stop-color="#ffffff" stop-opacity="0.85" />
                    <stop offset="62%" stop-color="#ffffff" stop-opacity="0.25" />
                    <stop offset="100%" stop-color="#ffffff" stop-opacity="0" />
                </radialGradient>
                <mask id="auth-dots-mask">
                    <rect width="560" height="560" fill="url(#auth-dots-fade)" />
                </mask>
                <clipPath id="auth-card-clip">
                    <rect x="166" y="150" width="228" height="290" rx="22" />
                </clipPath>
            </defs>

            <!-- Ambient depth -->
            <circle cx="280" cy="286" r="238" fill="url(#auth-halo)" />
            <circle cx="424" cy="154" r="128" fill="url(#auth-halo-info)" />
            <rect width="560" height="560" fill="url(#auth-dots)" mask="url(#auth-dots-mask)" />

            <!-- Slowly rotating orbits -->
            <g class="auth-orbit">
                <circle cx="280" cy="280" r="240" fill="none" stroke="var(--color-border)" stroke-width="1.4" stroke-dasharray="2 11" stroke-linecap="round" />
                <circle cx="280" cy="40" r="4" fill="var(--color-focus)" />
            </g>
            <g class="auth-orbit auth-orbit-rev">
                <circle cx="280" cy="280" r="198" fill="none" stroke="var(--color-border-strong)" stroke-width="1" stroke-dasharray="7 15" opacity="0.65" />
                <circle cx="82" cy="280" r="3" fill="var(--color-border-strong)" />
            </g>

            <!-- Connectors -->
            <path class="auth-flow" d="M118 186 C140 202 148 212 166 226" fill="none" stroke="var(--color-border-strong)" stroke-width="1.6" stroke-linecap="round" stroke-dasharray="4 7" />
            <path class="auth-flow auth-flow-slow" d="M394 292 C434 290 448 250 452 154" fill="none" stroke="var(--color-border-strong)" stroke-width="1.6" stroke-linecap="round" stroke-dasharray="4 7" />

            <!-- Floating badge: free registration -->
            <g transform="translate(86, 168)" filter="url(#auth-soft-shadow)">
                <g class="auth-float auth-float-b">
                    <circle r="30" fill="var(--color-surface)" stroke="var(--color-border)" />
                    <circle r="21" fill="none" stroke="var(--color-border-strong)" stroke-width="1" stroke-dasharray="3 4" opacity="0.8" />
                    <text y="-1" text-anchor="middle" font-size="13" font-weight="800" fill="var(--color-heading)" font-family="Inter, system-ui, sans-serif">100%</text>
                    <text y="12" text-anchor="middle" font-size="7.5" font-weight="700" letter-spacing="0.14em" fill="var(--color-muted)" font-family="Inter, system-ui, sans-serif">FREE</text>
                </g>
            </g>

            <!-- Candidate profile card -->
            <g filter="url(#auth-card-shadow)">
                <rect x="166" y="150" width="228" height="290" rx="22" fill="var(--color-surface)" stroke="var(--color-border)" stroke-width="1.5" />
            </g>

            <g clip-path="url(#auth-card-clip)">
                <rect class="auth-scan" x="166" y="150" width="228" height="3" fill="url(#auth-scan-grad)" />
            </g>

            <!-- Avatar + verification -->
            <circle cx="212" cy="200" r="26" fill="var(--color-subtle)" stroke="var(--color-border)" />
            <circle cx="212" cy="194" r="8" fill="none" stroke="var(--color-heading)" stroke-width="2.4" />
            <path d="M201 213c1.6-6 5.9-9.5 11-9.5s9.4 3.5 11 9.5" fill="none" stroke="var(--color-heading)" stroke-width="2.4" stroke-linecap="round" />
            <g class="auth-badge-pop">
                <circle cx="232" cy="217" r="8" fill="var(--color-success-soft)" stroke="var(--color-success)" stroke-width="1.2" />
                <path d="m228.5 217 2.4 2.4 4.2-4.8" fill="none" stroke="var(--color-success)" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
            </g>

            <!-- Identity lines -->
            <rect x="250" y="184" width="104" height="9" rx="4.5" fill="var(--color-heading)" opacity="0.9" />
            <rect x="250" y="200" width="74" height="7" rx="3.5" fill="var(--color-border-strong)" />
            <rect x="250" y="213" width="48" height="7" rx="3.5" fill="var(--color-border)" />

            <line x1="190" y1="244" x2="370" y2="244" stroke="var(--color-border)" />

            <!-- Detail rows -->
            <circle cx="199" cy="264" r="10" fill="var(--color-subtle)" />
            <path d="M199 259.5c2.3 0 4.2 1.9 4.2 4.2 0 3.2-4.2 6.3-4.2 6.3s-4.2-3.1-4.2-6.3c0-2.3 1.9-4.2 4.2-4.2Z" fill="none" stroke="var(--color-muted)" stroke-width="1.2" />
            <rect x="218" y="258" width="92" height="8" rx="4" fill="var(--color-border-strong)" />
            <rect x="322" y="258" width="48" height="8" rx="4" fill="var(--color-border)" />

            <circle cx="199" cy="286" r="10" fill="var(--color-subtle)" />
            <rect x="194.5" y="282.5" width="9" height="7" rx="1.5" fill="none" stroke="var(--color-muted)" stroke-width="1.2" />
            <path d="m195.5 283.5 3.5 3 3.5-3" fill="none" stroke="var(--color-muted)" stroke-width="1.2" />
            <rect x="218" y="280" width="76" height="8" rx="4" fill="var(--color-border-strong)" />
            <rect x="322" y="280" width="64" height="8" rx="4" fill="var(--color-border)" />

            <circle cx="199" cy="308" r="10" fill="var(--color-subtle)" />
            <path d="M195 303.5h2.2l1.1 2.9-1.3 1a6.4 6.4 0 0 0 3 3l1-1.3 2.9 1.1v2.2a1.2 1.2 0 0 1-1.3 1.2 9.6 9.6 0 0 1-8.8-8.8 1.2 1.2 0 0 1 1.2-1.3Z" fill="none" stroke="var(--color-muted)" stroke-width="1.1" stroke-linejoin="round" />
            <rect x="218" y="302" width="104" height="8" rx="4" fill="var(--color-border-strong)" />
            <rect x="322" y="302" width="36" height="8" rx="4" fill="var(--color-border)" />

            <!-- Skills -->
            <text x="190" y="340" font-size="8.5" font-weight="700" letter-spacing="0.09em" fill="var(--color-muted)" font-family="Inter, system-ui, sans-serif">KEY SKILLS</text>
            <rect x="190" y="350" width="56" height="20" rx="10" fill="var(--color-subtle)" stroke="var(--color-border)" />
            <text x="218" y="363.5" text-anchor="middle" font-size="8" font-weight="600" fill="var(--color-heading)" font-family="Inter, system-ui, sans-serif">Excel</text>
            <rect x="252" y="350" width="68" height="20" rx="10" fill="var(--color-subtle)" stroke="var(--color-border)" />
            <text x="286" y="363.5" text-anchor="middle" font-size="8" font-weight="600" fill="var(--color-heading)" font-family="Inter, system-ui, sans-serif">AutoCAD</text>
            <rect x="326" y="350" width="50" height="20" rx="10" fill="var(--color-subtle)" stroke="var(--color-border)" />
            <text x="351" y="363.5" text-anchor="middle" font-size="8" font-weight="600" fill="var(--color-heading)" font-family="Inter, system-ui, sans-serif">Python</text>

            <!-- Profile strength -->
            <text x="190" y="398" font-size="8.5" font-weight="700" letter-spacing="0.09em" fill="var(--color-muted)" font-family="Inter, system-ui, sans-serif">PROFILE STRENGTH</text>
            <text x="374" y="398" text-anchor="end" font-size="9.5" font-weight="800" fill="var(--color-heading)" font-family="Inter, system-ui, sans-serif">86%</text>
            <rect x="190" y="406" width="184" height="6" rx="3" fill="var(--color-border)" />
            <rect class="auth-progress" x="190" y="406" width="158" height="6" rx="3" fill="var(--color-heading)" />

            <!-- Floating match card -->
            <g transform="translate(352, 74)" filter="url(#auth-soft-shadow)">
                <g class="auth-float auth-float-a">
                    <rect width="170" height="76" rx="14" fill="var(--color-surface)" stroke="var(--color-border)" />
                    <rect x="14" y="14" width="28" height="28" rx="9" fill="var(--color-subtle)" />
                    <rect x="21" y="24.5" width="14" height="9" rx="1.6" fill="none" stroke="var(--color-heading)" stroke-width="1.3" />
                    <path d="M25 24.5v-1.4a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v1.4" fill="none" stroke="var(--color-heading)" stroke-width="1.3" />
                    <rect x="52" y="19" width="70" height="8" rx="4" fill="var(--color-heading)" opacity="0.85" />
                    <rect x="52" y="33" width="94" height="6" rx="3" fill="var(--color-border-strong)" />
                    <rect x="52" y="44" width="58" height="6" rx="3" fill="var(--color-border)" />
                    <rect x="14" y="52" width="66" height="14" rx="7" fill="var(--color-success-soft)" />
                    <text x="47" y="61.8" text-anchor="middle" font-size="7.5" font-weight="700" letter-spacing="0.04em" fill="var(--color-success)" font-family="Inter, system-ui, sans-serif">98% MATCH</text>
                </g>
            </g>

            <!-- Floating application card -->
            <g transform="translate(34, 452)" filter="url(#auth-soft-shadow)">
                <g class="auth-float auth-float-c">
                    <rect width="178" height="80" rx="14" fill="var(--color-surface)" stroke="var(--color-border)" />
                    <circle cx="32" cy="34" r="15" fill="var(--color-success-soft)" />
                    <path d="m25 34 4.6 4.6 9.4-10" fill="none" stroke="var(--color-success)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    <rect x="56" y="22" width="88" height="8" rx="4" fill="var(--color-heading)" opacity="0.85" />
                    <rect x="56" y="37" width="62" height="6" rx="3" fill="var(--color-border-strong)" />
                    <rect x="56" y="51" width="56" height="15" rx="7.5" fill="var(--color-subtle)" stroke="var(--color-border)" />
                    <text x="84" y="61.5" text-anchor="middle" font-size="7.5" font-weight="700" letter-spacing="0.06em" fill="var(--color-heading)" font-family="Inter, system-ui, sans-serif">APPLIED</text>
                </g>
            </g>

            <!-- Sparkles -->
            <g transform="translate(464, 330) scale(1.15)">
                <path class="auth-twinkle" d="M0-8C1.1-2.6 2.6-1.1 8 0 2.6 1.1 1.1 2.6 0 8-1.1 2.6-2.6 1.1-8 0-2.6-1.1-1.1-2.6 0-8Z" fill="var(--color-focus)" opacity="0.55" />
            </g>
            <g transform="translate(118, 352) scale(0.8)">
                <path class="auth-twinkle auth-twinkle-b" d="M0-8C1.1-2.6 2.6-1.1 8 0 2.6 1.1 1.1 2.6 0 8-1.1 2.6-2.6 1.1-8 0-2.6-1.1-1.1-2.6 0-8Z" fill="var(--color-border-strong)" opacity="0.6" />
            </g>
            <g transform="translate(444, 418) scale(0.7)">
                <path class="auth-twinkle auth-twinkle-c" d="M0-8C1.1-2.6 2.6-1.1 8 0 2.6 1.1 1.1 2.6 0 8-1.1 2.6-2.6 1.1-8 0-2.6-1.1-1.1-2.6 0-8Z" fill="var(--color-focus)" opacity="0.45" />
            </g>
            <g transform="translate(126, 112) scale(0.65)">
                <path class="auth-twinkle auth-twinkle-b" d="M0-8C1.1-2.6 2.6-1.1 8 0 2.6 1.1 1.1 2.6 0 8-1.1 2.6-2.6 1.1-8 0-2.6-1.1-1.1-2.6 0-8Z" fill="var(--color-border-strong)" opacity="0.5" />
            </g>
        </svg>
    </div>

    <div class="auth-visual-copy">
        <p class="auth-eyebrow"><span class="auth-eyebrow-dot"></span> Candidate gateway</p>
        <h2>Your next opportunity starts here.</h2>
        <p>Build one profile, then apply to verified openings across all 33 districts and follow every application in a single place.</p>
        <ul class="auth-points">
            <li><x-icon name="check-circle" :size="17" /> Free registration for every candidate</li>
            <li><x-icon name="shield" :size="17" /> Your details stay private and secure</li>
            <li><x-icon name="sparkles" :size="17" /> Track every application from one dashboard</li>
        </ul>
    </div>
</div>
