"""
Logimax DevTools - Backend Configuration
"""
from functools import lru_cache
from pathlib import Path
from pydantic_settings import BaseSettings


class Settings(BaseSettings):
    """Application settings"""
    
    # App
    app_name: str = "Logimax DevTools API"
    app_version: str = "1.0.0"
    debug: bool = True
    
    # Server
    host: str = "0.0.0.0"
    port: int = 8800
    
    # CORS
    cors_origins: list[str] = ["http://localhost:5173", "http://127.0.0.1:5173"]
    
    # Paths
    etail_root: Path = Path(r"c:\xampp\htdocs\etail_v3")
    logs_path: Path = Path(r"c:\xampp\htdocs\etail_v3\admin\logs")
    
    # Database (for settings persistence)
    database_url: str = "sqlite+aiosqlite:///./devtools.db"
    
    class Config:
        env_prefix = "DEVTOOLS_"
        env_file = ".env"
        extra = "ignore"


@lru_cache
def get_settings() -> Settings:
    """Get cached settings instance"""
    return Settings()
