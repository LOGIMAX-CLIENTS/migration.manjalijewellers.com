import { useState, useEffect } from 'react';

export type Density = 'comfortable' | 'compact';

export function useDensity() {
  const [density, setDensity] = useState<Density>(() => {
    const stored = localStorage.getItem('devtools.ui.density');
    return (stored as Density) || 'comfortable';
  });

  useEffect(() => {
    localStorage.setItem('devtools.ui.density', density);
    // Add class to body for global styling if needed
    if (density === 'compact') {
      document.body.classList.add('density-compact');
    } else {
      document.body.classList.remove('density-compact');
    }
  }, [density]);

  const toggleDensity = () => {
    setDensity((prev) => (prev === 'comfortable' ? 'compact' : 'comfortable'));
  };

  return { density, setDensity, toggleDensity };
}
