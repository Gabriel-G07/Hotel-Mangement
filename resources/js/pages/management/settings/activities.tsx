import { Head } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import SettingsLayout from '@/layouts/settings/layout';
import HeadingSmall from '@/components/heading-small';
import { Table, TableHead, TableCell, TableBody, TableRow } from '@/components/ui/table';

interface RecordsProps {
    logs: any[];
}

export default function Records({ logs }: RecordsProps) {
    return (
        <AppLayout breadcrumbs={[{ title: 'Activity Records', href: route('management.settings.activities.index') }]}>
            <Head title="Activity Records" />

            <SettingsLayout>
                <div className="space-y-6">
                    <HeadingSmall title="Activity Records" description="View all system activity logs." />

                    <Table data={logs}>
                        <TableHead>
                            <TableRow>
                                <TableCell>User</TableCell>
                                <TableCell>Action</TableCell>
                                <TableCell>Table - Column</TableCell>
                                <TableCell>New Data</TableCell>
                                <TableCell>Previous Data</TableCell>
                                <TableCell>Time</TableCell>
                            </TableRow>
                        </TableHead>
                        <TableBody>
                            {logs.map((log, index) => (
                                <TableRow key={index} index={index}>
                                    <TableCell>{log.user.username} ({log.user.full_name})</TableCell>
                                    <TableCell>{log.action}</TableCell>
                                    <TableCell>{log.table_column}</TableCell>
                                    <TableCell>{log.new_value !== null ? log.new_value : '-'}</TableCell>
                                    <TableCell>{log.old_value !== null ? log.old_value : '-'}</TableCell>
                                    <TableCell>{log.created_at}</TableCell>
                                </TableRow>
                            ))}
                        </TableBody>
                    </Table>
                </div>
            </SettingsLayout>
        </AppLayout>
    );
}
