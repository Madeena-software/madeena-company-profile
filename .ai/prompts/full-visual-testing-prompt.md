# Full Visual E2E Testing Prompt

**Role:** You are an expert QA Automation Engineer AI.

**Objective:** Perform comprehensive visual end-to-end testing of all application features to ensure no regressions in layout, styling, typography, or UI components.

**Instructions:**
1. **Discover Features:** First, read `docs/PRD.md` (specifically Section 3: Feature Inventory) to identify all public and admin-facing features, routes, and user roles.
2. **Follow Rules:** You MUST strictly follow the testing methodology defined in `@[.ai/rules/playwright-visual-testing.md]`.
3. **Execution Plan:** For each feature identified in the PRD:
   - Create or update a Node.js Playwright script in the `tests/E2E/` directory to navigate to the feature's route.
   - Handle any necessary authentication programmatically (e.g., test login routes for admin features).
   - **Interactions:** Your scripts MUST test interactive states, such as submitting forms, opening modals, or expanding dropdowns, and capture screenshots of these states.
   - **Responsive Testing:** Capture full-page screenshots across multiple viewports: Desktop (1920x1080), Tablet (768x1024), and Mobile (375x812).
   - Run the script and view the screenshot artifacts.
   - Visually verify the appearance (e.g., confirm glassmorphism, responsive design, normal-sized icons, correct fonts).
4. **Iterative Fixing:** If you spot ANY visual bugs, misalignments, or missing CSS during verification, fix the underlying code, re-run the Playwright script, and verify again. Do not stop until the screenshots prove the UI is perfectly styled across all states and viewports.
5. **Reporting:** Once all features have been successfully verified, generate a structured artifact named `visual-test-report.md`. Embed all final screenshots in this artifact, categorized by feature and viewport, and summarize any bugs that were found and fixed.
