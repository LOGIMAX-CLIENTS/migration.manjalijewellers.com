/**
 * App Component
 * Main application shell with routing
 */

import { useRoutes } from 'react-router-dom';
import { usePlugins } from '@core/plugin-system';
import Shell from './Shell';

// Create routes from plugins
function useAppRoutes() {
  const plugins = usePlugins();
  
  const routes = [
    {
      path: '/',
      element: <Shell />,
      children: [
        // Home route
        {
          index: true,
          element: <HomePage />,
        },
        // Plugin routes
        ...plugins.flatMap((plugin) => plugin.routes),
        // 404
        {
          path: '*',
          element: <NotFoundPage />,
        },
      ],
    },
  ];

  return useRoutes(routes);
}

function HomePage() {
  const plugins = usePlugins();

  return (
    <div className="p-6">
      <h1 className="text-2xl font-bold mb-2">Welcome to DevTools</h1>
      <p className="text-foreground-secondary mb-8">
        Select a tool from the sidebar to get started.
      </p>

      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        {plugins.map((plugin) => (
          <PluginCard key={plugin.id} plugin={plugin} />
        ))}
      </div>
    </div>
  );
}

function PluginCard({ plugin }: { plugin: { id: string; name: string; description: string; icon: React.ComponentType<{ className?: string }> } }) {
  const Icon = plugin.icon;

  return (
    <a
      href={`/${plugin.id}`}
      className="block p-4 bg-white dark:bg-neutral-800 rounded-lg border border-border hover:border-primary-500 transition-colors group"
    >
      <div className="flex items-center gap-3 mb-2">
        <div className="p-2 bg-primary-50 dark:bg-primary-900/20 rounded-lg text-primary-600 dark:text-primary-400 group-hover:bg-primary-100 dark:group-hover:bg-primary-900/30 transition-colors">
          <Icon className="w-5 h-5" />
        </div>
        <h3 className="font-semibold">{plugin.name}</h3>
      </div>
      <p className="text-sm text-foreground-secondary">{plugin.description}</p>
    </a>
  );
}

function NotFoundPage() {
  return (
    <div className="flex flex-col items-center justify-center h-full">
      <h1 className="text-4xl font-bold text-foreground-muted mb-2">404</h1>
      <p className="text-foreground-secondary">Page not found</p>
    </div>
  );
}

import { CommandPalette } from '@/components/CommandPalette';

export default function App() {
  const routes = useAppRoutes();
  
  return (
    <>
      <CommandPalette />
      {routes}
    </>
  );
}
