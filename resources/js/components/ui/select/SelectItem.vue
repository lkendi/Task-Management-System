<script setup lang="ts">
import { inject } from 'vue'

const { value, open } = inject('select') as { value: any; open: any }

const props = defineProps<{
    value: string
}>()

function handleClick() {
    value.value = props.value
    open.value = false
}
</script>

<template>
    <div class="relative flex w-full cursor-default select-none items-center rounded-sm py-1.5 pl-2 pr-8 text-sm outline-none focus:bg-accent focus:text-accent-foreground data-[disabled]:pointer-events-none data-[disabled]:opacity-50"
        @click="handleClick">
        <span class="absolute right-2 flex h-3.5 w-3.5 items-center justify-center">
            <span v-if="value === props.value" class="h-2 w-2 rounded-full bg-current" />
        </span>
        <slot />
    </div>
</template>