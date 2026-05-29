/**
 * Design Tokens - Index
 * Central export for all design tokens
 */

export * from './colors';
export * from './typography';
export * from './spacing';
export * from './motion';

import { colors, lightTheme, darkTheme, type Theme } from './colors';
import { typography, textStyles } from './typography';
import { spacing, radius, components } from './spacing';
import { motion, variants, transitions } from './motion';

export const tokens = {
  colors,
  lightTheme,
  darkTheme,
  typography,
  textStyles,
  spacing,
  radius,
  components,
  motion,
  variants,
  transitions,
} as const;

export type { Theme };

export default tokens;
