<script setup lang="ts">
import { inject } from 'vue'

const { value } = inject('select') as { value: any }
</script>

<template>
  <span class="truncate">
    <slot :value="value">
      {{ value }}
    </slot>
  </span>
</template>