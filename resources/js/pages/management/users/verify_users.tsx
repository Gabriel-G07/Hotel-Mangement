import React from 'react';
import { Head } from '@inertiajs/react';
import { type BreadcrumbItem } from '@/types';
import AppLayout from '@/layouts/app-layout';
import UsersLayout from '@/pages/management/users';
import HeadingSmall from '@/components/heading-small';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Users Activations',
        href: '/users/activate',
    },
];

export default function VerifyUsers() {
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Activate Users" />
            <UsersLayout>
                <div className="space-y-6">
                    <HeadingSmall title="Verify Users" description="Verify users in the system." />
                    {/* Implement verification logic here, e.g., display a list of unverified users */}
                </div>
            </UsersLayout>
        </AppLayout>
    );
}
