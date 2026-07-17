<template>
  <component
    :is="linkTo ? 'router-link' : 'div'"
    v-bind="linkTo ? { to: linkTo } : {}"
    class="inline-flex items-center gap-3 min-w-0"
    :class="linkTo ? 'no-underline' : ''"
  >
    <!-- Mark -->
    <span
      class="relative flex shrink-0 items-center justify-center overflow-hidden"
      :class="markBoxClass"
      aria-hidden="true"
    >
      <svg :class="markSvgClass" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
        <defs>
          <linearGradient :id="gradId" x1="8" y1="4" x2="34" y2="36" gradientUnits="userSpaceOnUse">
            <stop stop-color="#FF5B60" />
            <stop offset="0.55" stop-color="#FF7A5C" />
            <stop offset="1" stop-color="#FFC107" />
          </linearGradient>
        </defs>
        <!-- Soft plate -->
        <rect x="2" y="2" width="36" height="36" rx="10" :fill="plateFill" />
        <!-- Abstract P / ledger mark -->
        <path
          d="M12.5 28.5V11.5c0-1.1.9-2 2-2H22c4.05 0 7 2.7 7 6.35 0 3.55-2.85 6.15-6.7 6.15h-5.3v6.5c0 .55-.45 1-1 1h-2.5c-.55 0-1-.45-1-1z"
          :fill="`url(#${gradId})`"
        />
        <path
          d="M17.5 18.2h4.1c1.85 0 3.05-1.05 3.05-2.55S23.45 13.2 21.6 13.2h-4.1v5z"
          :fill="innerCut"
        />
        <!-- Accent spark / digital node -->
        <circle cx="29.5" cy="11" r="2.2" fill="#FFC107" />
        <circle cx="29.5" cy="11" r="1" fill="#1e1433" opacity="0.35" />
      </svg>
    </span>

    <!-- Wordmark -->
    <span v-if="showWordmark" class="min-w-0 flex flex-col leading-tight">
      <span class="font-bold tracking-tight truncate" :class="titleClass">
        Payroll Digital
      </span>
      <span v-if="showTagline" class="truncate font-medium" :class="taglineClass">
        {{ tagline }}
      </span>
    </span>
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

const uid = Math.random().toString(36).slice(2, 9);
const gradId = `pd-grad-${uid}`;

const showWordmark = computed(() => props.variant !== 'mark');
const showTagline = computed(() => props.variant === 'full');

const markBoxClass = computed(() => {
  const sizes = {
    sm: 'h-8 w-8 rounded-lg',
    md: 'h-10 w-10 rounded-xl',
    lg: 'h-12 w-12 rounded-xl',
  };
  return sizes[props.size] || sizes.md;
});

const markSvgClass = computed(() => 'h-full w-full');

const plateFill = computed(() => {
  if (props.theme === 'brand') return 'rgba(255,255,255,0.12)';
  if (props.theme === 'light') return '#1e1433';
  return '#1e1433';
});

const innerCut = computed(() => {
  if (props.theme === 'brand') return '#1e1433';
  return '#1e1433';
});

const titleClass = computed(() => {
  const size = {
    sm: 'text-sm',
    md: 'text-base sm:text-lg',
    lg: 'text-xl sm:text-2xl',
  }[props.size] || 'text-lg';

  if (props.theme === 'brand' || props.theme === 'dark') {
    return `${size} text-gold`;
  }
  return `${size} text-brand`;
});

const taglineClass = computed(() => {
  const size = props.size === 'lg' ? 'text-xs' : 'text-[10px] sm:text-xs';
  if (props.theme === 'brand' || props.theme === 'dark') {
    return `${size} text-white/50`;
  }
  return `${size} text-ink-muted`;
});
</script>
