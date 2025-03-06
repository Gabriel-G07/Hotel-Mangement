import React, { useEffect } from 'react';
import { type BreadcrumbItem, type SharedData } from '@/types';
import { Transition } from '@headlessui/react';
import { Head, Link, useForm, usePage } from '@inertiajs/react';
import { FormEventHandler } from 'react';
import DeleteRole from '@/components/delete-role';
import HeadingSmall from '@/components/heading-small';
import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/app-layout';
import SettingsLayout from '@/layouts/settings/layout';
import { ActionMessage } from '@/components/ui/action-message';
import { Table, TableHead, TableCell, TableBody, TableRow } from '@/components/ui/table';

interface RolesProps {
    roles: any[];
}

export default function Roles({ roles }: RolesProps) {
    const { data, setData, post, patch, delete: destroy, errors, processing } = useForm({
        role_name: '',
        description: '',
        role_id: '',
    });

    const [isEditing, setIsEditing] = React.useState(false);
    const [selectedRole, setSelectedRole] = React.useState<any>(null);

    const submit: FormEventHandler = (e) => {
        e.preventDefault();

        if (isEditing) {
            patch(route('roles.update', selectedRole?.role_id), {
                data,
                preserveScroll: true,
                onSuccess: () => {
                    setIsEditing(false);
                    setSelectedRole(null);
                    setData({
                        role_name: '',
                        description: '',
                        role_id: '',
                    });
                },
                onError: (errors) => {
                    console.error('Error updating role:', errors);
                },
            });
        } else {
            post(route('roles.store'), {
                data,
                preserveScroll: true,
                onSuccess: () => {
                    setData({
                        role_name: '',
                        description: '',
                        role_id: '',
                    });
                },
                onError: (errors) => {
                    console.error('Error creating role:', errors);
                },
            });
        }
    };

    const handleEdit = (e: React.MouseEvent, role: any) => {
        e.preventDefault();
        setIsEditing(true);
        setSelectedRole(role);
        setData({
            role_name: role.role_name,
            description: role.description,
            role_id: role.role_id,
        });
    };

    const handleCancel = () => {
        setIsEditing(false);
        setSelectedRole(null);
        setData({
            role_name: '',
            description: '',
            role_id: '',
        });
    };

    return (
        <AppLayout breadcrumbs={[{ title: 'Roles settings', href: route('roles.index') }]}>
            <Head title="Roles settings" />

            <SettingsLayout>
                <div className="space-y-6">
                    <HeadingSmall title="Roles information" description="Add, Edit or Remove Roles" />

                    <form onSubmit={submit} className="space-y-6">
                        <div className="grid gap-2">
                            <Label htmlFor="role_name">Role Name</Label>
                            <Input
                                id="role_name"
                                className="mt-1 block w-full"
                                value={data.role_name}
                                onChange={(e) => setData('role_name', e.target.value)}
                                required
                                autoComplete="role_name"
                                placeholder="Role Name"
                            />
                            {errors.role_name && <InputError message={errors.role_name} />}
                        </div>

                        <div className="grid gap-2">
                            <Label htmlFor="description">Description</Label>
                            <Input
                                id="description"
                                className="mt-1 block w-full"
                                value={data.description}
                                onChange={(e) => setData('description', e.target.value)}
                                required
                                autoComplete="description"
                                placeholder="Description"
                            />
                            {errors.description && <InputError message={errors.description} />}
                        </div>

                        {isEditing ? (
                            <div className="flex items-center gap-4">
                                <Button type="submit" disabled={processing}>
                                    Update Role
                                </Button>
                                <Button type="button" onClick={handleCancel}>
                                    Cancel
                                </Button>
                            </div>
                        ) : (
                            <Button type="submit" disabled={processing}>
                                Add Role
                            </Button>
                        )}
                    </form>

                    <Table>
                        <TableHead>
                            <TableRow>
                                <TableCell>Role Name</TableCell>
                                <TableCell>Description</TableCell>
                                <TableCell>Actions</TableCell>
                            </TableRow>
                        </TableHead>
                        <TableBody>
                            {roles.map((role) => (
                                <TableRow key={role.role_id}>
                                    <TableCell>{role.role_name}</TableCell>
                                    <TableCell>{role.description}</TableCell>
                                    <TableCell>
                                        <Link
                                            href="#"
                                            onClick={(e) => handleEdit(e, role)}
                                            className="text-foreground underline decoration-neutral-300 underline-offset-4 transition-colors duration-300 ease-out hover:decoration-current! dark:decoration-neutral-500"
                                        >
                                            Edit
                                        </Link>
                                        <DeleteRole role={role} />
                                    </TableCell>
                                </TableRow>
                            ))}
                        </TableBody>
                    </Table>
                </div>
            </SettingsLayout>
        </AppLayout>
    );
}
