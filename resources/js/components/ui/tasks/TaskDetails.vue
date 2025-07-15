<script setup lang="ts">
import Badge from '@/components/ui/badge/Badge.vue';
import Icon from '@/components/Icon.vue';

const props = defineProps<{ 
  task: {
    title: string;
    description?: string;
    status: string;
    priority: string;
    due_date?: string;
    project?: {
      id: number;
      name: string;
    };
    assigned_to?: {
      id: number;
      name: string;
    } | null;
    created_by?: {
      id: number;
      name: string;
    } | null;
    created_at: string;
  } 
}>();

const getStatusColor = (status: string) => {
  switch (status) {
    case 'completed': return 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300';
    case 'in_progress': return 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300';
    case 'pending': return 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300';
    default: return 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300';
  }
};

const getPriorityColor = (priority: string) => {
  switch (priority) {
    case 'high': return 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300';
    case 'medium': return 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-300';
    case 'low': return 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300';
    default: return 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300';
  }
};

const formatDate = (dateString: string) => {
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  });
};
</script>

<template>
  <div class="space-y-5">
    <div class="pb-3 border-b border-[#e3e3e0] dark:border-[#3E3E3A]">
      <h2 class="text-xl font-semibold text-foreground">Task Details</h2>
      <p class="text-sm text-muted-foreground mt-1">Complete information about this task</p>
    </div>
    
    <div class="space-y-4">
      <div>
        <h3 class="font-semibold text-foreground mb-2">{{ task.title }}</h3>
        <p class="text-sm text-muted-foreground">{{ task.description || 'No description provided' }}</p>
      </div>
      
      <div class="grid grid-cols-2 gap-4">
        <div class="space-y-1">
          <p class="text-xs text-muted-foreground">Project</p>
          <p class="text-sm text-foreground">
            {{ task.project?.name || 'No project' }}
          </p>
        </div>
        
        <div class="space-y-1">
          <p class="text-xs text-muted-foreground">Status</p>
          <Badge :class="getStatusColor(task.status)" class="text-sm">
            {{ task.status.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase()) }}
          </Badge>
        </div>
      </div>
      
      <div class="grid grid-cols-2 gap-4">
        <div class="space-y-1">
          <p class="text-xs text-muted-foreground">Priority</p>
          <Badge :class="getPriorityColor(task.priority)" class="text-sm">
            {{ task.priority.charAt(0).toUpperCase() + task.priority.slice(1) }}
          </Badge>
        </div>
        
        <div class="space-y-1">
          <p class="text-xs text-muted-foreground">Assigned To</p>
          <p class="text-sm text-foreground">
            {{ task.assigned_to?.name || 'Unassigned' }}
          </p>
        </div>
      </div>
      
      <div class="grid grid-cols-2 gap-4">
        <div class="space-y-1">
          <p class="text-xs text-muted-foreground">Created By</p>
          <p class="text-sm text-foreground">
            {{ task.created_by?.name || 'System' }}
          </p>
        </div>
        
        <div class="space-y-1">
          <p class="text-xs text-muted-foreground">Due Date</p>
          <div class="flex items-center gap-1.5">
            <Icon name="lucide:calendar" class="h-4 w-4 text-muted-foreground" />
            <p class="text-sm text-foreground">
              {{ task.due_date ? formatDate(task.due_date) : 'No due date' }}
            </p>
          </div>
        </div>
      </div>
      
      <div>
        <p class="text-xs text-muted-foreground">Created At</p>
        <div class="flex items-center gap-1.5">
          <Icon name="lucide:clock" class="h-4 w-4 text-muted-foreground" />
          <p class="text-sm text-foreground">{{ formatDate(task.created_at) }}</p>
        </div>
      </div>
    </div>
  </div>
</template>
