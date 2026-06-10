# `.ai/` — AI Agent Control Center

> **Single source of truth** for all AI-assisted development on this repository.

## Purpose

This directory provides persistent context, rules, and session state so that any AI coding agent can load the project's architecture, constraints, and history in a single pass — eliminating redundant scanning and ensuring consistency across sessions.

## Directory Structure

```
.ai/
├── README.md                          # ← You are here
├── history.md                         # Execution log (append-only)
├── memory.json                        # Machine-readable project metadata
│
├── memory/
│   └── state.md                       # Session state: stack, goals, milestones, health
│
├── rules/
│   ├── project-context.md             # Project overview, setup, conventions
│   ├── laravel-filament.md            # Stack-specific best practices & constraints
│   ├── server-access-constraints.md   # Deployment & server access rules
│   └── testing-pyramid.md             # Testing strategy (Unit > Feature > E2E)
│
└── prompt/
    ├── prompts.md                     # CORE framework & 4-phase session loop
    ├── prd-generator.md               # PRD generation prompt template
    └── bootstrap-new-repo.md          # New repo bootstrap prompt
```

## How to Use

### For AI Agents
1. **Start every session** by reading `memory/state.md` to load context.
2. **Consult `rules/`** before writing code — these files define hard constraints.
3. **Consult `docs/PRD.md`** for the full product requirements and data model.
4. **Append to `history.md`** at the end of every session with a summary of work done.
5. **Update `memory/state.md`** with new milestones, goals, and known issues.

### For Humans
- Edit `rules/` files to change coding conventions or deployment constraints.
- Review `history.md` to see a timeline of AI-assisted changes.
- Update the `Active Goal` in `memory/state.md` to steer the next AI session.
- Read `docs/PRD.md` for the comprehensive product documentation.

## Important Notes

- **Never delete `history.md`** — it is an append-only execution log.
- **`memory.json`** is machine-readable; prefer `memory/state.md` for human review.
- **All deployment must go through CI/CD** — see `rules/server-access-constraints.md`.
