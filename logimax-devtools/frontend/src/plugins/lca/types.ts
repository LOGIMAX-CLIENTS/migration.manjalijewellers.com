/**
 * LCA Plugin - Types
 * TypeScript types matching backend schemas
 */

export interface FunctionInfo {
  name: string;
  qualified_name: string;
  file: string;
  line: number;
  end_line?: number;
  class_name?: string;
}

export interface FunctionDetail extends FunctionInfo {
  callers: string[];
  callees: string[];
  caller_count: number;
  callee_count: number;
  code?: string;
}

export interface IndexStatus {
  exists: boolean;
  name?: string;
  path?: string;
  functions: number;
  calls: number;
}

export interface IndexProgress {
  total_files: number;
  processed_files: number;
  current_file: string;
  progress: number;
  is_complete: boolean;
  errors: string[];
}

export interface SearchResult {
  results: FunctionInfo[];
  total: number;
}

export interface CallInfo {
  target: string;
  line: number;
  call_type: 'calls' | 'called_by';
}

export interface Level2Deps {
  calls: string[];
  called_by: string[];
}

export interface ImpactResult {
  function: string;
  function_info: {
    file: string;
    line: number;
    end_line: number;
    class_name?: string;
  };
  calls: CallInfo[];
  level2: Record<string, Level2Deps>;
  risk_level: 'low' | 'medium' | 'high' | 'critical';
  total_impact: number;
}

export interface ExplanationResult {
  function: string;
  explanation: string;
  is_mock: boolean;
}

export interface GraphNode {
  id: string;
  label: string;
  type: 'center' | 'caller' | 'callee';
}

export interface GraphEdge {
  source: string;
  target: string;
}

export interface CallGraph {
  nodes: GraphNode[];
  edges: GraphEdge[];
}
