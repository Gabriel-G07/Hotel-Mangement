import React, { useEffect } from 'react';
import { type BreadcrumbItem, type SharedData } from '@/types';
import { Transition } from '@headlessui/react';
import { Head, Link, useForm, usePage } from '@inertiajs/react';
import { FormEventHandler } from 'react';
import DeleteRoomType from '@/components/delete-room-type';
import HeadingSmall from '@/components/heading-small';
import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/app/management/app-layout';
import SettingsLayout from '@/layouts/settings/management_layout';
import { ActionMessage } from '@/components/ui/action-message';
import { Table, TableHead, TableCell, TableBody, TableRow } from '@/components/ui/table';

interface RoomTypesProps {
    roomTypes: any[];
}

export default function RoomTypes({ roomTypes }: RoomTypesProps) {
    const { data, setData, post, patch, delete: destroy, errors, processing, recentlySuccessful } = useForm({
        room_type_name: '',
        description: '',
        room_type_id: '',
    });

    const [isEditing, setIsEditing] = React.useState(false);
    const [selectedRoomType, setSelectedRoomType] = React.useState<any>(null);

    const submit: FormEventHandler = (e) => {
        e.preventDefault();

        if (isEditing) {
            patch(route('management.settings.room_types.update', selectedRoomType?.room_type_id), {
                data,
                preserveScroll: true,
                onSuccess: () => {
                    setIsEditing(false);
                    setSelectedRoomType(null);
                    setData({
                        room_type_name: '',
                        description: '',
                        room_type_id: '',
                    });
                },
                onError: (errors) => {
                    console.error('Error updating room type:', errors);
                },
            });
        } else {
            post(route('management.settings.room_types.store'), {
                data,
                preserveScroll: true,
                onSuccess: () => {
                    setData({
                        room_type_name: '',
                        description: '',
                        room_type_id: '',
                    });
                },
                onError: (errors) => {
                    console.error('Error creating room type:', errors);
                },
            });
        }
    };

    const handleEdit = (e: React.MouseEvent, roomType: any) => {
        e.preventDefault();
        setIsEditing(true);
        setSelectedRoomType(roomType);
        setData({
            room_type_name: roomType.room_type_name,
            description: roomType.description,
            room_type_id: roomType.room_type_id,
        });
    };

    const handleCancel = () => {
        setIsEditing(false);
        setSelectedRoomType(null);
        setData({
            room_type_name: '',
            description: '',
            room_type_id: '',
        });
    };

    return (
        <AppLayout breadcrumbs={[{ title: 'Room Types settings', href: route('management.settings.room_types.index') }]}>
            <Head title="Room Types settings" />

            <SettingsLayout>
                <div className="space-y-6 flex flex-col lg:flex-row">
                    <div className="w-full lg:w-1/2">
                        <HeadingSmall title="Room Types information" description="Add, Edit or Remove Room Types" />
                        <div className="md:max-w-2xl max-w-xl">
                        <form onSubmit={submit} className="space-y-6">
                            <div className="grid gap-2">
                                <Label htmlFor="room_type_name">Room Type Name</Label>
                                <Input
                                    id="room_type_name"
                                    className="mt-1 block w-full"
                                    value={data.room_type_name}
                                    onChange={(e) => setData('room_type_name', e.target.value)}
                                    required
                                    autoComplete="room_type_name"
                                    placeholder="Room Type Name"
                                />
                                {errors.room_type_name && <InputError message={errors.room_type_name} />}
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="description">Description</Label>
                                <Input
                                    id="description"
                                    className="mt-1 block w-full"
                                    value={data.description}
                                    onChange={(e) => setData('description', e.target.value)}
                                    autoComplete="description"
                                    placeholder="Description"
                                />
                                {errors.description && <InputError message={errors.description} />}
                            </div>

                            {isEditing ? (
                                <div className="flex items-center gap-4">
                                    <Button type="submit" disabled={processing}>
                                        Update Room Type
                                    </Button>
                                    <Button type="button" onClick={handleCancel}>
                                        Cancel
                                    </Button>
                                </div>
                            ) : (
                                <div className="flex items-center gap-4">
                                    <Button type="submit" disabled={processing}>
                                        Add Room Type
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
                        <Table data={roomTypes}>
                            <TableHead>
                                <TableRow>
                                    <TableCell>Room Type Name</TableCell>
                                    <TableCell>Description</TableCell>
                                    <TableCell>Actions</TableCell>
                                </TableRow>
                            </TableHead>
                            <TableBody>
                                {roomTypes.map((roomType, index) => (
                                    <TableRow key={roomType.room_type_id} index={index}>
                                        <TableCell>{roomType.room_type_name}</TableCell>
                                        <TableCell>{roomType.description}</TableCell>
                                        <TableCell className="flex flex-col items-center">
                                            <Button variant="link" onClick={(e) => handleEdit(e, roomType)}>Edit</Button>
                                            <DeleteRoomType roomType={roomType} />
                                        </TableCell>
                                    </TableRow>
                                ))}
                            </TableBody>
                        </Table>
                    </div>
                </div>
            </SettingsLayout>
        </AppLayout>
    );
}
