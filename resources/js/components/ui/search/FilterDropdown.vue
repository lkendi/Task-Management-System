<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import Icon from '@/components/Icon.vue';
import { computed } from 'vue';
import { 
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';

interface FilterOption {
    value: string;
    label: string;
}

interface Props {
    modelValue: string;
    options: FilterOption[];
    label: string;
    placeholder?: string;
    showAllOption?: boolean;
    allOptionLabel?: string;
}

const props = withDefaults(defineProps<Props>(), {
    placeholder: 'Select...',
    showAllOption: true,
    allOptionLabel: 'All',
});

const emit = defineEmits<{
    'update:modelValue': [value: string];
}>();

const selectedOption = computed(() => {
    if (!props.modelValue) return props.allOptionLabel;
    return props.options.find(option => option.value === props.modelValue)?.label || props.allOptionLabel;
});

const handleSelect = (value: string) => {
    emit('update:modelValue', value);
};
</script>

<template>
    <div class="space-y-2">
        <Label>{{ label }}</Label>
        <DropdownMenu>
            <DropdownMenuTrigger as-child>
                <Button variant="outline" class="w-full justify-between">
                    <span class="truncate">{{ selectedOption }}</span>
                    <Icon name="chevron-down" class="h-4 w-4 ml-2 flex-shrink-0" />
                </Button>
            </DropdownMenuTrigger>
            <DropdownMenuContent class="w-[var(--radix-dropdown-menu-trigger-width)]">
                <DropdownMenuItem v-if="showAllOption" @click="handleSelect('')">
                    {{ allOptionLabel }}
                </DropdownMenuItem>
                <DropdownMenuSeparator v-if="showAllOption" />
                <DropdownMenuItem 
                    v-for="option in options" 
                    :key="option.value"
                    @click="handleSelect(option.value)"
                >
                    {{ option.label }}
                </DropdownMenuItem>
            </DropdownMenuContent>
        </DropdownMenu>
    </div>
</template> 