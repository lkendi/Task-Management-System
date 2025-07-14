<script setup lang="ts">
import { computed } from 'vue';

interface Props {
    variant?: 'default' | 'secondary' | 'destructive' | 'outline';
    class?: string;
}

const props = withDefaults(defineProps<Props>(), {
    variant: 'default',
});

const classes = computed(() => {
    const baseClasses = 'inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset';
    
    const variantClasses = {
        default: 'bg-primary text-primary-foreground ring-primary/20',
        secondary: 'bg-secondary text-secondary-foreground ring-secondary/20',
        destructive: 'bg-destructive text-destructive-foreground ring-destructive/20',
        outline: 'text-foreground ring-border',
    };
    
    return `${baseClasses} ${variantClasses[props.variant]} ${props.class || ''}`;
});
</script>

<template>
    <span :class="classes">
        <slot />
    </span>
</template> 