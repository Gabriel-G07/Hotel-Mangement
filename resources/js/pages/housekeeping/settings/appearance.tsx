import { Head } from '@inertiajs/react';

import AppearanceTabs from '@/components/appearance-tabs';
import HeadingSmall from '@/components/heading-small';
import { type BreadcrumbItem } from '@/types';

import AppLayout from '@/layouts/app/housekeeping/app-layout';
import HouseKeepingSettingsLayout from '@/layouts/settings/housekeeping_layout';
import { useAppearance } from '@/hooks/use-appearance';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Appearance settings',
        href: '/housekeeping/settings/appearance',
    },
];

export default function Appearance() {
    const { appearance, updateAppearance } = useAppearance();

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Appearance settings" />

            <HouseKeepingSettingsLayout>
                <div className="space-y-6">
                    <HeadingSmall title="Appearance settings" description="Update your account's appearance settings" />
                    <AppearanceTabs appearance={appearance} onChange={updateAppearance} />
                </div>
            </HouseKeepingSettingsLayout>
        </AppLayout>
    );
}
