<script setup lang="ts">
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { Link } from '@inertiajs/vue3';
import { LayoutGrid, Users, CheckSquare, BriefcaseBusiness } from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

const page = usePage();
const userRole = computed(() => page.props.auth?.role || 'user');

const mainNavItems = computed(() => {
    if (userRole.value === 'admin') {
        return [
            { title: 'Dashboard', href: '/dashboard', icon: LayoutGrid },
            { title: 'Users', href: '/users', icon: Users },
            { title: 'Projects', href: '/projects', icon: BriefcaseBusiness },
            { title: 'Tasks', href: '/tasks', icon: CheckSquare },
        ];
    } else {
        return [
            { title: 'My Tasks', href: '/user-dashboard', icon: CheckSquare  },
];
    }
});

</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="userRole === 'admin' ? '/dashboard' : '/user-dashboard'">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavUser />
        </SidebarFooter>

    </Sidebar>
    <slot />
</template>
