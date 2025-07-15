<script setup lang="ts">
import Icon from '@/components/Icon.vue';

interface Project {
    id: number;
    name: string;
    description: string;
    start_date?: string;
    end_date?: string;
}

const props = defineProps<{ 
    project: Project | null 
}>();

const emit = defineEmits(['close']);

function formatDate(dateString: string) {
    if (!dateString) return 'Not set';
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
}
</script>

<template>
    <div v-if="project" class="space-y-5">
        <div class="pb-3 border-b border-[#e3e3e0] dark:border-[#3E3E3A]">
            <h2 class="text-xl font-semibold text-foreground">Project Details</h2>
            <p class="text-sm text-muted-foreground mt-1">Complete information about this project</p>
        </div>
        
        <div class="space-y-4">
            <div>
                <h3 class="font-semibold text-foreground mb-2">{{ project.name }}</h3>
                <p class="text-sm text-muted-foreground">{{ project.description || 'No description provided' }}</p>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-1">
                    <p class="text-xs text-muted-foreground">Start Date</p>
                    <div class="flex items-center gap-1.5">
                        <Icon name="lucide:calendar" class="h-4 w-4 text-muted-foreground" />
                        <p class="text-sm text-foreground">
                            {{ formatDate(project.start_date ?? '') }}
                        </p>
                    </div>
                </div>
                <div class="space-y-1">
                    <p class="text-xs text-muted-foreground">End Date</p>
                    <div class="flex items-center gap-1.5">
                        <Icon name="lucide:calendar" class="h-4 w-4 text-muted-foreground" />
                        <p class="text-sm text-foreground">
                            {{ formatDate(project.end_date ?? '') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="pt-3 flex justify-end">
            <button
                @click="$emit('close')"
                class="px-4 py-2 text-sm rounded-md border border-[#e3e3e0] text-foreground hover:bg-[#f5f5f4] dark:hover:bg-[#2d2d2b] dark:border-[#3E3E3A]"
            >
                Close
            </button>
        </div>
    </div>
</template>
