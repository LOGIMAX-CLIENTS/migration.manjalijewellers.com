#!/usr/bin/env python3
"""
RAG Embedding Server — keeps BGE-M3 model warm in memory.

Usage:
    python rag_server.py              # Start server (default port 9876)
    python rag_server.py --port 9877  # Custom port

The discover command in cli.py tries this server first for embeddings.
If not running, falls back to loading the model directly (~40s cold start).
"""

import json
import os
import sys
import time
from http.server import HTTPServer, BaseHTTPRequestHandler
import argparse

# Add LCA path
PROJECT_ROOT = os.path.dirname(os.path.dirname(os.path.dirname(os.path.abspath(__file__))))
LCA_PATH = os.path.join(PROJECT_ROOT, 'logimax-devtools', 'backend', 'lca_core')
if LCA_PATH not in sys.path:
    sys.path.insert(0, LCA_PATH)

RAG_STORE_DIR = os.path.join(PROJECT_ROOT, '.rag_store')
DEFAULT_PORT = 9876

# Pre-load model at startup
_model = None
_collection = None


def _warmup():
    """Load the BGE-M3 model and ChromaDB collection into memory."""
    global _model, _collection
    
    print('  Loading BGE-M3 model...')
    t0 = time.time()
    from sentence_transformers import SentenceTransformer
    _model = SentenceTransformer('BAAI/bge-m3', trust_remote_code=True)
    print(f'  ✅ Model loaded in {time.time() - t0:.1f}s')
    
    print('  Loading ChromaDB collection...')
    t0 = time.time()
    try:
        import chromadb
        client = chromadb.PersistentClient(path=RAG_STORE_DIR)
        _collection = client.get_collection('codebase')
        count = _collection.count()
        print(f'  ✅ Collection loaded ({count} chunks) in {time.time() - t0:.1f}s')
    except Exception as e:
        print(f'  ⚠️  Collection failed: {e}')
        _collection = None


class RAGHandler(BaseHTTPRequestHandler):
    """Handle embedding and query requests."""
    
    def do_POST(self):
        content_length = int(self.headers.get('Content-Length', 0))
        body = self.rfile.read(content_length)
        
        try:
            data = json.loads(body)
        except json.JSONDecodeError:
            self._respond(400, {'error': 'Invalid JSON'})
            return
        
        if self.path == '/embed':
            self._handle_embed(data)
        elif self.path == '/query':
            self._handle_query(data)
        elif self.path == '/health':
            self._respond(200, {'status': 'ok', 'model_loaded': _model is not None})
        else:
            self._respond(404, {'error': 'Not found'})
    
    def do_GET(self):
        if self.path == '/health':
            self._respond(200, {
                'status': 'ok',
                'model_loaded': _model is not None,
                'collection_loaded': _collection is not None,
            })
        else:
            self._respond(404, {'error': 'Not found'})
    
    def _handle_embed(self, data):
        """Return embedding for a query text."""
        text = data.get('text', '')
        if not text:
            self._respond(400, {'error': 'Missing text'})
            return
        
        if _model is None:
            self._respond(503, {'error': 'Model not loaded'})
            return
        
        t0 = time.time()
        embedding = _model.encode([text], show_progress_bar=False, normalize_embeddings=True)
        elapsed = time.time() - t0
        
        self._respond(200, {
            'embedding': embedding[0].tolist(),
            'elapsed_ms': round(elapsed * 1000, 1),
        })
    
    def _handle_query(self, data):
        """Full RAG query — embed + ChromaDB search in one call."""
        query = data.get('query', '')
        n_results = data.get('n_results', 10)
        where_filter = data.get('where_filter')
        
        if not query:
            self._respond(400, {'error': 'Missing query'})
            return
        
        if _model is None or _collection is None:
            self._respond(503, {'error': 'Model or collection not loaded'})
            return
        
        t0 = time.time()
        
        # Embed query
        embedding = _model.encode([query], show_progress_bar=False, normalize_embeddings=True)
        
        # Query ChromaDB
        kwargs = {
            'query_embeddings': [embedding[0].tolist()],
            'n_results': n_results,
            'include': ['documents', 'metadatas', 'distances'],
        }
        if where_filter:
            kwargs['where'] = where_filter
        
        results = _collection.query(**kwargs)
        
        # Flatten results
        output = []
        if results and results['ids'] and results['ids'][0]:
            for i in range(len(results['ids'][0])):
                output.append({
                    'id': results['ids'][0][i],
                    'document': results['documents'][0][i] if results['documents'] else '',
                    'metadata': results['metadatas'][0][i] if results['metadatas'] else {},
                    'distance': results['distances'][0][i] if results['distances'] else 0,
                })
        
        elapsed = time.time() - t0
        self._respond(200, {
            'results': output,
            'count': len(output),
            'elapsed_ms': round(elapsed * 1000, 1),
        })
    
    def _respond(self, code, data):
        self.send_response(code)
        self.send_header('Content-Type', 'application/json')
        self.end_headers()
        self.wfile.write(json.dumps(data).encode())
    
    def log_message(self, format, *args):
        """Suppress default logging for clean output."""
        pass


def main():
    parser = argparse.ArgumentParser(description='RAG Embedding Server')
    parser.add_argument('--port', type=int, default=DEFAULT_PORT, help=f'Port (default: {DEFAULT_PORT})')
    args = parser.parse_args()
    
    print(f'🚀 RAG Server starting on port {args.port}')
    print(f'   Project: {PROJECT_ROOT}')
    print(f'   RAG store: {RAG_STORE_DIR}')
    print()
    
    _warmup()
    
    print(f'\n✅ Server ready at http://localhost:{args.port}')
    print(f'   Endpoints: GET /health, POST /query, POST /embed')
    print(f'   Press Ctrl+C to stop\n')
    
    server = HTTPServer(('127.0.0.1', args.port), RAGHandler)
    try:
        server.serve_forever()
    except KeyboardInterrupt:
        print('\n  Server stopped.')
        server.server_close()


if __name__ == '__main__':
    main()
