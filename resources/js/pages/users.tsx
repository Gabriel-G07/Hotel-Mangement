import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/react';
import Heading from '@/components/heading';
import { Button } from '@/components/ui/button';
import { Separator } from '@/components/ui/separator';
import { cn } from '@/lib/utils';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/react';
import { type PropsWithChildren } from 'react';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Users',
        href: '/users',
    },
];

const sidebarNavItems: NavItem[] = [
    {
        title: 'Users List',
        url: '/users/profile',
        icon: null,
    },
    {
        title: 'Active Users',
        url: '/users/add',
        icon: null,
    },
    {
        title: 'Verify Users',
        url: '/users/appearance',
        icon: null,
    },
    {
        title: 'Add New User',
        url: '/users/roles',
        icon: null,
    }
];

export default function UsersLayout({ children }: PropsWithChildren) {
    const currentPath = window.location.pathname;
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Users" />
        <div className="px-4 py-6">
            <Heading title="Users" description="Manage users" />

            <div className="flex flex-col space-y-8 lg:flex-row lg:space-y-0 lg:space-x-12">
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

                <div className="flex-1">
                    <section className="space-y-12">{children}</section>
                </div>
            </div>
        </div>

        </AppLayout>
    );
}
