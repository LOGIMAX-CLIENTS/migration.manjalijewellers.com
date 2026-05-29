import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { 
  Search, 
  GitBranch, 
  Brain, 
  Keyboard, 
  LayoutDashboard, 
  FileCode
} from 'lucide-react';

export function Help() {
  return (
    <div className="container mx-auto p-6 max-w-4xl overflow-y-auto h-full">
      <div className="mb-8">
        <h1 className="text-3xl font-bold mb-2">LCA Plugin Documentation</h1>
        <p className="text-muted-foreground">
          Guide to analyzing legacy code, generating tests, and automating workflows.
        </p>
      </div>

      <Tabs defaultValue="started" className="space-y-6">
        <TabsList className="grid w-full grid-cols-4">
          <TabsTrigger value="started">Getting Started</TabsTrigger>
          <TabsTrigger value="search">Search & Analysis</TabsTrigger>
          <TabsTrigger value="ai">AI Features</TabsTrigger>
          <TabsTrigger value="shortcuts">Shortcuts</TabsTrigger>
        </TabsList>

        {/* Getting Started */}
        <TabsContent value="started" className="space-y-4">
            <Card>
                <CardHeader>
                    <CardTitle className="flex items-center gap-2">
                        <LayoutDashboard className="w-5 h-5 text-primary" />
                        Dashboard Overview
                    </CardTitle>
                    <CardDescription>
                        The central hub for your legacy code analysis.
                    </CardDescription>
                </CardHeader>
                <CardContent className="space-y-4">
                    <p>
                        The dashboard provides a high-level view of your codebase's health and indexing status.
                    </p>
                    <ul className="list-disc pl-5 space-y-2 text-muted-foreground">
                        <li><strong>Index Status:</strong> Shows which `master_index.json` is currently active.</li>
                        <li><strong>Stats cards:</strong> Quick metrics on total functions, call relationships, and indexed files.</li>
                        <li><strong>Recent Activity:</strong> Shows the latest AI tasks and analysis jobs.</li>
                    </ul>
                </CardContent>
            </Card>
        </TabsContent>

        {/* Search & Analysis */}
        <TabsContent value="search" className="space-y-4">
            <Card>
                <CardHeader>
                    <CardTitle className="flex items-center gap-2">
                        <Search className="w-5 h-5 text-primary" />
                        Function Search
                    </CardTitle>
                </CardHeader>
                <CardContent className="space-y-4">
                    <p>
                        Use the Search page (or `Ctrl+K`) to find any function in the codebase.
                    </p>
                    <div className="grid gap-4 md:grid-cols-2">
                        <div className="border p-4 rounded-md">
                            <h3 className="font-semibold mb-2 flex items-center gap-2">
                                <FileCode className="w-4 h-4" /> Code Preview
                            </h3>
                            <p className="text-sm text-muted-foreground">
                                View syntax-highlighted PHP code directly in the browser by selecting a function.
                            </p>
                        </div>
                        <div className="border p-4 rounded-md">
                            <h3 className="font-semibold mb-2 flex items-center gap-2">
                                <GitBranch className="w-4 h-4" /> Impact Analysis
                            </h3>
                            <p className="text-sm text-muted-foreground">
                                Visual graph showing <strong>Callers</strong> (upstream) and <strong>Callees</strong> (downstream) to assess risk before changing code.
                            </p>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </TabsContent>

        {/* AI Features */}
        <TabsContent value="ai" className="space-y-4">
             <Card>
                <CardHeader>
                    <CardTitle className="flex items-center gap-2">
                        <Brain className="w-5 h-5 text-primary" />
                         AI Capabilities
                    </CardTitle>
                </CardHeader>
                <CardContent className="space-y-4">
                    <div className="border border-l-4 border-l-primary p-4 rounded bg-muted/20">
                        <h3 className="font-bold">One-Click Test Generation</h3>
                        <p className="text-sm mt-1">
                            Go to a function's details and click <strong>"Generate Test"</strong>. 
                            LCA will parse the controller, mock all `load-{'>'}model` calls, and generate a PHPUnit test skeleton.
                        </p>
                    </div>
                    
                    <div className="border border-l-4 border-l-blue-500 p-4 rounded bg-muted/20">
                        <h3 className="font-bold">Automated Explanation</h3>
                        <p className="text-sm mt-1">
                            The "Explain" tab uses AI to summarize what a legacy function does, identifying business logic and side effects.
                        </p>
                    </div>
                </CardContent>
            </Card>
        </TabsContent>

        {/* Shortcuts */}
        <TabsContent value="shortcuts" className="space-y-4">
            <Card>
                <CardHeader>
                    <CardTitle className="flex items-center gap-2">
                        <Keyboard className="w-5 h-5 text-primary" />
                        Keyboard Shortcuts
                    </CardTitle>
                    <CardDescription>
                        Navigate faster with these global hotkeys.
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div className="space-y-2">
                            <h3 className="font-semibold text-sm text-muted-foreground uppercase tracking-wider">Navigation</h3>
                            <div className="flex justify-between items-center border-b py-2">
                                <span>Go to Search</span>
                                <code className="bg-muted px-2 py-1 rounded text-xs font-mono">g s</code>
                            </div>
                            <div className="flex justify-between items-center border-b py-2">
                                <span>Go to Dashboard</span>
                                <code className="bg-muted px-2 py-1 rounded text-xs font-mono">g d</code>
                            </div>
                            <div className="flex justify-between items-center border-b py-2">
                                <span>Go to Test Runner</span>
                                <code className="bg-muted px-2 py-1 rounded text-xs font-mono">g t</code>
                            </div>
                             <div className="flex justify-between items-center border-b py-2">
                                <span>Go to Logs</span>
                                <code className="bg-muted px-2 py-1 rounded text-xs font-mono">g l</code>
                            </div>
                        </div>
                        
                        <div className="space-y-2">
                            <h3 className="font-semibold text-sm text-muted-foreground uppercase tracking-wider">Global</h3>
                            <div className="flex justify-between items-center border-b py-2">
                                <span>Command Palette</span>
                                <code className="bg-muted px-2 py-1 rounded text-xs font-mono">Ctrl + K</code>
                            </div>
                             <div className="flex justify-between items-center border-b py-2">
                                <span>Toggle Sidebar</span>
                                <code className="bg-muted px-2 py-1 rounded text-xs font-mono">Top Icon</code>
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </TabsContent>
      </Tabs>
    </div>
  );
}
