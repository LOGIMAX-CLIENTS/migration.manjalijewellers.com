/**
 * LCA Cockpit Plugin
 * Full rebuild with proper architecture
 */

import { BarChart3, FolderOpen, GitBranch, Target, Play } from 'lucide-react';
import type { DevToolsPlugin } from '@core/plugin-system';
import { Dashboard, Search as SearchPage, Workflow } from './pages';
import { Help } from './pages/Help';

export const lcaPlugin: DevToolsPlugin = {
  id: 'lca',
  name: 'LCA Cockpit',
  description: 'Code analysis, call graphs, and impact visualization',
  version: '2.0.0',
  icon: BarChart3,
  routes: [
    {
      path: 'lca',
      element: <Dashboard />,
    },
    {
      path: 'lca/search',
      element: <SearchPage />,
    },
    {
      path: 'lca/workflow',
      element: <Workflow />,
    },
    // Admin Path Aliases (for legacy/user preference)
    {
      path: 'admin/lca',
      element: <Dashboard />,
    },
    {
      path: 'admin/lca/search',
      element: <SearchPage />,
    },
    {
      path: 'admin/lca/workflow',
      element: <Workflow />,
    },
    {
       path: 'lca/help',
       element: <Help />
    }
  ],
  navigation: [
    { label: 'Index Generation', path: '/lca', icon: FolderOpen },
    { label: 'Impact Analysis', path: '/lca/search', icon: GitBranch },
    { label: 'Analyze', path: '/lca/workflow?tab=create', icon: Target },
    { label: 'Plan & Execution', path: '/lca/workflow?tab=plan', icon: Play },
  ],
  commands: [
    {
      id: 'lca.search',
      name: 'Search Functions',
      shortcut: 'ctrl+shift+f',
      handler: () => {
        window.location.href = '/lca/search';
      },
    },
  ],
};

export * from './types';
export * from './hooks';
