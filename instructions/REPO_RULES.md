# MoonLaunch Repo Rules (Non-Negotiable)

- This repository is the source of truth.
- No zip files. All work must be committed with history.
- No code may live in the root directory.

Folder rules:
- /app → Mobile app only
- /admin → Admin dashboard only
- /api → AWS Lambda code only
- /db → Database schema & migrations
- /infra → AWS infrastructure & deployment
- /contracts → Smart contracts

Secrets:
- No secrets committed.
- Use .env.example files only.

Pull Requests:
- Each PR must reference the relevant Figma screen or requirement.
- Screenshots required for UI changes.
