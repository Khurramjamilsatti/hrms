<template>
  <component
    :is="linkTo ? 'router-link' : 'div'"
    v-bind="linkTo ? { to: linkTo } : {}"
    class="inline-flex min-w-0 items-center"
    :class="linkTo ? 'no-underline' : ''"
    :aria-label="linkTo ? 'Payroll Digital home' : undefined"
  >
    <img
      :src="logoSrc"
      alt="Payroll Digital"
      class="block shrink-0 object-contain"
      :class="logoClass"
    >
  </component>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  /** full | compact | mark */
  variant: { type: String, default: 'full' },
  /** dark | light | brand — text colors for surrounding chrome */
  theme: { type: String, default: 'dark' },
  size: { type: String, default: 'md' }, // sm | md | lg
  tagline: { type: String, default: 'HR & Payroll Platform' },
  linkTo: { type: [String, Object], default: null },
});

const logoSrc = '/images/payroll-digital-logo.png';

const logoClass = computed(() => {
  const sizes = {
    mark: {
      sm: 'h-9 w-9',
      md: 'h-11 w-11',
      lg: 'h-14 w-14',
    },
    compact: {
      sm: 'h-12 w-12',
      md: 'h-14 w-14',
      lg: 'h-20 w-20',
    },
    full: {
      sm: 'h-14 w-14',
      md: 'h-16 w-16',
      lg: 'h-24 w-24',
    },
  };

  const variant = sizes[props.variant] || sizes.full;
  return variant[props.size] || variant.md;
});
</script>
