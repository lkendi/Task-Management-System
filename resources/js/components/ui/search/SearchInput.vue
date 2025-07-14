<script setup lang="ts">
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import Icon from '@/components/Icon.vue';
import { ref, watch } from 'vue';

interface Props {
    modelValue: string;
    placeholder?: string;
    label?: string;
    debounceMs?: number;
}

const props = withDefaults(defineProps<Props>(), {
    placeholder: 'Search...',
    label: 'Search',
    debounceMs: 300,
});

const emit = defineEmits<{
    'update:modelValue': [value: string];
    'search': [value: string];
}>();

const searchValue = ref(props.modelValue);

let searchTimeout: ReturnType<typeof setTimeout>;

watch(searchValue, (newValue) => {
    emit('update:modelValue', newValue);
    
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        emit('search', newValue);
    }, props.debounceMs);
});

watch(() => props.modelValue, (newValue) => {
    searchValue.value = newValue;
});
</script>

<template>
    <div class="space-y-2">
        <Label v-if="label" :for="`search-${Math.random()}`">{{ label }}</Label>
        <div class="relative">
            <Icon 
                name="search" 
                class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" 
            />
            <Input
                :id="`search-${Math.random()}`"
                v-model="searchValue"
                :placeholder="placeholder"
                class="pl-10"
            />
        </div>
    </div>
</template> 