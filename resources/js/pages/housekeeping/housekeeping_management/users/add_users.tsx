import React, { useState } from 'react';
import { Head, router, usePage } from '@inertiajs/react';
import { type BreadcrumbItem } from '@/types';
import AppLayout from '@/layouts/app/housekeeping/housekeeping_management/app-layout';
import UsersLayout from '@/pages/housekeeping/housekeeping_management/users';
import HeadingSmall from '@/components/heading-small';
import { CustomSelect } from '@/components/ui/custom-select';
import { Input } from '@/components/ui/input';

interface Role {
    role_id: number;
    role_name: string;
}

interface AddUsersProps {
    roles: Role[];
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
        national_id_number: '',
    });

    const handleChange = (e: React.ChangeEvent<HTMLInputElement | HTMLSelectElement>) => {
        setUser({ ...user, [e.target.name]: e.target.value });
    };

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        const formData = { ...user, role_id: user.role_id || null };
        router.post('/management/users', formData);
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Add User" />
            <UsersLayout>
                <div className="space-y-6">
                    <HeadingSmall title="Add User" description="Add a new user to the system." />
                    <form onSubmit={handleSubmit} className="space-y-4">
                        <Input
                            type="text"
                            name="first_name"
                            value={user.first_name}
                            onChange={handleChange}
                            placeholder="First Name"
                        />
                        <Input
                            type="text"
                            name="last_name"
                            value={user.last_name}
                            onChange={handleChange}
                            placeholder="Last Name"
                        />
                        <Input
                            type="text"
                            name="phone_number"
                            value={user.phone_number}
                            onChange={handleChange}
                            placeholder="Phone Number"
                        />
                        <Input
                            type="email"
                            name="email"
                            value={user.email}
                            onChange={handleChange}
                            placeholder="Email"
                        />
                        <Input
                            type="text"
                            name="national_id_number"
                            value={user.national_id_number}
                            onChange={handleChange}
                            placeholder="National ID Number"
                        />
                        <CustomSelect
                            name="role_id"
                            value={user.role_id}
                            onChange={handleChange}
                            options={roles.filter(role => role.role_name).map(role => ({
                                value: role.role_id,
                                label: role.role_name
                            }))}
                            placeholder="Select Role"
                        />
                        <button type="submit" className="bg-blue-500 text-white p-2 rounded">
                            Add User
                        </button>
                    </form>
                </div>
            </UsersLayout>
        </AppLayout>
    );
}
