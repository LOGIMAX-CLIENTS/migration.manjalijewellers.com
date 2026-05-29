/**
 * Design Tokens - Spacing
 * Based on 4px grid system
 */

export const spacing = {
  0: '0',
  px: '1px',
  0.5: '0.125rem',  // 2px
  1: '0.25rem',     // 4px
  1.5: '0.375rem',  // 6px
  2: '0.5rem',      // 8px
  2.5: '0.625rem',  // 10px
  3: '0.75rem',     // 12px
  3.5: '0.875rem',  // 14px
  4: '1rem',        // 16px
  5: '1.25rem',     // 20px
  6: '1.5rem',      // 24px
  7: '1.75rem',     // 28px
  8: '2rem',        // 32px
  9: '2.25rem',     // 36px
  10: '2.5rem',     // 40px
  11: '2.75rem',    // 44px
  12: '3rem',       // 48px
  14: '3.5rem',     // 56px
  16: '4rem',       // 64px
  20: '5rem',       // 80px
  24: '6rem',       // 96px
  28: '7rem',       // 112px
  32: '8rem',       // 128px
};

export const radius = {
  none: '0',
  sm: '0.25rem',    // 4px
  md: '0.375rem',   // 6px
  lg: '0.5rem',     // 8px
  xl: '0.75rem',    // 12px
  '2xl': '1rem',    // 16px
  '3xl': '1.5rem',  // 24px
  full: '9999px',
};

// Component-specific spacing
export const components = {
  sidebar: {
    width: '16rem',         // 256px
    widthCollapsed: '4rem', // 64px
  },
  header: {
    height: '4rem',         // 64px
  },
  panel: {
    padding: spacing[6],
    gap: spacing[4],
  },
  card: {
    padding: spacing[4],
    radius: radius.lg,
  },
  button: {
    paddingX: spacing[4],
    paddingY: spacing[2],
    radius: radius.md,
  },
  input: {
    paddingX: spacing[3],
    paddingY: spacing[2],
    radius: radius.md,
  },
};
