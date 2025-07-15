<script setup lang="ts">
import { onClickOutside } from '@vueuse/core'
import { inject } from 'vue'

const { open, triggerRef, contentRef } = inject('select') as {
  open: any;
  triggerRef: any;
  contentRef: any;
};

onClickOutside(contentRef, () => {
    open.value = false
})
</script>

<template>
    <div v-if="open" ref="contentRef"
        class="absolute z-50 min-w-[8rem] overflow-hidden rounded-md border bg-popover text-popover-foreground shadow-md animate-in fade-in-80"
        :style="{
            top: `${triggerRef?.offsetHeight + 4}px`,
            width: `${triggerRef?.offsetWidth}px`
        }">
        <div class="p-1">
            <slot />
        </div>
    </div>
</template>