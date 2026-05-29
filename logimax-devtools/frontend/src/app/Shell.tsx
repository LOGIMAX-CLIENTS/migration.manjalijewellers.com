import { Outlet, NavLink } from 'react-router-dom';
import { usePlugins } from '@core/plugin-system';
import { cn } from '@/lib/utils';
// import { ThemeToggle } from '@/components/ThemeToggle';
import { LayoutDashboard, Settings, ChevronLeft, ChevronRight } from 'lucide-react';
import { useState } from 'react';
import { Button } from '@/components/ui/button';
// import { ResizablePanelGroup, ResizablePanel, ResizableHandle } from '@/components/ui/resizable';
import { useGlobalShortcuts } from '@/hooks/useGlobalShortcuts';

export default function Shell() {
  const plugins = usePlugins();
  // const location = useLocation();
  const [collapsed, setCollapsed] = useState(false);
  
  // Enable Global Keyboard Shortcuts
  useGlobalShortcuts();

  // Group plugins by category (just a simple mock grouping for now)
  const mainPlugins = plugins.filter(p => !p.id.includes('settings'));
  // const settingsPlugins = plugins.filter(p => p.id.includes('settings'));

  return (
    <div className="flex h-screen bg-background overflow-hidden">
      {/* Sidebar */}
      <div 
        className={cn(
          "flex flex-col border-r bg-muted/10 transition-all duration-300 ease-in-out relative",
          collapsed ? "w-[60px]" : "w-[240px]"
        )}
      >
        {/* Header */}
        <div className="h-14 flex items-center px-4 border-b">
          <div className="flex items-center gap-2 font-bold text-lg text-primary overflow-hidden whitespace-nowrap">
            <LayoutDashboard className="w-5 h-5 shrink-0" />
            <span className={cn("transition-opacity duration-300", collapsed ? "opacity-0 w-0" : "opacity-100")}>
              DevTools
            </span>
          </div>
        </div>

        {/* Toggle Button */}
        <Button 
          variant="ghost" 
          size="icon" 
          className="absolute -right-3 top-16 h-6 w-6 rounded-full border bg-background shadow-md z-10"
          onClick={() => setCollapsed(!collapsed)}
        >
            {collapsed ? <ChevronRight className="w-3 h-3" /> : <ChevronLeft className="w-3 h-3" />}
        </Button>

        {/* Navigation */}
        <nav className="flex-1 overflow-y-auto py-4 flex flex-col gap-1 px-2">
            {!collapsed && <div className="text-xs font-medium text-muted-foreground px-2 mb-2">TOOLS</div>}
            
            {mainPlugins.map((plugin) => (
                <NavLink
                key={plugin.id}
                to={`/${plugin.id}`}
                className={({ isActive }) =>
                    cn(
                    "flex items-center gap-3 px-3 py-2 rounded-md transition-colors text-sm",
                    isActive 
                        ? "bg-primary text-primary-foreground font-medium" 
                        : "text-muted-foreground hover:bg-muted hover:text-foreground"
                    )
                }
                title={collapsed ? plugin.name : undefined}
                >
                <plugin.icon className="w-4 h-4 shrink-0" />
                <span className={cn("truncate transition-all duration-300", collapsed ? "opacity-0 w-0" : "opacity-100")}>{plugin.name}</span>
                </NavLink>
            ))}

            <div className="mt-auto"></div>
            {/* Settings or Bottom Links */}
            {!collapsed && <div className="text-xs font-medium text-muted-foreground px-2 mb-2 mt-4">SYSTEM</div>}
             <NavLink
                to="/settings"
                className={({ isActive }) =>
                    cn(
                    "flex items-center gap-3 px-3 py-2 rounded-md transition-colors text-sm",
                    isActive 
                        ? "bg-primary text-primary-foreground font-medium" 
                        : "text-muted-foreground hover:bg-muted hover:text-foreground"
                    )
                }
                title={collapsed ? "Settings" : undefined}
                >
                <Settings className="w-4 h-4 shrink-0" />
                <span className={cn("truncate transition-all duration-300", collapsed ? "opacity-0 w-0" : "opacity-100")}>Settings</span>
            </NavLink>
        </nav>

        {/* Footer */}
        <div className="p-4 border-t flex justify-center">
            {/* <ThemeToggle /> */}
        </div>
      </div>

      {/* Main Content */}
      <div className="flex-1 flex flex-col min-w-0 overflow-hidden">
        <Outlet />
      </div>
    </div>
  );
}
