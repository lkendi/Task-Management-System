<script setup lang="ts">
import { ref, watch } from 'vue';

interface Project {
    id?: number;
    name: string;
    description: string;
    start_date?: string;
    end_date?: string;
}

const props = defineProps<{ 
    project?: Project | null 
}>();

const emit = defineEmits(['close', 'submit']);

const form = ref<Project>({
    name: '',
    description: '',
    start_date: '',
    end_date: ''
});

const errors = ref<Record<string, string>>({});

watch(() => props.project, (newProject) => {
    if (newProject) {
        form.value = {
            name: newProject.name,
            description: newProject.description,
            start_date: newProject.start_date || '',
            end_date: newProject.end_date || ''
        };
    }
}, { immediate: true });

function submit() {
    errors.value = {};
    let hasError = false;
    if (!form.value.name.trim()) {
        errors.value.name = 'Name is required';
        hasError = true;
    }
    if (!form.value.start_date) {
        errors.value.start_date = 'The start date is required.';
        hasError = true;
    }
    if (!form.value.end_date) {
        errors.value.end_date = 'The end date is required.';
        hasError = true;
    }
    if (form.value.start_date && form.value.end_date) {
        if (form.value.end_date <= form.value.start_date) {
            errors.value.end_date = 'The end date must be after the start date.';
            hasError = true;
        }
    }
    if (hasError) return;
    emit('submit', {
        ...form.value,
        ...(props.project?.id && { id: props.project.id })
    });
}
</script>

<template>
    <form @submit.prevent="submit" class="space-y-5">
        <h2 class="text-xl font-semibold text-foreground pb-2 border-b border-[#e3e3e0] dark:border-[#3E3E3A]">
            {{ project?.id ? 'Edit Project' : 'Create New Project' }}
        </h2>
        
        <div class="space-y-3">
            <div>
                <label class="block text-sm font-medium text-foreground mb-1.5">Name</label>
                <input 
                    v-model="form.name"
                    placeholder="Project name" 
                    required
                    class="w-full px-3.5 py-2.5 text-sm rounded-md border border-[#e3e3e0] bg-[#FDFDFC] dark:bg-[#161615] dark:border-[#3E3E3A] focus:outline-none focus:ring-2 focus:ring-[#1b1b18] dark:focus:ring-[#EDEDEC]"
                    :class="{ 'border-red-500': errors.name }"
                />
                <p v-if="errors.name" class="text-red-500 text-sm mt-1">{{ errors.name }}</p>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-foreground mb-1.5">Description</label>
                <textarea 
                    v-model="form.description"
                    placeholder="Project description" 
                    rows="3"
                    class="w-full px-3.5 py-2.5 text-sm rounded-md border border-[#e3e3e0] bg-[#FDFDFC] dark:bg-[#161615] dark:border-[#3E3E3A] focus:outline-none focus:ring-2 focus:ring-[#1b1b18] dark:focus:ring-[#EDEDEC]"
                />
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1.5">Start Date</label>
                    <input
                        v-model="form.start_date"
                        type="date"
                        class="w-full px-3.5 py-2.5 text-sm rounded-md border border-[#e3e3e0] bg-[#FDFDFC] dark:bg-[#161615] dark:border-[#3E3E3A] focus:outline-none focus:ring-2 focus:ring-[#1b1b18] dark:focus:ring-[#EDEDEC]"
                        :class="{ 'border-red-500': errors.start_date }"
                    />
                    <p v-if="errors.start_date" class="text-red-500 text-sm mt-1">{{ errors.start_date }}</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1.5">End Date</label>
                    <input
                        v-model="form.end_date"
                        type="date"
                        class="w-full px-3.5 py-2.5 text-sm rounded-md border border-[#e3e3e0] bg-[#FDFDFC] dark:bg-[#161615] dark:border-[#3E3E3A] focus:outline-none focus:ring-2 focus:ring-[#1b1b18] dark:focus:ring-[#EDEDEC]"
                        :class="{ 'border-red-500': errors.end_date }"
                    />
                    <p v-if="errors.end_date" class="text-red-500 text-sm mt-1">{{ errors.end_date }}</p>
                </div>
            </div>
        </div>
        
        <div class="pt-3 flex justify-end gap-3">
            <button
                type="button"
                class="px-4 py-2 text-sm rounded-md border border-[#e3e3e0] text-foreground hover:bg-[#f5f5f4] dark:hover:bg-[#2d2d2b] dark:border-[#3E3E3A]"
                @click="$emit('close')"
            >
                Cancel
            </button>
            <button
                type="submit"
                class="px-4 py-2 text-sm rounded-md bg-[#1b1b18] text-white hover:bg-black dark:bg-[#EDEDEC] dark:text-[#1C1C1A] dark:hover:bg-white"
            >
                {{ project?.id ? 'Update' : 'Create' }}
            </button>
        </div>
    </form>
</template> 
