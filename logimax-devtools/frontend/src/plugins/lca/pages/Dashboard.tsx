/**
 * LCA Plugin - Dashboard Page
 * Overview and quick actions using shadcn/ui components
 */

import { useState } from 'react';
import { useOutletContext } from 'react-router-dom';
import { BarChart3, GitBranch, Search, Target, FolderOpen, Trash2, RefreshCw, Loader2 } from 'lucide-react';
import { BarChart, Bar, XAxis, YAxis, Tooltip, ResponsiveContainer, CartesianGrid, PieChart, Pie, Cell } from 'recharts';
import { useLcaIndexes, useLcaStatus, useStartIndexing, useLcaIndexProgress } from '../hooks';

import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Badge } from '@/components/ui/badge';
import { Progress } from '@/components/ui/progress';

interface ContextType {
  density: 'comfortable' | 'compact';
}

export function Dashboard() {
  const [selectedIndex, setSelectedIndex] = useState<string>('');
  const [indexPath, setIndexPath] = useState('');
  const [indexName, setIndexName] = useState('');
  const [activeIndexingName, setActiveIndexingName] = useState<string | null>(null);
  
  const { density } = useOutletContext<ContextType>();
  const isCompact = density === 'compact';

  const { data: indexes = [], isLoading: loadingIndexes, refetch } = useLcaIndexes();
  const { data: status } = useLcaStatus(selectedIndex || indexes[0] || '');
  const { data: progress } = useLcaIndexProgress(activeIndexingName || '', !!activeIndexingName);
  const startIndexing = useStartIndexing();

  // Reset when complete
  if (progress?.is_complete && activeIndexingName) {
    // Small delay to show 100%
    setTimeout(() => {
        setActiveIndexingName(null);
        refetch(); // Refresh list
    }, 1000);
  }

  const handleStartIndex = () => {
    if (!indexPath || !indexName) return;
    setActiveIndexingName(indexName);
    startIndexing.mutate({ path: indexPath, name: indexName });
    setIndexPath('');
    setIndexName('');
  };

  // Mock data for charts
  const distributionData = [
      { name: 'Admin', value: 45 },
      { name: 'Core', value: 30 },
      { name: 'Plugins', value: 15 },
      { name: 'Tests', value: 10 },
  ];
  
  const complexityData = [
      { name: 'Low', value: 300, color: '#22c55e' },
      { name: 'Medium', value: 120, color: '#eab308' },
      { name: 'High', value: 45, color: '#ef4444' },
      { name: 'Critical', value: 12, color: '#7f1d1d' },
  ];

  return (
    <div className={`${isCompact ? 'p-2 space-y-2' : 'p-6 space-y-6'}`}>
      {/* Header */}
      {!isCompact && (
        <div className="flex items-center gap-3">
          <div className="p-3 bg-gradient-to-br from-primary/20 to-primary/5 rounded-xl border border-primary/20">
            <BarChart3 className="w-7 h-7 text-primary" />
          </div>
          <div>
            <h1 className="text-2xl font-bold tracking-tight">LCA Cockpit</h1>
            <p className="text-muted-foreground">Code analysis and impact visualization</p>
          </div>
        </div>
      )}

      {/* Stats Cards */}
      <div className={`grid ${isCompact ? 'grid-cols-3 gap-2' : 'grid-cols-1 md:grid-cols-3 gap-4'}`}>
        <Card className={`bg-gradient-to-br from-card to-muted/30 ${isCompact ? '' : ''}`}>
          <CardHeader className={`flex flex-row items-center justify-between ${isCompact ? 'p-3 pb-1' : 'pb-2'}`}>
            <CardTitle className="text-sm font-medium text-muted-foreground">Functions Indexed</CardTitle>
            <GitBranch className="h-4 w-4 text-muted-foreground" />
          </CardHeader>
          <CardContent className={isCompact ? 'p-3 pt-1' : ''}>
            <div className={`${isCompact ? 'text-xl' : 'text-3xl'} font-bold`}>{(status?.functions ?? 0).toLocaleString()}</div>
            {!isCompact && <p className="text-xs text-muted-foreground mt-1">Across all parsed files</p>}
          </CardContent>
        </Card>

        <Card className="bg-gradient-to-br from-card to-muted/30">
          <CardHeader className={`flex flex-row items-center justify-between ${isCompact ? 'p-3 pb-1' : 'pb-2'}`}>
            <CardTitle className="text-sm font-medium text-muted-foreground">Calls Mapped</CardTitle>
            <Target className="h-4 w-4 text-muted-foreground" />
          </CardHeader>
          <CardContent className={isCompact ? 'p-3 pt-1' : ''}>
            <div className={`${isCompact ? 'text-xl' : 'text-3xl'} font-bold`}>{(status?.calls ?? 0).toLocaleString()}</div>
            {!isCompact && <p className="text-xs text-muted-foreground mt-1">Function dependencies tracked</p>}
          </CardContent>
        </Card>

        <Card className="bg-gradient-to-br from-card to-muted/30">
          <CardHeader className={`flex flex-row items-center justify-between ${isCompact ? 'p-3 pb-1' : 'pb-2'}`}>
            <CardTitle className="text-sm font-medium text-muted-foreground">Active Index</CardTitle>
            <Search className="h-4 w-4 text-muted-foreground" />
          </CardHeader>
          <CardContent className={isCompact ? 'p-3 pt-1' : ''}>
            <div className={`${isCompact ? 'text-lg' : 'text-xl'} font-bold truncate`}>{status?.name ?? 'None'}</div>
            {!isCompact && <p className="text-xs text-muted-foreground mt-1 truncate">{status?.path ?? 'Create an index to get started'}</p>}
          </CardContent>
        </Card>
      </div>

      {/* Analytics Charts */}
      <div className={`grid ${isCompact ? 'grid-cols-2 gap-2' : 'grid-cols-1 md:grid-cols-2 gap-4'}`}>
        <Card>
          <CardHeader className={isCompact ? 'p-3 pb-2' : ''}>
            <CardTitle className="text-base">Function Distribution</CardTitle>
            {!isCompact && <CardDescription>Functions per module/directory</CardDescription>}
          </CardHeader>
          <CardContent className={isCompact ? 'p-3 pt-0' : ''}>
             <div className="h-[200px] w-full">
                <ResponsiveContainer width="100%" height="100%">
                    <BarChart data={distributionData}>
                        <CartesianGrid strokeDasharray="3 3" opacity={0.3} />
                        <XAxis dataKey="name" fontSize={12} tickLine={false} axisLine={false} />
                        <YAxis fontSize={12} tickLine={false} axisLine={false} />
                        <Tooltip 
                            contentStyle={{ borderRadius: '8px', border: 'none', boxShadow: '0 4px 12px rgba(0,0,0,0.1)' }}
                            cursor={{ fill: 'transparent' }}
                        />
                        <Bar dataKey="value" fill="#3b82f6" radius={[4, 4, 0, 0]} barSize={20} />
                    </BarChart>
                </ResponsiveContainer>
             </div>
          </CardContent>
        </Card>
        
        <Card>
          <CardHeader className={isCompact ? 'p-3 pb-2' : ''}>
             <CardTitle className="text-base">Complexity Risks</CardTitle>
             {!isCompact && <CardDescription>Files by risk level</CardDescription>}
          </CardHeader>
          <CardContent className={isCompact ? 'p-3 pt-0' : ''}>
             <div className="h-[200px] w-full">
                <ResponsiveContainer width="100%" height="100%">
                    <PieChart>
                        <Pie
                            data={complexityData}
                            cx="50%"
                            cy="50%"
                            innerRadius={60}
                            outerRadius={80}
                            paddingAngle={5}
                            dataKey="value"
                        >
                            {complexityData.map((entry, index) => (
                                <Cell key={`cell-${index}`} fill={entry.color} />
                            ))}
                        </Pie>
                        <Tooltip />
                    </PieChart>
                </ResponsiveContainer>
             </div>
          </CardContent>
        </Card>
      </div>

      {/* Index Management */}
      <div className={`grid ${isCompact ? 'grid-cols-2 gap-2' : 'grid-cols-1 lg:grid-cols-2 gap-6'}`}>
        {/* New Index */}
        <Card>
          <CardHeader className={isCompact ? 'p-3 pb-2' : ''}>
            <CardTitle className="flex items-center gap-2 text-base">
              <FolderOpen className={isCompact ? 'w-4 h-4' : 'w-5 h-5'} />
              Index New Codebase
            </CardTitle>
            {!isCompact && (
              <CardDescription>
                Parse PHP, JavaScript, and Python files to build a call graph
              </CardDescription>
            )}
          </CardHeader>
          <CardContent className={`space-y-4 ${isCompact ? 'p-3 pt-0' : ''}`}>
            {!activeIndexingName ? (
                <>
                    <div className="space-y-2">
                      <label className="text-sm font-medium">Path</label>
                      <Input
                        value={indexPath}
                        onChange={(e) => setIndexPath(e.target.value)}
                        placeholder="c:\xampp\htdocs\etail_v3\admin"
                        className={isCompact ? 'h-8 text-xs' : ''}
                      />
                    </div>
                    <div className="space-y-2">
                      <label className="text-sm font-medium">Index Name</label>
                      <Input
                        value={indexName}
                        onChange={(e) => setIndexName(e.target.value)}
                        placeholder="etail-admin"
                        className={isCompact ? 'h-8 text-xs' : ''}
                      />
                    </div>
                    <Button
                      onClick={handleStartIndex}
                      disabled={!indexPath || !indexName || startIndexing.isPending}
                      className={`w-full ${isCompact ? 'h-8' : ''}`}
                    >
                      {startIndexing.isPending ? (
                        <>
                          <Loader2 className="w-4 h-4 mr-2 animate-spin" />
                          Starting...
                        </>
                      ) : (
                        <>
                          <FolderOpen className="w-4 h-4 mr-2" />
                          Start Indexing
                        </>
                      )}
                    </Button>
                </>
            ) : (
                <div className="space-y-4 py-2">
                    <div className="flex items-center justify-between text-sm">
                        <span className="font-medium">Indexing {activeIndexingName}...</span>
                        <span className="text-muted-foreground">
                            {progress ? Math.round(progress.progress) : 0}%
                        </span>
                    </div>
                    <Progress value={progress?.progress || 0} className="h-2" />
                    <div className="text-xs text-muted-foreground truncate">
                        {progress?.current_file ? `Parsing: ${progress.current_file.split('\\').pop()}` : 'Initializing...'}
                    </div>
                    <div className="text-xs text-muted-foreground flex justify-between">
                         <span>{progress?.processed_files || 0} / {progress?.total_files || '?'} files</span>
                         {progress?.errors?.length ? <span className="text-destructive">{progress.errors.length} errors</span> : null}
                    </div>
                </div>
            )}
          </CardContent>
        </Card>

        {/* Existing Indexes */}
        <Card>
          <CardHeader className={isCompact ? 'p-3 pb-2' : ''}>
            <div className="flex items-center justify-between">
              <div>
                <CardTitle className="text-base">Available Indexes</CardTitle>
                {!isCompact && <CardDescription>Select an index to view or delete</CardDescription>}
              </div>
              <Button variant="ghost" size="icon" onClick={() => refetch()} className={isCompact ? 'h-6 w-6' : ''}>
                <RefreshCw className="w-4 h-4" />
              </Button>
            </div>
          </CardHeader>
          <CardContent className={isCompact ? 'p-3 pt-0' : ''}>
            {loadingIndexes ? (
              <div className="flex items-center justify-center py-8 text-muted-foreground">
                <Loader2 className="w-5 h-5 animate-spin mr-2" />
                Loading...
              </div>
            ) : indexes.length === 0 ? (
              <div className="flex flex-col items-center justify-center py-8 text-muted-foreground">
                <FolderOpen className="w-10 h-10 mb-3 opacity-40" />
                <p>No indexes yet</p>
                <p className="text-sm">Create one to get started</p>
              </div>
            ) : (
              <ul className="space-y-2">
                {indexes.map((name) => (
                  <li key={name} className={`flex items-center justify-between rounded-lg border bg-muted/30 hover:bg-muted/50 transition-colors ${isCompact ? 'p-2' : 'p-3'}`}>
                    <button 
                      onClick={() => setSelectedIndex(name)}
                      className="flex items-center gap-2 font-medium"
                    >
                      <Badge variant={selectedIndex === name ? "default" : "secondary"}>
                        {name}
                      </Badge>
                    </button>
                    <Button variant="ghost" size="icon" className={`text-destructive hover:bg-destructive/10 ${isCompact ? 'h-6 w-6' : 'h-8 w-8'}`}>
                      <Trash2 className="w-4 h-4" />
                    </Button>
                  </li>
                ))}
              </ul>
            )}
          </CardContent>
        </Card>
      </div>
    </div>
  );
}
