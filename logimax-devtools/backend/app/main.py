"""
Logimax DevTools - FastAPI Backend
Main application entry point
"""
from contextlib import asynccontextmanager
from fastapi import FastAPI
from fastapi.middleware.cors import CORSMiddleware

from .config import get_settings
from .core.plugin_system import plugin_registry

# Import and register plugins
from .plugins.lca import LcaPlugin
from .plugins.testing import TestingPlugin
from .plugins.logs import LogsPlugin

plugin_registry.register(LcaPlugin())
plugin_registry.register(TestingPlugin())
plugin_registry.register(LogsPlugin())


@asynccontextmanager
async def lifespan(app: FastAPI):
    """Application lifespan handler"""
    # Startup
    await plugin_registry.init_all()
    yield
    # Shutdown
    await plugin_registry.shutdown_all()


def create_app() -> FastAPI:
    """Create and configure FastAPI application"""
    settings = get_settings()
    
    app = FastAPI(
        title=settings.app_name,
        version=settings.app_version,
        lifespan=lifespan,
    )
    
    # CORS
    app.add_middleware(
        CORSMiddleware,
        allow_origins=settings.cors_origins,
        allow_credentials=True,
        allow_methods=["*"],
        allow_headers=["*"],
    )
    
    # Health check
    @app.get("/health")
    async def health_check():
        return {"status": "healthy", "version": settings.app_version}
    
    # Register plugin routers
    for prefix, router in plugin_registry.get_all_routers():
        app.include_router(router, prefix=prefix)
    
    return app


app = create_app()


if __name__ == "__main__":
    import uvicorn
    settings = get_settings()
    uvicorn.run(
        "app.main:app",
        host=settings.host,
        port=settings.port,
        reload=settings.debug,
    )
