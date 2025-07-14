<script setup lang="ts">
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import Icon from '@/components/Icon.vue';
import { SearchInput, FilterDropdown } from '@/components/ui/search';

interface FilterOption {
    value: string;
    label: string;
}

interface FilterConfig {
    key: string;
    label: string;
    options: FilterOption[];
    showAllOption?: boolean;
    allOptionLabel?: string;
}

interface Props {
    searchValue: string;
    filters: Record<string, string>;
    filterConfigs: FilterConfig[];
    searchPlaceholder?: string;
    title?: string;
    description?: string;
}

const props = withDefaults(defineProps<Props>(), {
    searchPlaceholder: 'Search...',
    title: 'Filters',
    description: 'Search and filter data',
});

const emit = defineEmits<{
    'update:searchValue': [value: string];
    'update:filters': [filters: Record<string, string>];
    'search': [value: string];
    'clear': [];
}>();

const handleSearchUpdate = (value: string) => {
    emit('update:searchValue', value);
};

const handleSearch = (value: string) => {
    emit('search', value);
};

const handleFilterUpdate = (key: string, value: string) => {
    const newFilters = { ...props.filters, [key]: value };
    emit('update:filters', newFilters);
};

const clearFilters = () => {
    emit('clear');
};
</script>

<template>
    <Card>
        <CardHeader>
            <div class="flex items-center justify-between">
                <div>
                    <CardTitle class="text-lg">{{ title }}</CardTitle>
                    <CardDescription>{{ description }}</CardDescription>
                </div>
                <Button variant="outline" @click="clearFilters" size="sm">
                    <Icon name="x" class="h-4 w-4 mr-2" />
                    Clear Filters
                </Button>
            </div>
        </CardHeader>
        <CardContent>
            <div class="grid gap-4 md:grid-cols-4">
                <SearchInput
                    :model-value="searchValue"
                    :placeholder="searchPlaceholder"
                    @update:model-value="handleSearchUpdate"
                    @search="handleSearch"
                />

                <FilterDropdown
                    v-for="config in filterConfigs"
                    :key="config.key"
                    :model-value="filters[config.key] || ''"
                    :options="config.options"
                    :label="config.label"
                    :show-all-option="config.showAllOption"
                    :all-option-label="config.allOptionLabel"
                    @update:model-value="(value) => handleFilterUpdate(config.key, value)"
                />
            </div>
        </CardContent>
    </Card>
</template>
