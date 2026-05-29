import { create } from 'zustand';
import { persist } from 'zustand/middleware';

interface LcaSettingsState {
  activeIndex: string;
  setActiveIndex: (index: string) => void;
  density: 'comfortable' | 'compact';
  setDensity: (density: 'comfortable' | 'compact') => void;
  showMinimap: boolean;
  toggleMinimap: () => void;
}

export const useLcaSettings = create<LcaSettingsState>()(
  persist(
    (set) => ({
      activeIndex: '',
      setActiveIndex: (index) => set({ activeIndex: index }),
      density: 'comfortable',
      setDensity: (density) => set({ density }),
      showMinimap: false,
      toggleMinimap: () => set((state) => ({ showMinimap: !state.showMinimap })),
    }),
    {
      name: 'lca-settings', // unique name for localStorage key
    }
  )
);
