import { NavFooter } from '@/components/nav-footer';
import { NavMain } from '@/components/nav-main';
import { NavUser } from '@/components/views/accounting/nav-user';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/react';
import { BookOpen, Folder, LayoutGrid, Home,  DollarSign, Utensils, ChartBar, Users } from 'lucide-react';
import AppLogo from '../../app-logo';

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        url: '/accounting/dashboard',
        icon: LayoutGrid,
    },
    {
        title: 'Payments',
        url: '/accounting/payments',
        icon: DollarSign,
    },
    {
        title: 'General Ledger',
        url: '/accounting/general_ledger',
        icon: Home,
    },
    {
        title: 'Restaurant Reception',
        url: '/accounting/accounts_payables',
        icon: Utensils,
    },
    {
        title: 'Statistics Reception',
        url: '/accounting/accounts_receivables',
        icon: ChartBar,
    },
    {
        title: 'Payments',
        url: '/accounting/invoice_management',
        icon: DollarSign,
    },
    {
        title: 'General Ledger',
        url: '/accounting/financial_reporting',
        icon: Home,
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
                            <Link href="/accounting/dashboard" prefetch>
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
