/**
 * Plugin System - Types
 * Defines the contract for all DevTools plugins
 */

import { RouteObject } from 'react-router-dom';

/**
 * Plugin definition interface
 * All plugins must implement this interface
 */
export interface DevToolsPlugin {
  // Identity
  id: string;
  name: string;
  description: string;
  version: string;

  // UI
  icon: React.ComponentType<{ className?: string }>;
  routes: RouteObject[];
  navigation?: NavigationItem[];

  // Optional features
  commands?: CommandDefinition[];
  settings?: SettingDefinition[];
  statusWidget?: React.ComponentType;

  // Lifecycle hooks
  onInit?: () => Promise<void>;
  onActivate?: () => void;
  onDeactivate?: () => void;
}

/**
 * Navigation item for plugin sub-navigation
 */
export interface NavigationItem {
  label: string;
  path: string;
  icon?: React.ComponentType<{ className?: string }>;
}

/**
 * Command definition for keyboard shortcuts and command palette
 */
export interface CommandDefinition {
  id: string;
  name: string;
  description?: string;
  shortcut?: string;
  handler: () => void;
  category?: string;
}

/**
 * Setting definition for plugin configuration
 */
export interface SettingDefinition {
  key: string;
  type: 'string' | 'number' | 'boolean' | 'select';
  label: string;
  description?: string;
  default: string | number | boolean;
  options?: Array<{ value: string; label: string }>;
  validate?: (value: unknown) => boolean | string;
}

/**
 * Plugin registration context
 */
export interface PluginContext {
  // API client
  api: {
    get: <T>(path: string) => Promise<T>;
    post: <T>(path: string, data?: unknown) => Promise<T>;
    put: <T>(path: string, data?: unknown) => Promise<T>;
    delete: <T>(path: string) => Promise<T>;
  };

  // Notifications
  notify: {
    success: (message: string) => void;
    error: (message: string) => void;
    warning: (message: string) => void;
    info: (message: string) => void;
  };

  // File operations
  file: {
    openInEditor: (path: string, line?: number) => void;
  };

  // Settings
  settings: {
    get: <T>(key: string, defaultValue: T) => T;
    set: (key: string, value: unknown) => void;
  };
}

/**
 * Plugin state
 */
export interface PluginState {
  id: string;
  isActive: boolean;
  isLoading: boolean;
  error?: string;
}
