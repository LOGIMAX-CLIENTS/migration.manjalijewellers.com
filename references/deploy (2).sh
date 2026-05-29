#!/bin/bash
# Deployment script - fully configurable via environment variables

# --- CONFIG ---
# Required variables - will fail if not set
: "${REPO_PATH:?REPO_PATH environment variable is required}"
: "${DEPLOY_BRANCH:?DEPLOY_BRANCH environment variable is required}"

# Optional variables with defaults
DEPLOY_LOG="${DEPLOY_LOG:-$REPO_PATH/deploy.log}"
SSH_KEY="${SSH_KEY:-$HOME/.ssh/id_rsa_deploy}"
LOCK_FILE="${LOCK_FILE:-/tmp/deploy_$DEPLOY_BRANCH.lock}"
GIT_REMOTE="${GIT_REMOTE:-origin}"
DEPLOY_SOURCE="${DEPLOY_SOURCE:-Webhook-Auto}"
# ----------------

# Prevent concurrent deployments
if [ -f "$LOCK_FILE" ]; then
    echo "⚠️  Deployment for $DEPLOY_BRANCH already in progress. Exiting." >> "$DEPLOY_LOG"
    exit 1
fi

# Create lock file
touch "$LOCK_FILE"
trap 'rm -f "$LOCK_FILE"' EXIT

cd "$REPO_PATH" || { 
    echo "❌ Failed to enter repo path $REPO_PATH" >> "$DEPLOY_LOG"
    exit 1 
}

# Use specific SSH key for GitHub if provided
if [ -f "$SSH_KEY" ]; then
    export GIT_SSH_COMMAND="ssh -i $SSH_KEY -o IdentitiesOnly=yes -o StrictHostKeyChecking=no"
fi

timestamp=$(date '+%Y-%m-%d %H:%M:%S')
deploy_source="${DEPLOY_SOURCE}"

echo "🔄 Automated Deployment Started at $timestamp" >> "$DEPLOY_LOG"
echo "📋 Branch: $DEPLOY_BRANCH" >> "$DEPLOY_LOG"
echo "🚀 Source: $deploy_source" >> "$DEPLOY_LOG"
echo "📁 Repo Path: $REPO_PATH" >> "$DEPLOY_LOG"

# Get current commit
before_commit=$(git rev-parse --short HEAD)

# Reset and pull updates
echo "🔄 Fetching and applying updates..." >> "$DEPLOY_LOG"
git fetch --all
git reset --hard "$GIT_REMOTE/$DEPLOY_BRANCH"
git clean -fd -q
git pull "$GIT_REMOTE" "$DEPLOY_BRANCH"

# Get new commit info
after_commit=$(git rev-parse --short HEAD)
commit_msg=$(git log -1 --pretty=%B | head -n1)
commit_author=$(git log -1 --pretty=format:'%an')

# Log deployment
log_entry="[$timestamp] - $deploy_source | Branch: $DEPLOY_BRANCH | From: $before_commit → $after_commit | By: $commit_author | Msg: $commit_msg"
echo "$log_entry" >> "$DEPLOY_LOG"



# Clear CI cache if exists
if [ -n "$CLEAR_CACHE" ]; then
find . -name "cache" -type d -exec rm -rf {} + 2>/dev/null || true
    echo "🧹 Cache cleared" >> "$DEPLOY_LOG"
fi

if [ $? -eq 0 ]; then
    echo "✅ Automated deployment completed at $(date)" >> "$DEPLOY_LOG"
    echo "📊 Changes applied: $before_commit → $after_commit" >> "$DEPLOY_LOG"
    STATUS="success"
    MESSAGE="Deployment completed successfully"
else
    echo "❌ Automated deployment failed at $(date)" >> "$DEPLOY_LOG"
    STATUS="failed"
    MESSAGE="Deployment failed - check logs"
fi

# Remove lock file
rm -f "$LOCK_FILE"

# Run post-deploy script if exists
if [ -n "$POST_DEPLOY_SCRIPT" ] && [ -f "$POST_DEPLOY_SCRIPT" ]; then
    echo "🔧 Running post-deploy script: $POST_DEPLOY_SCRIPT" >> "$DEPLOY_LOG"
    bash "$POST_DEPLOY_SCRIPT" >> "$DEPLOY_LOG" 2>&1
fi

exit 0