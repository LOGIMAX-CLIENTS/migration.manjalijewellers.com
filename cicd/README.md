# CI/CD Directory

Organized CI/CD files for deployment automation.

## Structure

```
cicd/
├── server/           # Files to copy to client/source servers
│   ├── webhook-handler.php    # Client webhook (routes staging/production)
│   ├── deploy.php             # Source webhook (routes by branch)
│   ├── unified-deploy.sh      # Symlink deployment script
│   ├── rollback.sh            # Rollback to previous release
│   ├── setup-client-server.sh # Setup script for new clients
│   └── setup-source-environments.sh  # Setup all source envs
│
├── docs/             # Visual documentation
│   ├── cicd-dashboard.html     # Management dashboard
│   ├── cicd-1-flow.html        # CI/CD flow overview
│   ├── cicd-2-existing-client.html
│   ├── cicd-3-new-client.html
│   └── cicd-visual-guide.html
│
└── templates/        # Templates for client repos
    └── deploy.yml              # Client workflow template

.github/workflows/    # GitHub Actions (stay here)
├── deploy-source.yml      # Source repo multi-env workflow
├── deploy-reusable.yml    # Reusable deployment workflow
├── deploy-report.yml      # Email notifications
└── deploy-notifications.yml
```

## Quick Links

- **Dashboard**: [cicd/docs/cicd-dashboard.html](docs/cicd-dashboard.html)
- **Flow Diagram**: [cicd/docs/cicd-1-flow.html](docs/cicd-1-flow.html)

## Deployment

### Source Version (retail_v5)
- Push to `develop` → test_etail_v3
- Push to `qa` → QA
- Push to `support` → Support
- Push to `main` → etail (production)

### Client Repos
- Push to `staging` → Staging server
- Push to `production` → Production server
