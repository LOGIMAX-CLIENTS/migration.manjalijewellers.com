/**
 * Plugin Registry
 * Central registry for managing DevTools plugins
 */

import { DevToolsPlugin, PluginState } from './types';

class PluginRegistryClass {
  private plugins: Map<string, DevToolsPlugin> = new Map();
  private states: Map<string, PluginState> = new Map();
  private listeners: Set<() => void> = new Set();
  private cache: DevToolsPlugin[] | null = null; // Cache for getAll()

  /**
   * Register a plugin
   */
  register(plugin: DevToolsPlugin): void {
    if (this.plugins.has(plugin.id)) {
      console.warn(`Plugin "${plugin.id}" is already registered. Skipping.`);
      return;
    }

    this.plugins.set(plugin.id, plugin);
    this.states.set(plugin.id, {
      id: plugin.id,
      isActive: false,
      isLoading: false,
    });

    this.cache = null; // Invalidate cache
    this.notifyListeners();
  }

  /**
   * Unregister a plugin
   */
  unregister(pluginId: string): void {
    const plugin = this.plugins.get(pluginId);
    if (plugin?.onDeactivate) {
      plugin.onDeactivate();
    }
    this.plugins.delete(pluginId);
    this.states.delete(pluginId);
    this.cache = null; // Invalidate cache
    this.notifyListeners();
  }

  /**
   * Get a plugin by ID
   */
  get(pluginId: string): DevToolsPlugin | undefined {
    return this.plugins.get(pluginId);
  }

  /**
   * Get all registered plugins
   */
  getAll(): DevToolsPlugin[] {
    if (!this.cache) {
      this.cache = Array.from(this.plugins.values());
    }
    return this.cache;
  }

  /**
   * Get plugin state
   */
  getState(pluginId: string): PluginState | undefined {
    return this.states.get(pluginId);
  }

  /**
   * Initialize a plugin
   */
  async initialize(pluginId: string): Promise<void> {
    const plugin = this.plugins.get(pluginId);
    const state = this.states.get(pluginId);

    if (!plugin || !state) {
      throw new Error(`Plugin "${pluginId}" not found`);
    }

    if (state.isActive) {
      return;
    }

    this.updateState(pluginId, { isLoading: true });

    try {
      if (plugin.onInit) {
        await plugin.onInit();
      }
      this.updateState(pluginId, { isActive: true, isLoading: false });

      if (plugin.onActivate) {
        plugin.onActivate();
      }
    } catch (error) {
      this.updateState(pluginId, {
        isLoading: false,
        error: error instanceof Error ? error.message : 'Unknown error',
      });
      throw error;
    }
  }

  /**
   * Initialize all plugins
   */
  async initializeAll(): Promise<void> {
    const plugins = this.getAll();
    await Promise.all(plugins.map((p) => this.initialize(p.id)));
  }

  /**
   * Get all routes from all plugins
   */
  getAllRoutes() {
    return this.getAll().flatMap((plugin) => plugin.routes);
  }

  /**
   * Subscribe to registry changes
   */
  subscribe(listener: () => void): () => void {
    this.listeners.add(listener);
    return () => this.listeners.delete(listener);
  }

  private updateState(pluginId: string, updates: Partial<PluginState>): void {
    const current = this.states.get(pluginId);
    if (current) {
      this.states.set(pluginId, { ...current, ...updates });
      this.notifyListeners();
    }
  }

  private notifyListeners(): void {
    this.listeners.forEach((listener) => listener());
  }
}

// Singleton instance
export const PluginRegistry = new PluginRegistryClass();
