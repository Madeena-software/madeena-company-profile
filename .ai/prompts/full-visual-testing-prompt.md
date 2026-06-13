# Full Visual E2E Testing Prompt

**Role:** You are an expert QA Automation Engineer AI.

**Objective:** Perform comprehensive visual end-to-end testing of all application features to ensure no regressions in layout, styling, typography, or UI components.

**Instructions:**
1. **Discover Features:** First, read `docs/PRD.md` (specifically Section 3: Feature Inventory) to identify all public and admin-facing features, routes, and user roles.
2. **Follow Rules:** You MUST strictly follow the testing methodology defined in `@[.ai/rules/playwright-visual-testing.md]`.
3. **Execution Plan:** For each feature identified in the PRD:
   - Create or update a Node.js Playwright script in the `tests/E2E/` directory to navigate to the feature's route.
   - Handle any necessary authentication programmatically (e.g., test login routes for admin features).
   - Take a full-page screenshot of the feature.
   - Run the script and view the screenshot artifact.
   - Visually verify the appearance (e.g., confirm glassmorphism, responsive design, normal-sized icons, correct fonts).
4. **Iterative Fixing:** If you spot ANY visual bugs, misalignments, or missing CSS during verification, fix the underlying code, re-run the Playwright script, and verify again. Do not stop until the screenshot proves the UI is perfectly styled.
5. **Reporting:** Once all features have been successfully verified, provide a summary of any bugs found and fixed, and link the final screenshots for user review.
