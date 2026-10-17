---
name: frontend
description: Build and improve accessible, responsive frontend interfaces, client-side state, forms, API integrations, and browser behavior.
---

# Frontend

Use this skill for UI, HTML, CSS, JavaScript/TypeScript, browser interactions, and frontend architecture.

## Workflow

- Inspect the existing pages, component patterns, styles, assets, build tooling, and supported browsers before changing the interface.
- Preserve the visual language and responsive behavior already present unless a redesign is requested.
- Build semantic, keyboard-accessible interfaces with visible focus states, labels, sensible headings, and usable error messages.
- Treat loading, empty, success, validation-error, network-error, and permission-denied states as part of the feature.
- Keep API calls, state transitions, and rendering responsibilities clear. Do not put secrets in browser code.
- Prefer progressive enhancement and lightweight solutions for this project unless its existing stack clearly calls for a framework.

## Verification

- Check the changed flow at narrow and wide viewport sizes.
- Test keyboard navigation, form validation, loading/error states, and basic screen-reader semantics.
- Run the project’s available build, lint, type-check, and test commands.
- Inspect the browser console and network requests for errors, failed assets, and accidental sensitive data.
