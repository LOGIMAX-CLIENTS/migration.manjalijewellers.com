"""
Backend Plugin System
Defines the plugin interface and registry for backend plugins
"""
from abc import ABC, abstractmethod
from typing import Any
from fastapi import APIRouter


class BackendPlugin(ABC):
    """Abstract base class for all backend plugins"""
    
    @property
    @abstractmethod
    def id(self) -> str:
        """Unique plugin identifier"""
        pass
    
    @property
    @abstractmethod
    def name(self) -> str:
        """Human-readable plugin name"""
        pass
    
    @property
    def version(self) -> str:
        """Plugin version"""
        return "1.0.0"
    
    @abstractmethod
    def get_router(self) -> APIRouter:
        """Return FastAPI router with plugin endpoints"""
        pass
    
    def get_mcp_tools(self) -> list[dict]:
        """Return MCP tool definitions for Antigravity integration"""
        return []
    
    async def on_init(self) -> None:
        """Called when plugin is initialized"""
        pass
    
    async def on_shutdown(self) -> None:
        """Called when application shuts down"""
        pass


class PluginRegistry:
    """Central registry for backend plugins"""
    
    def __init__(self):
        self._plugins: dict[str, BackendPlugin] = {}
    
    def register(self, plugin: BackendPlugin) -> None:
        """Register a plugin"""
        if plugin.id in self._plugins:
            raise ValueError(f"Plugin '{plugin.id}' is already registered")
        self._plugins[plugin.id] = plugin
    
    def get(self, plugin_id: str) -> BackendPlugin | None:
        """Get a plugin by ID"""
        return self._plugins.get(plugin_id)
    
    def get_all(self) -> list[BackendPlugin]:
        """Get all registered plugins"""
        return list(self._plugins.values())
    
    def get_all_routers(self) -> list[tuple[str, APIRouter]]:
        """Get all plugin routers with their prefixes"""
        return [(f"/api/{p.id}", p.get_router()) for p in self._plugins.values()]
    
    def get_all_mcp_tools(self) -> list[dict]:
        """Get all MCP tools from all plugins"""
        tools = []
        for plugin in self._plugins.values():
            tools.extend(plugin.get_mcp_tools())
        return tools
    
    async def init_all(self) -> None:
        """Initialize all plugins"""
        for plugin in self._plugins.values():
            await plugin.on_init()
    
    async def shutdown_all(self) -> None:
        """Shutdown all plugins"""
        for plugin in self._plugins.values():
            await plugin.on_shutdown()


# Singleton instance
plugin_registry = PluginRegistry()
