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

interface Task {
    id: number;
    title: string;
    description: string;
    status: string;
    priority: string;
    due_date: string | null;
    assigned_to: string | null;
    created_by: string | null;
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
        sort?: string;
        direction?: 'asc' | 'desc';
    };
    users: User[];
}

const props = defineProps<Props>();

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
            { value: 'pending', label: 'Pending' },
            { value: 'in_progress', label: 'In Progress' },
            { value: 'completed', label: 'Completed' },
        ],
    },
    {
        key: 'priority',
        label: 'Priority',
        options: [
            { value: 'high', label: 'High' },
            { value: 'medium', label: 'Medium' },
            { value: 'low', label: 'Low' },
        ],
    },
    {
        key: 'assigned_to',
        label: 'Assigned To',
        options: props.users.map(user => ({ value: user.id.toString(), label: user.name })),
    },
    {
        key: 'due_date',
        label: 'Due Date',
        options: [
            { value: 'overdue', label: 'Overdue' },
            { value: 'today', label: 'Today' },
            { value: 'week', label: 'This Week' },
            { value: 'month', label: 'This Month' },
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

const handleSort = (sort: string, direction: 'asc' | 'desc') => {
    const params = {
        ...filters,
        sort,
        direction,
    };
    router.get('/tasks', params, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
};

const handleTaskAction = (action: any, task: Task) => {
    if (action.href) {
        const href = typeof action.href === 'function' ? action.href(task) : action.href;
        router.get(href);
    } else if (action.label === 'Delete' || action.label === 'Delete Task') {
        if (confirm(`Are you sure you want to delete "${task.title}"?`)) {
            router.delete(`/tasks/${task.id}`);
        }
    } else if (action.label === 'View') {
        router.get(`/tasks/${task.id}`);
    } else if (action.label === 'Edit') {
        router.get(`/tasks/${task.id}/edit`);
    }
};
</script>

<template>
    <Head title="Tasks - Task Management System" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <PageHeader
                title="Tasks"
                description="Manage and track your tasks"
                action-label="Create Task"
                action-href="/tasks/create"
            />

            <FilterBar
                :search-value="search"
                :filters="filters"
                :filter-configs="filterConfigs"
                search-placeholder="Search tasks..."
                @update:search-value="search = $event"
                @update:filters="filters = $event"
                @search="search = $event"
                @clear="clearFilters"
            />

            <DataTable
                title="Tasks"
                :pagination="tasks"
                empty-message="Try adjusting your search or filters"
                empty-icon="lucide:clipboard-list"
                :sort-options="sortOptions"
                :current-sort="filters.sort || ''"
                :sort-direction="(filters.direction as 'asc' | 'desc') || 'asc'"
                @pagination="handlePagination"
                @sort="handleSort"
            >
                <template #default>
                    <div class="space-y-4">
                        <DataTableRow
                            v-for="task in tasks.data"
                            :key="task.id"
                            :item="task"
                            :actions="taskActions"
                            :show-action-icons="true"
                            :show-badges="false"
                            base-path="/tasks"
                            @action="handleTaskAction"
                        >
                            <template #avatar>
                                <div class="flex-shrink-0">
                                    <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center">
                                        <Icon :name="getStatusIcon(task.status)" class="h-5 w-5 text-primary" />
                                    </div>
                                </div>
                            </template>

                            <template #content>
                                <div class="flex items-center gap-2 mb-1">
                                    <h4 class="font-medium text-sm truncate">{{ task.title }}</h4>
                                    <Badge :class="getPriorityColor(task.priority)" class="text-xs">
                                        <Icon :name="getPriorityIcon(task.priority)" class="h-3 w-3 mr-1" />
                                        {{ task.priority }}
                                    </Badge>
                                    <Badge :class="getStatusColor(task.status)" class="text-xs">
                                        {{ formatStatus(task.status) }}
                                    </Badge>
                                    <Badge v-if="task.is_overdue" variant="destructive" class="text-xs">
                                        Overdue
                                    </Badge>
                                </div>
                                <p v-if="task.description" class="text-sm text-muted-foreground truncate mb-2">
                                    {{ task.description }}
                                </p>
                                <div class="flex items-center gap-4 text-xs text-muted-foreground">
                                    <span v-if="task.assigned_to">
                                        Assigned to {{ task.assigned_to }}
                                    </span>
                                    <span v-if="task.created_by">
                                        Created by {{ task.created_by }}
                                    </span>
                                    <span v-if="task.due_date">
                                        Due {{ task.due_date }}
                                    </span>
                                    <span>Created {{ task.created_at }}</span>
                                </div>
                            </template>
                        </DataTableRow>
                    </div>
                </template>
            </DataTable>
        </div>
    </AppLayout>
</template>
