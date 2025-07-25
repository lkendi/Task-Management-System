/* eslint-disable vue/no-deprecated-filter */
<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { PageHeader } from '@/components/ui/page';
import { FilterBar } from '@/components/ui/filters';
import { DataTable, DataTableRow } from '@/components/ui/data-table';
import Badge from '@/components/ui/badge/Badge.vue';
import Icon from '@/components/Icon.vue';
import { useFilters } from '@/composables/useFilters';
import Modal from '@/components/ui/modal/Modal.vue';
import TaskForm from '@/components/ui/tasks/TaskForm.vue';
import TaskDetails from '@/components/ui/tasks/TaskDetails.vue';
import { ref } from 'vue';

interface Task {
    id: number;
    title: string;
    description: string;
    status: string;
    priority: string;
    due_date: string | null;
    project?: {
        id: number;
        name: string;
    } | null;
    assigned_to: User | null;
    created_by: User | null;
    created_at: string;
    is_overdue: boolean;
}

interface User {
    id: number;
    name: string;
}

interface Props {
    tasks: {
        data: Task[];
        current_page: number;
        last_page: number;
        per_page: number;
        total: number;
        links: any[];
    };
    filters: {
        search: string;
        status: string;
        priority: string;
        assigned_to: string;
        due_date: string;
        project: string;
        sort: string;
        direction: 'asc' | 'desc';
    };
    users: User[];
    statuses: string[];
    priorities: string[];
    projects: any[];
}

const props = defineProps<Props>();

const users = props.users ?? [];
const projects = props.projects ?? [];

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Tasks',
        href: '/tasks',
    },
];

const { search, filters, clearFilters } = useFilters(props.filters, '/tasks');

const filterConfigs = [
    {
        key: 'status',
        label: 'Status',
        options: [
            { value: '', label: 'All Statuses' },
            { value: 'pending', label: 'Pending' },
            { value: 'in_progress', label: 'In Progress' },
            { value: 'completed', label: 'Completed' },
        ],
    },
    {
        key: 'priority',
        label: 'Priority',
        options: [
            { value: '', label: 'All Priorities' },
            { value: 'high', label: 'High' },
            { value: 'medium', label: 'Medium' },
            { value: 'low', label: 'Low' },
        ],
    },
    {
        key: 'assigned_to',
        label: 'Assigned To',
        options: [
            { value: '', label: 'All Users' },
            { value: 'unassigned', label: 'Unassigned' },
            ...users.map(user => ({ value: user.id.toString(), label: user.name })),
        ],
    },
    {
        key: 'due_date',
        label: 'Due Date',
        options: [
            { value: '', label: 'All Dates' },
            { value: 'overdue', label: 'Overdue' },
            { value: 'today', label: 'Today' },
            { value: 'week', label: 'This Week' },
            { value: 'month', label: 'This Month' },
        ],
    },
    {
        key: 'project',
        label: 'Project',
        options: [
            { value: '', label: 'All Projects' },
            ...projects.map(project => ({ value: project.id.toString(), label: project.name })),
        ],
    },
];

const sortOptions = [
    { key: 'title', label: 'Title' },
    { key: 'status', label: 'Status' },
    { key: 'priority', label: 'Priority' },
    { key: 'due_date', label: 'Due Date' },
    { key: 'created_at', label: 'Created Date' },
];

const taskActions = [
    { label: 'View Task', href: (task: Task) => `/tasks/${task.id}`, icon: 'eye' },
    { label: 'Edit Task', href: (task: Task) => `/tasks/${task.id}/edit`, icon: 'edit' },
    { label: 'Delete Task', variant: 'destructive' as const, icon: 'trash' },
];

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
        case 'completed':
            return 'check-circle';
        case 'in_progress':
            return 'play-circle';
        case 'pending':
            return 'clock';
        default:
            return 'circle';
    }
};

const getPriorityIcon = (priority: string) => {
    switch (priority) {
        case 'high':
            return 'alert-triangle';
        case 'medium':
            return 'minus-circle';
        case 'low':
            return 'check-circle';
        default:
            return 'circle';
    }
};

const handlePagination = (url: string) => {
    router.get(url);
};

