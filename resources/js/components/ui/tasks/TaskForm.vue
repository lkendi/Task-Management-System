<script setup lang="ts">
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps<{ 
  task?: any;
  projects: Array<{ id: number, name: string }>;
  users: Array<{ id: number, name: string }>;
}>();

const emit = defineEmits(['submitted']);

const form = ref({
  title: props.task?.title || '',
  description: props.task?.description || '',
  status: props.task?.status || 'pending',
  priority: props.task?.priority || 'medium',
  due_date: props.task?.due_date || '',
  project_id: props.task?.project_id || '',
  assigned_to: props.task?.assigned_to?.id || '',
});

function submit() {
  const payload = {
    ...form.value,
    assigned_to: form.value.assigned_to && typeof form.value.assigned_to === 'object'
      ? form.value.assigned_to.id
      : form.value.assigned_to || null,
  };
  if (props.task?.id) {
    router.put(`/tasks/${props.task.id}`, payload, {
      onSuccess: () => emit('submitted')
    });
  } else {
    router.post('/tasks', payload, {
      onSuccess: () => emit('submitted')
    });
  }
}
</script>

<template>
  <form @submit.prevent="submit" class="space-y-5">
    <h2 class="text-xl font-semibold text-foreground pb-2 border-b border-[#e3e3e0] dark:border-[#3E3E3A]">
      {{ props.task ? 'Edit Task' : 'Create New Task' }}
    </h2>
    
    <div class="space-y-3">
      <div>
        <label class="block text-sm font-medium text-foreground mb-1.5">Project</label>
        <select 
          v-model="form.project_id"
          required
          class="w-full px-3.5 py-2.5 text-sm rounded-md border border-[#e3e3e0] bg-[#FDFDFC] dark:bg-[#161615] dark:border-[#3E3E3A] focus:outline-none focus:ring-2 focus:ring-[#1b1b18] dark:focus:ring-[#EDEDEC]"
        >
          <option value="">Select Project</option>
          <option 
            v-for="project in projects" 
            :key="project.id" 
            :value="project.id"
          >
            {{ project.name }}
          </option>
        </select>
      </div>

      <div>
        <label class="block text-sm font-medium text-foreground mb-1.5">Title</label>
        <input
          v-model="form.title"
          placeholder="Task title"
          required
          class="w-full px-3.5 py-2.5 text-sm rounded-md border border-[#e3e3e0] bg-[#FDFDFC] dark:bg-[#161615] dark:border-[#3E3E3A] focus:outline-none focus:ring-2 focus:ring-[#1b1b18] dark:focus:ring-[#EDEDEC]"
        />
      </div>
      
      <div>
        <label class="block text-sm font-medium text-foreground mb-1.5">Description</label>
        <textarea
          v-model="form.description"
          placeholder="Task description"
          rows="3"
          class="w-full px-3.5 py-2.5 text-sm rounded-md border border-[#e3e3e0] bg-[#FDFDFC] dark:bg-[#161615] dark:border-[#3E3E3A] focus:outline-none focus:ring-2 focus:ring-[#1b1b18] dark:focus:ring-[#EDEDEC]"
        />
      </div>
      
      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium text-foreground mb-1.5">Status</label>
          <select 
            v-model="form.status" 
            class="w-full px-3.5 py-2.5 text-sm rounded-md border border-[#e3e3e0] bg-[#FDFDFC] dark:bg-[#161615] dark:border-[#3E3E3A] focus:outline-none focus:ring-2 focus:ring-[#1b1b18] dark:focus:ring-[#EDEDEC]"
          >
            <option value="pending">Pending</option>
            <option value="in_progress">In Progress</option>
            <option value="completed">Completed</option>
          </select>
        </div>
        
        <div>
          <label class="block text-sm font-medium text-foreground mb-1.5">Priority</label>
          <select 
            v-model="form.priority" 
            class="w-full px-3.5 py-2.5 text-sm rounded-md border border-[#e3e3e0] bg-[#FDFDFC] dark:bg-[#161615] dark:border-[#3E3E3A] focus:outline-none focus:ring-2 focus:ring-[#1b1b18] dark:focus:ring-[#EDEDEC]"
          >
            <option value="high">High</option>
            <option value="medium">Medium</option>
            <option value="low">Low</option>
          </select>
        </div>
      </div>
      
      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium text-foreground mb-1.5">Due Date</label>
          <input
            v-model="form.due_date"
            type="date"
            class="w-full px-3.5 py-2.5 text-sm rounded-md border border-[#e3e3e0] bg-[#FDFDFC] dark:bg-[#161615] dark:border-[#3E3E3A] focus:outline-none focus:ring-2 focus:ring-[#1b1b18] dark:focus:ring-[#EDEDEC]"
          />
        </div>
        
        <div>
          <label class="block text-sm font-medium text-foreground mb-1.5">Assigned To</label>
          <select 
            v-model="form.assigned_to"
            class="w-full px-3.5 py-2.5 text-sm rounded-md border border-[#e3e3e0] bg-[#FDFDFC] dark:bg-[#161615] dark:border-[#3E3E3A] focus:outline-none focus:ring-2 focus:ring-[#1b1b18] dark:focus:ring-[#EDEDEC]"
          >
            <option value="">Unassigned</option>
            <option 
              v-for="user in users" 
              :key="user.id" 
              :value="user.id"
            >
              {{ user.name }}
            </option>
          </select>
        </div>
      </div>
    </div>
    
    <div class="pt-3 flex justify-end gap-3">
      <button
        type="button"
        class="px-4 py-2 text-sm rounded-md border border-[#e3e3e0] text-foreground hover:bg-[#f5f5f4] dark:hover:bg-[#2d2d2b] dark:border-[#3E3E3A]"
        @click="$emit('submitted')"
      >
        Cancel
      </button>
      <button
        type="submit"
        class="px-4 py-2 text-sm rounded-md bg-[#1b1b18] text-white hover:bg-black dark:bg-[#EDEDEC] dark:text-[#1C1C1A] dark:hover:bg-white"
      >
        Save Task
      </button>
    </div>
  </form>
</template>
