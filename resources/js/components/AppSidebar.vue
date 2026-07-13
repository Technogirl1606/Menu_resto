<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { UtensilsCrossed, QrCode, Eye } from '@lucide/vue';
import AppLogo from '@/components/AppLogo.vue';
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { dashboard } from '@/routes';
import type { NavItem } from '@/types';

const page = usePage();

// Les catégories sont partagées globalement par HandleInertiaRequests (voir middleware).
// Un lien de sidebar est généré automatiquement pour chacune : Menu du jour, Boissons, etc.
const categoryNavItems = computed<NavItem[]>(() =>
    ((page.props.categories as { id: number; name: string; slug: string }[]) ?? []).map((cat) => ({
        title: cat.name,
        href: `/admin/items/${cat.slug}`,
        icon: UtensilsCrossed,
    }))
);

const mainNavItems = computed<NavItem[]>(() => [
    {
        title: 'Voir le menu',
        href: '/menu',
        icon: Eye,
        target: '_blank',
    },
    ...categoryNavItems.value,
    {
        title: 'QR code',
        href: '/admin/qr-code',
        icon: QrCode,
    },
]);

const footerNavItems: NavItem[] = [];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="dashboard()">
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
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>