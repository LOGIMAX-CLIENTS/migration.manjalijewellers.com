/**
 * Design Tokens - Motion (Animations)
 */

export const motion = {
  duration: {
    instant: '0ms',
    fast: '150ms',
    normal: '250ms',
    slow: '400ms',
    slower: '600ms',
  },

  easing: {
    linear: 'linear',
    default: 'cubic-bezier(0.4, 0, 0.2, 1)',      // Ease in-out
    easeIn: 'cubic-bezier(0.4, 0, 1, 1)',
    easeOut: 'cubic-bezier(0, 0, 0.2, 1)',
    easeInOut: 'cubic-bezier(0.4, 0, 0.2, 1)',
    spring: 'cubic-bezier(0.34, 1.56, 0.64, 1)', // Bouncy
    bounce: 'cubic-bezier(0.68, -0.55, 0.265, 1.55)',
  },
};

// Framer Motion variants
export const variants = {
  fadeIn: {
    initial: { opacity: 0 },
    animate: { opacity: 1 },
    exit: { opacity: 0 },
  },

  slideUp: {
    initial: { opacity: 0, y: 20 },
    animate: { opacity: 1, y: 0 },
    exit: { opacity: 0, y: -10 },
  },

  slideDown: {
    initial: { opacity: 0, y: -20 },
    animate: { opacity: 1, y: 0 },
    exit: { opacity: 0, y: 10 },
  },

  slideLeft: {
    initial: { opacity: 0, x: 20 },
    animate: { opacity: 1, x: 0 },
    exit: { opacity: 0, x: -10 },
  },

  slideRight: {
    initial: { opacity: 0, x: -20 },
    animate: { opacity: 1, x: 0 },
    exit: { opacity: 0, x: 10 },
  },

  scale: {
    initial: { opacity: 0, scale: 0.95 },
    animate: { opacity: 1, scale: 1 },
    exit: { opacity: 0, scale: 0.95 },
  },

  stagger: {
    animate: {
      transition: {
        staggerChildren: 0.05,
      },
    },
  },
};

// Transition presets
export const transitions = {
  fast: {
    duration: 0.15,
    ease: [0.4, 0, 0.2, 1],
  },
  normal: {
    duration: 0.25,
    ease: [0.4, 0, 0.2, 1],
  },
  slow: {
    duration: 0.4,
    ease: [0.4, 0, 0.2, 1],
  },
  spring: {
    type: 'spring',
    stiffness: 300,
    damping: 20,
  },
};
