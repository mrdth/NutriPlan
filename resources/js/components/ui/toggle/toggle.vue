<template>
  <Root
    :pressed="pressed"
    class="inline-flex items-center justify-center whitespace-nowrap rounded-md font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 data-[state=on]:bg-accent data-[state=on]:text-accent-foreground h-10 px-4 py-2 text-sm"
    :class="className"
    v-bind="$attrs"
    @update:pressed="$emit('update:pressed', $event)"
  >
    <slot />
  </Root>
</template>

<script setup lang="ts">
import { Toggle as Root } from 'radix-vue';
import { cn } from '@/lib/utils';
import { computed } from 'vue';

const props = defineProps<{
  pressed?: boolean;
  variant?: 'default' | 'outline';
  size?: 'default' | 'sm' | 'lg';
  className?: string;
}>();

const className = computed(() => {
  return cn(
    props.variant === 'outline' && 'border border-input bg-transparent hover:bg-accent hover:text-accent-foreground',
    props.size === 'sm' && 'h-9 px-3',
    props.size === 'lg' && 'h-11 px-5',
    props.className,
  );
});

defineEmits<{
  'update:pressed': [value: boolean];
}>();
</script> 