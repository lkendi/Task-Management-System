<script setup lang="ts">
import { ref, provide, computed } from 'vue'

const props = defineProps<{
  modelValue?: string
}>()

const emit = defineEmits(['update:modelValue'])

const value = computed({
  get: () => props.modelValue,
  set: (val) => emit('update:modelValue', val)
})

const open = ref(false)

provide('select', {
  value,
  open,
  setOpen: (val: boolean) => { open.value = val }
})
</script>

<template>
  <div class="relative">
    <slot />
  </div>
</template>
