import { useState } from 'react';
import { Card, CardContent } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { ArrowRight, AlertTriangle, ShieldCheck, ShieldAlert, Network, Layers, Target } from 'lucide-react';
import { VisualGraph } from './VisualGraph';
// We'll reuse the same hook as Search.tsx for now, passing data down is better but direct hook usage works for MVP
import { useLcaFunctionDetails } from '../hooks';


interface ImpactViewProps {
  index: string;
  functionName: string;
  isCompact: boolean;
  onNavigate?: (fnName: string) => void;
}

type GraphMode = 'network' | 'mindmap';

export function ImpactView({ index, functionName, isCompact, onNavigate }: ImpactViewProps) {
  const [graphMode, setGraphMode] = useState<GraphMode>('network');
  const { data: details, isLoading } = useLcaFunctionDetails(index, functionName);

  if (isLoading) {
    return <div className="p-4 text-muted-foreground">Analyzing impact...</div>;
  }

  if (!details) {
    return <div className="p-4 text-muted-foreground">No details available.</div>;
  }

  // Risk Assessment Logic
  const totalImpact = details.caller_count + details.callee_count;
  let riskLevel: 'HIGH' | 'MEDIUM' | 'LOW' = 'LOW';
  if (totalImpact > 10) riskLevel = 'HIGH';
  else if (totalImpact > 5) riskLevel = 'MEDIUM';

  const riskColor = {
    HIGH: "bg-red-100 text-red-700 border-red-200",
    MEDIUM: "bg-yellow-100 text-yellow-700 border-yellow-200",
    LOW: "bg-green-100 text-green-700 border-green-200"
  };

  const RiskIcon = {
    HIGH: ShieldAlert,
    MEDIUM: AlertTriangle,
    LOW: ShieldCheck
  }[riskLevel];

  return (
    <div className="flex flex-col h-full bg-background/50">
      {/* Header / Toolbar */}
      <div className={`flex-none ${isCompact ? 'p-2' : 'p-4'} border-b bg-background flex items-center justify-between gap-4`}>
        <div className="flex items-center gap-3">
          <Badge variant="outline" className={`${riskColor[riskLevel]} ${isCompact ? 'text-[10px] px-1' : ''} flex items-center gap-1`}>
            <RiskIcon className="w-3 h-3" />
            {riskLevel} RISK
          </Badge>
          
          <div className="flex items-center gap-4 text-sm text-muted-foreground">
            <div className="flex items-center gap-1">
              <span className="font-bold text-foreground">{details.caller_count}</span>
              <span>Called By</span>
            </div>
            <div className="flex items-center gap-1">
              <span className="font-bold text-foreground">{details.callee_count}</span>
              <span>Calls</span>
            </div>
            {/* Breadcrumb-ish location */}
            <div className="flex items-center gap-1 text-xs border-l pl-3 ml-2 truncate max-w-[300px]">
                <Target className="w-3 h-3 text-red-400" />
                <span className="font-mono text-muted-foreground">{details.file.split(/[/\\]/).pop()}:{details.line}</span>
            </div>
          </div>
        </div>

        <div className="flex bg-muted rounded-md p-1">
          <Button 
            variant="ghost" 
            size="sm" 
            className={`h-7 px-3 ${graphMode === 'network' ? 'bg-indigo-500 text-white shadow-sm hover:bg-indigo-600 hover:text-white' : 'text-muted-foreground'}`}
            onClick={() => setGraphMode('network')}
          >
            <Network className="w-3 h-3 mr-2" />
            Network
          </Button>
          <Button 
            variant="ghost" 
            size="sm" 
            className={`h-7 px-3 ${graphMode === 'mindmap' ? 'bg-pink-500 text-white shadow-sm hover:bg-pink-600 hover:text-white' : 'text-muted-foreground'}`}
            onClick={() => setGraphMode('mindmap')}
          >
            <Layers className="w-3 h-3 mr-2" />
            Mind Map
          </Button>
        </div>
      </div>

      {/* Main Content: Split Vertical (Graph Top, Lists Bottom) */}
      <div className="flex-1 flex flex-col overflow-hidden">
          
          {/* Top: Graph (65%) */}
          <div className="flex-[2] relative border-b min-h-[300px] bg-dots-pattern">
              <div className="absolute inset-0">
                 <VisualGraph index={index} functionName={functionName} isCompact={isCompact} mode={graphMode} />
              </div>
          </div>

          {/* Bottom: Lists (35%) */}
          <div className={`flex-1 min-h-[200px] overflow-hidden flex flex-col ${isCompact ? 'p-2' : 'p-3'}`}>
              <div className="grid grid-cols-2 gap-4 h-full">
               {/* Upstream / Callers */}
               <Card className="flex flex-col h-full border-purple-200 bg-purple-50/30 dark:bg-purple-950/10 shadow-sm">
                 <CardContent className="p-0 flex flex-col h-full">
                    <div className="p-2 px-3 border-b border-purple-100 dark:border-purple-900/50 bg-purple-100/50 dark:bg-purple-900/20">
                      <h4 className="font-semibold text-purple-700 dark:text-purple-300 flex items-center gap-2 text-sm">
                        <div className="p-1 rounded bg-purple-200 dark:bg-purple-800">
                             <ArrowRight className="w-3 h-3 text-purple-700 dark:text-purple-100" /> 
                        </div>
                        Called By (Upstream)
                      </h4>
                    </div>
                    <div className="flex-1 overflow-auto p-2 space-y-1">
                      {details.callers.length > 0 ? (
                        details.callers.map((caller: string) => (
                          <div 
                            key={caller} 
                            className="group flex items-center gap-2 p-1.5 rounded hover:bg-white dark:hover:bg-purple-900/30 border border-transparent hover:border-purple-100 cursor-pointer transition-all"
                            onClick={() => onNavigate?.(caller)}
                          >
                            <span className="font-mono text-xs text-purple-900 dark:text-purple-100 break-all">{caller}</span>
                          </div>
                        ))
                      ) : (
                         <div className="p-4 text-center text-xs text-muted-foreground italic">
                           No known callers in the index.
                         </div>
                      )}
                    </div>
                 </CardContent>
               </Card>

               {/* Downstream / Callees */}
               <Card className="flex flex-col h-full border-emerald-200 bg-emerald-50/30 dark:bg-emerald-950/10 shadow-sm">
                 <CardContent className="p-0 flex flex-col h-full">
                    <div className="p-2 px-3 border-b border-emerald-100 dark:border-emerald-900/50 bg-emerald-100/50 dark:bg-emerald-900/20">
                      <h4 className="font-semibold text-emerald-700 dark:text-emerald-300 flex items-center gap-2 text-sm">
                        <div className="p-1 rounded bg-emerald-200 dark:bg-emerald-800">
                             <Target className="w-3 h-3 text-emerald-700 dark:text-emerald-100" /> 
                        </div>
                        Calls (Downstream)
                      </h4>
                    </div>
                    <div className="flex-1 overflow-auto p-2 space-y-1">
                      {details.callees.length > 0 ? (
                        details.callees.map((callee: string) => (
                          <div 
                            key={callee} 
                            className="group flex items-center gap-2 p-1.5 rounded hover:bg-white dark:hover:bg-emerald-900/30 border border-transparent hover:border-emerald-100 cursor-pointer transition-all"
                            onClick={() => onNavigate?.(callee)}
                          >
                            <span className="font-mono text-xs text-emerald-900 dark:text-emerald-100 break-all">{callee}</span>
                          </div>
                        ))
                      ) : (
                         <div className="p-4 text-center text-xs text-muted-foreground italic">
                           No outgoing calls.
                         </div>
                      )}
                    </div>
                 </CardContent>
               </Card>
            </div>
          </div>
      </div>
    </div>
  );
}
