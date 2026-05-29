"""
Git Service - Local commit, push, and PR creation
Handles all Git operations for the pipeline workflow
"""
import asyncio
import subprocess
from pathlib import Path
from typing import Any


class GitService:
    """Service for Git operations in the pipeline workflow"""
    
    def __init__(self, repo_path: str = None):
        # Default to etail_v3 admin directory
        self.repo_path = repo_path or "C:/xampp/htdocs/etail_v3/admin"
        self.github_owner = "Logimax-Technologies"
        self.github_repo = "etail_development_src"
    
    async def _run_git(self, *args: str, cwd: str = None) -> tuple[int, str, str]:
        """Run a git command asynchronously"""
        cmd = ["git"] + list(args)
        process = await asyncio.create_subprocess_exec(
            *cmd,
            cwd=cwd or self.repo_path,
            stdout=subprocess.PIPE,
            stderr=subprocess.PIPE
        )
        stdout, stderr = await process.communicate()
        return process.returncode, stdout.decode(), stderr.decode()
    
    async def get_current_branch(self) -> str:
        """Get the current branch name"""
        code, stdout, _ = await self._run_git("branch", "--show-current")
        if code == 0:
            return stdout.strip()
        return "main"
    
    async def create_branch(self, branch_name: str, base: str = "main") -> bool:
        """Create a new branch from base"""
        # Fetch latest
        await self._run_git("fetch", "origin")
        
        # Checkout base and pull
        code, _, _ = await self._run_git("checkout", base)
        if code != 0:
            return False
        
        await self._run_git("pull", "origin", base)
        
        # Create new branch
        code, _, stderr = await self._run_git("checkout", "-b", branch_name)
        return code == 0
    
    async def stage_files(self, files: list[str]) -> bool:
        """Stage files for commit"""
        if not files:
            # Stage all changes
            code, _, _ = await self._run_git("add", "-A")
        else:
            for file in files:
                code, _, _ = await self._run_git("add", file)
                if code != 0:
                    return False
        return True
    
    async def create_local_commit(
        self,
        branch_name: str,
        files: list[str],
        message: str
    ) -> str:
        """Create a local commit (not pushed)"""
        # Create branch if doesn't exist
        current = await self.get_current_branch()
        if current != branch_name:
            await self.create_branch(branch_name)
        
        # Stage files
        await self.stage_files(files)
        
        # Create commit
        code, stdout, stderr = await self._run_git("commit", "-m", message)
        
        if code != 0:
            # Check if nothing to commit
            if "nothing to commit" in stderr or "nothing to commit" in stdout:
                # Get last commit SHA
                code, sha, _ = await self._run_git("rev-parse", "HEAD")
                return sha.strip()
            raise Exception(f"Commit failed: {stderr}")
        
        # Get commit SHA
        code, sha, _ = await self._run_git("rev-parse", "HEAD")
        return sha.strip()
    
    async def push_branch(self, branch_name: str) -> bool:
        """Push branch to remote"""
        code, _, stderr = await self._run_git(
            "push", "-u", "origin", branch_name
        )
        if code != 0:
            raise Exception(f"Push failed: {stderr}")
        return True
    
    async def create_pull_request(
        self,
        title: str,
        body: str,
        head: str,
        base: str = "main"
    ) -> dict[str, Any]:
        """Create a pull request using GitHub MCP or API"""
        # Try to use GitHub MCP if available
        try:
            # This will be called via MCP in the router
            return {
                "url": f"https://github.com/{self.github_owner}/{self.github_repo}/pull/new/{head}",
                "number": None,  # Will be set after actual PR creation
                "head": head,
                "base": base,
                "title": title,
                "status": "pending_creation"
            }
        except Exception as e:
            raise Exception(f"Failed to create PR: {e}")
    
    async def get_diff(self, base: str = "main") -> str:
        """Get diff between current branch and base"""
        current = await self.get_current_branch()
        code, stdout, _ = await self._run_git("diff", f"{base}...{current}")
        return stdout
    
    async def get_changed_files(self, base: str = "main") -> list[str]:
        """Get list of files changed compared to base"""
        current = await self.get_current_branch()
        code, stdout, _ = await self._run_git(
            "diff", "--name-only", f"{base}...{current}"
        )
        if code == 0:
            return [f.strip() for f in stdout.strip().split("\n") if f.strip()]
        return []
    
    async def abort_changes(self) -> bool:
        """Abort all uncommitted changes"""
        await self._run_git("checkout", ".")
        await self._run_git("clean", "-fd")
        return True
    
    async def checkout_main(self) -> bool:
        """Checkout main branch"""
        code, _, _ = await self._run_git("checkout", "main")
        return code == 0
