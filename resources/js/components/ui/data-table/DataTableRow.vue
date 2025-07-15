<script setup lang="ts">
import { Button } from '@/components/ui/button';
import Badge from '@/components/ui/badge/Badge.vue';
import Icon from '@/components/Icon.vue';
import { 
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuSeparator,
    DropdownMenuLabel,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { 
    Tooltip,
    TooltipContent,
    TooltipProvider,
    TooltipTrigger,
} from '@/components/ui/tooltip';

interface Action {
    label: string;
    href?: string | ((item: any) => string);
    method?: 'get' | 'post' | 'put' | 'delete';
    variant?: 'default' | 'destructive';
    icon?: string;
}

interface Props {
    item: any;
    actions?: Action[];
    showActions?: boolean;
    showActionIcons?: boolean;
    showBadges?: boolean;
    basePath?: string;
}

const props = withDefaults(defineProps<Props>(), {
    actions: () => [],
    showActions: true,
    showActionIcons: true,
    showBadges: true,
    basePath: '',
});

const emit = defineEmits<{
    'action': [action: Action, item: any];
}>();

const handleAction = (action: Action) => {
    emit('action', action, props.item);
};

const getDefaultActions = () => {
    const basePath = props.basePath || '';
    return [
        { label: 'View', href: (item: any) => `${basePath}/${item.id}`, icon: 'eye', variant: 'default' as const },
        { label: 'Edit', href: (item: any) => `${basePath}/${item.id}/edit`, icon: 'edit', variant: 'default' as const },
        { label: 'Delete', icon: 'trash', variant: 'destructive' as const },
    ];
};
</script>

<template>
    <div class="flex items-center justify-between p-4 rounded-lg border border-border hover:bg-muted/50 transition-colors">
        <div class="flex items-center space-x-4">
            <slot name="avatar" />
            
            <div class="flex-1 min-w-0">
                <slot name="content" />
            </div>
        </div>

        <div v-if="showActions" class="flex items-center gap-2">
            <slot v-if="showBadges" name="badges" />
            
            <div v-if="showActionIcons" class="flex items-center gap-1 ml-2">
                <TooltipProvider>
                    <Tooltip v-for="action in getDefaultActions()" :key="action.label">
                        <TooltipTrigger as-child>
                            <Button 
                                variant="outline" 
                                size="sm"
                                class="h-8 w-8 p-0 flex items-center justify-center"
                                :class="{ 
                                    'text-red-600 hover:text-red-700 hover:bg-red-50 border-red-200': action.variant === 'destructive',
                                    'hover:bg-muted': action.variant !== 'destructive'
                                }"
                                @click="handleAction(action)"
                            >
                                <Icon :name="action.icon" class="h-4 w-4" />
                            </Button>
                        </TooltipTrigger>
                        <TooltipContent>
                            <p>{{ action.label }}</p>
                        </TooltipContent>
                    </Tooltip>
                </TooltipProvider>
            </div>
        </div>
    </div>
</template> 