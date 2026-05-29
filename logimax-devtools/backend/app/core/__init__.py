"""Core package init"""
from .plugin_system import BackendPlugin, plugin_registry

__all__ = ["BackendPlugin", "plugin_registry"]
