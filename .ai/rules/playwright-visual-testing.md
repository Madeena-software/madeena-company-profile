# Playwright Visual Verification Rule

When making any User Interface (UI) changes across the entire project (both the public frontend and the Filament Admin panel), agents MUST verify the changes visually using Playwright.

## Workflow Loop
1. **Write/Update Script**: Create or update a Playwright Node.js script that navigates to the modified page (handling authentication if necessary) and takes a full-page screenshot. 
2. **Permanent Storage**: Save these Playwright scripts permanently in the `tests/E2E/` directory for reuse and future regression testing, rather than writing them to temporary scratchpads.
3. **Execution**: Run the script using Node (e.g., `node tests/E2E/admin_screenshot.cjs`).
4. **Visual Verification**: View the generated screenshot artifact using the `view_file` tool to visually verify that the layout, colors, typography, icons, and styling are correct.
5. **Iterate**: If the screenshot reveals any bugs, misalignments, or broken styling (like massive icons or missing CSS), fix the underlying code, run the script again, and re-verify. **Do not stop this loop until the visual appearance is absolutely correct.**
