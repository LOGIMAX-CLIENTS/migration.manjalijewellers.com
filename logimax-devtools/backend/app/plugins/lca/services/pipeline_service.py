"""
Pipeline Service - Development Workflow Orchestration
Manages the 10-stage workflow with persistent state and multi-stage approvals
"""
import json
import uuid
import aiosqlite
from datetime import datetime
from enum import Enum
from typing import Any, Literal
from pathlib import Path


class TaskStatus(str, Enum):
    """Task status through the 10-stage pipeline"""
    # Stage 1
    DRAFT = "draft"
    # Stage 2
    ANALYZING = "analyzing"
    ANALYZED = "analyzed"
    # Stage 3
    PLANNING = "planning"
    PLANNED = "planned"
    # Stage 4
    PENDING_PLAN_APPROVAL = "pending_plan_approval"
    PLAN_APPROVED = "plan_approved"
    PLAN_REJECTED = "plan_rejected"
    # Stage 5
    IN_PROGRESS = "in_progress"
    IMPLEMENTED = "implemented"
    # Stage 6
    TESTING = "testing"
    TESTS_PASSED = "tests_passed"
    TESTS_FAILED = "tests_failed"
    # Stage 7
    COMMITTING = "committing"
    COMMITTED = "committed"
    # Stage 8
    PENDING_COMMIT_APPROVAL = "pending_commit_approval"
    COMMIT_APPROVED = "commit_approved"
    COMMIT_REJECTED = "commit_rejected"
    # Stage 9
    PUSHING = "pushing"
    PR_CREATED = "pr_created"
    # Stage 10
    PENDING_TL_APPROVAL = "pending_tl_approval"
    MERGED = "merged"
    # Error states
    FAILED = "failed"
    CANCELLED = "cancelled"