const handleSort = (sort: string) => {
    const direction = filters.value.sort === sort && filters.value.direction === 'asc' ? 'desc' : 'asc';
    filters.value.sort = sort;
    filters.value.direction = direction;
    router.get('/tasks', filters.value, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
};

const showModal = ref(false);
const modalType = ref('');
const modalTask = ref(null);

function openModal(type: string, task: any = null) {
    modalType.value = type;
    modalTask.value = task && typeof task === 'object' ? Object.assign({}, task, {
        project: task.project ?? undefined,
        assigned_to: task.assigned_to ?? null,
        created_by: task.created_by ?? null
    }) : null;
    showModal.value = true;
}

function closeModal() {
    showModal.value = false;
    modalTask.value = null;
}

function handleDelete(task: Task) {
    if (confirm(`Are you sure you want to delete "${task.title}"?`)) {
        router.delete(`/tasks/${task.id}`, {
            onSuccess: () => closeModal()
        });
    }
}

console.log('Tasks data:', props.tasks.data);
</script>

<template>

    <Head title="Tasks" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 sm:gap-6 p-4 sm:p-6">
            <PageHeader title="Tasks" description="Manage and track your tasks" action-label="Create Task"
                action-href="" @action="() => openModal('create')" />

            <FilterBar :search-value="search" :filters="filters" :filter-configs="filterConfigs"
                search-placeholder="Search tasks..." @update:search-value="search = $event"
                @update:filters="filters = $event" @search="search = $event" @clear="clearFilters" />

            <div class="overflow-x-auto">
                <DataTable title="Tasks" :pagination="tasks" empty-message="Try adjusting your search or filters"
                    empty-icon="lucide:clipboard-list" :sort-options="sortOptions" :current-sort="filters.sort"
                    :sort-direction="filters.direction as 'asc' | 'desc'" @pagination="handlePagination"
                    @sort="handleSort">
                    <template #default>
                        <div class="space-y-3">
                            <DataTableRow v-for="task in tasks.data" :key="task.id" :item="task" :actions="taskActions"
                                :show-action-icons="true" :show-badges="false" base-path="/tasks" @action="(action, task) => {
                                    if (action.label === 'Edit' || action.label === 'Edit Task') openModal('edit', task);
                                    else if (action.label === 'View' || action.label === 'View Task') openModal('view', task);
                                    else if (action.label === 'Delete' || action.label === 'Delete Task') handleDelete(task);
                                }">
                                <template #avatar>
                                    <div class="flex-shrink-0">
                                        <div
                                            class="w-8 h-8 sm:w-10 sm:h-10 rounded-full bg-primary/10 flex items-center justify-center">
                                            <Icon :name="getStatusIcon(task.status)"
                                                class="h-4 w-4 sm:h-5 sm:w-5 text-primary" />
                                        </div>
                                    </div>
                                </template>

                                <template #content>
                                    <div class="flex flex-col sm:flex-row sm:items-center gap-1 sm:gap-2 mb-1">
                                        <h4 class="font-medium text-sm truncate">{{ task.title }}</h4>
                                        <div class="flex flex-wrap gap-1">
                                            <Badge :class="getPriorityColor(task.priority)" class="text-xs py-0.5">
                                                <Icon :name="getPriorityIcon(task.priority)" class="h-3 w-3 mr-1" />
                                                <span class="truncate">{{ task.priority }}</span>
                                            </Badge>
                                            <Badge :class="getStatusColor(task.status)" class="text-xs py-0.5">
                                                <span class="truncate">{{ formatStatus(task.status) }}</span>
                                            </Badge>
                                            <Badge v-if="task.is_overdue" variant="destructive" class="text-xs py-0.5">
                                                Overdue
                                            </Badge>
                                        </div>
                                    </div>
                                    <p v-if="task.description"
                                        class="text-sm text-muted-foreground truncate mb-2 hidden sm:block">
                                        {{ task.description }}
                                    </p>
                                    <div
                                        class="flex flex-col sm:flex-row sm:items-center gap-1 sm:gap-3 text-xs text-muted-foreground">
                                        <span v-if="task.project?.id" class="truncate hidden sm:inline">
                                            Project: {{ task.project.name }}
                                        </span>
                                        <span v-if="task.assigned_to && task.assigned_to.name" class="truncate">
                                            {{ task.assigned_to.name }}
                                        </span>
                                        <span v-if="task.due_date" class="truncate">
                                            Due {{ new Date(task.due_date).toLocaleDateString() }}
                                        </span>
                                    </div>
                                </template>
                            </DataTableRow>
                        </div>
                    </template>
                </DataTable>
            </div>
        </div>
    </AppLayout>

    <Modal :show="showModal" @close="closeModal">
        <template v-if="modalType === 'create'">
            <TaskForm :users="users" :projects="projects" :statuses="props.statuses" :priorities="props.priorities"
                @submitted="closeModal" />
        </template>
        <template v-else-if="modalType === 'edit' && modalTask">
            <TaskForm :task="modalTask" :users="users" :projects="projects" :statuses="props.statuses"
                :priorities="props.priorities" @submitted="closeModal" />
        </template>
        <template v-else-if="modalType === 'view' && modalTask">
            <TaskDetails :task="modalTask" :projects="projects" />
        </template>
    </Modal>
</template>
