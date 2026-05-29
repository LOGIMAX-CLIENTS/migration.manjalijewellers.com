import { useEffect, useRef } from 'react';
import { Network } from 'vis-network';
import { DataSet } from 'vis-data';
import { useLcaCallGraph } from '../hooks';

interface VisualGraphProps {
  index: string;
  functionName: string;
  isCompact: boolean;
  mode: 'network' | 'mindmap';
}

export function VisualGraph({ index, functionName, isCompact, mode }: VisualGraphProps) {
  const { data: graph, isLoading } = useLcaCallGraph(index, functionName);
  const containerRef = useRef<HTMLDivElement>(null);
  const networkRef = useRef<Network | null>(null);

  useEffect(() => {
    if (!graph || !containerRef.current) return;

    // 1. Prepare Data
            // Transform our generic graph nodes to Vis.js nodes
            const visNodes = new DataSet(
                graph.nodes.map((n: any) => {
                    const isCenter = n.type === 'center';
                    const isCaller = n.type === 'caller';

                    // Premium Color Palette
                    let color = {
                        background: '#ffffff',
                        border: '#64748b',
                        highlight: { background: '#f1f5f9', border: '#475569' },
                        hover: { background: '#f8fafc', border: '#475569' }
                    };

                    if (isCenter) {
                        color = {
                            background: '#4f46e5', // Indigo-600
                            border: '#4338ca',     // Indigo-700
                            highlight: { background: '#4338ca', border: '#3730a3' },
                            hover: { background: '#4338ca', border: '#3730a3' }
                        };
                    } else if (isCaller) {
                        color = {
                            background: '#f3e8ff', // Purple-100
                            border: '#d8b4fe',     // Purple-300
                            highlight: { background: '#e9d5ff', border: '#c084fc' },
                            hover: { background: '#e9d5ff', border: '#c084fc' }
                        };
                    } else {
                        color = {
                            background: '#ecfdf5', // Emerald-50
                            border: '#6ee7b7',     // Emerald-300
                            highlight: { background: '#d1fae5', border: '#34d399' },
                            hover: { background: '#d1fae5', border: '#34d399' }
                        };
                    }

                    // Font styling
                    const fontColor = isCenter ? '#ffffff' : (isCaller ? '#6b21a8' : '#065f46');
                    const label = isCenter ? `<b>${n.label}</b>` : n.label;

                    return {
                        id: n.id,
                        label: label,
                        group: n.type,
                        shape: 'box',
                        color: color,
                        font: {
                            color: fontColor,
                            size: isCompact ? 12 : 14,
                            face: 'Inter',
                            multi: 'html', // Enable HTML specific styling (bold)
                            vadjust: 0
                        },
                        margin: 12, // Increased margin for breathing room
                        borderWidth: 1,
                        shadow: {
                            enabled: true,
                            color: 'rgba(0,0,0,0.1)', // Soft shadow
                            size: 10,
                            x: 4,
                            y: 4
                        },
                        shapeProperties: {
                            borderRadius: 6 // Rounded corners
                        },
                        level: isCenter ? 0 : (isCaller ? -1 : 1)
                    };
                })
            );

            const visEdges = new DataSet(
                graph.edges.map((e: any) => ({
                    id: `${e.source}-${e.target}`,
                    from: e.source,
                    to: e.target,
                    arrows: {
                        to: { enabled: true, scaleFactor: 0.5, type: 'arrow' }
                    },
                    color: {
                        color: '#cbd5e1', // Slate-300
                        highlight: '#94a3b8',
                        hover: '#64748b'
                    },
                    width: 2,
                    smooth: {
                        type: 'cubicBezier',
                        forceDirection: mode === 'mindmap' ? 'horizontal' : 'none',
                        roundness: 0.4
                    }
                }))
            );

            // 2. Configure Options based on Mode
            const options: any = {
                physics: {
                    enabled: mode === 'network',
                    stabilization: true,
                    solver: 'forceAtlas2Based',
                    forceAtlas2Based: {
                        springLength: 200, // More space
                        springConstant: 0.05,
                        damping: 0.9,
                        avoidOverlap: 1
                    }
                },
                layout: {
                    hierarchical: mode === 'mindmap' ? {
                        enabled: true,
                        direction: 'LR',
                        sortMethod: 'directed',
                        levelSeparation: 300, // Wider for readability
                        nodeSpacing: 100,
                        shakeTowards: 'roots'
                    } : {
                        enabled: false
                    }
                },
                interaction: {
                    hover: true,
                    tooltipDelay: 200,
                    zoomView: true,
                    dragView: true,
                    navigationButtons: false,
                    keyboard: false
                },
                nodes: {
                    shape: 'box',
                    fixed: false
                }
            };

    // 3. Initialize Network
    if (networkRef.current) {
        networkRef.current.destroy();
    }
    
    networkRef.current = new Network(containerRef.current, { nodes: visNodes, edges: visEdges } as any, options);

    return () => {
      if (networkRef.current) {
        networkRef.current.destroy();
        networkRef.current = null;
      }
    };
  }, [graph, isCompact, mode]);

  if (isLoading) return <div className="p-4 text-muted-foreground">Loading graph...</div>;
  if (!graph || !graph.nodes.length) return <div className="p-4 text-muted-foreground">No graph data available.</div>;

  return (
    <div className="h-full w-full relative">
       <div ref={containerRef} className="h-full w-full min-h-[400px]" />
       
       {/* Legend Overlay */}
       <div className="absolute bottom-4 right-4 flex flex-col gap-2 p-3 bg-white/90 dark:bg-slate-900/90 backdrop-blur border rounded-lg shadow-sm text-xs z-10">
         <div className="font-semibold mb-1 opacity-70">Legend</div>
         <div className="flex items-center gap-2">
             <div className="w-3 h-3 rounded-full border border-slate-400 bg-slate-50"></div> 
             <span>Called By</span>
         </div>
         <div className="flex items-center gap-2">
             <div className="w-3 h-3 rounded-full bg-blue-500 border border-blue-600"></div> 
             <span>Selected</span>
         </div>
         <div className="flex items-center gap-2">
             <div className="w-3 h-3 rounded-md border border-green-500 bg-green-50"></div> 
             <span>Calls</span>
         </div>
       </div>
    </div>
  );
}
