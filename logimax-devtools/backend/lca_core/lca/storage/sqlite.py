"""
LCA Core - SQLite Storage Implementation
Implements the Storage interface using SQLite for persistence
"""
import json
import aiosqlite
from pathlib import Path
from typing import Any

from lca_core.lca.core import Storage, CodeIndex, FunctionDef


class SQLiteStorage(Storage):
    """SQLite-based storage for LCA indexes"""
    
    def __init__(self, db_path: str = "lca_index.db"):
        self.db_path = db_path
        self._initialized = False
    
    async def _init_db(self) -> None:
        """Initialize database schema"""
        if self._initialized:
            return
            
        async with aiosqlite.connect(self.db_path) as db:
            await db.executescript("""
                CREATE TABLE IF NOT EXISTS indexes (
                    name TEXT PRIMARY KEY,
                    path TEXT NOT NULL,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                );
                
                CREATE TABLE IF NOT EXISTS functions (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    index_name TEXT NOT NULL,
                    name TEXT NOT NULL,
                    qualified_name TEXT NOT NULL,
                    file TEXT NOT NULL,
                    line INTEGER NOT NULL,
                    end_line INTEGER,
                    class_name TEXT,
                    FOREIGN KEY (index_name) REFERENCES indexes(name) ON DELETE CASCADE
                );
                
                CREATE TABLE IF NOT EXISTS calls (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    index_name TEXT NOT NULL,
                    caller TEXT NOT NULL,
                    callee TEXT NOT NULL,
                    line INTEGER,
                    FOREIGN KEY (index_name) REFERENCES indexes(name) ON DELETE CASCADE
                );
                
                CREATE INDEX IF NOT EXISTS idx_functions_name ON functions(name);
                CREATE INDEX IF NOT EXISTS idx_functions_qualified ON functions(qualified_name);
                CREATE INDEX IF NOT EXISTS idx_functions_index ON functions(index_name);
                CREATE INDEX IF NOT EXISTS idx_calls_caller ON calls(caller);
                CREATE INDEX IF NOT EXISTS idx_calls_callee ON calls(callee);
                CREATE INDEX IF NOT EXISTS idx_calls_index ON calls(index_name);
            """)
            await db.commit()
        
        self._initialized = True
    
    async def save_index(self, index: CodeIndex) -> None:
        """Save an index to the database"""
        await self._init_db()
        
        async with aiosqlite.connect(self.db_path) as db:
            # Delete existing index if present
            await db.execute("DELETE FROM functions WHERE index_name = ?", (index.name,))
            await db.execute("DELETE FROM calls WHERE index_name = ?", (index.name,))
            await db.execute("DELETE FROM indexes WHERE name = ?", (index.name,))
            
            # Insert index
            await db.execute(
                "INSERT INTO indexes (name, path) VALUES (?, ?)",
                (index.name, index.path)
            )
            
            # Insert functions
            for fn in index.functions.values():
                await db.execute(
                    """INSERT INTO functions 
                       (index_name, name, qualified_name, file, line, end_line, class_name)
                       VALUES (?, ?, ?, ?, ?, ?, ?)""",
                    (index.name, fn.name, fn.qualified_name, fn.file, fn.line, fn.end_line, fn.class_name)
                )
            
            # Insert calls (now with line numbers)
            for caller, callees in index.calls.items():
                for callee_info in callees:
                    # Handle both tuple (callee, line) and legacy string format
                    if isinstance(callee_info, tuple):
                        callee, line = callee_info
                    else:
                        callee, line = callee_info, None
                    await db.execute(
                        "INSERT INTO calls (index_name, caller, callee, line) VALUES (?, ?, ?, ?)",
                        (index.name, caller, callee, line)
                    )
            
            await db.commit()
    
    async def load_index(self, name: str) -> CodeIndex | None:
        """Load an index by name"""
        await self._init_db()
        
        async with aiosqlite.connect(self.db_path) as db:
            db.row_factory = aiosqlite.Row
            
            # Get index metadata
            cursor = await db.execute(
                "SELECT * FROM indexes WHERE name = ?", (name,)
            )
            row = await cursor.fetchone()
            if not row:
                return None
            
            index = CodeIndex(name=row["name"], path=row["path"])
            
            # Load functions
            cursor = await db.execute(
                "SELECT * FROM functions WHERE index_name = ?", (name,)
            )
            async for row in cursor:
                fn = FunctionDef(
                    name=row["name"],
                    file=row["file"],
                    line=row["line"],
                    end_line=row["end_line"],
                    class_name=row["class_name"],
                )
                index.functions[fn.qualified_name] = fn
            
            # Load calls (with line numbers)
            cursor = await db.execute(
                "SELECT caller, callee, line FROM calls WHERE index_name = ?", (name,)
            )
            async for row in cursor:
                caller = row["caller"]
                if caller not in index.calls:
                    index.calls[caller] = []
                line = row["line"] if row["line"] is not None else 0
                index.calls[caller].append((row["callee"], line))
            
            return index
    
    async def list_indexes(self) -> list[str]:
        """List all available indexes"""
        await self._init_db()
        
        async with aiosqlite.connect(self.db_path) as db:
            cursor = await db.execute("SELECT name FROM indexes")
            rows = await cursor.fetchall()
            return [row[0] for row in rows]
    
    async def delete_index(self, name: str) -> bool:
        """Delete an index"""
        await self._init_db()
        
        async with aiosqlite.connect(self.db_path) as db:
            await db.execute("DELETE FROM functions WHERE index_name = ?", (name,))
            await db.execute("DELETE FROM calls WHERE index_name = ?", (name,))
            result = await db.execute("DELETE FROM indexes WHERE name = ?", (name,))
            await db.commit()
            return result.rowcount > 0
    
    async def get_callers(self, index_name: str, function: str) -> list[tuple[str, int]]:
        """Get all functions that call the given function with line numbers"""
        await self._init_db()
        
        async with aiosqlite.connect(self.db_path) as db:
            cursor = await db.execute(
                "SELECT DISTINCT caller, line FROM calls WHERE index_name = ? AND callee = ?",
                (index_name, function)
            )
            rows = await cursor.fetchall()
            return [(row[0], row[1] or 0) for row in rows]
    
    async def get_callees(self, index_name: str, function: str) -> list[tuple[str, int]]:
        """Get all functions called by the given function with line numbers"""
        await self._init_db()
        
        async with aiosqlite.connect(self.db_path) as db:
            cursor = await db.execute(
                "SELECT DISTINCT callee, line FROM calls WHERE index_name = ? AND caller = ?",
                (index_name, function)
            )
            rows = await cursor.fetchall()
            return [(row[0], row[1] or 0) for row in rows]
    
    async def search_functions(
        self, index_name: str, query: str, limit: int = 20
    ) -> list[FunctionDef]:
        """Search functions by name pattern"""
        await self._init_db()
        
        async with aiosqlite.connect(self.db_path) as db:
            db.row_factory = aiosqlite.Row
            cursor = await db.execute(
                """SELECT * FROM functions 
                   WHERE index_name = ? AND (name LIKE ? OR qualified_name LIKE ?)
                   LIMIT ?""",
                (index_name, f"%{query}%", f"%{query}%", limit)
            )
            
            results = []
            async for row in cursor:
                results.append(FunctionDef(
                    name=row["name"],
                    file=row["file"],
                    line=row["line"],
                    end_line=row["end_line"],
                    class_name=row["class_name"],
                ))
            return results
    
    async def get_stats(self, index_name: str) -> dict[str, Any]:
        """Get statistics for an index"""
        await self._init_db()
        
        async with aiosqlite.connect(self.db_path) as db:
            cursor = await db.execute(
                "SELECT COUNT(*) FROM functions WHERE index_name = ?",
                (index_name,)
            )
            fn_count = (await cursor.fetchone())[0]
            
            cursor = await db.execute(
                "SELECT COUNT(*) FROM calls WHERE index_name = ?",
                (index_name,)
            )
            call_count = (await cursor.fetchone())[0]
            
            return {
                "functions": fn_count,
                "calls": call_count,
            }
