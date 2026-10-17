# GYRS Portal Minimal UI/UX Design System

This document is mandatory for every Laravel, Supabase, frontend, and content change. It defines one calm, modern, accessible visual language for the public job portal, candidate workspace, and admin workspace.

## 1. Design intent

GYRS should feel trustworthy, quiet, civic, and easy to use. The interface should reduce friction for a first-time job seeker while giving administrators dense, organized information without visual noise.

The visual direction is **awesome minimal**:

- Light surfaces, generous spacing, clear hierarchy.
- Neutral text led by `#747574` instead of loud blue or black.
- Thin borders and soft shadows instead of heavy cards and gradients.
- Outline-first actions instead of filled buttons everywhere.
- One strong action per section; secondary actions stay visually quiet.
- Real content, useful empty states, and clear feedback in every flow.

Do not introduce a new visual style for an individual page. New screens must use the tokens and components below.

## 2. Core design principles

1. **Clarity before decoration** — every section needs a clear purpose and heading.
2. **Trust through transparency** — show source, dates, status, next steps, and official links.
3. **Progressive disclosure** — show the most useful summary first; keep long details expandable or structured.
4. **Accessible by default** — keyboard support, visible focus, labels, readable contrast, and meaningful errors are required.
5. **Calm density** — admin screens can be information-rich, but use spacing, grouping, and table hierarchy instead of tiny text.
6. **Consistent actions** — the same action must look and behave the same across public, candidate, and admin views.

## 3. Color system

Use CSS custom properties or the equivalent Laravel design-token layer. Do not hard-code arbitrary colors in templates.

```css
:root {
  --color-text: #747574;
  --color-heading: #4f504f;
  --color-muted: #969896;
  --color-subtle: #f1f3f1;
  --color-page: #f7f8f7;
  --color-surface: #ffffff;
  --color-surface-raised: #fbfcfb;
  --color-border: #dfe2df;
  --color-border-strong: #bfc4bf;
  --color-accent: #747574;
  --color-accent-hover: #5f605f;
  --color-focus: #8d9a91;
  --color-success: #547a61;
  --color-success-soft: #edf4ef;
  --color-warning: #9a7840;
  --color-warning-soft: #faf5e9;
  --color-danger: #9a5d5d;
  --color-danger-soft: #faeeee;
  --color-info: #647c86;
  --color-info-soft: #eef4f5;
  --shadow-sm: 0 1px 3px rgb(61 65 61 / 6%);
  --shadow-md: 0 10px 28px rgb(61 65 61 / 8%);
  --radius-sm: 8px;
  --radius-md: 12px;
  --radius-lg: 18px;
}
```

### Color rules

- Body text uses `--color-text`; headings use `--color-heading`.
- Use `--color-accent` for links, selected navigation, focus-adjacent emphasis, and neutral outline actions.
- Use semantic colors only for status meaning: success, warning, danger, and info.
- Never communicate status by color alone; include text, an icon with accessible label, or both.
- Keep the page background slightly tinted and content surfaces white so sections remain distinct without heavy shadows.
- Avoid the old blue-first, orange-first, dark login, and gradient-heavy treatments.

## 4. Typography

- Preferred font: `Inter`, with `system-ui`, `-apple-system`, and `Segoe UI` fallbacks.
- Use one font family across the product unless a deliberate government-brand decision is approved.
- Body size: `1rem`, line-height `1.55`.
- Small metadata: minimum `0.8125rem`, line-height `1.4`.
- Page title: clamp between `2rem` and `3.25rem` depending on viewport.
- Section heading: `1.25rem` to `1.75rem`.
- Use weight 400 for body, 500 for labels, 600 for actions, and 700 for important headings.
- Avoid all-caps except for short metadata labels; use letter spacing sparingly.

## 5. Layout and spacing

- Use a centered container with a maximum width around `1180px` to `1240px`.
- Desktop page gutters: `32px`; tablet: `24px`; mobile: `16px`.
- Use a 4px spacing base: `4, 8, 12, 16, 24, 32, 48, 64`.
- Standard section padding: `64px 0` desktop, `40px 0` mobile.
- Use CSS grid/flex for layout; do not position content with absolute coordinates.
- Keep the primary content column readable; long job descriptions should not span the full viewport.
- On mobile, sidebars become filter drawers or stacked sections, tables become cards or horizontally scrollable with a clear label.

## 6. Shared components

### Header and navigation

- Public header: government context, GYRS identity, primary links, candidate sign-in, and a compact mobile menu.
- Candidate header: page identity, candidate name/status, dashboard navigation, logout.
- Admin header: admin identity, role/status, dashboard navigation, logout; do not mix public promotional content into the admin shell.
- Navigation uses plain text plus restrained icons. Active state is an accent underline or subtle neutral surface, not a filled pill by default.

### Buttons

Default button style is outline:

```css
.btn-outline {
  background: transparent;
  color: var(--color-accent);
  border: 1px solid var(--color-border-strong);
  border-radius: var(--radius-sm);
  min-height: 40px;
  padding: 0 16px;
  font-weight: 600;
  transition: background-color .18s ease, border-color .18s ease, color .18s ease;
}

.btn-outline:hover,
.btn-outline:focus-visible {
  background: var(--color-subtle);
  border-color: var(--color-accent);
  color: var(--color-accent-hover);
}
```

