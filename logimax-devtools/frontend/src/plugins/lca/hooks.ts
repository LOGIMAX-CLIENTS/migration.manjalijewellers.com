/**
 * LCA Plugin - API Hooks
 * React Query hooks for LCA operations
 */

import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import type { 
  IndexStatus, IndexProgress, SearchResult, 
  ImpactResult, CallGraph, FunctionDetail, ExplanationResult
} from './types';

const API_BASE = 'http://localhost:8800/api/lca';

// Fetch helper
async function fetchLca<T>(endpoint: string): Promise<T> {
  const res = await fetch(`${API_BASE}${endpoint}`);
  if (!res.ok) throw new Error(`API error: ${res.statusText}`);
  return res.json();
}

async function postLca<T>(endpoint: string, data: object): Promise<T> {
  const res = await fetch(`${API_BASE}${endpoint}`, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(data),
  });
  if (!res.ok) throw new Error(`API error: ${res.statusText}`);
  return res.json();
}

// ============ Hooks ============

export function useLcaIndexes() {
  return useQuery<string[]>({
    queryKey: ['lca', 'indexes'],
    queryFn: () => fetchLca('/indexes'),
  });
}

export function useLcaStatus(name: string) {
  return useQuery<IndexStatus>({
    queryKey: ['lca', 'status', name],
    queryFn: () => fetchLca(`/status/${name}`),
    enabled: !!name,
  });
}

export function useLcaSearch(indexName: string, query: string, limit = 20) {
  return useQuery<SearchResult>({
    queryKey: ['lca', 'search', indexName, query, limit],
    queryFn: () => fetchLca(`/search/${indexName}?q=${encodeURIComponent(query)}&limit=${limit}`),
    enabled: !!indexName && !!query && query.length >= 2,
  });
}

export function useLcaFunctionDetails(indexName: string, functionName: string) {
  return useQuery<FunctionDetail>({
    queryKey: ['lca', 'function', indexName, functionName],
    queryFn: () => fetchLca(`/function/${indexName}/${encodeURIComponent(functionName)}`),
    enabled: !!indexName && !!functionName,
  });
}

export function useLcaImpact(indexName: string, functionName: string, depth = 2) {
  return useQuery<ImpactResult>({
    queryKey: ['lca', 'impact', indexName, functionName, depth],
    queryFn: () => fetchLca(`/impact/${indexName}/${encodeURIComponent(functionName)}?depth=${depth}`),
    enabled: !!indexName && !!functionName,
  });
}

export function useLcaCallGraph(indexName: string, functionName: string, depth = 2) {
  return useQuery<CallGraph>({
    queryKey: ['lca', 'graph', indexName, functionName, depth],
    queryFn: () => fetchLca(`/graph/${indexName}/${encodeURIComponent(functionName)}?depth=${depth}`),
    enabled: !!indexName && !!functionName,
  });
}

export function useLcaIndexProgress(name: string, enabled = false) {
  return useQuery<IndexProgress>({
    queryKey: ['lca', 'progress', name],
    queryFn: () => fetchLca(`/index/progress/${name}`),
    enabled: enabled && !!name,
    refetchInterval: (query) => query.state.data?.is_complete ? false : 1000,
  });
}

export function useStartIndexing() {
  const queryClient = useQueryClient();
  
  return useMutation({
    mutationFn: (data: { path: string; name: string; exclude_patterns?: string[] }) =>
      postLca('/index', data),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['lca', 'indexes'] });
    },
  });
}

export function useLcaExplanation(indexName: string, functionName: string) {
  return useQuery<ExplanationResult>({
    queryKey: ['lca', 'explain', indexName, functionName],
    queryFn: () => fetchLca(`/explain/${indexName}/${encodeURIComponent(functionName)}`),
    enabled: !!indexName && !!functionName,
  });
}

export function useGenerateTest() {
    return useMutation({
        mutationFn: (data: { file_path: string }) => 
            postLca<{ code: string, success: boolean }>(`/gen-test?file_path=${encodeURIComponent(data.file_path)}`, {}),
    });
}
