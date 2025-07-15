<template>
  <transition name="modal-fade">
    <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center bg-black/30 backdrop-blur-sm">
      <div class="relative w-full max-w-lg mx-4 sm:mx-0 bg-white rounded-lg border border-[#e3e3e0] shadow-lg dark:bg-[#161615] dark:border-[#3E3E3A] p-0 overflow-hidden">
        <div class="p-6">
          <div class="flex justify-between items-start mb-4">
            <h3 class="text-lg font-semibold text-foreground">
              <slot name="title"></slot>
            </h3>
            <button
              class="p-1 rounded-full hover:bg-[#f5f5f4] dark:hover:bg-[#2d2d2b] text-[#706f6c] hover:text-[#1b1b18] dark:text-[#A1A09A] dark:hover:text-[#EDEDEC] transition"
              @click="$emit('close')"
              aria-label="Close modal"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>
          <div class="py-2">
            <slot />
          </div>
        </div>
        <div class="px-6 py-4 bg-[#fafaf9] dark:bg-[#1d1d1b] border-t border-[#e3e3e0] dark:border-[#3E3E3A] flex justify-end gap-2">
          <slot name="footer"></slot>
        </div>
      </div>
    </div>
  </transition>
</template>

<script setup>
defineProps({ show: Boolean });
</script>

<style scoped>
.modal-fade-enter-active,
.modal-fade-leave-active {
  transition: opacity 0.2s ease;
}
.modal-fade-enter-from,
.modal-fade-leave-to {
  opacity: 0;
}
</style>
