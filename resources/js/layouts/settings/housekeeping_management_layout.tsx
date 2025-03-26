import Heading from '@/components/heading';
import { Button } from '@/components/ui/button';
import { Separator } from '@/components/ui/separator';
import { cn } from '@/lib/utils';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/react';
import { type PropsWithChildren } from 'react';

const sidebarNavItems: NavItem[] = [
    {
        title: 'Profile',
        url: '/housekeeping/management/settings/profile',
        icon: null,
    },
    {
        title: 'Password',
        url: '/housekeeping/management/settings/password',
        icon: null,
    },
    {
        title: 'Appearance',
        url: '/housekeeping/management/settings/appearance',
        icon: null,
    },
    {
        title: 'Duties',
        url: '/housekeeping/management/settings/appearance',
        icon: null,
    },
];

export default function HouseKeepingManagementSettingsLayout({ children }: PropsWithChildren) {
    const currentPath = window.location.pathname;

    return (
        <div className="px-4 py-6 overflow-y-auto overflow-x-hidden h-screen flex flex-col">
            <Heading title="Settings" description="Manage your profile, account and system settings" />

            <div className="flex flex-col space-y-8 lg:flex-row lg:space-y-0 lg:space-x-12 flex-wrap">
                <aside className="w-full max-w-xl lg:w-48">
                    <nav className="flex flex-col space-y-1 space-x-0">
                        {sidebarNavItems.map((item) => (
                            <Button
                                key={item.url}
                                size="sm"
                                variant="ghost"
                                asChild
                                className={cn('w-full justify-start', {
                                    'bg-muted': currentPath === item.url,
                                })}
                            >
                                <Link href={item.url} prefetch>
                                    {item.title}
                                </Link>
                            </Button>
                        ))}
                    </nav>
                </aside>

                <Separator className="my-6 md:hidden" />

                <div className="flex-1 overflow-y-hidden">
                    <section className="space-y-12">{children}</section>
                </div>
            </div>
        </div>
    );
}
