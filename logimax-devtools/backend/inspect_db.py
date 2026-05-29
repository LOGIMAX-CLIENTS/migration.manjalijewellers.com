import asyncio
import aiosqlite
import os

async def list_indexes():
    db_path = "lca_index.db"
    if not os.path.exists(db_path):
        print("DB does not exist")
        return

    async with aiosqlite.connect(db_path) as db:
        try:
            async with db.execute("SELECT name, path FROM indexes") as cursor:
                rows = await cursor.fetchall()
                print(f"Indexes found: {len(rows)}")
                for row in rows:
                    print(f"- {row[0]} ({row[1]})")
        except Exception as e:
            print(f"Error querying DB: {e}")

if __name__ == "__main__":
    asyncio.run(list_indexes())
