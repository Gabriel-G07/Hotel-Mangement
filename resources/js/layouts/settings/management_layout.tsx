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
        url: '/management/settings/profile',
        icon: null,
    },
    {
        title: 'Password',
        url: '/management/settings/password',
        icon: null,
    },
    {
        title: 'Appearance',
        url: '/management/settings/appearance',
        icon: null,
    },
    {
        title: 'Roles',
        url: '/management/settings/roles',
        icon: null,
    },
    {
        title: 'Activities',
        url: '/management/settings/activities',
        icon: null,
    },
    {
        title: 'Rooms',
        url: '/management/settings/rooms',
        icon: null,
    },
    {
        title: 'Room Types',
        url: '/management/settings/room_types',
        icon: null,
    },
    {
        title: 'Currencies',
        url: '/management/settings/currencies',
        icon: null,
    },
];

export default function ManagementSettingsLayout({ children }: PropsWithChildren) {
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
