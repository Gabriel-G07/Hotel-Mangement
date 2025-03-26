import React, { useState } from 'react';
import { Head, router } from '@inertiajs/react';
import { type BreadcrumbItem } from '@/types';
import AppLayout from '@/layouts/app/management/app-layout';
import UsersLayout from '@/pages/management/users';
import { Table, TableHead, TableCell, TableBody, TableRow } from '@/components/ui/table';
import HeadingSmall from '@/components/heading-small';
import { Button } from '@/components/ui/button';
import { Card, CardHeader, CardTitle, CardContent, CardFooter } from '@/components/ui/card';

interface VerifyUsersProps {
    users: { id: number; first_name: string; last_name: string; role: string; is_verified: boolean }[];
}

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Users Activations',
        href: '/users/activate',
    },
];

export default function VerifyUsers({ users }: VerifyUsersProps) {
    const [selectedUserId, setSelectedUserId] = useState<number | null>(null);

    const handleUserClick = (userId: number) => {
        setSelectedUserId(userId);
    };

    const handleVerifyClick = (userId: number) => {
        router.post(`/management/users/verify/${userId}`);
    };

    const displayedUser = users.find(user => user.id === selectedUserId);

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Activate Users" />
            <UsersLayout>
                <div className="flex space-x-4">
                    <div className="w-1/2">
                        <HeadingSmall title="Unverified Users" description="View and verify unverified users in the system." />
                        <Table data={users}>
                            <TableHead>
                                <TableRow>
                                    <TableCell>First Name</TableCell>
                                    <TableCell>Last Name</TableCell>
                                    <TableCell>Role</TableCell>
                                </TableRow>
                            </TableHead>
                            <TableBody>
                                {users.map((user) => (
                                    <TableRow key={user.id} index={user.id} onClick={() => handleUserClick(user.id)}>
                                        <TableCell>{user.first_name}</TableCell>
                                        <TableCell>{user.last_name}</TableCell>
                                        <TableCell>{user.role}</TableCell>
                                    </TableRow>
                                ))}
                            </TableBody>
                        </Table>
                    </div>
                    <div className="w-1/2">
                        <HeadingSmall title="Selected User's Information" description="Click a user to see full information about that user." />
                        {selectedUserId && displayedUser ? (
                            <Card>
                                <CardHeader>
                                    <CardTitle>User Details</CardTitle>
                                </CardHeader>
                                <CardContent>
                                    <p><strong>First Name:</strong> {displayedUser.first_name}</p>
                                    <p><strong>Last Name:</strong> {displayedUser.last_name}</p>
                                    <p><strong>Role:</strong> {displayedUser.role}</p>
                                    <Button onClick={() => handleVerifyClick(displayedUser.id)} className="bg-green-500 text-white p-2 rounded">Verify User</Button>
                                </CardContent>
                                <CardFooter>
                                    <Button onClick={() => setSelectedUserId(null)} className="bg-gray-500 text-white p-2 rounded">Close</Button>
                                </CardFooter>
                            </Card>
                        ) : (
                            <Card>
                                <CardHeader>
                                    <CardTitle>Selected User's Information</CardTitle>
                                </CardHeader>
                                <CardContent>
                                    <p>Click a user to see full information about that user.</p>
                                </CardContent>
                            </Card>
                        )}
                    </div>
                </div>
            </UsersLayout>
        </AppLayout>
    );
}
