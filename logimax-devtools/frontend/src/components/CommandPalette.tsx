import { useEffect, useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { 
  CommandDialog, 
  CommandEmpty, 
  CommandGroup, 
  CommandInput, 
  CommandItem, 
  CommandList, 
  CommandSeparator 
} from './ui/command';
import { FileCode, Search, Activity, HelpCircle, FileText } from 'lucide-react';
import { useLcaSearch, useLcaIndexes } from '@/plugins/lca/hooks';
import { useLcaSettings } from '@/plugins/lca/store';

export function CommandPalette() {
  const [open, setOpen] = useState(false);
  const [query, setQuery] = useState('');
  const navigate = useNavigate();

  // Global Keyboard Shortcut: Ctrl+K / Cmd+K
  useEffect(() => {
    const down = (e: KeyboardEvent) => {
      if (e.key === 'k' && (e.metaKey || e.ctrlKey)) {
        e.preventDefault();
        setOpen((open) => !open);
      }
    };

    document.addEventListener('keydown', down);
    return () => document.removeEventListener('keydown', down);
  }, []);

  // LCA Search Integration
  // Use persistent active index
  const { data: indexes = [] } = useLcaIndexes();
  const { activeIndex } = useLcaSettings();
  const effectiveIndex = activeIndex || indexes[0] || '';
  
  // Only search if query is at least 2 chars
  const { data: searchResults, isLoading } = useLcaSearch(effectiveIndex, query, 10);

  const runCommand = (command: () => void) => {
    setOpen(false);
    command();
    setQuery(''); // Reset query
  };

  return (
    <CommandDialog open={open} onOpenChange={setOpen}>
      <CommandInput
        placeholder="Type a command or search functions..."
        value={query}
        onValueChange={setQuery}
      />
      <CommandList>
        <CommandEmpty>No results found.</CommandEmpty>

        {/* Core Navigation Group */}
        {query.length < 2 && (
            <CommandGroup heading="Suggestions">
            <CommandItem onSelect={() => runCommand(() => navigate('/lca'))}>
                <Search className="mr-2 h-4 w-4" />
                <span>LCA Dashboard</span>
            </CommandItem>
            <CommandItem onSelect={() => runCommand(() => navigate('/lca/help'))}>
                <HelpCircle className="mr-2 h-4 w-4" />
                <span>Documentation & Shortcuts</span>
            </CommandItem>
            <CommandItem onSelect={() => runCommand(() => navigate('/lca/search'))}>
                <FileCode className="mr-2 h-4 w-4" />
                <span>Function Search</span>
            </CommandItem>
            <CommandItem onSelect={() => runCommand(() => navigate('/tests'))}>
                <Activity className="mr-2 h-4 w-4" />
                <span>Test Runner</span>
            </CommandItem>
            <CommandItem onSelect={() => runCommand(() => navigate('/logs'))}>
                <FileText className="mr-2 h-4 w-4" />
                <span>Log Viewer</span>
            </CommandItem>
            </CommandGroup>
        )}
        
        <CommandSeparator />

        {/* Dynamic LCA Search Results */}
        {query.length >= 2 && effectiveIndex && (
            <CommandGroup heading={`Functions in ${effectiveIndex}`}>
                {isLoading && <CommandItem disabled>Searching...</CommandItem>}
                
                {searchResults?.results.map((fn) => (
                    <CommandItem 
                        key={fn.qualified_name}
                        value={fn.name}
                        onSelect={() => runCommand(() => navigate(`/lca/search?fn=${encodeURIComponent(fn.qualified_name)}`))}
                    >
                        <FileCode className="mr-2 h-4 w-4" />
                        <span>{fn.name}</span>
                        <span className="ml-2 text-xs text-muted-foreground">{fn.file.split(/[/\\]/).pop()}</span>
                    </CommandItem>
                ))}
            </CommandGroup>
        )}
      </CommandList>
    </CommandDialog>
  );
}
