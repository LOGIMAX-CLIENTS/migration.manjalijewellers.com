import { useEffect, useState } from 'react';
import { useNavigate } from 'react-router-dom';

export function useGlobalShortcuts() {
  const navigate = useNavigate();
  const [lastKey, setLastKey] = useState<{ key: string; time: number } | null>(null);

  useEffect(() => {
    const handleKeyDown = (e: KeyboardEvent) => {
      // Ignore if typing in an input/textarea
      const target = e.target as HTMLElement;
      if (target.tagName === 'INPUT' || target.tagName === 'TEXTAREA' || target.isContentEditable) {
        return;
      }

      const now = Date.now();
      const char = e.key.toLowerCase();

      // Check for sequence
      if (lastKey && (now - lastKey.time < 800)) {
        if (lastKey.key === 'g') {
          switch (char) {
            case 's': // g s -> Search
              e.preventDefault();
              navigate('/lca/search');
              break;
            case 'd': // g d -> Dashboard
              e.preventDefault();
              navigate('/lca');
              break;
            case 't': // g t -> Tests
              e.preventDefault();
              navigate('/tests');
              break;
            case 'l': // g l -> Logs
              e.preventDefault();
              navigate('/logs');
              break;
          }
        }
        // Reset sequence
        setLastKey(null);
      } else {
        // Start sequence
        if (char === 'g') {
          setLastKey({ key: char, time: now });
        } else {
          setLastKey(null);
        }
      }

      // Single Key Actions
      // '/' -> Trigger Command Palette (simulated by triggering a click or dispatching event if needed, 
      // but CommandPalette usually listens for Ctrl+K. We can add / listener there or here if we expose state)
      // For now, let's stick to 'g' sequences here as CommandPalette handles its own global shortcut.
    };

    window.addEventListener('keydown', handleKeyDown);
    return () => window.removeEventListener('keydown', handleKeyDown);
  }, [lastKey, navigate]);
}