class PipelineTask:
    """Model representing a pipeline task"""
    def __init__(
        self,
        id: str = None,
        title: str = "",
        type: str = "BUGFIX",
        description: str = "",
        index_name: str = "etail-admin",
        target_files: list[str] = None,
        target_functions: list[str] = None,
        assigned_to: str = None,
        priority: str = "MEDIUM",
        status: TaskStatus = TaskStatus.DRAFT,
        current_stage: int = 1,
        # Stage results
        impact_report: dict = None,
        ai_plan: str = None,
        plan_approved_by: str = None,
        plan_approved_at: datetime = None,
        plan_comments: str = None,
        changed_files: list[str] = None,
        test_results: dict = None,
        commit_sha: str = None,
        commit_message: str = None,
        commit_approved_by: str = None,
        commit_approved_at: datetime = None,
        branch_name: str = None,
        pr_url: str = None,
        pr_number: int = None,
        merged_at: datetime = None,
        merged_by: str = None,
        # Timestamps
        created_at: datetime = None,
        updated_at: datetime = None,
        error_message: str = None,
    ):
        self.id = id or str(uuid.uuid4())
        self.title = title
        self.type = type
        self.description = description
        self.index_name = index_name
        self.target_files = target_files or []
        self.target_functions = target_functions or []
        self.assigned_to = assigned_to
        self.priority = priority
        self.status = status
        self.current_stage = current_stage
        # Stage results
        self.impact_report = impact_report
        self.ai_plan = ai_plan
        self.plan_approved_by = plan_approved_by
        self.plan_approved_at = plan_approved_at
        self.plan_comments = plan_comments
        self.changed_files = changed_files or []
        self.test_results = test_results
        self.commit_sha = commit_sha
        self.commit_message = commit_message
        self.commit_approved_by = commit_approved_by
        self.commit_approved_at = commit_approved_at
        self.branch_name = branch_name
        self.pr_url = pr_url
        self.pr_number = pr_number
        self.merged_at = merged_at
        self.merged_by = merged_by
        # Timestamps
        self.created_at = created_at or datetime.now()
        self.updated_at = updated_at or datetime.now()
        self.error_message = error_message
    
    def to_dict(self) -> dict:
        """Convert to dictionary for API response"""
        return {
            "id": self.id,
            "title": self.title,
            "type": self.type,
            "description": self.description,
            "index_name": self.index_name,
            "target_files": self.target_files,
            "target_functions": self.target_functions,
            "assigned_to": self.assigned_to,
            "priority": self.priority,
            "status": self.status.value if isinstance(self.status, TaskStatus) else self.status,
            "current_stage": self.current_stage,
            "impact_report": self.impact_report,
            "ai_plan": self.ai_plan,
            "plan_approved_by": self.plan_approved_by,
            "plan_approved_at": self.plan_approved_at.isoformat() if self.plan_approved_at else None,
            "plan_comments": self.plan_comments,
            "changed_files": self.changed_files,
            "test_results": self.test_results,
            "commit_sha": self.commit_sha,
            "commit_message": self.commit_message,
            "commit_approved_by": self.commit_approved_by,
            "commit_approved_at": self.commit_approved_at.isoformat() if self.commit_approved_at else None,
            "branch_name": self.branch_name,
            "pr_url": self.pr_url,
            "pr_number": self.pr_number,
            "merged_at": self.merged_at.isoformat() if self.merged_at else None,
            "merged_by": self.merged_by,
            "created_at": self.created_at.isoformat() if self.created_at else None,
            "updated_at": self.updated_at.isoformat() if self.updated_at else None,
            "error_message": self.error_message,
            # Computed fields
            "next_action": self._get_next_action(),
            "can_proceed": self._can_proceed(),
        }
    
    def _get_next_action(self) -> str:
        """Get the next action based on current status"""
        actions = {
            TaskStatus.DRAFT: "Run Impact Analysis",
            TaskStatus.ANALYZED: "Generate AI Plan",
            TaskStatus.PLANNED: "Submit for Plan Approval",
            TaskStatus.PENDING_PLAN_APPROVAL: "Awaiting Plan Approval",
            TaskStatus.PLAN_APPROVED: "Start Implementation",
            TaskStatus.IMPLEMENTED: "Run Tests",
            TaskStatus.TESTS_PASSED: "Create Local Commit",
            TaskStatus.COMMITTED: "Submit for Commit Approval",
            TaskStatus.PENDING_COMMIT_APPROVAL: "Awaiting Commit Approval",
            TaskStatus.COMMIT_APPROVED: "Push & Create PR",
            TaskStatus.PR_CREATED: "Awaiting TL Review in GitHub",
            TaskStatus.PENDING_TL_APPROVAL: "Awaiting TL Merge",
            TaskStatus.MERGED: "Completed",
            TaskStatus.TESTS_FAILED: "Fix Tests and Retry",
            TaskStatus.PLAN_REJECTED: "Revise Plan",
            TaskStatus.COMMIT_REJECTED: "Revise Changes",
            TaskStatus.FAILED: "Review Error",
        }
        return actions.get(self.status, "Unknown")
    
    def _can_proceed(self) -> bool:
        """Check if task can proceed to next stage"""
        blocked_statuses = [
            TaskStatus.PENDING_PLAN_APPROVAL,
            TaskStatus.PENDING_COMMIT_APPROVAL,
            TaskStatus.PENDING_TL_APPROVAL,
            TaskStatus.TESTS_FAILED,
            TaskStatus.PLAN_REJECTED,
            TaskStatus.COMMIT_REJECTED,
            TaskStatus.FAILED,
            TaskStatus.MERGED,
            TaskStatus.CANCELLED,
        ]
        return self.status not in blocked_statuses


