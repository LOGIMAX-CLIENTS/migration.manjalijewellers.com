/**
 * LCA Plugin - Search Page
 * Function search with results using shadcn/ui
 */

import { useState, useEffect } from 'react';
import { useSearchParams } from 'react-router-dom';
import { Search as SearchIcon, FileCode, ArrowRight, ChevronRight, GitBranch, Target, ExternalLink, X, Info } from 'lucide-react';
import { useLcaIndexes, useLcaSearch, useLcaFunctionDetails, useLcaExplanation, useGenerateTest } from '../hooks';
import { ImpactView } from './ImpactView';
import { CodePreview } from '../components/CodePreview';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogDescription } from '@/components/ui/dialog';
import { Copy, Check, LayoutTemplate } from 'lucide-react';
import { useLcaSettings } from '../store';

import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { Input } from '@/components/ui/input';
// import { ScrollArea } from '@/components/ui/scroll-area';
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "@/components/ui/select";
import { ResizablePanelGroup, ResizablePanel, ResizableHandle } from '@/components/ui/resizable';
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table';

export function Search() {
  const [searchParams, setSearchParams] = useSearchParams();
  const [query, setQuery] = useState('');
  
  // Persistent Settings
  const { activeIndex, setActiveIndex, density, setDensity } = useLcaSettings();
  const isCompact = density === 'compact';

  // Sync URL param 'fn' with state
  const paramFn = searchParams.get('fn');
  const [selectedFn, setSelectedFn] = useState<string | null>(paramFn);

  useEffect(() => {
    if (paramFn !== selectedFn) {
        setSelectedFn(paramFn);
    }
  }, [paramFn]);

  // Update URL when selection changes
  const handleSelectFn = (fn: string | null) => {
    setSelectedFn(fn);
    if (fn) {
        setSearchParams({ fn });
    } else {
        setSearchParams({});
    }
  };

  const { data: indexes = [] } = useLcaIndexes();
  const effectiveIndex = activeIndex || indexes[0] || '';
  
  // Auto-select first index if none selected
  useEffect(() => {
    if (!activeIndex && indexes.length > 0) {
        setActiveIndex(indexes[0]);
    }
  }, [indexes, activeIndex, setActiveIndex]);
  
  const { data: results, isLoading } = useLcaSearch(effectiveIndex, query);
  const { data: details } = useLcaFunctionDetails(effectiveIndex, selectedFn || '');

  // Test Gen State
  const [testDialogOpen, setTestDialogOpen] = useState(false);
  const [generatedCode, setGeneratedCode] = useState('');
  const [copied, setCopied] = useState(false);
  const generateTest = useGenerateTest();

  const handleGenerateTest = () => {
    if (!details?.file) return;
    generateTest.mutate({ file_path: details.file }, {
        onSuccess: (data) => {
            setGeneratedCode(data.code);
            setTestDialogOpen(true);
        }
    });
  };

  const copyToClipboard = () => {
    navigator.clipboard.writeText(generatedCode);
    setCopied(true);
    setTimeout(() => setCopied(false), 2000);
  };


  return (
    <>
    <ResizablePanelGroup direction="horizontal" className="h-full items-stretch">
      {/* Search Panel (Sidebar) */}
      <ResizablePanel defaultSize={25} minSize={20} maxSize={40} className="flex flex-col border-r">
        {/* Search Header */}
        <div className={`${isCompact ? 'p-2' : 'p-4'} border-b bg-muted/30`}>
          <div className="flex gap-3">
            <Select value={effectiveIndex} onValueChange={setActiveIndex}>
              <SelectTrigger className={`w-[180px] ${isCompact ? 'h-8 text-xs' : ''}`}>
                <SelectValue placeholder="Select index" />
              </SelectTrigger>
              <SelectContent>
                {indexes.map((name) => (
                  <SelectItem key={name} value={name} className={isCompact ? 'text-xs' : ''}>{name}</SelectItem>
                ))}
              </SelectContent>
            </Select>
            <div className="flex-1 relative">
              <SearchIcon className={`absolute left-3 top-1/2 -translate-y-1/2 text-muted-foreground ${isCompact ? 'w-3 h-3' : 'w-4 h-4'}`} />
              <Input
                value={query}
                onChange={(e) => setQuery(e.target.value)}
                placeholder="Search functions..."
                className={`pl-9 ${isCompact ? 'h-8 text-xs' : ''}`}
              />
            </div>
          </div>
        </div>

        {/* Results */}
        <div className="flex-1 overflow-auto">
          {!query ? (
            <div className="flex flex-col items-center justify-center h-full text-muted-foreground">
              <SearchIcon className={`${isCompact ? 'w-8 h-8' : 'w-12 h-12'} mb-4 opacity-30`} />
              <p className="font-medium">Enter a function name to search</p>
              <p className="text-sm mt-1">Results will appear here</p>
            </div>
          ) : isLoading ? (
            <div className="p-4 text-muted-foreground">Searching...</div>
          ) : !results?.results.length ? (
            <div className="flex flex-col items-center justify-center h-full text-muted-foreground">
              <p>No results found for "{query}"</p>
            </div>
          ) : (
            <Table>
              <TableHeader>
                <TableRow className={isCompact ? 'h-8 bg-muted/50' : 'bg-muted/50'}>
                  <TableHead className={`${isCompact ? 'h-8 py-0 text-xs' : ''} w-[300px]`}>Function</TableHead>
                  <TableHead className={isCompact ? 'h-8 py-0 text-xs' : ''}>File</TableHead>
                  <TableHead className={`${isCompact ? 'h-8 py-0 text-xs' : ''} w-[80px]`}>Line</TableHead>
                </TableRow>
              </TableHeader>
              <TableBody>
                {results.results.map((fn) => (
                  <TableRow 
                    key={`${fn.file}:${fn.line}`}
                    className={`cursor-pointer ${selectedFn === fn.qualified_name ? 'bg-primary/5' : ''} ${isCompact ? 'h-7' : ''}`}
                    onClick={() => handleSelectFn(fn.qualified_name)}
                  >
                    <TableCell className={`font-medium ${isCompact ? 'py-1 text-xs' : ''}`}>
                      {fn.class_name && (
                        <span className="text-muted-foreground">{fn.class_name}.</span>
                      )}
                      {fn.name}
                    </TableCell>
                    <TableCell className={`text-muted-foreground truncate max-w-[250px] ${isCompact ? 'py-1 text-xs' : 'text-sm'}`}>
                      {fn.file.split(/[/\\]/).pop()}
                    </TableCell>
                    <TableCell className={isCompact ? 'py-1' : ''}>
                      <Badge variant="outline" className={isCompact ? 'h-5 px-1 text-[10px]' : ''}>{fn.line}</Badge>
                    </TableCell>
                  </TableRow>
                ))}
              </TableBody>
            </Table>
          )}
        </div>
      </ResizablePanel>
      
      <ResizableHandle withHandle />

      {/* Details Panel (Main Content) */}
      <ResizablePanel defaultSize={75}>
        <div className="h-full flex flex-col bg-muted/20">
        {selectedFn && details ? (
            <Tabs defaultValue="info" className="flex flex-col h-full">
            <div className={`flex-none ${isCompact ? 'p-2' : 'p-4'} border-b bg-background`}>
                <div className="flex justify-between items-start mb-4">
                  <div>
                    <h3 className={`${isCompact ? 'text-sm' : 'text-lg'} font-bold truncate max-w-[300px]`}>{details.name}</h3>
                    <p className={`${isCompact ? 'text-xs' : 'text-sm'} text-muted-foreground font-mono truncate max-w-[300px]`}>{details.qualified_name}</p>
                  </div>
                  {isCompact && (
                    <div className="flex items-center gap-1">
                      <Button variant="ghost" size="icon" className="h-6 w-6" onClick={() => handleSelectFn(null)}>
                        <X className="w-3 h-3" />
                      </Button>
                    </div>
                  )}
                  
                  {/* Test Generator Action */}
                  <Button variant="outline" size="sm" className="ml-auto mr-2" onClick={handleGenerateTest} disabled={generateTest.isPending}>
                    {generateTest.isPending ? 'Generating...' : 'Generate Test'}
                  </Button>
                  
                  {/* Density Toggle */}
                  <Button 
                    variant="ghost" 
                    size="icon" 
                    className="h-8 w-8" 
                    onClick={() => setDensity(isCompact ? 'comfortable' : 'compact')}
                    title="Toggle Density"
                  >
                    <LayoutTemplate className="w-4 h-4" />
                  </Button>
                </div>
                
                <TabsList className="w-full">
                    <TabsTrigger value="info" className="flex-1">Info</TabsTrigger>
                    <TabsTrigger value="code" className="flex-1">Code</TabsTrigger>
                    <TabsTrigger value="impact" className="flex-1">Impact Analysis</TabsTrigger>
                    <TabsTrigger value="explain" className="flex-1">Explain</TabsTrigger>
                </TabsList>
            </div>

            <div className="flex-1 overflow-auto">
                <TabsContent value="info" className={`m-0 ${isCompact ? 'p-3 space-y-3' : 'p-4 space-y-4'}`}>
                    {/* ... Info Content ... */}
                    <Card className={isCompact ? 'shadow-none' : ''}>
                        <CardHeader className={isCompact ? 'p-2' : 'pb-2'}>
                            <CardTitle className="text-sm">Location</CardTitle>
                        </CardHeader>
                        <CardContent className={isCompact ? 'p-2 pt-0' : ''}>
                            <Button variant="link" className={`p-0 h-auto ${isCompact ? 'text-xs' : 'text-sm'}`}>
                            <ExternalLink className="w-3 h-3 mr-1" />
                            {details.file.split(/[/\\]/).pop()}:{details.line}
                            </Button>
                        </CardContent>
                    </Card>

                    <div className="grid grid-cols-2 gap-3">
                    <Card className="bg-primary/5 border-primary/20">
                        <CardContent className={isCompact ? 'p-2 pt-2' : 'pt-4'}>
                        <div className="flex items-center gap-2">
                            <GitBranch className={`${isCompact ? 'w-3 h-3' : 'w-4 h-4'} text-primary`} />
                            <span className={`${isCompact ? 'text-lg' : 'text-2xl'} font-bold`}>{details.caller_count}</span>
                        </div>
                        <p className="text-xs text-muted-foreground mt-1">Callers</p>
                        </CardContent>
                    </Card>
                    <Card className="bg-green-500/5 border-green-500/20">
                        <CardContent className={isCompact ? 'p-2 pt-2' : 'pt-4'}>
                        <div className="flex items-center gap-2">
                            <Target className={`${isCompact ? 'w-3 h-3' : 'w-4 h-4'} text-green-500`} />
                            <span className={`${isCompact ? 'text-lg' : 'text-2xl'} font-bold`}>{details.callee_count}</span>
                        </div>
                        <p className="text-xs text-muted-foreground mt-1">Callees</p>
                        </CardContent>
                    </Card>
                    </div>

                    {details.callers.length > 0 && (
                    <Card className={isCompact ? 'shadow-none' : ''}>
                        <CardHeader className={isCompact ? 'p-2 pb-1' : 'pb-2'}>
                        <CardTitle className="text-sm flex items-center gap-2">
                            <ArrowRight className="w-4 h-4" />
                            Called by ({details.callers.length})
                        </CardTitle>
                        </CardHeader>
                        <CardContent className={`space-y-1 ${isCompact ? 'p-2 pt-0' : ''}`}>
                        {details.callers.slice(0, 8).map((caller: string) => (
                            <div key={caller} className={`flex items-center gap-2 rounded hover:bg-muted transition-colors ${isCompact ? 'text-xs py-0.5 px-1' : 'text-sm py-1 px-2'}`}>
                            <ChevronRight className="w-3 h-3 text-muted-foreground" />
                            <span className="font-mono truncate">{caller}</span>
                            </div>
                        ))}
                        {details.callers.length > 8 && (
                            <p className="text-xs text-muted-foreground pl-5">
                            +{details.callers.length - 8} more
                            </p>
                        )}
                        </CardContent>
                    </Card>
                    )}

                    {details.callees.length > 0 && (
                    <Card className={isCompact ? 'shadow-none' : ''}>
                        <CardHeader className={isCompact ? 'p-2 pb-1' : 'pb-2'}>
                        <CardTitle className="text-sm flex items-center gap-2">
                            <Target className="w-4 h-4" />
                            Calls ({details.callees.length})
                        </CardTitle>
                        </CardHeader>
                        <CardContent className={`space-y-1 ${isCompact ? 'p-2 pt-0' : ''}`}>
                        {details.callees.slice(0, 8).map((callee: string) => (
                            <div key={callee} className={`flex items-center gap-2 rounded hover:bg-muted transition-colors ${isCompact ? 'text-xs py-0.5 px-1' : 'text-sm py-1 px-2'}`}>
                            <ChevronRight className="w-3 h-3 text-muted-foreground" />
                            <span className="font-mono truncate">{callee}</span>
                            </div>
                        ))}
                        {details.callees.length > 8 && (
                            <p className="text-xs text-muted-foreground pl-5">
                            +{details.callees.length - 8} more
                            </p>
                        )}
                        </CardContent>
                    </Card>
                    )}
                </TabsContent>

                <TabsContent value="code" className="m-0 h-full relative">
                    <CodePreview 
                        code={details.code || ''} 
                        language="php" 
                        className="absolute inset-0"
                    />
                </TabsContent>

                <TabsContent value="impact" className="m-0 h-full">
                    <ImpactView 
                        index={activeIndex} 
                        functionName={details?.qualified_name || ''} 
                        isCompact={isCompact} 
                        onNavigate={(fn) => handleSelectFn(fn)}
                    />
                </TabsContent>

                <TabsContent value="explain" className={`m-0 ${isCompact ? 'p-3' : 'p-4'} h-full overflow-auto`}>
                    <AIExplanation index={activeIndex} functionName={details?.qualified_name || ''} isCompact={isCompact} />
                </TabsContent>
            </div>
            </Tabs>
        ) : (
          <div className="flex flex-col items-center justify-center h-full text-muted-foreground">
            <FileCode className={`${isCompact ? 'w-8 h-8' : 'w-10 h-10'} mb-3 opacity-30`} />
            <p className="font-medium">Select a function</p>
            <p className="text-sm mt-1">Details will appear here</p>
          </div>
        )}
        </div>
      </ResizablePanel>
    </ResizablePanelGroup>
      
      {/* Test Code Dialog */}
      <Dialog open={testDialogOpen} onOpenChange={setTestDialogOpen}>
        <DialogContent className="max-w-3xl max-h-[80vh] flex flex-col">
            <DialogHeader>
                <DialogTitle>Generated Unit Test</DialogTitle>
                <DialogDescription>
                    PHPUnit skeleton with mocks for models and libraries.
                </DialogDescription>
            </DialogHeader>
            <div className="flex-1 overflow-hidden relative border rounded-md min-h-[400px]">
                <CodePreview code={generatedCode} language="php" className="h-full" />
                <Button 
                    size="icon" 
                    variant="secondary" 
                    className="absolute top-2 right-2 h-8 w-8" 
                    onClick={copyToClipboard}
                >
                    {copied ? <Check className="w-4 h-4 text-green-500" /> : <Copy className="w-4 h-4" />}
                </Button>
            </div>
        </DialogContent>
      </Dialog>
    </>
  );
}

function AIExplanation({ index, functionName, isCompact }: { index: string; functionName: string; isCompact: boolean }) {
  const { data: explanation, isLoading, error } = useLcaExplanation(index, functionName);

  if (isLoading) return <div className="p-4 text-muted-foreground">Generating explanation...</div>;
  if (error) return <div className="p-4 text-red-500">Error: {(error as Error).message}</div>;
  if (!explanation) return <div className="p-4 text-muted-foreground">No explanation available.</div>;

  return (
    <div className="space-y-3">
        {explanation.is_mock && (
            <div className={`text-blue-500 bg-blue-500/10 border-blue-500/20 p-2 rounded text-xs border flex items-center gap-2`}>
                <Info className="w-4 h-4" />
                <span>Running in Mock Mode. Set OPENAI_API_KEY to enable real AI.</span>
            </div>
        )}
        <div className={`markdown-preview ${isCompact ? 'text-xs' : 'text-sm'} leading-relaxed whitespace-pre-wrap`}>
            {explanation.explanation}
        </div>
    </div>
  );
}
