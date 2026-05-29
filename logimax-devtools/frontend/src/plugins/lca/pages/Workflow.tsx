/**
 * LCA Plugin - Workflow Page
 * Change Request Management with Persistence
 */

import { useState, useEffect } from 'react';
import { useOutletContext, useSearchParams } from 'react-router-dom';
import { 
  GitBranch, Search, AlertTriangle, CheckCircle2, 
  Clock, Play, FileText, ChevronRight, Loader2,
  ArrowRight, Target, Zap, List, Plus, Trash2, RefreshCw
} from 'lucide-react';

import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import { Badge } from '@/components/ui/badge';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { useLcaIndexes, useLcaSearch } from '../hooks';

interface ContextType {
  density: 'comfortable' | 'compact';
}

interface Task {
  id: string;
  title: string;
  status: string;
  target_function?: string;
  intent?: string;
  index_name: string;
  created_at?: string;
  impact_report?: {
    direct_callers: string[];
    indirect_callers: string[];
    affected_files: string[];
    risk_level: string;
  };
  ai_plan?: string;
  subtasks?: { id: string; title: string; status: string; order: number }[];
}

const API_BASE = 'http://localhost:8800/api/lca';

const STATUS_LABELS: Record<string, { label: string; color: string }> = {
  created: { label: 'Created', color: 'bg-gray-500' },
  analyzing: { label: 'Analyzing', color: 'bg-blue-500' },
  impact_done: { label: 'Impact Done', color: 'bg-yellow-500' },
  pending_approval: { label: 'Pending Approval', color: 'bg-orange-500' },
  approved: { label: 'Approved', color: 'bg-purple-500' },
  in_progress: { label: 'In Progress', color: 'bg-indigo-500' },
  completed: { label: 'Completed', color: 'bg-green-500' },
};

