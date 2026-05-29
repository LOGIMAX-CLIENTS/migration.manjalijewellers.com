/**
 * Plugin System - Context & Hooks
 * React context for plugin system access
 */

import React, { createContext, useContext, useEffect, useState, useSyncExternalStore } from 'react';
import { PluginRegistry } from './PluginRegistry';
import { DevToolsPlugin, PluginContext as IPluginContext, PluginState } from './types';

// Create context
const PluginSystemContext = createContext<IPluginContext | null>(null);

/**
 * Plugin Context Provider
 */
export function PluginProvider({ children }: { children: React.ReactNode }) {
  const [isInitialized, setIsInitialized] = useState(false);

  useEffect(() => {
    // Initialize all plugins on mount
    PluginRegistry.initializeAll()
      .then(() => setIsInitialized(true))
      .catch(console.error);
  }, []);

  // Context value with services
  const contextValue: IPluginContext = {
    api: {
      get: async (path) => {
        const response = await fetch(`http://localhost:8800${path}`);
        return response.json();
      },
      post: async (path, data) => {
        const response = await fetch(`http://localhost:8800${path}`, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(data),
        });
        return response.json();
      },
      put: async (path, data) => {
        const response = await fetch(`http://localhost:8800${path}`, {
          method: 'PUT',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(data),
        });
        return response.json();
      },
      delete: async (path) => {
        const response = await fetch(`http://localhost:8800${path}`, {
          method: 'DELETE',
        });
        return response.json();
      },
    },
    notify: {
      success: (message) => console.log('✅', message),
      error: (message) => console.error('❌', message),
      warning: (message) => console.warn('⚠️', message),
      info: (message) => console.info('ℹ️', message),
    },
    file: {
      openInEditor: (path, line) => {
        // Default to VS Code
        const url = `vscode://file/${path}${line ? `:${line}` : ''}`;
        window.open(url, '_blank');
      },
    },
    settings: {
      get: (key, defaultValue) => {
        const stored = localStorage.getItem(`devtools.${key}`);
        return stored ? JSON.parse(stored) : defaultValue;
      },
      set: (key, value) => {
        localStorage.setItem(`devtools.${key}`, JSON.stringify(value));
      },
    },
  };

  if (!isInitialized) {
    return (
      <div className="flex h-screen items-center justify-center">
        <div className="text-center">
          <div className="animate-spin h-8 w-8 border-4 border-primary-500 border-t-transparent rounded-full mx-auto mb-4" />
          <p className="text-foreground-secondary">Loading DevTools...</p>
        </div>
      </div>
    );
  }

  return (
    <PluginSystemContext.Provider value={contextValue}>
      {children}
    </PluginSystemContext.Provider>
  );
}

/**
 * Hook to access plugin context
 */
export function usePluginContext(): IPluginContext {
  const context = useContext(PluginSystemContext);
  if (!context) {
    throw new Error('usePluginContext must be used within PluginProvider');
  }
  return context;
}

/**
 * Hook to get all plugins
 */
export function usePlugins(): DevToolsPlugin[] {
  return useSyncExternalStore(
    PluginRegistry.subscribe.bind(PluginRegistry),
    () => PluginRegistry.getAll()
  );
}

/**
 * Hook to get a specific plugin
 */
export function usePlugin(pluginId: string): DevToolsPlugin | undefined {
  return useSyncExternalStore(
    PluginRegistry.subscribe.bind(PluginRegistry),
    () => PluginRegistry.get(pluginId)
  );
}

/**
 * Hook to get plugin state
 */
export function usePluginState(pluginId: string): PluginState | undefined {
  return useSyncExternalStore(
    PluginRegistry.subscribe.bind(PluginRegistry),
    () => PluginRegistry.getState(pluginId)
  );
}