- Primary action: outline accent button.
- Secondary action: lighter border and neutral text.
- Destructive action: danger outline; require confirmation for deletes/archive.
- Success action: success outline; use filled success only for a confirmed status or completion state.
- Do not use more than one visually dominant button in a card or section.
- Buttons must have clear disabled, loading, hover, focus, and touch states.
- Link-like actions should be real links; state-changing actions should be buttons.

### Cards and panels

- White surface, 1px border, `--radius-md`, and `--shadow-sm`.
- Use a raised shadow only for a selected, hovered, or primary interactive panel.
- Card header: title, concise metadata, optional action.
- Card body: one purpose; do not combine unrelated datasets.
- Card footer: status or next action separated by a top border.

### Forms

- Every input needs a visible label; placeholders are examples, not labels.
- Use consistent height, border, radius, helper text, error text, and focus treatment.
- Group related fields into clear sections; place required fields before optional fields.
- Show inline validation near the field and a summary at the top for long forms.
- Preserve entered values after validation errors.
- File upload must show accepted type, size, current file, upload progress, and failure recovery.

### Status badges and progress

- Use concise text such as `Published`, `Under review`, `Shortlisted`, `Closed`.
- Badges are compact supporting metadata, not the main action.
- Application tracker uses four explicit stages: Applied, Under Review, Shortlisted, Interview Call.
- A future status must remain understandable without the visual stepper; include text and timestamps.

### Tables

- Use tables for admin comparison and audit work, not public browsing.
- Keep headers visible, align numeric values, and use row actions grouped consistently.
- On mobile, prioritize key columns and provide a details view instead of shrinking text below readable size.
- Confirm destructive actions and make export/filter controls easy to find.

## 7. Page-by-page UX

### Public home and job explorer

1. Government context and GYRS identity.
2. Short value proposition with one search task.
3. Search bar with keyword, employment type, and district.
4. Job count, filter controls, and result list.
5. Job cards with title, organization, location, type, salary, closing date, skills, and `View details` outline action.
6. Trust/compliance content, how-it-works steps, departments, FAQs, and contact footer.

Avoid overwhelming the hero with statistics, multiple competing CTAs, or decorative gradients. The first screen should help someone search for work immediately.

### Job details

- Start with title, organization, location, type, salary, closing date, and verification/source.
- Keep the main `Apply now` action visible but outline-based and clearly labeled as an external redirect when applicable.
- Structure long content with sections: overview, eligibility, vacancy, responsibilities, selection, how to apply, official notification.
- Keep related jobs and sharing actions secondary.
- Show an explicit expired/closed state and prevent applications when the deadline has passed.

### Candidate login and dashboard

- Login should explain the next step and distinguish OTP from password access without fake credentials in production.
- Dashboard opening area: candidate identity, verification state, profile completeness, and one useful next action.
- Applications should be the primary tab; recommendation, dossier, events, and skill improvement are secondary.
- Show empty states that teach the candidate what to do next.
- Never expose private candidate data in URLs or page source beyond what the authenticated user is allowed to see.

### Admin dashboard

- Start with a compact KPI row and operational alerts, not marketing content.
- Use tabs or navigation for jobs, candidates, applications, and audit log.
- Put filters and search above the data set; preserve filters during pagination.
- Make create/edit forms structured and scannable with save/cancel and unsaved-change handling.
- Make audit history immutable in presentation and clearly identify actor, action, time, and affected record.

## 8. Required interface states

Every server-backed screen must design and implement:

- Loading/skeleton state.
- First-use empty state with a next step.
- Successful content state.
- Validation error state.
- Network/server failure state with retry.
- Unauthorized/forbidden state.
- Expired/closed data state where relevant.
- Destructive confirmation state.
- Success feedback after mutations through inline feedback or a toast that is also announced accessibly.

## 9. Accessibility and quality bar

- Meet WCAG 2.2 AA intent for text, controls, focus, and contrast.
- Use semantic landmarks, one clear page heading, correctly associated labels, and logical heading order.
- All interactive controls must be keyboard reachable and have a visible `:focus-visible` style.
- Modals must trap focus, close predictably, restore focus, and expose a useful accessible name.
- Respect `prefers-reduced-motion`; do not depend on animation for meaning.
- Test at 320px, 768px, 1024px, and wide desktop widths.
- Test keyboard-only navigation, zoom to 200%, screen-reader names, contrast, and error recovery.
- Check console errors, broken links, failed network calls, and accidental secret exposure before release.

## 10. Implementation order

1. Add tokens and base typography.
2. Rebuild layout shells and navigation.
3. Rebuild buttons, links, forms, cards, badges, tables, alerts, and modal behavior.
4. Migrate public job search and job details.
5. Migrate candidate login/dashboard.
6. Migrate admin dashboard and data workflows.
7. Run responsive and accessibility verification on every page.

## 11. Design review checklist

- Does the page use the shared tokens rather than arbitrary colors?
- Is `#747574` used as the primary body text direction?
- Are actions outline-first with only one dominant action per section?
- Can a new user understand what to do next without training?
- Are loading, empty, error, forbidden, and success states included?
- Does the layout remain usable at mobile width and 200% zoom?
- Are labels, focus, contrast, and modal behavior accessible?
- Does the page feel like GYRS rather than a generic Bootstrap screen?
