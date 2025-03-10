import React, { useState } from 'react';
import { Head, router, usePage } from '@inertiajs/react';
import { type BreadcrumbItem } from '@/types';
import AppLayout from '@/layouts/app-layout';
import UsersLayout from '@/pages/management/users';
import HeadingSmall from '@/components/heading-small';

interface Role {
    role_id: number;
    role_name: string;
}

interface AddUsersProps {
    roles: Role;
}

const breadcrumbs: BreadcrumbItem= [
    {
        title: 'Add User',
        href: 'management/users/add',
    },
];

export default function AddUsers({ roles }: AddUsersProps) {
    const [user, setUser] = useState({
        first_name: '',
        last_name: '',
        phone_number: '',
        email: '',
        role_id: '',
        national_id_number: '', // Add national_id_number
    });

    const handleChange = (e: React.ChangeEvent<HTMLInputElement | HTMLSelectElement>) => {
        setUser({ ...user, [e.target.name]: e.target.value });
    };

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        router.post('/management/users', user);
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Add User" />
            <UsersLayout>
                <div className="space-y-6">
                    <HeadingSmall title="Add User" description="Add a new user to the system." />
                    <form onSubmit={handleSubmit} className="space-y-4">
                        <input
                            type="text"
                            name="first_name"
                            value={user.first_name}
                            onChange={handleChange}
                            placeholder="First Name"
                            className="w-full p-2 border rounded"
                        />
                        <input
                            type="text"
                            name="last_name"
                            value={user.last_name}
                            onChange={handleChange}
                            placeholder="Last Name"
                            className="w-full p-2 border rounded"
                        />
                        <input
                            type="text"
                            name="phone_number"
                            value={user.phone_number}
                            onChange={handleChange}
                            placeholder="Phone Number"
                            className="w-full p-2 border rounded"
                        />
                        <input
                            type="email"
                            name="email"
                            value={user.email}
                            onChange={handleChange}
                            placeholder="Email"
                            className="w-full p-2 border rounded"
                        />
                        <input
                            type="text"
                            name="national_id_number" // Add this input
                            value={user.national_id_number}
                            onChange={handleChange}
                            placeholder="National ID Number"
                            className="w-full p-2 border rounded"
                        />
                        <select
                            name="role_id"
                            value={user.role_id}
                            onChange={handleChange}
                            className="w-full p-2 border rounded"
                        >
                            <option value="">Select Role</option>
                            {roles.map((role) => (
                                <option key={role.role_id} value={role.role_id}>
                                    {role.role_name}
                                </option>
                            ))}
                        </select>
                        <button type="submit" className="bg-blue-500 text-white p-2 rounded">
                            Add User
                        </button>
                    </form>
                </div>
            </UsersLayout>
        </AppLayout>
    );
}
