import { NavFooter } from '@/components/nav-footer';
import { NavMain } from '@/components/nav-main';
import { NavUser } from '@/components/views/restaurant/restaurant_management/nav-user';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/react';
import { BookOpen, Folder, LayoutGrid, Home,  DollarSign, Utensils, ChartBar, Users } from 'lucide-react';
import AppLogo from '../../../app-logo';

const mainNavItems: NavItem[] = [
    {
        title: 'Reception Dashboard',
        url: '/restaurant/management/dashboard',
        icon: LayoutGrid,
    },
    {
        title: 'Our Menu',
        url: '/restaurant/management/our_menu',
        icon: DollarSign,
    },
    {
        title: 'Edit Menu',
        url: '/restaurant/management/menu_iterms',
        icon: Home,
    },
    {
        title: 'Sales',
        url: '/restaurant/management/sales',
        icon: Utensils,
    },
];

const footerNavItems: NavItem[] = [
    {
        title: 'User Manual',
        url: 'https://laravel.com/docs/starter-kits',
        icon: BookOpen,
    },
];

export function AppSidebar() {
    return (
        <Sidebar collapsible="icon" variant="inset">
            <SidebarHeader>
                <SidebarMenu>
                    <SidebarMenuItem>
                        <SidebarMenuButton size="lg" asChild>
                            <Link href="/restaurant/management/dashboard" prefetch>
                                <AppLogo />
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </SidebarMenu>
            </SidebarHeader>

            <SidebarContent>
                <NavMain items={mainNavItems} />
            </SidebarContent>

            <SidebarFooter>
                <NavFooter items={footerNavItems} className="mt-auto" />
                <NavUser />
            </SidebarFooter>
        </Sidebar>
    );
}
