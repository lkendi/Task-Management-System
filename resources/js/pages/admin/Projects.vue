<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { PageHeader } from '@/components/ui/page';
import { FilterBar } from '@/components/ui/filters';
import { DataTable, DataTableRow } from '@/components/ui/data-table';
import Icon from '@/components/Icon.vue';
import { useFilters } from '@/composables/useFilters';
import Modal from '@/components/ui/modal/Modal.vue';
import ProjectForm from '@/components/ui/projects/ProjectForm.vue';
import ProjectDetails from '@/components/ui/projects/ProjectDetails.vue';
import { ref } from 'vue';

interface Project {
    id: number;
    name: string;
    description: string;
    created_at: string;
    start_date?: string;
    end_date?: string;
}

interface Props {
    projects: {
        data: Project[];
        current_page: number;
        last_page: number;
        per_page: number;
        total: number;
        links: any[];
    };
    filters: {
        search: string;
    };
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Projects',
        href: '/projects',
    },
];

const { search, filters, clearFilters } = useFilters(props.filters, '/projects');

const filterConfigs: any[] = [
    {
        key: 'search',
        label: 'Search',
        type: 'text',
        placeholder: 'Search by project name or description...'
    },
];

const projectActions = [
    { label: 'View Project', icon: 'eye' },
    { label: 'Edit Project', icon: 'edit' },
    { label: 'Delete Project', variant: 'destructive' as const, icon: 'trash' },
];

const showModal = ref(false);
const modalType = ref('');
const modalProject = ref<Project | null>(null);

function openModal(type: string, project: Project | null = null) {
    modalType.value = type;
    modalProject.value = project;
    showModal.value = true;
}
function closeModal() {
    showModal.value = false;
    modalProject.value = null;
}
function handleDelete(project: Project) {
    if (confirm(`Are you sure you want to delete "${project.name}"?`)) {
        router.delete(`/projects/${project.id}`, {
            onSuccess: () => closeModal()
        });
    }
}
const handlePagination = (url: string) => {
    router.get(url);
};
function handleProjectAction(action: any, project: Project) {
    if (action.label === 'Edit Project' || action.label === 'Edit') openModal('edit', project);
    else if (action.label === 'View Project' || action.label === 'View') openModal('view', project);
    else if (action.label === 'Delete Project' || action.label === 'Delete') handleDelete(project);
}

// Define a type for the payload sent to the backend
// Use index signature to match Record<string, FormDataConvertible>
type ProjectPayload = {
    [key: string]: string;
    name: string;
    description: string;
};

function handleProjectFormSubmit(data: Record<string, string>) {
    if (modalType.value === 'edit' && modalProject.value) {
        router.put(`/projects/${modalProject.value.id}`, data, {
            onSuccess: () => closeModal(),
        });
    } else {
        router.post('/projects', data, {
            onSuccess: () => closeModal(),
        });
    }
}
</script>

<template>
    <Head title="Projects" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <PageHeader title="Projects" description="Manage your projects and details" action-label="Create Project"
                action-href="" @action="() => openModal('create')" />

            <DataTable
                title="Projects"
                :pagination="projects"
                empty-message="Try adjusting your search or filters"
                empty-icon="lucide:folder"
                @pagination="handlePagination"
            >
                <template #default>
                    <div class="space-y-4">
                        <DataTableRow
                            v-for="project in projects.data"
                            :key="project.id"
                            :item="project"
                            :actions="projectActions"
                            :show-action-icons="true"
                            :show-badges="false"
                            base-path="/projects"
                            @action="(action, project) => handleProjectAction(action, project)"
                        >
                            <template #avatar>
                                <div class="flex-shrink-0">
                                    <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center">
                                        <Icon name="folder" class="text-primary w-6 h-6" />
                                    </div>
                                </div>
                            </template>
                            <template #content>
                                <div class="flex items-center gap-2 mb-1">
                                    <h4 class="font-medium text-sm truncate">{{ project.name }}</h4>
                                </div>
                                <p class="text-sm text-muted-foreground truncate mb-2">{{ project.description }}</p>
                                <div class="flex items-center gap-4 text-xs text-muted-foreground">
                                    <span>Created {{ new Date(project.created_at).toLocaleDateString() }}</span>
                                    <span>Start: {{ project.start_date ? new Date(project.start_date).toLocaleDateString() : '-' }}</span>
                                    <span>End: {{ project.end_date ? new Date(project.end_date).toLocaleDateString() : '-' }}</span>
                                </div>
                            </template>
                        </DataTableRow>
                    </div>
                </template>
            </DataTable>

            <Modal :show="showModal" @close="closeModal">
                <template v-if="modalType === 'create'">
                    <ProjectForm :project="null" :is-open="showModal" @close="closeModal" @submit="handleProjectFormSubmit" />
                </template>
                <template v-else-if="modalType === 'edit'">
                    <ProjectForm :project="modalProject" :is-open="showModal" @close="closeModal" @submit="handleProjectFormSubmit" />
                </template>
                <template v-else-if="modalType === 'view'">
                    <ProjectDetails :project="modalProject" :is-open="showModal" @close="closeModal" @edit="() => openModal('edit', modalProject)" />
                </template>
            </Modal>
        </div>
    </AppLayout>
</template>
