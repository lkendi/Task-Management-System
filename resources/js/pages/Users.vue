<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { PageHeader } from '@/components/ui/page';
import { FilterBar } from '@/components/ui/filters';
import { DataTable, DataTableRow } from '@/components/ui/data-table';
import Badge from '@/components/ui/badge/Badge.vue';
import { useFilters } from '@/composables/useFilters';

interface User {
    id: number;
    name: string;
    email: string;
    email_verified_at: string | null;
    created_at: string;
    roles: string[];
    permissions: string[];
    assigned_tasks_count: number;
    created_tasks_count: number;
}

interface Props {
    users: {
        data: User[];
        current_page: number;
        last_page: number;
        per_page: number;
        total: number;
        links: any[];
    };
    filters: {
        search: string;
        role: string;
        verified: string;
        sort?: string;
        direction?: 'asc' | 'desc';
    };
    roles: string[];
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Users',
        href: '/users',
    },
];

const { search, filters, clearFilters } = useFilters(props.filters, '/users');

const filterConfigs = [
    {
        key: 'role',
        label: 'Role',
        options: props.roles.map(role => ({ value: role, label: role })),
    },
    {
        key: 'verified',
        label: 'Verification Status',
        options: [
            { value: 'true', label: 'Verified' },
            { value: 'false', label: 'Unverified' },
        ],
    },
];

const sortOptions = [
    { key: 'name', label: 'Name' },
    { key: 'email', label: 'Email' },
    { key: 'created_at', label: 'Created Date' },
    { key: 'assigned_tasks_count', label: 'Assigned Tasks' },
    { key: 'created_tasks_count', label: 'Created Tasks' },
];

const userActions = [
    { label: 'View Profile', href: (user: User) => `/users/${user.id}`, icon: 'eye' },
    { label: 'Edit User', href: (user: User) => `/users/${user.id}/edit`, icon: 'edit' },
    { label: 'Delete User', variant: 'destructive' as const, icon: 'trash' },
];

const getVerificationStatus = (verifiedAt: string | null) => {
    return verifiedAt ? 'Verified' : 'Unverified';
};

const getVerificationColor = (verifiedAt: string | null) => {
    return verifiedAt 
        ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300'
        : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300';
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
    router.get('/users', params, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
};

const handleUserAction = (action: any, user: User) => {
    if (action.href) {
        const href = typeof action.href === 'function' ? action.href(user) : action.href;
        router.get(href);
    } else if (action.label === 'Delete' || action.label === 'Delete User') {
        if (confirm(`Are you sure you want to delete ${user.name}?`)) {
            router.delete(`/users/${user.id}`);
        }
    } else if (action.label === 'View') {
        router.get(`/users/${user.id}`);
    } else if (action.label === 'Edit') {
        router.get(`/users/${user.id}/edit`);
    }
};
</script>

<template>
    <Head title="Users - Task Management System" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <PageHeader
                title="Users"
                description="Manage system users and their permissions"
                action-label="Add User"
                action-href="/users/create"
            />

            <FilterBar
                :search-value="search"
                :filters="filters"
                :filter-configs="filterConfigs"
                search-placeholder="Search by name or email..."
                @update:search-value="search = $event"
                @update:filters="filters = $event"
                @search="search = $event"
                @clear="clearFilters"
            />

            <DataTable
                title="Users"
                :pagination="users"
                empty-message="Try adjusting your search or filters"
                empty-icon="lucide:users"
                :sort-options="sortOptions"
                :current-sort="filters.sort || ''"
                :sort-direction="(filters.direction as 'asc' | 'desc') || 'asc'"
                @pagination="handlePagination"
                @sort="handleSort"
            >
                <template #default>
                    <div class="space-y-4">
                        <DataTableRow
                            v-for="user in users.data"
                            :key="user.id"
                            :item="user"
                            :actions="userActions"
                            :show-action-icons="true"
                            :show-badges="true"
                            base-path="/users"
                            @action="handleUserAction"
                        >
                            <template #avatar>
                                <div class="flex-shrink-0">
                                    <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center">
                                        <span class="text-sm font-medium text-primary">
                                            {{ user.name.charAt(0).toUpperCase() }}
                                        </span>
                                    </div>
                                </div>
                            </template>

                            <template #content>
                                <div class="flex items-center gap-2 mb-1">
                                    <h4 class="font-medium text-sm truncate">{{ user.name }}</h4>
                                    <Badge :class="getVerificationColor(user.email_verified_at)" class="text-xs">
                                        {{ getVerificationStatus(user.email_verified_at) }}
                                    </Badge>
                                </div>
                                <p class="text-sm text-muted-foreground truncate">{{ user.email }}</p>
                                <div class="flex items-center gap-4 mt-2 text-xs text-muted-foreground">
                                    <span>Joined {{ user.created_at }}</span>
                                    <span>{{ user.assigned_tasks_count }} assigned tasks</span>
                                    <span>{{ user.created_tasks_count }} created tasks</span>
                                </div>
                            </template>

                            <template #badges>
                                <div class="flex gap-1">
                                    <Badge 
                                        v-for="role in user.roles" 
                                        :key="role"
                                        variant="secondary"
                                        class="text-xs"
                                    >
                                        {{ role }}
                                    </Badge>
                                </div>
                            </template>
                        </DataTableRow>
                    </div>
                </template>
            </DataTable>
        </div>
    </AppLayout>
</template>
