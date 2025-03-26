import React, { useState } from 'react';
import { Head } from '@inertiajs/react';
import { type BreadcrumbItem } from '@/types';
import AppLayout from '@/layouts/app/housekeeping/housekeeping_management/app-layout';
import UsersLayout from '@/pages/housekeeping/housekeeping_management/users';
import { Table, TableHead, TableCell, TableBody, TableRow } from '@/components/ui/table';
import HeadingSmall from '@/components/heading-small';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { CustomSelect } from '@/components/ui/custom-select';
import { Card, CardHeader, CardTitle, CardContent, CardFooter } from '@/components/ui/card';

interface UsersListProps {
    users: { id: number; first_name: string; last_name: string; role: string; is_verified: boolean }[];
    roles: { role_id: number; role_name: string }[];
}

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Users List',
        href: '/users/list',
    },
];

export default function UsersList({ users, roles }: UsersListProps) {
    const [selectedUserId, setSelectedUserId] = useState<number | null>(null);
    const [editMode, setEditMode] = useState(false);
    const [editedUser, setEditedUser] = useState<any>(null);

    const handleUserClick = (userId: number) => {
        const user = users.find(user => user.id === userId);
        setSelectedUserId(userId);
        setEditedUser(user);
        setEditMode(false);
    };

    const handleEditClick = () => {
        setEditMode(true);
    };

    const handleInputChange = (e: React.ChangeEvent<HTMLInputElement | HTMLSelectElement>) => {
        setEditedUser({ ...editedUser, [e.target.name]: e.target.value });
    };

    const handleSaveClick = () => {
        // Save logic here
        setEditMode(false);
    };

    const handleCancelClick = () => {
        setEditMode(false);
    };

    const handleDeactivateClick = () => {
        // Deactivate logic here
    };

    const displayedUser = users.find(user => user.id === selectedUserId);

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Users List" />
            <UsersLayout>
                <div className="flex space-x-4">
                    <div className="w-1/2">
                        <HeadingSmall title="Users List" description="View all users in the system." />
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
                                    {editMode ? (
                                        <div>
                                            <Label htmlFor="first_name">First Name</Label>
                                            <Input type="text" name="first_name" value={editedUser?.first_name || ''} onChange={handleInputChange} placeholder="First Name" className="w-full p-2 border rounded mb-2" />
                                            <Label htmlFor="last_name">Last Name</Label>
                                            <Input type="text" name="last_name" value={editedUser?.last_name || ''} onChange={handleInputChange} placeholder="Last Name" className="w-full p-2 border rounded mb-2" />
                                            <Label htmlFor="username">Username</Label>
                                            <Input type="text" name="username" value={editedUser?.username || ''} onChange={handleInputChange} placeholder="Username" className="w-full p-2 border rounded mb-2" />
                                            <Label htmlFor="email">Email</Label>
                                            <Input type="text" name="email" value={editedUser?.email || ''} onChange={handleInputChange} placeholder="Email" className="w-full p-2 border rounded mb-2" />
                                            <Label htmlFor="national_id_number">National ID</Label>
                                            <Input type="text" name="national_id_number" value={editedUser?.national_id_number || ''} onChange={handleInputChange} placeholder="National ID" className="w-full p-2 border rounded mb-2" />
                                            <Label htmlFor="phone_number">Phone Number</Label>
                                            <Input type="text" name="phone_number" value={editedUser?.phone_number || ''} onChange={handleInputChange} placeholder="Phone Number" className="w-full p-2 border rounded mb-2" />
                                            <Label htmlFor="role_id">Role</Label>
                                            <CustomSelect name="role_id" value={editedUser?.role_id || ''} onChange={handleInputChange} options={roles.map(role => ({
                                                value: role.role_id,
                                                label: role.role_name
                                            }))} placeholder="Select Role" className="w-full p-2 border rounded mb-2" />
                                            <Button onClick={handleSaveClick} className="bg-blue-500 text-white p-2 rounded">Save</Button>
                                            <Button onClick={handleCancelClick} className="bg-gray-500 text-white p-2 rounded">Cancel</Button>
                                        </div>
                                    ) : (
                                        <div>
                                            <p><strong>First Name:</strong> {displayedUser.first_name}</p>
                                            <p><strong>Last Name:</strong> {displayedUser.last_name}</p>
                                            <p><strong>Username:</strong> {displayedUser.username}</p>
                                            <p><strong>Email:</strong> {displayedUser.email}</p>
                                            <p><strong>National ID:</strong> {displayedUser.national_id_number}</p>
                                            <p><strong>Phone Number:</strong> {displayedUser.phone_number}</p>
                                            <p><strong>Role:</strong> {displayedUser.role}</p>
                                            <Button onClick={handleEditClick} className="bg-green-500 text-white p-2 rounded">Edit Info</Button>
                                            <Button onClick={handleDeactivateClick} className="bg-red-500 text-white p-2 rounded">Deactivate User</Button>
                                        </div>
                                    )}
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
