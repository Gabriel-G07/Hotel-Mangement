import React, { useEffect } from 'react';
import { type BreadcrumbItem, type SharedData } from '@/types';
import { Transition } from '@headlessui/react';
import { Head, Link, useForm, usePage } from '@inertiajs/react';
import { FormEventHandler } from 'react';
import DeleteRoom from '@/components/delete-room';
import HeadingSmall from '@/components/heading-small';
import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/app/management/app-layout';
import SettingsLayout from '@/layouts/settings/management_layout';
import { ActionMessage } from '@/components/ui/action-message';
import { Table, TableHead, TableCell, TableBody, TableRow } from '@/components/ui/table';
import { CustomSelect } from '@/components/ui/custom-select';

interface RoomsProps {
    rooms: any[];
    roomTypes: any[];
    baseCurrency: any;
}

export default function Rooms({ rooms, roomTypes, baseCurrency }: RoomsProps) {
    const { data, setData, post, patch, delete: destroy, errors, processing, recentlySuccessful } = useForm({
        room_number: '',
        room_type_id: '',
        price_per_night: '',
        room_id: '',
        currency_id: baseCurrency?.currency_id || '',
    });

    const [isEditing, setIsEditing] = React.useState(false);
    const [selectedRoom, setSelectedRoom] = React.useState<any>(null);

    const submit: FormEventHandler = (e) => {
        e.preventDefault();

        if (isEditing) {
            patch(route('management.settings.rooms.update', selectedRoom?.room_id), {
                data,
                preserveScroll: true,
                onSuccess: () => {
                    setIsEditing(false);
                    setSelectedRoom(null);
                    setData({
                        room_number: '',
                        room_type_id: '',
                        price_per_night: '',
                        room_id: '',
                        currency_id: baseCurrency?.currency_id || '',
                    });
                },
                onError: (errors) => {
                    console.error('Error updating room:', errors);
                },
            });
        } else {
            post(route('management.settings.rooms.store'), {
                data,
                preserveScroll: true,
                onSuccess: () => {
                    setData({
                        room_number: '',
                        room_type_id: '',
                        price_per_night: '',
                        room_id: '',
                        currency_id: baseCurrency?.currency_id || '',
                    });
                },
                onError: (errors) => {
                    console.error('Error creating room:', errors);
                },
            });
        }
    };

    const handleEdit = (e: React.MouseEvent, room: any) => {
        e.preventDefault();
        setIsEditing(true);
        setSelectedRoom(room);
        setData({
            room_number: room.room_number,
            room_type_id: room.room_type_id,
            price_per_night: room.price_per_night,
            room_id: room.room_id,
            currency_id: baseCurrency?.currency_id || '',
        });
    };

    const handleCancel = () => {
        setIsEditing(false);
        setSelectedRoom(null);
        setData({
            room_number: '',
            room_type_id: '',
            price_per_night: '',
            room_id: '',
            currency_id: baseCurrency?.currency_id || '',
        });
    };

    return (
        <AppLayout breadcrumbs={[{ title: 'Rooms settings', href: route('management.settings.rooms.index') }]}>
            <Head title="Rooms settings" />

            <SettingsLayout>
                <div className="space-y-6 flex flex-col lg:flex-row">
                    <div className="w-full lg:w-1/2">
                        <HeadingSmall title="Rooms information" description="Add, Edit or Remove Rooms" />
                        <div className="md:max-w-2xl max-w-xl">
                        <form onSubmit={submit} className="space-y-6">
                            <div className="grid gap-2">
                                <Label htmlFor="room_number">Room Number</Label>
                                <Input
                                    id="room_number"
                                    className="mt-1 block w-full"
                                    value={data.room_number}
                                    onChange={(e) => setData('room_number', e.target.value)}
                                    required
                                    autoComplete="off"
                                    placeholder="Room Number"
                                />
                                {errors.room_number && <InputError message={errors.room_number} />}
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="room_type_id">Room Type</Label>
                                <CustomSelect
                                    id="room_type_id"
                                    name="room_type_id"
                                    value={data.room_type_id}
                                    onChange={(e) => setData('room_type_id', e.target.value)}
                                    options={roomTypes.map((roomType) => ({
                                        value: roomType.room_type_id,
                                        label: roomType.room_type_name
                                    }))}
                                    placeholder="Select Room Type"
                                    required
                                />
                                {errors.room_type_id && <InputError message={errors.room_type_id} />}
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="price_per_night">Price Per Night ({baseCurrency?.currency_code})</Label>
                                <Input
                                    id="price_per_night"
                                    className="mt-1 block w-full"
                                    value={data.price_per_night}
                                    onChange={(e) => setData('price_per_night', e.target.value)}
                                    required
                                    autoComplete="price_per_night"
                                    placeholder={`Price Per Night (${baseCurrency?.currency_code})`}
                                />
                                {errors.price_per_night && <InputError message={errors.price_per_night} />}
                            </div>

                            {isEditing ? (
                                <div className="flex items-center gap-4">
                                    <Button type="submit" disabled={processing}>
                                        Update Room
                                    </Button>
                                    <Button type="button" onClick={handleCancel}>
                                        Cancel
                                    </Button>
                                </div>
                            ) : (
                                <div className="flex items-center gap-4">
                                    <Button type="submit" disabled={processing}>
                                        Add Room
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
                        <Table data={rooms}>
                            <TableHead>
                                <TableRow>
                                    <TableCell>Room Number</TableCell>
                                    <TableCell>Room Type</TableCell>
                                    <TableCell>Price ({baseCurrency?.currency_code})</TableCell>
                                    <TableCell>Is Available</TableCell>
                                    <TableCell>Actions</TableCell>
                                </TableRow>
                            </TableHead>
                            <TableBody>
                                {rooms.map((room, index) => (
                                    <TableRow key={room.room_id} index={index}>
                                        <TableCell>{room.room_number}</TableCell>
                                        <TableCell>{room.room_type.room_type_name}</TableCell>
                                        <TableCell>{room.price_per_night}</TableCell>
                                        <TableCell>{room.is_available ? 'Yes' : 'No'}</TableCell>
                                        <TableCell className="flex flex-col items-center">
                                            <Button variant="link" onClick={(e) => handleEdit(e, room)}>Edit</Button>
                                            <DeleteRoom room={room} />
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