export function Workflow() {
  const { density } = useOutletContext<ContextType>();
  const isCompact = density === 'compact';
  
  const [searchParams] = useSearchParams();
  const initialTab = searchParams.get('tab') || 'create';

  const [viewMode, setViewMode] = useState<'list' | 'detail'>('list');
  const [activeTab, setActiveTab] = useState(initialTab);
  const [targetFunction, setTargetFunction] = useState('');
  const [intent, setIntent] = useState('');
  const [searchQuery, setSearchQuery] = useState('');
  const [currentTask, setCurrentTask] = useState<Task | null>(null);
  const [tasks, setTasks] = useState<Task[]>([]);
  const [isLoading, setIsLoading] = useState(false);
  const [error, setError] = useState<string | null>(null);
  
  const { data: indexes = [] } = useLcaIndexes();
  const activeIndex = indexes[0] || '';
  const { data: searchResults } = useLcaSearch(activeIndex, searchQuery, 10);

  // Load tasks on mount
  useEffect(() => {
    loadTasks();
    
    // Handle deep linking to tabs
    const tabParam = searchParams.get('tab');
    if (tabParam) {
      // If jumping to create (Analyze), ensure we are in detail mode for new task
      if (tabParam === 'create' || tabParam === 'analyze') {
        setViewMode('detail');
        setCurrentTask(null);
        setActiveTab('create');
      } else {
        // For plan/execute, we ideally want to see a relevant task, 
        // but without an ID we just stay in list view or default state
        if (activeTab !== tabParam) {
             setActiveTab(tabParam);
        }
      }
    }
  }, [searchParams]);

  const loadTasks = async () => {
    try {
      const res = await fetch(`${API_BASE}/workflow`);
      if (res.ok) {
        const data = await res.json();
        setTasks(data);
      }
    } catch (e) {
      console.error('Failed to load tasks:', e);
    }
  };

  const loadTask = async (taskId: string) => {
    try {
      const res = await fetch(`${API_BASE}/workflow/${taskId}`);
      if (res.ok) {
        const data = await res.json();
        setCurrentTask(data);
        setViewMode('detail');
        
        // Set correct tab based on status
        if (data.subtasks?.length) setActiveTab('execute');
        else if (data.ai_plan) setActiveTab('plan');
        else if (data.impact_report) setActiveTab('plan');
        else if (data.status !== 'created') setActiveTab('analyze');
        else setActiveTab('create');
      }
    } catch (e) {
      setError((e as Error).message);
    }
  };

  const createTask = async () => {
    if (!targetFunction || !activeIndex) return;
    
    setIsLoading(true);
    setError(null);
    
    try {
      const res = await fetch(
        `${API_BASE}/workflow/create?index_name=${activeIndex}&target_function=${encodeURIComponent(targetFunction)}&intent=${encodeURIComponent(intent)}`,
        { method: 'POST' }
      );
      const data = await res.json();
      setCurrentTask(data);
      setTasks(prev => [data, ...prev]);
      setActiveTab('analyze');
      setTargetFunction('');
      setIntent('');
      setSearchQuery('');
    } catch (e) {
      setError((e as Error).message);
    } finally {
      setIsLoading(false);
    }
  };

  const analyzeImpact = async () => {
    if (!currentTask) return;
    
    setIsLoading(true);
    setError(null);
    
    try {
      const res = await fetch(`${API_BASE}/workflow/${currentTask.id}/analyze`, { method: 'POST' });
      const data = await res.json();
      setCurrentTask(prev => prev ? { ...prev, impact_report: data, status: 'impact_done' } : null);
      await loadTasks(); // Refresh list
      setActiveTab('plan');
    } catch (e) {
      setError((e as Error).message);
    } finally {
      setIsLoading(false);
    }
  };

  const generatePlan = async () => {
    if (!currentTask) return;
    
    setIsLoading(true);
    setError(null);
    
    try {
      const res = await fetch(`${API_BASE}/workflow/${currentTask.id}/plan`, { method: 'POST' });
      const data = await res.json();
      setCurrentTask(prev => prev ? { ...prev, ai_plan: data.plan, status: 'pending_approval' } : null);
      await loadTasks();
    } catch (e) {
      setError((e as Error).message);
    } finally {
      setIsLoading(false);
    }
  };

  const approvePlan = async () => {
    if (!currentTask) return;
    
    setIsLoading(true);
    setError(null);
    
    try {
      const res = await fetch(`${API_BASE}/workflow/${currentTask.id}/approve`, { method: 'POST' });
      const data = await res.json();
      setCurrentTask(data);
      await loadTasks();
      setActiveTab('execute');
    } catch (e) {
      setError((e as Error).message);
    } finally {
      setIsLoading(false);
    }
  };

  const deleteTask = async (taskId: string) => {
    try {
      await fetch(`${API_BASE}/workflow/${taskId}`, { method: 'DELETE' });
      setTasks(prev => prev.filter(t => t.id !== taskId));
      if (currentTask?.id === taskId) {
        setCurrentTask(null);
        setViewMode('list');
      }
    } catch (e) {
      setError((e as Error).message);
    }
  };

  const getRiskColor = (level: string) => {
    switch (level) {
      case 'critical': return 'bg-red-500/20 text-red-400 border-red-500/30';
      case 'high': return 'bg-orange-500/20 text-orange-400 border-orange-500/30';
      case 'medium': return 'bg-yellow-500/20 text-yellow-400 border-yellow-500/30';
      default: return 'bg-green-500/20 text-green-400 border-green-500/30';
    }
  };

  // Task List View
  if (viewMode === 'list') {
    return (
      <div className={`${isCompact ? 'p-2 space-y-2' : 'p-6 space-y-6'}`}>
        {/* Header */}
        {!isCompact && (
          <div className="flex items-center justify-between">
            <div className="flex items-center gap-3">
              <div className="p-3 bg-gradient-to-br from-purple-500/20 to-purple-500/5 rounded-xl border border-purple-500/20">
                <Zap className="w-7 h-7 text-purple-400" />
              </div>
              <div>
                <h1 className="text-2xl font-bold tracking-tight">Workflow Tasks</h1>
                <p className="text-muted-foreground">{tasks.length} task{tasks.length !== 1 ? 's' : ''} • Persistent storage enabled</p>
              </div>
            </div>
            <div className="flex gap-2">
              <Button variant="outline" size="sm" onClick={loadTasks}>
                <RefreshCw className="w-4 h-4 mr-2" />
                Refresh
              </Button>
              <Button onClick={() => { setViewMode('detail'); setCurrentTask(null); setActiveTab('create'); }}>
                <Plus className="w-4 h-4 mr-2" />
                New Task
              </Button>
            </div>
          </div>
        )}

        {error && (
          <div className="p-3 bg-destructive/10 border border-destructive/20 rounded-lg text-destructive text-sm">
            {error}
          </div>
        )}

        {/* Task List */}
        {tasks.length === 0 ? (
          <Card>
            <CardContent className="flex flex-col items-center justify-center py-16 text-center">
              <List className="w-12 h-12 text-muted-foreground/40 mb-4" />
              <h3 className="text-lg font-medium mb-2">No Tasks Yet</h3>
              <p className="text-muted-foreground mb-4">Create your first workflow task to get started</p>
              <Button onClick={() => { setViewMode('detail'); setCurrentTask(null); setActiveTab('create'); }}>
                <Plus className="w-4 h-4 mr-2" />
                Create Task
              </Button>
            </CardContent>
          </Card>
        ) : (
          <div className="space-y-2">
            {tasks.map(task => (
              <Card 
                key={task.id} 
                className="cursor-pointer hover:border-primary/50 transition-colors"
                onClick={() => loadTask(task.id)}
              >
                <CardContent className="p-4 flex items-center justify-between">
                  <div className="flex items-center gap-4">
                    <div className={`w-3 h-3 rounded-full ${STATUS_LABELS[task.status]?.color || 'bg-gray-500'}`} />
                    <div>
                      <div className="font-medium">{task.title}</div>
                      <div className="text-sm text-muted-foreground flex items-center gap-2">
                        <code className="text-xs">{task.target_function}</code>
                        {task.created_at && (
                          <span>• {new Date(task.created_at).toLocaleDateString()}</span>
                        )}
                      </div>
                    </div>
                  </div>
                  <div className="flex items-center gap-2">
                    <Badge variant="outline">
                      {STATUS_LABELS[task.status]?.label || task.status}
                    </Badge>
                    <Button
                      variant="ghost"
                      size="icon"
                      onClick={(e) => { e.stopPropagation(); deleteTask(task.id); }}
                    >
                      <Trash2 className="w-4 h-4 text-muted-foreground hover:text-destructive" />
                    </Button>
                  </div>
                </CardContent>
              </Card>
            ))}
          </div>
        )}
      </div>
    );
  }

  // Detail View (Create/Edit Task)
  return (
    <div className={`${isCompact ? 'p-2 space-y-2' : 'p-6 space-y-6'}`}>
      {/* Header */}
      {!isCompact && (
        <div className="flex items-center gap-3">
          <Button variant="ghost" size="sm" onClick={() => setViewMode('list')}>
            ← Back to List
          </Button>
          <div className="flex-1" />
          {currentTask && (
            <Badge variant="outline">
              {STATUS_LABELS[currentTask.status]?.label || currentTask.status}
            </Badge>
          )}
        </div>
      )}

      {error && (
        <div className="p-3 bg-destructive/10 border border-destructive/20 rounded-lg text-destructive text-sm">
          {error}
        </div>
      )}

      {/* Workflow Progress */}
      <div className="flex items-center gap-2 text-sm">
        <Badge variant={activeTab === 'create' ? 'default' : 'secondary'}>1. Select Target</Badge>
        <ChevronRight className="w-4 h-4 text-muted-foreground" />
        <Badge variant={activeTab === 'analyze' ? 'default' : 'secondary'}>2. Analyze Impact</Badge>
        <ChevronRight className="w-4 h-4 text-muted-foreground" />
        <Badge variant={activeTab === 'plan' ? 'default' : 'secondary'}>3. Review Plan</Badge>
        <ChevronRight className="w-4 h-4 text-muted-foreground" />
        <Badge variant={activeTab === 'execute' ? 'default' : 'secondary'}>4. Execute</Badge>
      </div>

      <Tabs value={activeTab} onValueChange={setActiveTab} className="space-y-4">
        <TabsList className="grid w-full grid-cols-4">
          <TabsTrigger value="create">Create</TabsTrigger>
          <TabsTrigger value="analyze" disabled={!currentTask}>Analyze</TabsTrigger>
          <TabsTrigger value="plan" disabled={!currentTask?.impact_report}>Plan</TabsTrigger>
          <TabsTrigger value="execute" disabled={!currentTask?.subtasks}>Execute</TabsTrigger>
        </TabsList>

        {/* Tab 1: Create Task */}
        <TabsContent value="create">
          <Card>
            <CardHeader>
              <CardTitle className="flex items-center gap-2">
                <Target className="w-5 h-5" />
                New Workflow Task
              </CardTitle>
              <CardDescription>
                Select the function you want to modify and describe your intent
              </CardDescription>
            </CardHeader>
            <CardContent className="space-y-4">
              <div className="space-y-2">
                <label className="text-sm font-medium">Search Function</label>
                <Input
                  value={searchQuery}
                  onChange={(e) => setSearchQuery(e.target.value)}
                  placeholder="Search for a function..."
                />
                {searchResults && searchResults.results.length > 0 && searchQuery.length >= 2 && (
                  <div className="border rounded-lg max-h-40 overflow-auto">
                    {searchResults.results.map((fn) => (
                      <button
                        key={fn.qualified_name}
                        onClick={() => {
                          setTargetFunction(fn.qualified_name);
                          setSearchQuery('');
                        }}
                        className="w-full text-left px-3 py-2 hover:bg-muted/50 text-sm border-b last:border-b-0"
                      >
                        <span className="font-mono">{fn.name}</span>
                        <span className="text-muted-foreground ml-2">{fn.file}</span>
                      </button>
                    ))}
                  </div>
                )}
              </div>

              {targetFunction && (
                <div className="p-3 bg-muted/30 rounded-lg flex items-center gap-2">
                  <CheckCircle2 className="w-4 h-4 text-green-500" />
                  <span className="font-mono text-sm">{targetFunction}</span>
                </div>
              )}

              <div className="space-y-2">
                <label className="text-sm font-medium">Intent (What do you want to change?)</label>
                <Textarea
                  value={intent}
                  onChange={(e) => setIntent(e.target.value)}
                  placeholder="e.g., Add VAT calculation support"
                  rows={3}
                />
              </div>

              <Button 
                onClick={createTask} 
                disabled={!targetFunction || !intent || isLoading}
                className="w-full"
              >
                {isLoading ? <Loader2 className="w-4 h-4 mr-2 animate-spin" /> : <ArrowRight className="w-4 h-4 mr-2" />}
                Create Task
              </Button>
            </CardContent>
          </Card>
        </TabsContent>

        {/* Tab 2: Analyze Impact */}
        <TabsContent value="analyze">
          <Card>
            <CardHeader>
              <CardTitle className="flex items-center gap-2">
                <GitBranch className="w-5 h-5" />
                Impact Analysis
              </CardTitle>
              <CardDescription>
                Analyze the blast radius of your change
              </CardDescription>
            </CardHeader>
            <CardContent className="space-y-4">
              {currentTask && (
                <div className="p-4 bg-muted/30 rounded-lg space-y-2">
                  <div className="font-medium">{currentTask.title}</div>
                  <div className="text-sm text-muted-foreground">Target: <code>{currentTask.target_function}</code></div>
                </div>
              )}

              {currentTask?.impact_report ? (
                <div className="space-y-4">
                  <div className="grid grid-cols-3 gap-3">
                    <div className="p-3 bg-muted/20 rounded-lg text-center">
                      <div className="text-2xl font-bold">{currentTask.impact_report.direct_callers.length}</div>
                      <div className="text-xs text-muted-foreground">Direct Callers</div>
                    </div>
                    <div className="p-3 bg-muted/20 rounded-lg text-center">
                      <div className="text-2xl font-bold">{currentTask.impact_report.indirect_callers.length}</div>
                      <div className="text-xs text-muted-foreground">Indirect Callers</div>
                    </div>
                    <div className="p-3 bg-muted/20 rounded-lg text-center">
                      <div className="text-2xl font-bold">{currentTask.impact_report.affected_files.length}</div>
                      <div className="text-xs text-muted-foreground">Affected Files</div>
                    </div>
                  </div>

                  <div className={`inline-flex items-center gap-2 px-3 py-1 rounded-full border ${getRiskColor(currentTask.impact_report.risk_level)}`}>
                    <AlertTriangle className="w-4 h-4" />
                    Risk Level: {currentTask.impact_report.risk_level.toUpperCase()}
                  </div>

                  <Button onClick={() => setActiveTab('plan')} className="w-full">
                    <ArrowRight className="w-4 h-4 mr-2" />
                    Continue to Planning
                  </Button>
                </div>
              ) : (
                <Button onClick={analyzeImpact} disabled={isLoading} className="w-full">
                  {isLoading ? <Loader2 className="w-4 h-4 mr-2 animate-spin" /> : <Search className="w-4 h-4 mr-2" />}
                  Run Impact Analysis
                </Button>
              )}
            </CardContent>
          </Card>
        </TabsContent>

        {/* Tab 3: Plan */}
        <TabsContent value="plan">
          <Card>
            <CardHeader>
              <CardTitle className="flex items-center gap-2">
                <FileText className="w-5 h-5" />
                AI Implementation Plan
              </CardTitle>
              <CardDescription>
                Review the AI-generated refactoring steps
              </CardDescription>
            </CardHeader>
            <CardContent className="space-y-4">
              {currentTask?.ai_plan ? (
                <>
                  <div className="p-4 bg-muted/20 rounded-lg whitespace-pre-wrap text-sm font-mono max-h-[400px] overflow-auto">
                    {currentTask.ai_plan}
                  </div>
                  <div className="flex gap-2">
                    <Button onClick={generatePlan} variant="outline" disabled={isLoading}>
                      Regenerate Plan
                    </Button>
                    <Button onClick={approvePlan} disabled={isLoading} className="flex-1">
                      {isLoading ? <Loader2 className="w-4 h-4 mr-2 animate-spin" /> : <CheckCircle2 className="w-4 h-4 mr-2" />}
                      Approve & Start Execution
                    </Button>
                  </div>
                </>
              ) : (
                <Button onClick={generatePlan} disabled={isLoading} className="w-full">
                  {isLoading ? <Loader2 className="w-4 h-4 mr-2 animate-spin" /> : <Zap className="w-4 h-4 mr-2" />}
                  Generate AI Plan
                </Button>
              )}
            </CardContent>
          </Card>
        </TabsContent>

        {/* Tab 4: Execute */}
        <TabsContent value="execute">
          <Card>
            <CardHeader>
              <CardTitle className="flex items-center gap-2">
                <Play className="w-5 h-5" />
                Execution Tracker
              </CardTitle>
              <CardDescription>
                Track your progress through the implementation
              </CardDescription>
            </CardHeader>
            <CardContent>
              {currentTask?.subtasks && currentTask.subtasks.length > 0 ? (
                <ul className="space-y-2">
                  {currentTask.subtasks.map((subtask) => (
                    <li 
                      key={subtask.id} 
                      className="flex items-center gap-3 p-3 bg-muted/20 rounded-lg"
                    >
                      <div className={`w-6 h-6 rounded-full flex items-center justify-center ${
                        subtask.status === 'completed' ? 'bg-green-500' : 'bg-muted'
                      }`}>
                        {subtask.status === 'completed' ? (
                          <CheckCircle2 className="w-4 h-4 text-white" />
                        ) : (
                          <span className="text-xs">{subtask.order}</span>
                        )}
                      </div>
                      <span className={subtask.status === 'completed' ? 'line-through text-muted-foreground' : ''}>
                        {subtask.title}
                      </span>
                    </li>
                  ))}
                </ul>
              ) : (
                <div className="text-center py-8 text-muted-foreground">
                  <Clock className="w-10 h-10 mx-auto mb-3 opacity-40" />
                  <p>No subtasks yet. Approve a plan to start execution tracking.</p>
                </div>
              )}
            </CardContent>
          </Card>
        </TabsContent>
      </Tabs>
    </div>
  );
}
