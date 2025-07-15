<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { DataTable, DataTableRow } from '@/components/ui/data-table';
import TaskDetails from '@/components/ui/tasks/TaskDetails.vue';
import Modal from '@/components/ui/modal/Modal.vue';
import { ref } from 'vue';

interface Task {
    id: number;
    title: string;
    description: string;
    status: string;
    priority: string;
    due_date: string | null;
    created_at: string;
    is_overdue: boolean;
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
}

const props = defineProps<Props>();
const showModal = ref(false);
const modalTask = ref<Task | null>(null);

function openModal(task: Task) {
    modalTask.value = task;
    showModal.value = true;
}
function closeModal() {
    showModal.value = false;
    modalTask.value = null;
}
function updateStatus(task: Task, status: string) {
    router.put(`/tasks/${task.id}`, { status }, {
        onSuccess: () => closeModal(),
    });
}
</script>
<template>
    <Head title="My Tasks" />
    <AppLayout>
        <div class="flex flex-col gap-6 p-6">
            <h1 class="text-2xl font-semibold text-foreground mb-4">My Tasks</h1>
            <DataTable title="My Tasks" :pagination="tasks">
                <template #default>
                    <div class="space-y-4">
                        <DataTableRow
                            v-for="task in tasks.data"
                            :key="task.id"
                            :item="task"
                            :actions="[{ label: 'View Details', icon: 'eye' }]"
                            :show-action-icons="true"
                            :show-badges="false"
                            @action="() => openModal(task)"
                        >
                            <template #content>
                                <div class="flex items-center gap-2 mb-1">
                                    <h4 class="font-medium text-sm truncate">{{ task.title }}</h4>
                                </div>
                                <p class="text-sm text-muted-foreground truncate mb-2">{{ task.description }}</p>
                                <div class="flex items-center gap-4 text-xs text-muted-foreground">
                                    <span>Status: {{ task.status }}</span>
                                    <span>Due: {{ task.due_date ? new Date(task.due_date).toLocaleDateString() : '-' }}</span>
                                </div>
                            </template>
                        </DataTableRow>
                    </div>
                </template>
            </DataTable>
            <Modal :show="showModal" @close="closeModal">
                <TaskDetails v-if="modalTask" :task="{ ...modalTask, due_date: modalTask.due_date ?? undefined }" @close="closeModal" @update-status="(status: string) => { if (modalTask) updateStatus(modalTask, status) }" />
            </Modal>
        </div>
    </AppLayout>
</template> 