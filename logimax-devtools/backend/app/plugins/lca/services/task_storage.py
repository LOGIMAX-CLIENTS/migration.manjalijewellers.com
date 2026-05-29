"""
LCA Plugin - Task Storage (SQLite)
Persistent storage for workflow tasks
"""
import json
import aiosqlite
from datetime import datetime
from typing import Optional, Any
from pathlib import Path


class TaskStorage:
    """SQLite-based storage for workflow tasks"""
    
    def __init__(self, db_path: str = "tasks.db"):
        self.db_path = db_path
        self._initialized = False
    
    async def _init_db(self) -> None:
        """Initialize database schema"""
        if self._initialized:
            return
        
        async with aiosqlite.connect(self.db_path) as db:
            await db.executescript("""
                CREATE TABLE IF NOT EXISTS tasks (
                    id TEXT PRIMARY KEY,
                    title TEXT NOT NULL,
                    description TEXT,
                    target_function TEXT,
                    target_file TEXT,
                    intent TEXT,
                    index_name TEXT NOT NULL,
                    status TEXT NOT NULL DEFAULT 'created',
                    
                    -- Impact Report (JSON)
                    impact_report TEXT,
                    
                    -- AI
                    ai_plan TEXT,
                    ai_explanation TEXT,
                    
                    -- Subtasks (JSON array)
                    subtasks TEXT,
                    
                    -- Implementation
                    files_changed TEXT,
                    implementation_notes TEXT,
                    
                    -- Test Results (JSON)
                    test_results TEXT,
                    
                    -- Timestamps
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    approved_at TIMESTAMP,
                    completed_at TIMESTAMP
                );
                
                CREATE INDEX IF NOT EXISTS idx_tasks_status ON tasks(status);
                CREATE INDEX IF NOT EXISTS idx_tasks_index ON tasks(index_name);
            """)
            await db.commit()
        
        self._initialized = True
    
    async def create_task(
        self,
        id: str,
        title: str,
        index_name: str,
        target_function: Optional[str] = None,
        intent: Optional[str] = None,
        description: Optional[str] = None,
        status: str = "created",
        ai_plan: Optional[str] = None
    ) -> dict:
        """Create a new task"""
        await self._init_db()
        
        async with aiosqlite.connect(self.db_path) as db:
            await db.execute(
                """INSERT INTO tasks (id, title, index_name, target_function, intent, description, status, ai_plan)
                   VALUES (?, ?, ?, ?, ?, ?, ?, ?)""",
                (id, title, index_name, target_function, intent, description, status, ai_plan)
            )
            await db.commit()
        
        return await self.get_task(id)
    
    async def get_task(self, task_id: str) -> Optional[dict]:
        """Get a task by ID"""
        await self._init_db()
        
        async with aiosqlite.connect(self.db_path) as db:
            db.row_factory = aiosqlite.Row
            cursor = await db.execute("SELECT * FROM tasks WHERE id = ?", (task_id,))
            row = await cursor.fetchone()
            
            if not row:
                return None
            
            return self._row_to_dict(row)
    
    async def list_tasks(self, status: Optional[str] = None, limit: int = 50) -> list[dict]:
        """List all tasks, optionally filtered by status"""
        await self._init_db()
        
        async with aiosqlite.connect(self.db_path) as db:
            db.row_factory = aiosqlite.Row
            
            if status:
                cursor = await db.execute(
                    "SELECT * FROM tasks WHERE status = ? ORDER BY created_at DESC LIMIT ?",
                    (status, limit)
                )
            else:
                cursor = await db.execute(
                    "SELECT * FROM tasks ORDER BY created_at DESC LIMIT ?",
                    (limit,)
                )
            
            rows = await cursor.fetchall()
            return [self._row_to_dict(row) for row in rows]
    
    async def update_task(self, task_id: str, **updates) -> Optional[dict]:
        """Update task fields"""
        await self._init_db()
        
        # Serialize JSON fields
        json_fields = ['impact_report', 'subtasks', 'files_changed', 'test_results']
        for field in json_fields:
            if field in updates and updates[field] is not None:
                updates[field] = json.dumps(updates[field])
        
        # Build update query
        set_clauses = ", ".join(f"{k} = ?" for k in updates.keys())
        values = list(updates.values())
        values.append(datetime.now().isoformat())  # updated_at
        values.append(task_id)
        
        async with aiosqlite.connect(self.db_path) as db:
            await db.execute(
                f"UPDATE tasks SET {set_clauses}, updated_at = ? WHERE id = ?",
                values
            )
            await db.commit()
        
        return await self.get_task(task_id)
    
    async def update_status(self, task_id: str, status: str) -> Optional[dict]:
        """Update task status with timestamp tracking"""
        await self._init_db()
        
        updates = {"status": status, "updated_at": datetime.now().isoformat()}
        
        if status == "approved":
            updates["approved_at"] = datetime.now().isoformat()
        elif status == "completed":
            updates["completed_at"] = datetime.now().isoformat()
        
        async with aiosqlite.connect(self.db_path) as db:
            set_clauses = ", ".join(f"{k} = ?" for k in updates.keys())
            values = list(updates.values())
            values.append(task_id)
            
            await db.execute(f"UPDATE tasks SET {set_clauses} WHERE id = ?", values)
            await db.commit()
        
        return await self.get_task(task_id)
    
    async def delete_task(self, task_id: str) -> bool:
        """Delete a task"""
        await self._init_db()
        
        async with aiosqlite.connect(self.db_path) as db:
            result = await db.execute("DELETE FROM tasks WHERE id = ?", (task_id,))
            await db.commit()
            return result.rowcount > 0
    
    def _row_to_dict(self, row: aiosqlite.Row) -> dict:
        """Convert database row to dictionary"""
        result = dict(row)
        
        # Parse JSON fields
        json_fields = ['impact_report', 'subtasks', 'files_changed', 'test_results']
        for field in json_fields:
            if result.get(field):
                try:
                    result[field] = json.loads(result[field])
                except json.JSONDecodeError:
                    pass
        
        return result
