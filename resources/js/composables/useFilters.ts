import { ref, watch } from 'vue';
import { router } from '@inertiajs/vue3';

interface FilterState {
    search: string;
    [key: string]: string;
}

export function useFilters(initialFilters: FilterState, route: string) {
    const search = ref(initialFilters.search || '');
    const filters = ref<Record<string, string>>(initialFilters);
    
    let searchTimeout: ReturnType<typeof setTimeout>;

    watch(search, () => {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            updateFilters();
        }, 300);
    });

    watch(filters, () => {
        updateFilters();
    }, { deep: true });

    const updateFilters = () => {
        const params = {
            search: search.value,
            ...filters.value,
        };
        
        router.get(route, params, {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        });
    };

    const clearFilters = () => {
        search.value = '';
        filters.value = {};
        updateFilters();
    };

    const setFilter = (key: string, value: string) => {
        filters.value[key] = value;
    };

    const getFilter = (key: string) => {
        return filters.value[key] || '';
    };

    return {
        search,
        filters,
        updateFilters,
        clearFilters,
        setFilter,
        getFilter,
    };
} 