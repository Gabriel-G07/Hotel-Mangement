import { NavFooter } from '@/components/nav-footer';
import { NavMain } from '@/components/nav-main';
import { NavUser } from '@/components/views/housekeeping/housekeeping_management/nav-user';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/react';
import { BookOpen, Folder, LayoutGrid, Home,  DollarSign, Utensils, ChartBar, Users } from 'lucide-react';
import AppLogo from '../../../app-logo';

const mainNavItems: NavItem[] = [
    {
        title: 'H_Manager Dashboard',
        url: '/housekeeping/management/dashboard',
        icon: LayoutGrid,
    },
    {
        title: 'House Keepers',
        url: '/housekeeping/management/housekeepers',
        icon: DollarSign,
    },
    {
        title: 'Duty Assigments',
        url: '/housekeeping/management/duties',
        icon: Home,
    },
    {
        title: 'Active Duties',
        url: '/housekeeping/management/active_duties',
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
                            <Link href="/housekeeping/management/dashboard" prefetch>
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
