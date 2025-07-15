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
import UserForm from '@/components/ui/users/UserForm.vue';
import UserDetails from '@/components/ui/users/UserDetails.vue';
import { ref } from 'vue';

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

const filterConfigs = [
    {
        key: 'role',
        label: 'Role',
        options: [
            ...props.roles.map(role => ({ value: role, label: role.charAt(0).toUpperCase() + role.slice(1) })),
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
        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <PageHeader
                title="Users"
                description="Manage system users and their permissions"
                action-label="Create User"
                action-href=""
                @action="() => openModal('create')"
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
                :current-sort="filters.sort"
                :sort-direction="filters.direction as 'asc' | 'desc'"
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
                            @action="(action, user) => {
                                if (action.label === 'Edit' || action.label === 'Edit User') openModal('edit', user);
                                else if (action.label === 'View' || action.label === 'View User') openModal('view', user);
                                else if (action.label === 'Delete' || action.label === 'Delete User') handleDelete(user);
                            }"
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
                                        {{ user.email_verified_at ? 'Verified' : 'Unverified' }}
                                    </Badge>
                                </div>
                                <p class="text-sm text-muted-foreground truncate">{{ user.email }}</p>
                                <div class="flex items-center gap-4 mt-2 text-xs text-muted-foreground">
                                    <span>
                                        {{ user.roles && user.roles.length > 0
                                            ? user.roles.map(r => r.charAt(0).toUpperCase() + r.slice(1)).join(', ')
                                            : 'No roles' }}
                                    </span>
                                    <span>Joined {{ new Date(user.created_at).toLocaleDateString() }}</span>
                                    <span>{{ user.assigned_tasks_count }} assigned tasks</span>
                                    <span>{{ user.created_tasks_count }} created tasks</span>
                                </div>
                            </template>
                        </DataTableRow>
                    </div>
                </template>
            </DataTable>
        </div>
    </AppLayout>

    <Modal :show="showModal" @close="closeModal">
        <template v-if="modalType === 'create'">
            <UserForm
                :roles="props.roles"
                @submitted="closeModal"
            />
        </template>
        <template v-else-if="modalType === 'edit' && modalUser">
            <UserForm
                :user="modalUser"
                :roles="props.roles"
                @submitted="closeModal"
            />
        </template>
        <template v-else-if="modalType === 'view' && modalUser">
            <UserDetails :user="modalUser" />
        </template>
    </Modal>
</template>
