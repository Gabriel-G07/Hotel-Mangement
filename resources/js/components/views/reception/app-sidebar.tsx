import { NavFooter } from '@/components/nav-footer';
import { NavMain } from '@/components/nav-main';
import { NavUser } from '@/components/nav-user';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/react';
import { BookOpen, Folder, LayoutGrid, Home,  DollarSign, Utensils, ChartBar, Users } from 'lucide-react';
import AppLogo from '../../app-logo';

const mainNavItems: NavItem[] = [
    {
        title: 'Reception Dashboard',
        url: 'reception/dashboard',
        icon: LayoutGrid,
    },
    {
        title: 'Payments',
        url: 'reception/payments',
        icon: DollarSign,
    },
    {
        title: 'Bookings',
        url: 'reception/bookings',
        icon: Home,
    },
    {
        title: 'Restaurant',
        url: 'reception/restaurant',
        icon: Utensils,
    },
    {
        title: 'Statistics',
        url: 'reception/statistics',
        icon: ChartBar,
    },
];

const footerNavItems: NavItem[] = [
    {
        title: 'Repository From Reception',
        url: 'https://github.com/laravel/react-starter-kit',
        icon: Folder,
    },
    {
        title: 'Documentation',
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
                            <Link href="reception/dashboard" prefetch>
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
