import React, { useState } from 'react';
import { Head, router } from '@inertiajs/react';
import { type BreadcrumbItem } from '@/types';
import AppLayout from '@/layouts/app-layout';
import UsersLayout from '@/pages/users';
import { Table, TableHead, TableCell, TableBody, TableRow } from '@/components/ui/table';
import HeadingSmall from '@/components/heading-small';

interface UsersListProps {
    users: { id: number; first_name: string; last_name: string; role: string }[];
    selectedUser?: any;
}

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Users List',
        href: '/users/list',
    },
];

export default function UsersList({ users, selectedUser }: UsersListProps) {
    const [selectedUserId, setSelectedUserId] = useState<number | null>(selectedUser?.id || null);
    const [editMode, setEditMode] = useState(false);
    const [editedUser, setEditedUser] = useState<any>(selectedUser || null);

    const handleUserClick = (userId: number) => {
        setSelectedUserId(userId);
        router.get(`/users/${userId}`);
        setEditMode(false);
    };

    const handleEditClick = () => {
        setEditMode(true);
        setEditedUser(selectedUser);
    };

    const handleInputChange = (e: React.ChangeEvent<HTMLInputElement | HTMLSelectElement>) => {
        setEditedUser({ ...editedUser, [e.target.name]: e.target.value });
    };

    const handleSaveClick = () => {
        router.put(`/users/${selectedUserId}`, editedUser);
        setEditMode(false);
    };

    const handleVerifyClick = () => {
      router.post(`/users/verify/${selectedUserId}`);
    }

    const displayedUser = users?.find(user => user.id === selectedUserId);

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
                                    {users && users.map((user) => (
                                        <TableRow key={user.id} index={user.id} onClick={() => handleUserClick(user.id)}>
                                            <TableCell>{user.first_name}</TableCell>
                                            <TableCell>{user.last_name}</TableCell>
                                            <TableCell>{user.role}</TableCell>
                                        </TableRow>
                                    ))}
                                </TableBody>
                        </Table>
                        </div>
                        {selectedUserId && displayedUser && (
                        <div className="w-1/2">
                            <HeadingSmall title="User Details" description="View user details." />
                            {editMode ? (
                            <div>
                                <input type="text" name="first_name" value={editedUser?.first_name || ''} onChange={handleInputChange} placeholder="First Name" className="w-full p-2 border rounded mb-2" />
                                <input type="text" name="last_name" value={editedUser?.last_name || ''} onChange={handleInputChange} placeholder="Last Name" className="w-full p-2 border rounded mb-2" />
                                <input type="text" name="username" value={editedUser?.username || ''} onChange={handleInputChange} placeholder="Username" className="w-full p-2 border rounded mb-2" />
                                <input type="text" name="email" value={editedUser?.email || ''} onChange={handleInputChange} placeholder="Email" className="w-full p-2 border rounded mb-2" />
                                <input type="text" name="national_id_number" value={editedUser?.national_id_number || ''} onChange={handleInputChange} placeholder="National ID" className="w-full p-2 border rounded mb-2" />
                                <input type="text" name="phone_number" value={editedUser?.phone_number || ''} onChange={handleInputChange} placeholder="Phone Number" className="w-full p-2 border rounded mb-2" />
                                <select name="role_id" value={editedUser?.role_id || ''} onChange={handleInputChange} className="w-full p-2 border rounded mb-2">
                                <option value="">Select Role</option>
                                {/* Fetch roles from database and map here */}
                                </select>
                                <button onClick={handleSaveClick} className="bg-blue-500 text-white p-2 rounded">Save</button>
                            </div>
                            ) : (
                            <div>
                                <p><strong>First Name:</strong> {displayedUser.first_name}</p>
                                <p><strong>Last Name:</strong> {displayedUser.last_name}</p>
                                {selectedUser && <p><strong>Username:</strong> {selectedUser.username}</p>}
                                <p><strong>Email:</strong> {selectedUser?.email}</p>
                                <p><strong>National ID:</strong> {selectedUser?.national_id_number}</p>
                                <p><strong>Phone Number:</strong> {selectedUser?.phone_number}</p>
                                <p><strong>Role:</strong> {displayedUser.role}</p>
                                <button onClick={handleEditClick} className="bg-green-500 text-white p-2 rounded">Edit Info</button>
                                <button onClick={handleVerifyClick} className="bg-green-500 text-white p-2 rounded">Verify User</button>
                            </div>
                        )}
                    </div>
                )}
            </div>
        </UsersLayout>
        </AppLayout>
    );
    }
