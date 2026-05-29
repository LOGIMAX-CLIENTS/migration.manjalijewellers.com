import React from 'react';
import ReactDOM from 'react-dom/client';
import { BrowserRouter } from 'react-router-dom';
import { QueryClient, QueryClientProvider } from '@tanstack/react-query';
import { PluginProvider } from '@core/plugin-system';
import App from './app/App';
import './index.css';

// Register plugins
import './plugins';

// Create React Query client
const queryClient = new QueryClient({
  defaultOptions: {
    queries: {
      staleTime: 1000 * 60, // 1 minute
      refetchOnWindowFocus: false,
    },
  },
});

ReactDOM.createRoot(document.getElementById('root')!).render(
  <React.StrictMode>
    <QueryClientProvider client={queryClient}>
      <BrowserRouter>
        <PluginProvider>
          <App />
        </PluginProvider>
      </BrowserRouter>
    </QueryClientProvider>
  </React.StrictMode>
);
