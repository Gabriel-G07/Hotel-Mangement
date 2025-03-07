import { Head } from '@inertiajs/react';

import AppearanceTabs from '@/components/appearance-tabs';
import HeadingSmall from '@/components/heading-small';
import { type BreadcrumbItem } from '@/types';

import AppLayout from '@/layouts/app-layout';
import UsersLayout from '@/pages/users';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Users List',
        href: '/ussers/list',
    },
];

export default function Appearance() {
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Users list" />

            <UsersLayout>
                <div className="space-y-6">

                </div>
            </UsersLayout>
        </AppLayout>
    );
}
