/* eslint-disable vue/no-deprecated-filter */
<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router, usePage } from '@inertiajs/vue3';
import { PageHeader } from '@/components/ui/page';
import { FilterBar } from '@/components/ui/filters';
import { DataTable, DataTableRow } from '@/components/ui/data-table';
import Badge from '@/components/ui/badge/Badge.vue';
import { useFilters } from '@/composables/useFilters';
import Modal from '@/components/ui/modal/Modal.vue';
import UserForm from '@/components/ui/users/UserForm.vue';
import UserDetails from '@/components/ui/users/UserDetails.vue';
import { ref } from 'vue';

interface User {
    id: number;
    name: string;
    email: string;
    email_verified_at: string | null;
    created_at: string;
    role: string;
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
        sort: string;
        direction: 'asc' | 'desc';
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

function capitalize(str: string) {
    return str.charAt(0).toUpperCase() + str.slice(1);
}

const filterConfigs = [
    {
        key: 'role',
        label: 'Role',
        options: [
            ...props.roles.map(role => ({ value: role, label: capitalize(role) })),
        ],
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
    { label: 'View User', href: (user: User) => `/users/${user.id}`, icon: 'eye' },
    { label: 'Edit User', href: (user: User) => `/users/${user.id}/edit`, icon: 'edit' },
    { label: 'Delete User', variant: 'destructive' as const, icon: 'trash' },
];

const getVerificationColor = (verifiedAt: string | null) => {
    return verifiedAt
        ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300'
        : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300';
};

const handlePagination = (url: string) => {
    router.get(url);
};

const handleSort = (sort: string) => {
    const direction = filters.value.sort === sort && filters.value.direction === 'asc' ? 'desc' : 'asc';
    filters.value.sort = sort;
    filters.value.direction = direction;
    router.get('/users', filters.value, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
};

const showModal = ref(false);
const modalType = ref('');
const modalUser = ref(null);

const page = usePage();
const currentUserId = page.props.auth.user.id;

function openModal(type: string, user = null) {
    modalType.value = type;
    modalUser.value = user;
    showModal.value = true;
}

function closeModal() {
    showModal.value = false;
    modalUser.value = null;
}

function handleDelete(user: User) {
    if (confirm(`Are you sure you want to delete "${user.name}"?`)) {
        router.delete(`/users/${user.id}`, {
            onSuccess: () => closeModal()
        });
    }
}
</script>

<template>

    <Head title="Users" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 sm:gap-6 p-4 sm:p-6">
            <PageHeader title="Users" description="Manage system users and their permissions" action-label="Create User"
                action-href="" @action="() => openModal('create')" />

            <FilterBar :search-value="search" :filters="filters" :filter-configs="filterConfigs"
                search-placeholder="Search by name or email..." @update:search-value="search = $event"
                @update:filters="filters = $event" @search="search = $event" @clear="clearFilters" />

            <div class="overflow-x-auto">
                <DataTable title="Users" :pagination="users" empty-message="Try adjusting your search or filters"
                    empty-icon="lucide:users" :sort-options="sortOptions" :current-sort="filters.sort"
                    :sort-direction="filters.direction as 'asc' | 'desc'" @pagination="handlePagination"
                    @sort="handleSort">
                    <template #default>
                        <div class="space-y-3">
                            <DataTableRow v-for="user in users.data" :key="user.id" :item="user" :actions="userActions"
                                :show-action-icons="true" :show-badges="true" base-path="/users" @action="(action, user) => {
                                    if ((action.label === 'Edit' || action.label === 'Edit User')) openModal('edit', user);
                                    else if ((action.label === 'View' || action.label === 'View User')) openModal('view', user);
                                    else if ((action.label === 'Delete' || action.label === 'Delete User') && user.id !== currentUserId) handleDelete(user);
                                }"
                                :disable-actions="(action: any, user: User) => (action.label === 'Delete' || action.label === 'Delete User') && user.id === currentUserId">
                                <template #avatar>
                                    <div class="flex-shrink-0">
                                        <div
                                            class="w-8 h-8 sm:w-10 sm:h-10 rounded-full bg-primary/10 flex items-center justify-center">
                                            <span class="text-sm font-medium text-primary">
                                                {{ user.name.split(' ').map(n => n[0]).join('').toUpperCase() }}
                                            </span>
                                        </div>
                                    </div>
                                </template>

                                <template #content>
                                    <div class="flex flex-col sm:flex-row sm:items-center gap-1 sm:gap-2 mb-1">
                                        <h4 class="font-medium text-sm truncate">{{ user.name }}</h4>
                                        <Badge :class="getVerificationColor(user.email_verified_at)"
                                            class="text-xs py-0.5">
                                            {{ user.email_verified_at ? 'Verified' : 'Unverified' }}
                                        </Badge>
                                    </div>
                                    <p class="text-sm text-muted-foreground truncate">{{ user.email }}</p>
                                    <div
                                        class="flex flex-col sm:flex-row sm:items-center gap-1 sm:gap-3 mt-1 text-xs text-muted-foreground">
                                        <span class="truncate">
                                            Role: {{ user.role ? capitalize(user.role) : 'No role' }}
                                        </span>
                                        <span class="hidden sm:inline">{{ user.assigned_tasks_count }} tasks</span>
                                    </div>
                                </template>

                                <template #action="{ action, user }">
                                    <button
                                        v-if="(action.label === 'Delete' || action.label === 'Delete User') && user.id === currentUserId"
                                        class="opacity-50 cursor-not-allowed text-gray-400 flex items-center gap-1 px-2 py-1 rounded"
                                        title="You cannot delete your own account"
                                        disabled
                                    >
                                        <span v-if="action.icon" :class="['icon', action.icon]"></span>
                                        {{ action.label }}
                                    </button>
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
            <UserForm :roles="props.roles" @submitted="closeModal" />
        </template>
        <template v-else-if="modalType === 'edit' && modalUser">
            <UserForm :user="modalUser" :roles="props.roles" @submitted="closeModal" />
        </template>
        <template v-else-if="modalType === 'view' && modalUser">
            <UserDetails :user="modalUser" />
        </template>
    </Modal>
</template>
