import { Breadcrumbs } from '@/components/breadcrumbs';
import { SidebarTrigger } from '@/components/ui/sidebar';
import { type BreadcrumbItem as BreadcrumbItemType } from '@/types';
import { useEffect } from 'react';
import { useAppearance, fetchAppearanceFromBackend, applyTheme } from '@/hooks/use-appearance';

export function AppSidebarHeader({ breadcrumbs = [] }: { breadcrumbs?: BreadcrumbItemType[] }) {
    useEffect(() => {

        const fetchAndApplyTheme = async () => {
            const userTheme = await fetchAppearanceFromBackend();
            if (userTheme) {
                applyTheme(userTheme as 'light' | 'dark' | 'system');
            }
        };

        fetchAndApplyTheme();
    }, []);

    return (
        <header className="border-sidebar-border/50 flex h-16 shrink-0 items-center gap-2 border-b px-6 transition-[width,height] ease-linear group-has-data-[collapsible=icon]/sidebar-wrapper:h-12 md:px-4">
            <div className="flex items-center gap-2">
                <SidebarTrigger className="-ml-1" />
                <Breadcrumbs breadcrumbs={breadcrumbs} />
            </div>
        </header>
    );
}
