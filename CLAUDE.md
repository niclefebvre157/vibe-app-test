# Claude Code Rules for This Repo

## Non-negotiables
- Start all multi-file tasks in plan mode.
- Never change environment config without explaining risk and asking for confirmation.
- Tests must use a dedicated test database. Never run tests against dev data.

## Architecture
- Laravel app using Breeze auth (unless changed explicitly).
- Use policies or gates for authorization.
- Keep controllers thin. Put business logic in Services or Actions.

## Database
- Use migrations for all schema changes.
- Use model factories for test data.
- Favor explicit relationships and pivot tables.
- Prefer descriptive pivot tables (for example: team_memberships over team_user).
- Store role or status on pivot tables when membership semantics matter.


## Product requirements

### Teams and Membership (MVP foundation)
- A user can create a team.
- The creating user becomes a team member with role = "admin".
- A user can belong to multiple teams.
- A team has many members via a membership pivot table.
- Team membership roles start with: admin, player.

### Access rules
- Only team members can view a team.
- Only admins can manage team settings or future invites.


## Workflow
- One worktree per feature area. Do not mix tasks across worktrees.
- Every PR must include tests for core flows touched.
- If a mistake happens, update CLAUDE.md to prevent recurrence.

## Auth
- This project uses Laravel Breeze with Blade and Tailwind.
- Do not introduce Jetstream, Livewire, or Inertia unless explicitly requested.
- Auth routes and views live under resources/views/auth.

## Scope control
- Build features incrementally.
- Do not preemptively implement future features.
- Respect declared NON-GOALS in plan-mode prompts.
- When in doubt, stop at the MVP boundary and ask.

## Naming conventions
- Routes should be RESTful and predictable.
- Prefer plural resource names (/teams, /games).
- Avoid clever abstractions early. Optimize for clarity.

## Mail (Local Dev)
- Local development uses Mailpit via Docker Desktop.
- Mailpit runs on 127.0.0.1:1025 with UI at http://localhost:8025.
- Do not assume mail infrastructure exists unless Mailpit is running.