class PipelineService:
    """Service to orchestrate the development workflow pipeline"""
    
    def __init__(self, db_path: str = "pipeline.db"):
        self.db_path = db_path
        self._initialized = False
    
    async def _init_db(self) -> None:
        """Initialize database schema"""
        if self._initialized:
            return
        
        async with aiosqlite.connect(self.db_path) as db:
            await db.executescript("""
                CREATE TABLE IF NOT EXISTS pipeline_tasks (
                    id TEXT PRIMARY KEY,
                    title TEXT NOT NULL,
                    type TEXT NOT NULL,
                    description TEXT,
                    index_name TEXT NOT NULL,
                    target_files TEXT,
                    target_functions TEXT,
                    assigned_to TEXT,
                    priority TEXT DEFAULT 'MEDIUM',
                    status TEXT NOT NULL DEFAULT 'draft',
                    current_stage INTEGER DEFAULT 1,
                    
                    -- Stage 2: Impact Analysis
                    impact_report TEXT,
                    
                    -- Stage 3: AI Plan
                    ai_plan TEXT,
                    
                    -- Stage 4: Plan Approval
                    plan_approved_by TEXT,
                    plan_approved_at TIMESTAMP,
                    plan_comments TEXT,
                    
                    -- Stage 5: Implementation
                    changed_files TEXT,
                    
                    -- Stage 6: Testing
                    test_results TEXT,
                    
                    -- Stage 7: Local Commit
                    commit_sha TEXT,
                    commit_message TEXT,
                    
                    -- Stage 8: Commit Approval
                    commit_approved_by TEXT,
                    commit_approved_at TIMESTAMP,
                    
                    -- Stage 9: Push + PR
                    branch_name TEXT,
                    pr_url TEXT,
                    pr_number INTEGER,
                    
                    -- Stage 10: TL Merge
                    merged_at TIMESTAMP,
                    merged_by TEXT,
                    
                    -- Error handling
                    error_message TEXT,
                    
                    -- Timestamps
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                );
                
                CREATE INDEX IF NOT EXISTS idx_tasks_status ON pipeline_tasks(status);
                CREATE INDEX IF NOT EXISTS idx_tasks_type ON pipeline_tasks(type);
                CREATE INDEX IF NOT EXISTS idx_tasks_assigned ON pipeline_tasks(assigned_to);
            """)
            await db.commit()
        
        self._initialized = True
    
    async def create_task(
        self,
        title: str,
        type: Literal["BUGFIX", "CR", "NR"],
        description: str = "",
        index_name: str = "etail-admin",
        target_files: list[str] = None,
        target_functions: list[str] = None,
        assigned_to: str = None,
        priority: str = "MEDIUM",
    ) -> PipelineTask:
        """Stage 1: Create a new task"""
        await self._init_db()
        
        task = PipelineTask(
            title=title,
            type=type,
            description=description,
            index_name=index_name,
            target_files=target_files or [],
            target_functions=target_functions or [],
            assigned_to=assigned_to,
            priority=priority,
            status=TaskStatus.DRAFT,
            current_stage=1,
        )
        
        async with aiosqlite.connect(self.db_path) as db:
            await db.execute("""
                INSERT INTO pipeline_tasks (
                    id, title, type, description, index_name,
                    target_files, target_functions, assigned_to, priority,
                    status, current_stage, created_at, updated_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            """, (
                task.id, task.title, task.type, task.description, task.index_name,
                json.dumps(task.target_files), json.dumps(task.target_functions),
                task.assigned_to, task.priority,
                task.status.value, task.current_stage,
                task.created_at.isoformat(), task.updated_at.isoformat()
            ))
            await db.commit()
        
        return task
    
    async def get_task(self, task_id: str) -> PipelineTask | None:
        """Get a task by ID"""
        await self._init_db()
        
        async with aiosqlite.connect(self.db_path) as db:
            db.row_factory = aiosqlite.Row
            cursor = await db.execute(
                "SELECT * FROM pipeline_tasks WHERE id = ?", (task_id,)
            )
            row = await cursor.fetchone()
            if not row:
                return None
            
            return self._row_to_task(row)
    
    async def list_tasks(
        self,
        status: str = None,
        type: str = None,
        assigned_to: str = None,
        limit: int = 50,
    ) -> list[PipelineTask]:
        """List tasks with optional filters"""
        await self._init_db()
        
        query = "SELECT * FROM pipeline_tasks WHERE 1=1"
        params = []
        
        if status:
            query += " AND status = ?"
            params.append(status)
        if type:
            query += " AND type = ?"
            params.append(type)
        if assigned_to:
            query += " AND assigned_to = ?"
            params.append(assigned_to)
        
        query += " ORDER BY updated_at DESC LIMIT ?"
        params.append(limit)
        
        async with aiosqlite.connect(self.db_path) as db:
            db.row_factory = aiosqlite.Row
            cursor = await db.execute(query, params)
            rows = await cursor.fetchall()
            return [self._row_to_task(row) for row in rows]
    
    async def update_task(self, task: PipelineTask) -> None:
        """Update a task in the database"""
        await self._init_db()
        task.updated_at = datetime.now()
        
        async with aiosqlite.connect(self.db_path) as db:
            await db.execute("""
                UPDATE pipeline_tasks SET
                    title = ?, type = ?, description = ?, index_name = ?,
                    target_files = ?, target_functions = ?, assigned_to = ?,
                    priority = ?, status = ?, current_stage = ?,
                    impact_report = ?, ai_plan = ?,
                    plan_approved_by = ?, plan_approved_at = ?, plan_comments = ?,
                    changed_files = ?, test_results = ?,
                    commit_sha = ?, commit_message = ?,
                    commit_approved_by = ?, commit_approved_at = ?,
                    branch_name = ?, pr_url = ?, pr_number = ?,
                    merged_at = ?, merged_by = ?,
                    error_message = ?, updated_at = ?
                WHERE id = ?
            """, (
                task.title, task.type, task.description, task.index_name,
                json.dumps(task.target_files), json.dumps(task.target_functions),
                task.assigned_to, task.priority,
                task.status.value if isinstance(task.status, TaskStatus) else task.status,
                task.current_stage,
                json.dumps(task.impact_report) if task.impact_report else None,
                task.ai_plan,
                task.plan_approved_by,
                task.plan_approved_at.isoformat() if task.plan_approved_at else None,
                task.plan_comments,
                json.dumps(task.changed_files) if task.changed_files else None,
                json.dumps(task.test_results) if task.test_results else None,
                task.commit_sha, task.commit_message,
                task.commit_approved_by,
                task.commit_approved_at.isoformat() if task.commit_approved_at else None,
                task.branch_name, task.pr_url, task.pr_number,
                task.merged_at.isoformat() if task.merged_at else None,
                task.merged_by,
                task.error_message, task.updated_at.isoformat(),
                task.id
            ))
            await db.commit()
    
    async def delete_task(self, task_id: str) -> bool:
        """Delete a task"""
        await self._init_db()
        
        async with aiosqlite.connect(self.db_path) as db:
            result = await db.execute(
                "DELETE FROM pipeline_tasks WHERE id = ?", (task_id,)
            )
            await db.commit()
            return result.rowcount > 0
    
    # ========== Stage Transition Methods ==========
    
    async def run_impact_analysis(self, task_id: str) -> dict:
        """Stage 2: Run impact analysis"""
        task = await self.get_task(task_id)
        if not task:
            raise ValueError(f"Task {task_id} not found")
        
        task.status = TaskStatus.ANALYZING
        task.current_stage = 2
        await self.update_task(task)
        
        # Import here to avoid circular imports
        from .impact_service import ImpactService
        impact_service = ImpactService()
        
        # Analyze each target function
        impact_results = []
        total_impact = 0
        max_risk = "LOW"
        
        for func in task.target_functions:
            try:
                result = await impact_service.analyze(task.index_name, func)
                impact_results.append(result)
                total_impact += result.get("total_impact", 0)
                risk = result.get("risk_level", "LOW")
                if risk == "CRITICAL" or (risk == "HIGH" and max_risk != "CRITICAL"):
                    max_risk = risk
                elif risk == "MEDIUM" and max_risk not in ["CRITICAL", "HIGH"]:
                    max_risk = risk
            except Exception as e:
                impact_results.append({"function": func, "error": str(e)})
        
        task.impact_report = {
            "functions_analyzed": len(task.target_functions),
            "files_analyzed": len(task.target_files),
            "total_impact": total_impact,
            "risk_level": max_risk,
            "details": impact_results,
        }
        task.status = TaskStatus.ANALYZED
        await self.update_task(task)
        
        return task.impact_report
    
    async def generate_plan(self, task_id: str) -> str:
        """Stage 3: Generate AI implementation plan"""
        task = await self.get_task(task_id)
        if not task:
            raise ValueError(f"Task {task_id} not found")
        
        task.status = TaskStatus.PLANNING
        task.current_stage = 3
        await self.update_task(task)
        
        # Generate AI plan (mock for now, integrate with actual AI later)
        plan = f"""# Implementation Plan for {task.title}

## Type: {task.type}
## Priority: {task.priority}

### Impact Summary
- Risk Level: {task.impact_report.get('risk_level', 'N/A') if task.impact_report else 'N/A'}
- Total Functions Affected: {task.impact_report.get('total_impact', 0) if task.impact_report else 0}

### Target Files
{chr(10).join(f'- {f}' for f in task.target_files) if task.target_files else '- No specific files targeted'}

### Target Functions
{chr(10).join(f'- {f}' for f in task.target_functions) if task.target_functions else '- No specific functions targeted'}

### Implementation Steps
1. Review current implementation
2. Identify affected code paths
3. Implement changes
4. Add/update unit tests
5. Run full test suite
6. Commit and submit for review

### Testing Strategy
- Run PHPUnit tests for backend changes
- Run Jest tests for frontend changes
- Manual verification of affected functionality

### Rollback Plan
If issues arise, revert to previous commit and investigate.
"""
        
        task.ai_plan = plan
        task.status = TaskStatus.PLANNED
        await self.update_task(task)
        
        return plan
    
    async def submit_for_plan_approval(self, task_id: str) -> PipelineTask:
        """Submit task for plan approval"""
        task = await self.get_task(task_id)
        if not task:
            raise ValueError(f"Task {task_id} not found")
        
        task.status = TaskStatus.PENDING_PLAN_APPROVAL
        task.current_stage = 4
        await self.update_task(task)
        return task
    
    async def approve_plan(
        self, task_id: str, approved_by: str, comments: str = None
    ) -> PipelineTask:
        """Stage 4: Approve the implementation plan"""
        task = await self.get_task(task_id)
        if not task:
            raise ValueError(f"Task {task_id} not found")
        
        task.plan_approved_by = approved_by
        task.plan_approved_at = datetime.now()
        task.plan_comments = comments
        task.status = TaskStatus.PLAN_APPROVED
        await self.update_task(task)
        return task
    
    async def reject_plan(
        self, task_id: str, rejected_by: str, comments: str
    ) -> PipelineTask:
        """Reject the implementation plan"""
        task = await self.get_task(task_id)
        if not task:
            raise ValueError(f"Task {task_id} not found")
        
        task.plan_comments = f"Rejected by {rejected_by}: {comments}"
        task.status = TaskStatus.PLAN_REJECTED
        await self.update_task(task)
        return task
    
    async def start_implementation(self, task_id: str) -> PipelineTask:
        """Stage 5: Mark task as in progress"""
        task = await self.get_task(task_id)
        if not task:
            raise ValueError(f"Task {task_id} not found")
        
        task.status = TaskStatus.IN_PROGRESS
        task.current_stage = 5
        await self.update_task(task)
        return task
    
    async def complete_implementation(
        self, task_id: str, changed_files: list[str]
    ) -> PipelineTask:
        """Mark implementation as complete"""
        task = await self.get_task(task_id)
        if not task:
            raise ValueError(f"Task {task_id} not found")
        
        task.changed_files = changed_files
        task.status = TaskStatus.IMPLEMENTED
        await self.update_task(task)
        return task
    
    async def run_tests(self, task_id: str) -> dict:
        """Stage 6: Run automated tests"""
        task = await self.get_task(task_id)
        if not task:
            raise ValueError(f"Task {task_id} not found")
        
        task.status = TaskStatus.TESTING
        task.current_stage = 6
        await self.update_task(task)
        
        # Import and run tests
        from .test_service import TestService
        test_service = TestService()
        
        results = await test_service.run_tests(task.changed_files)
        task.test_results = results
        
        if results.get("passed", False):
            task.status = TaskStatus.TESTS_PASSED
        else:
            task.status = TaskStatus.TESTS_FAILED
        
        await self.update_task(task)
        return results
    
    async def create_commit(
        self, task_id: str, message: str = None
    ) -> PipelineTask:
        """Stage 7: Create local commit (not pushed)"""
        task = await self.get_task(task_id)
        if not task:
            raise ValueError(f"Task {task_id} not found")
        
        task.status = TaskStatus.COMMITTING
        task.current_stage = 7
        await self.update_task(task)
        
        from .git_service import GitService
        git_service = GitService()
        
        # Create branch name
        branch_name = f"{task.type.lower()}/{task.id[:8]}-{task.title.lower().replace(' ', '-')[:30]}"
        task.branch_name = branch_name
        
        # Create commit message
        commit_message = message or f"[{task.type}] {task.title}\n\nTask ID: {task.id}"
        task.commit_message = commit_message
        
        # Create local commit
        commit_sha = await git_service.create_local_commit(
            branch_name=branch_name,
            files=task.changed_files,
            message=commit_message
        )
        task.commit_sha = commit_sha
        task.status = TaskStatus.COMMITTED
        await self.update_task(task)
        
        return task
    
    async def submit_for_commit_approval(self, task_id: str) -> PipelineTask:
        """Submit commit for approval before push"""
        task = await self.get_task(task_id)
        if not task:
            raise ValueError(f"Task {task_id} not found")
        
        task.status = TaskStatus.PENDING_COMMIT_APPROVAL
        task.current_stage = 8
        await self.update_task(task)
        return task
    
    async def approve_commit(
        self, task_id: str, approved_by: str
    ) -> PipelineTask:
        """Stage 8: Approve commit for push"""
        task = await self.get_task(task_id)
        if not task:
            raise ValueError(f"Task {task_id} not found")
        
        task.commit_approved_by = approved_by
        task.commit_approved_at = datetime.now()
        task.status = TaskStatus.COMMIT_APPROVED
        await self.update_task(task)
        return task
    
    async def push_and_create_pr(self, task_id: str) -> PipelineTask:
        """Stage 9: Push branch and create PR"""
        task = await self.get_task(task_id)
        if not task:
            raise ValueError(f"Task {task_id} not found")
        
        task.status = TaskStatus.PUSHING
        task.current_stage = 9
        await self.update_task(task)
        
        from .git_service import GitService
        git_service = GitService()
        
        # Push branch
        await git_service.push_branch(task.branch_name)
        
        # Create PR
        pr_result = await git_service.create_pull_request(
            title=f"[{task.type}] {task.title}",
            body=f"""## Task: {task.title}

**Type:** {task.type}
**Priority:** {task.priority}
**Task ID:** {task.id}

### Description
{task.description}

### Impact Analysis
- Risk Level: {task.impact_report.get('risk_level', 'N/A') if task.impact_report else 'N/A'}
- Functions Affected: {task.impact_report.get('total_impact', 0) if task.impact_report else 0}

### Test Results
- Passed: {task.test_results.get('passed', False) if task.test_results else 'N/A'}

### Approvals
- Plan Approved By: {task.plan_approved_by or 'N/A'}
- Commit Approved By: {task.commit_approved_by or 'N/A'}
""",
            head=task.branch_name,
            base="main"
        )
        
        task.pr_url = pr_result.get("url")
        task.pr_number = pr_result.get("number")
        task.status = TaskStatus.PR_CREATED
        await self.update_task(task)
        
        return task
    
    async def mark_merged(
        self, task_id: str, merged_by: str
    ) -> PipelineTask:
        """Stage 10: Mark PR as merged (called after TL merges in GitHub)"""
        task = await self.get_task(task_id)
        if not task:
            raise ValueError(f"Task {task_id} not found")
        
        task.merged_by = merged_by
        task.merged_at = datetime.now()
        task.status = TaskStatus.MERGED
        task.current_stage = 10
        await self.update_task(task)
        return task
    
    # ========== Helper Methods ==========
    
    def _row_to_task(self, row) -> PipelineTask:
        """Convert database row to PipelineTask"""
        return PipelineTask(
            id=row["id"],
            title=row["title"],
            type=row["type"],
            description=row["description"],
            index_name=row["index_name"],
            target_files=json.loads(row["target_files"]) if row["target_files"] else [],
            target_functions=json.loads(row["target_functions"]) if row["target_functions"] else [],
            assigned_to=row["assigned_to"],
            priority=row["priority"],
            status=TaskStatus(row["status"]) if row["status"] else TaskStatus.DRAFT,
            current_stage=row["current_stage"] or 1,
            impact_report=json.loads(row["impact_report"]) if row["impact_report"] else None,
            ai_plan=row["ai_plan"],
            plan_approved_by=row["plan_approved_by"],
            plan_approved_at=datetime.fromisoformat(row["plan_approved_at"]) if row["plan_approved_at"] else None,
            plan_comments=row["plan_comments"],
            changed_files=json.loads(row["changed_files"]) if row["changed_files"] else [],
            test_results=json.loads(row["test_results"]) if row["test_results"] else None,
            commit_sha=row["commit_sha"],
            commit_message=row["commit_message"],
            commit_approved_by=row["commit_approved_by"],
            commit_approved_at=datetime.fromisoformat(row["commit_approved_at"]) if row["commit_approved_at"] else None,
            branch_name=row["branch_name"],
            pr_url=row["pr_url"],
            pr_number=row["pr_number"],
            merged_at=datetime.fromisoformat(row["merged_at"]) if row["merged_at"] else None,
            merged_by=row["merged_by"],
            error_message=row["error_message"],
            created_at=datetime.fromisoformat(row["created_at"]) if row["created_at"] else None,
            updated_at=datetime.fromisoformat(row["updated_at"]) if row["updated_at"] else None,
        )
