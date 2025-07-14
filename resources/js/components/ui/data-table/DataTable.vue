<script setup lang="ts">
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import Icon from '@/components/Icon.vue';
import { 
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';

interface PaginationData {
    data: any[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    links: any[];
}

interface SortOption {
    key: string;
    label: string;
}

interface Props {
    title: string;
    description?: string;
    pagination: PaginationData;
    emptyMessage?: string;
    emptyIcon?: string;
    sortOptions?: SortOption[];
    currentSort?: string;
    sortDirection?: 'asc' | 'desc';
}

const props = withDefaults(defineProps<Props>(), {
    emptyMessage: 'No data found',
    emptyIcon: 'lucide:database',
    sortOptions: () => [],
    currentSort: '',
    sortDirection: 'asc',
});

const emit = defineEmits<{
    'pagination': [url: string];
    'sort': [sort: string, direction: 'asc' | 'desc'];
}>();

const handlePagination = (url: string) => {
    emit('pagination', url);
};

const handleSort = (sortKey: string) => {
    const newDirection = props.currentSort === sortKey && props.sortDirection === 'asc' ? 'desc' : 'asc';
    emit('sort', sortKey, newDirection);
};

const getSortIcon = (sortKey: string) => {
    if (props.currentSort !== sortKey) return 'lucide:arrow-up-down';
    return props.sortDirection === 'asc' ? 'lucide:arrow-up' : 'lucide:arrow-down';
};
</script>

<template>
    <Card>
        <CardHeader>
            <div class="flex items-center justify-between">
                <div>
                    <CardTitle>{{ title }} ({{ pagination.total }})</CardTitle>
                    <CardDescription v-if="description">
                        {{ description }}
                    </CardDescription>
                </div>
                
                <DropdownMenu v-if="sortOptions.length > 0">
                    <DropdownMenuTrigger as-child>
                        <Button variant="outline" size="sm">
                            <Icon name="arrow-up-down" class="h-4 w-4 mr-2" />
                            Sort
                        </Button>
                    </DropdownMenuTrigger>
                    <DropdownMenuContent align="end">
                        <DropdownMenuItem 
                            v-for="option in sortOptions" 
                            :key="option.key"
                            @click="handleSort(option.key)"
                            :class="{ 'bg-accent': currentSort === option.key }"
                        >
                            <Icon :name="getSortIcon(option.key)" class="h-4 w-4 mr-2" />
                            {{ option.label }}
                                                            <Icon 
                                    v-if="currentSort === option.key" 
                                    :name="sortDirection === 'asc' ? 'arrow-up' : 'arrow-down'" 
                                    class="h-4 w-4 ml-auto" 
                                />
                        </DropdownMenuItem>
                    </DropdownMenuContent>
                </DropdownMenu>
            </div>
        </CardHeader>
        <CardContent>
            <div v-if="pagination.data.length === 0" class="text-center py-12">
                <Icon :name="emptyIcon" class="h-12 w-12 mx-auto mb-4 text-muted-foreground opacity-50" />
                <h3 class="text-lg font-medium mb-2">No data found</h3>
                <p class="text-muted-foreground">{{ emptyMessage }}</p>
            </div>

            <div v-else>
                <slot />
            </div>

            <div v-if="pagination.last_page > 1" class="flex items-center justify-between mt-6">
                <p class="text-sm text-muted-foreground">
                    Showing {{ (pagination.current_page - 1) * pagination.per_page + 1 }} to 
                    {{ Math.min(pagination.current_page * pagination.per_page, pagination.total) }} of {{ pagination.total }} results
                </p>
                
                <div class="flex gap-2">
                    <Button 
                        v-for="link in pagination.links" 
                        :key="link.label"
                        :variant="link.active ? 'default' : 'outline'"
                        :disabled="!link.url"
                        size="sm"
                        @click="handlePagination(link.url)"
                        v-html="link.label"
                    />
                </div>
            </div>
        </CardContent>
    </Card>
</template> 