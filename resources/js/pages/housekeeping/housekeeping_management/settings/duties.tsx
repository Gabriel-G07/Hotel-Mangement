// Duties.tsx
import React, { useEffect } from 'react';
import { type BreadcrumbItem, type SharedData } from '@/types';
import { Transition } from '@headlessui/react';
import { Head, Link, useForm, usePage } from '@inertiajs/react';
import { FormEventHandler } from 'react';
import DeleteDuty from '@/components/delete-duty';
import HeadingSmall from '@/components/heading-small';
import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/app/housekeeping/housekeeping_management/app-layout';
import HouseKeepingManagementSettingsLayout from '@/layouts/settings/housekeeping_management_layout';
import { ActionMessage } from '@/components/ui/action-message';
import { Table, TableHead, TableCell, TableBody, TableRow } from '@/components/ui/table';

interface DutiesProps {
    roles: any[];
}

export default function Duties({ roles }: DutiesProps) {
    const { data, setData, post, patch, delete: destroy, errors, processing, recentlySuccessful } = useForm({
        role_name: '',
        description: '',
        role_id: '',
    });

    const [isEditing, setIsEditing] = React.useState(false);
    const [selectedDuty, setSelectedDuty] = React.useState<any>(null);

    const submit: FormEventHandler = (e) => {
        e.preventDefault();

        if (isEditing) {
            patch(route('housekeeping.management.settings.roles.update', selectedDuty?.role_id), {
                data,
                preserveScroll: true,
                onSuccess: () => {
                    setIsEditing(false);
                    setSelectedDuty(null);
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
            post(route('housekeeping.management.settings.roles.store'), {
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
        setSelectedDuty(role);
        setData({
            role_name: role.role_name,
            description: role.description,
            role_id: role.role_id,
        });
    };

    const handleCancel = () => {
        setIsEditing(false);
        setSelectedDuty(null);
        setData({
            role_name: '',
            description: '',
            role_id: '',
        });
    };

    return (
        <AppLayout breadcrumbs={[{ title: 'Duties settings', href: route('housekeeping.management.settings.roles.index') }]}>
            <Head title="Duties settings" />

            <HouseKeepingManagementSettingsLayout>
                <div className="space-y-6 flex flex-col lg:flex-row">
                    <div className="w-full lg:w-1/2">
                        <HeadingSmall title="Duties information" description="Add, Edit or Remove Duties" />
                        <div className="md:max-w-2xl max-w-xl">
                        <form onSubmit={submit} className="space-y-6">
                            <div className="grid gap-2">
                                <Label htmlFor="role_name">Duty Name</Label>
                                <Input
                                    id="role_name"
                                    className="mt-1 block w-full"
                                    value={data.role_name}
                                    onChange={(e) => setData('role_name', e.target.value)}
                                    required
                                    autoComplete="role_name"
                                    placeholder="Duty Name"
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
                                        Update Duty
                                    </Button>
                                    <Button type="button" onClick={handleCancel}>
                                        Cancel
                                    </Button>
                                </div>
                            ) : (
                                <div className="flex items-center gap-4">
                                    <Button type="submit" disabled={processing}>
                                        Add Duty
                                    </Button>
                                    <Transition
                                        show={recentlySuccessful}
                                        enter="transition ease-in-out"
                                        enterFrom="opacity-0"
                                        leave="transition ease-in-out"
                                        leaveTo="opacity-0"
                                    >
                                        <p className="text-sm text-neutral-600">Saved</p>
                                    </Transition>
                                </div>
                            )}
                        </form>
                        </div>
                    </div>

                    <div className="w-full lg:w-1/2 mt-6 lg:mt-0">
                        <Table data={roles}>
                            <TableHead>
                                <TableRow>
                                    <TableCell>Duty Name</TableCell>
                                    <TableCell>Description</TableCell>
                                <TableCell>Actions</TableCell>
                            </TableRow>
                        </TableHead>
                        <TableBody>
                            {roles.map((role, index) => (
                                <TableRow key={role.role_id} index={index}>
                                    <TableCell>{role.role_name}</TableCell>
                                    <TableCell>{role.description}</TableCell>
                                    <TableCell className="flex flex-col items-center">
                                        <Button variant="link" onClick={(e) => handleEdit(e, role)}>Edit</Button>
                                        <DeleteDuty role={role} />
                                    </TableCell>
                                </TableRow>
                            ))}
                        </TableBody>
                    </Table>
                </div>
            </div>
        </HouseKeepingManagementSettingsLayout>
    </AppLayout>
);
}
