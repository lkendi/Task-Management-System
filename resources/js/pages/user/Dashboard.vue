<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { Inertia } from '@inertiajs/inertia';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import Badge from '@/components/ui/badge/Badge.vue';
import Button from '@/components/ui/button/Button.vue';
import Icon from '@/components/Icon.vue';
import { ref, computed } from 'vue';
import TaskDetails from '@/components/ui/tasks/TaskDetails.vue';
import Modal from '@/components/ui/modal/Modal.vue';

interface User {
    id: number;
    name: string;
}

interface Task {
    id: number;
    title: string;
    description: string;
    status: string;
    priority: string;
    due_date?: string;
    project?: {
        id: number;
        name: string;
    } | null;
    assigned_to: User | null;
    created_by: User | null;
    created_at: string;
    is_overdue: boolean;
}

interface TaskStats {
    total?: number;
    pending?: number;
    in_progress?: number;
    completed?: number;
    overdue?: number;
}

interface Stats {
    tasks?: TaskStats;
    users?: {
        total?: number;
        active?: number;
    };
    priority?: {
        high?: number;
        medium?: number;
        low?: number;
    };
}

const props = defineProps<{
    stats?: Stats;
    recentTasks?: Task[];
}>();

const stats = computed(() => props.stats || {});
const recentTasks = computed(() => props.recentTasks || []);

const getStatusColor = (status: string) => {
    switch (status) {
        case 'completed':
            return 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300';
        case 'in_progress':
            return 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300';
        case 'pending':
            return 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300';
        default:
            return 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300';
    }
};

const getPriorityColor = (priority: string) => {
    switch (priority) {
        case 'high':
            return 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300';
        case 'medium':
            return 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-300';
        case 'low':
            return 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300';
        default:
            return 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300';
    }
};

const formatStatus = (status: string) => {
    return status.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase());
};

const getStatusIcon = (status: string) => {
    switch (status) {
        case 'completed': return 'lucide:check-circle';
        case 'in_progress': return 'lucide:play-circle';
        case 'pending': return 'lucide:clock';
        default: return 'lucide:circle';
    }
};

const getPriorityIcon = (priority: string) => {
    switch (priority) {
        case 'high': return 'lucide:alert-triangle';
        case 'medium': return 'lucide:minus-circle';
        case 'low': return 'lucide:check-circle';
        default: return 'lucide:circle';
    }
};

const showModal = ref(false);
const modalTask = ref<Task | null>(null);
const statusUpdate = ref<{ [taskId: number]: string }>({});

function openModal(task: Task) {
    modalTask.value = {
        id: task.id,
        title: task.title,
        description: task.description || '',
        status: task.status,
        priority: task.priority,
        due_date: task.due_date === null ? undefined : task.due_date,
        project: task.project ?? undefined,
        assigned_to: typeof task.assigned_to === 'string'
            ? { id: 0, name: task.assigned_to }
            : task.assigned_to || null,
        created_by: typeof task.created_by === 'string'
            ? { id: 0, name: task.created_by }
            : task.created_by || null,
        created_at: task.created_at,
        is_overdue: task.is_overdue || false
    };
    showModal.value = true;
}

function closeModal() {
    showModal.value = false;
    modalTask.value = null;
}

const computedModalTask = computed(() => {
    if (!modalTask.value) return null;
    return {
        ...modalTask.value,
        project: modalTask.value.project ?? undefined,
    };
});

function setStatus(task: Task, status: string) {
    statusUpdate.value[task.id] = status;
}

function updateStatus(task: Task) {
    const newStatus = statusUpdate.value[task.id] || task.status;
    Inertia.patch(`/tasks/${task.id}`, { status: newStatus });
}
</script>

<template>

    <Head title="Dashboard" />
    <AppLayout>
        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-semibold text-foreground">Welcome back!</h1>
                    <p class="text-muted-foreground">Here's a summary of your tasks.</p>
                </div>
            </div>
            <div class="grid gap-4 grid-cols-1 sm:grid-cols-2 lg:grid-cols-4">
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Total Assigned</CardTitle>
                        <Icon name="lucide:clipboard-list" class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ stats.tasks?.total || 0 }}</div>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="text-xs text-green-600 dark:text-green-400 font-medium">
                                {{ stats.tasks?.completed || 0 }} done
                            </span>
                            <span class="text-xs text-muted-foreground">
                                {{ stats.tasks?.in_progress || 0 }} in progress
                            </span>
                        </div>
                    </CardContent>
                </Card>
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Pending</CardTitle>
                        <Icon name="lucide:clock" class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ stats.tasks?.pending || 0 }}</div>
                    </CardContent>
                </Card>
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Overdue</CardTitle>
                        <Icon name="lucide:alert-triangle" class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold text-red-600 dark:text-red-400">{{ stats.tasks?.overdue || 0 }}
                        </div>
                        <p class="text-xs text-red-600 dark:text-red-400 mt-1">
                            Needs attention
                        </p>
                    </CardContent>
                </Card>
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Completed</CardTitle>
                        <Icon name="lucide:check-circle" class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ stats.tasks?.completed || 0 }}</div>
                    </CardContent>
                </Card>
            </div>
            <Card class="mt-6">
                <CardHeader>
                    <CardTitle>Recent Tasks</CardTitle>
                    <CardDescription>
                        Your latest assigned tasks
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="space-y-3">
                        <div v-if="!recentTasks.length" class="text-center py-8 text-muted-foreground">
                            <Icon name="lucide:clipboard-list" class="h-12 w-12 mx-auto mb-4 opacity-50" />
                            <p>No tasks assigned yet</p>
                        </div>
                        <div v-for="task in recentTasks" :key="task.id"
                            class="flex items-center gap-4 p-3 rounded-lg border border-border hover:bg-muted/50 transition-colors">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center">
                                    <Icon :name="getStatusIcon(task.status)" class="h-5 w-5 text-primary" />
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4 class="font-medium text-sm truncate mb-1">{{ task.title }}</h4>
                                <div class="flex flex-wrap items-center gap-1 mb-1">
                                    <Badge :class="getPriorityColor(task.priority)" class="text-xs">
                                        <Icon :name="getPriorityIcon(task.priority)" class="h-3 w-3 mr-1" />
                                        {{ task.priority }}
                                    </Badge>
                                    <Badge :class="getStatusColor(task.status)" class="text-xs">
                                        {{ formatStatus(task.status) }}
                                    </Badge>
                                </div>
                                <div class="flex items-center gap-3 text-xs text-muted-foreground">
                                    <span v-if="task.due_date" class="flex items-center">
                                        <Icon name="lucide:calendar" class="h-3 w-3 mr-1" />
                                        Due {{ task.due_date }}
                                    </span>
                                </div>
                            </div>
                            <Button variant="outline" size="sm" @click="openModal(task)">View</Button>
                            <select
                                v-model="statusUpdate[task.id]"
                                class="border rounded px-2 py-1 mr-2 bg-white dark:bg-gray-800 text-black dark:text-white"
                                style="min-width: 120px;"
                            >
                                <option value="pending">Pending</option>
                                <option value="in_progress">In Progress</option>
                                <option value="completed">Completed</option>
                            </select>
                            <Button variant="secondary" size="sm" @click="updateStatus(task)">
                                Update Status
                            </Button>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
    <Modal :show="showModal" @close="closeModal">
        <template #title>Task Details</template>
        <TaskDetails v-if="computedModalTask" :task="computedModalTask" />
        <template #footer>
            <Button variant="outline" @click="closeModal">Close</Button>
        </template>
    </Modal>
</template>
