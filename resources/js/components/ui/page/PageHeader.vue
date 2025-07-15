<script setup lang="ts">
import { Button } from '@/components/ui/button';

interface Props {
    title: string;
    description?: string;
    actionLabel?: string;
    actionHref?: string;
    actionMethod?: 'get' | 'post' | 'put' | 'delete';
}

const props = withDefaults(defineProps<Props>(), {
    actionLabel: 'Add New',
    actionMethod: 'get',
});

const emit = defineEmits<{
    'action': [];
}>();

const handleAction = () => {
    emit('action');
};
</script>

<template>
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-foreground">{{ title }}</h1>
            <p v-if="description" class="text-muted-foreground">{{ description }}</p>
        </div>
        
        <Button v-if="actionLabel" as-child>
            <a v-if="actionHref" :href="actionHref">
                {{ actionLabel }}
            </a>
            <button v-else @click="handleAction">
                {{ actionLabel }}
            </button>
        </Button>
    </div>
</template> 