import React, { useState, useEffect } from 'react';
import AppLayout from '@/layouts/app/reception/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/react';
import { Transition } from '@headlessui/react';
import InputError from '@/components/input-error';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';
import { Card } from '@/components/ui/card';
import { Label } from '@/components/ui/label';
import { CustomSelect } from '@/components/ui/custom-select';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Reception Bookings',
        href: '/reception/bookings',
    },
];

interface BookingInfo {
    guestNationalId: string;
    guestName: string;
    guestSurname: string;
    guestemail: string;
    guestphone: string;
    guestAddress: string;
    selectedRooms: string[];
    checkInDate: Date;
    checkOutDate: Date;
}

interface BookingsProps {
    availableRoomTypes: any[];
    availableRooms: any[];
    availableGuests: any[];
    bookingInfo: BookingInfo[];
}

// Component to display user suggestions
const UserSuggestions = ({ suggestions, onSelect }) => {
    return (
        <div className="bg-white dark:bg-gray-800 border rounded shadow p-2 mt-2">
            {suggestions.map(user => (
                <div
                    key={user.id}
                    className="p-2 hover:bg-gray-200 dark:hover:bg-gray-700 cursor-pointer"
                    onClick={() => onSelect(user)}
                >
                    <p>{user.first_name} {user.last_name} <a className="text-sm text-gray-500"> {user.email}</a></p>

                </div>
            ))}
        </div>
    );
};

export default function Bookings({ availableRoomTypes, availableRooms, availableGuests }: BookingsProps) {
    const { data, setData, post, errors, processing, recentlySuccessful, reset } = useForm({
        bookingType: 'Self', // Default to "Self"
        guestNationalId: '',
        guestName: '',
        guestSurname: '',
        guestemail: '',
        guestphone: '',
        guestAddress: '',
        bookerNationalId: '',
        bookerName: '',
        bookerSurname: '',
        bookeremail: '',
        bookerphone: '',
        selectedRooms: [] as string[],
        checkInDate: '',
        checkOutDate: '',
    });

    const [selectedRooms, setSelectedRooms] = useState([{ availableRoomType: '', roomNumber: '', roomPrice: '' }]);
    const [suggestedUsers, setSuggestedUsers] = useState([]);
    const [roomOptions, setRoomOptions] = useState([]);
    const [price, setPrice] = useState(0);
    const [users, setUsers] = useState([]);
    const [activeInput, setActiveInput] = useState('');
    const [availableRoomsCount, setAvailableRoomsCount] = useState({});

    useEffect(() => {
        const countRoomsAndTypes = async () => {
            const count = {};
            availableRooms.forEach(room => {
                if (room.is_available) {
                    count[room.room_type_id] = (count[room.room_type_id] || 0) + 1;
                }
            });
            setAvailableRoomsCount(count);
        };

        countRoomsAndTypes();
    }, [availableRooms]);

    useEffect(() => {
        if (Array.isArray(availableGuests)) {
            setUsers(availableGuests);
        } else {
            console.error('Unexpected format for availableGuests:', availableGuests);
        }
    }, [availableGuests]);

    const filterSuggestedUsers = (query: string, inputType: string) => {
        if (!query.trim()) {
            setSuggestedUsers([]);
            return;
        }

        let filteredUsers = [];
        switch (inputType) {
            case 'guestNationalId':
                filteredUsers = users.filter(user =>
                    user.national_id_number && user.national_id_number.toLowerCase().includes(query.toLowerCase())
                );
                break;
            case 'guestphone':
                filteredUsers = users.filter(user =>
                    user.phone_number && user.phone_number.toLowerCase().includes(query.toLowerCase())
                );
                break;
            case 'guestemail':
                filteredUsers = users.filter(user =>
                    user.email && user.email.toLowerCase().includes(query.toLowerCase())
                );
                break;
            case 'guestName':
                filteredUsers = users.filter(user =>
                    user.first_name && user.first_name.toLowerCase().includes(query.toLowerCase())
                );
                break;
            case 'guestSurname':
                filteredUsers = users.filter(user =>
                    user.last_name && user.last_name.toLowerCase().includes(query.toLowerCase())
                );
                break;
            case 'bookerNationalId':
                filteredUsers = users.filter(user =>
                    user.national_id_number && user.national_id_number.toLowerCase().includes(query.toLowerCase())
                );
                break;
            case 'bookerphone':
                filteredUsers = users.filter(user =>
                    user.phone_number && user.phone_number.toLowerCase().includes(query.toLowerCase())
                );
                break;
            case 'bookeremail':
                filteredUsers = users.filter(user =>
                    user.email && user.email.toLowerCase().includes(query.toLowerCase())
                );
                break;
            case 'bookerName':
                filteredUsers = users.filter(user =>
                    user.first_name && user.first_name.toLowerCase().includes(query.toLowerCase())
                );
                break;
            case 'bookerSurname':
                filteredUsers = users.filter(user =>
                    user.last_name && user.last_name.toLowerCase().includes(query.toLowerCase())
                );
                break;
            default:
                filteredUsers = [];
        }

        setSuggestedUsers(filteredUsers);
    };

    const handleInputChange = (e: React.ChangeEvent<HTMLInputElement>, inputType: string) => {
        const value = e.target.value;
        setActiveInput(inputType);
        setData(inputType, value);
        filterSuggestedUsers(value, inputType);
    };

    const handleUserSelect = (user) => {
        if (activeInput.startsWith('booker')) {
            setData({
                ...data,
                bookerNationalId: user.national_id_number,
                bookerName: user.first_name,
                bookerSurname: user.last_name,
                bookeremail: user.email,
                bookerphone: user.phone_number,
            });
        } else {
            setData({
                ...data,
                guestNationalId: user.national_id_number,
                guestName: user.first_name,
                guestSurname: user.last_name,
                guestemail: user.email,
                guestphone: user.phone_number,
                guestAddress: user.address,
            });
        }
        setSuggestedUsers([]);
    };

    const handleBookingTypeChange = (e: React.ChangeEvent<HTMLSelectElement>) => {
        setData('bookingType', e.target.value);
    };

    const handleAddRoom = () => {
        setSelectedRooms([...selectedRooms, { availableRoomType: '', roomNumber: '', roomPrice: '' }]);
    };

    const handleRoomTypeButtonClick = (index, roomTypeId) => {
        const newSelectedRooms = [...selectedRooms];
        newSelectedRooms[index] = { ...newSelectedRooms[index], availableRoomType: roomTypeId };

        const filteredRooms = availableRooms.filter(room => room.room_type_id === roomTypeId);
        setRoomOptions(filteredRooms.map(room => ({
            value: room.room_number,
            label: `${room.room_number} - ${room.price_per_night}`
        })));

        if (filteredRooms.length > 0) {
            const randomRoom = filteredRooms[Math.floor(Math.random() * filteredRooms.length)];
            newSelectedRooms[index].roomNumber = randomRoom.room_number;
            newSelectedRooms[index].roomPrice = randomRoom.price_per_night;
        } else {
            newSelectedRooms[index].roomNumber = '';
            newSelectedRooms[index].roomPrice = '';
        }

        setSelectedRooms(newSelectedRooms);
        calculatePrice(newSelectedRooms);
    };

    const handleReselectRoom = (index) => {
        const newSelectedRooms = [...selectedRooms];
        const roomTypeId = newSelectedRooms[index].availableRoomType;

        const filteredRooms = availableRooms.filter(room => room.room_type_id === roomTypeId);

        if (filteredRooms.length > 0) {
            const randomRoom = filteredRooms[Math.floor(Math.random() * filteredRooms.length)];
            newSelectedRooms[index].roomNumber = randomRoom.room_number;
            newSelectedRooms[index].roomPrice = randomRoom.price_per_night;
        } else {
            newSelectedRooms[index].roomNumber = '';
            newSelectedRooms[index].roomPrice = '';
        }

        setSelectedRooms(newSelectedRooms);
        calculatePrice(newSelectedRooms);
    };

    const calculatePrice = (selectedRooms) => {
        const totalPrice = selectedRooms.reduce((acc, room) => acc + (parseFloat(room.roomPrice) || 0), 0);
        setPrice(totalPrice);
    };

    const handleRemoveRoom = (index) => {
        const newSelectedRooms = selectedRooms.filter((_, i) => i !== index);
        setSelectedRooms(newSelectedRooms);
        calculatePrice(newSelectedRooms);
    };

    useEffect(() => {
        const selectedRoomNumbers = selectedRooms.map(room => room.roomNumber);
        setData('selectedRooms', selectedRoomNumbers);
    }, [selectedRooms]);

    const handleSubmit = async (e) => {
        e.preventDefault();

        const selectedRoomNumbers = selectedRooms.map(room => room.roomNumber);

        const requestData = {
            ...data,
            selectedRooms: selectedRoomNumbers,
        };
        console.log('Data being sent to the backend:', requestData);

        post(route('reception.bookings.store'), requestData, {
            preserveScroll: true,
            onSuccess: () => {
                reset();
                setSelectedRooms([{ availableRoomType: '', roomNumber: '', roomPrice: '' }]);
                setPrice(0);
            },
            onError: (errors) => {
                console.error('Error creating booking:', errors);
            },
        });
    };

    const handleCancel = () => {
        reset();
        setSelectedRooms([{ availableRoomType: '', roomNumber: '', roomPrice: '' }]);
        setPrice(0);
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Bookings On Reception" />
            <div className="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
                {/* Layout for "Self" */}
                {data.bookingType === 'Self' && (
                    <div className="grid gap-4 md:grid-cols-2">
                        {/* Guest Information Card */}
                        <Card className="p-6">
                            <h2 className="text-lg font-semibold">Guest Information</h2>
                            <div className="space-y-6">
                                <div className="grid gap-2">
                                    <Label htmlFor="bookingType">Booking Type</Label>
                                    <CustomSelect
                                        id="bookingType"
                                        name="bookingType"
                                        value={data.bookingType}
                                        onChange={handleBookingTypeChange}
                                        options={[
                                            { value: 'Self', label: 'Self' },
                                            { value: 'Other', label: 'Other' },
                                        ]}
                                    />
                                </div>
                                <div className="grid gap-2">
                                    <Label htmlFor="nationalId">Guest National ID Number</Label>
                                    <Input
                                        id="nationalId"
                                        name="guestNationalId"
                                        value={data.guestNationalId}
                                        onChange={(e) => handleInputChange(e, 'guestNationalId')}
                                        required
                                        placeholder="Enter National ID Number"
                                        autoComplete="off"
                                    />
                                    {errors.guestNationalId && <InputError message={errors.guestNationalId} />}
                                    {activeInput === 'guestNationalId' && suggestedUsers.length > 0 && (
                                        <UserSuggestions
                                            suggestions={suggestedUsers}
                                            onSelect={handleUserSelect}
                                        />
                                    )}
                                </div>
                                <div className="grid gap-2">
                                    <Label htmlFor="guestName">Guest Name</Label>
                                    <Input
                                        id="guestName"
                                        name="guestName"
                                        value={data.guestName}
                                        onChange={(e) => handleInputChange(e, 'guestName')}
                                        required
                                        placeholder="Enter Guest Name"
                                        autoComplete="off"
                                    />
                                    {errors.guestName && <InputError message={errors.guestName} />}
                                    {activeInput === 'guestName' && suggestedUsers.length > 0 && (
                                        <UserSuggestions
                                            suggestions={suggestedUsers}
                                            onSelect={handleUserSelect}
                                        />
                                    )}
                                </div>
                                <div className="grid gap-2">
                                    <Label htmlFor="guestSurname">Guest Surname</Label>
                                    <Input
                                        id="guestSurname"
                                        name="guestSurname"
                                        value={data.guestSurname}
                                        onChange={(e) => handleInputChange(e, 'guestSurname')}
                                        required
                                        placeholder="Enter Guest Surname"
                                        autoComplete="off"
                                    />
                                    {errors.guestSurname && <InputError message={errors.guestSurname} />}
                                    {activeInput === 'guestSurname' && suggestedUsers.length > 0 && (
                                        <UserSuggestions
                                            suggestions={suggestedUsers}
                                            onSelect={handleUserSelect}
                                        />
                                    )}
                                </div>
                                <div className="grid gap-2">
                                    <Label htmlFor="guestemail">Guest Email</Label>
                                    <Input
                                        id="guestemail"
                                        name="guestemail"
                                        type="email"
                                        value={data.guestemail}
                                        onChange={(e) => handleInputChange(e, 'guestemail')}
                                        required
                                        placeholder="Enter Guest Email"
                                        autoComplete="off"
                                    />
                                    {errors.guestemail && <InputError message={errors.guestemail} />}
                                    {activeInput === 'guestemail' && suggestedUsers.length > 0 && (
                                        <UserSuggestions
                                            suggestions={suggestedUsers}
                                            onSelect={handleUserSelect}
                                        />
                                    )}
                                </div>
                                <div className="grid gap-2">
                                    <Label htmlFor="guestphone">Guest Phone</Label>
                                    <Input
                                        id="guestphone"
                                        name="guestphone"
                                        value={data.guestphone}
                                        onChange={(e) => handleInputChange(e, 'guestphone')}
                                        required
                                        placeholder="Enter Guest Phone"
                                        autoComplete="off"
                                    />
                                    {errors.guestphone && <InputError message={errors.guestphone} />}
                                    {activeInput === 'guestphone' && suggestedUsers.length > 0 && (
                                        <UserSuggestions
                                            suggestions={suggestedUsers}
                                            onSelect={handleUserSelect}
                                        />
                                    )}
                                </div>
                                <div className="grid gap-2">
                                    <Label htmlFor="guestAddress">Guest Address</Label>
                                    <Input
                                        id="guestAddress"
                                        name="guestAddress"
                                        value={data.guestAddress}
                                        onChange={(e) => handleInputChange(e, 'guestAddress')}
                                        required
                                        placeholder="Enter Guest Address"
                                        autoComplete="off"
                                    />
                                    {errors.guestAddress && <InputError message={errors.guestAddress} />}
                                </div>
                            </div>
                        </Card>

                        {/* Selected Rooms Card */}
                        <Card className="p-6">
                            <h2 className="text-lg font-semibold">Selected Rooms</h2>
                            <div className="space-y-6">
                                <div className="grid gap-2">
                                    <Label htmlFor="checkInDate">Check-In Date</Label>
                                    <Input
                                        id="checkInDate"
                                        name="checkInDate"
                                        type="date"
                                        value={data.checkInDate}
                                        onChange={(e) => setData('checkInDate', e.target.value)}
                                        required
                                        autoComplete="off"
                                    />
                                    {errors.checkInDate && <InputError message={errors.checkInDate} />}
                                </div>
                                <div className="grid gap-2">
                                    <Label htmlFor="checkOutDate">Check-Out Date</Label>
                                    <Input
                                        id="checkOutDate"
                                        name="checkOutDate"
                                        type="date"
                                        value={data.checkOutDate}
                                        onChange={(e) => setData('checkOutDate', e.target.value)}
                                        required
                                        autoComplete="off"
                                    />
                                    {errors.checkOutDate && <InputError message={errors.checkOutDate} />}
                                </div>
                                {selectedRooms.map((room, index) => (
                                    <div key={index} className="space-y-6">
                                        <div className="grid gap-2">
                                            <Label>Room Type</Label>
                                            <div className="flex flex-wrap gap-2">
                                                {availableRoomTypes.map(availableRoomType => (
                                                    <Button
                                                        className="mt-4"
                                                        key={availableRoomType.room_type_id}
                                                        type="button"
                                                        onClick={() => handleRoomTypeButtonClick(index, availableRoomType.room_type_id)}
                                                    >
                                                        {availableRoomType.room_type_name} ({availableRoomsCount[availableRoomType.room_type_id] || 0})
                                                    </Button>
                                                ))}
                                            </div>
                                        </div>
                                        {room.roomNumber && (
                                            <div className="grid gap-2">
                                                <Label>Room Number</Label>
                                                <div className="flex items-center">
                                                    <p className="mt-4">{room.roomNumber}</p>
                                                    <Button type="button" onClick={() => handleReselectRoom(index)} className="ml-2">
                                                        Reselect Room
                                                    </Button>
                                                </div>
                                            </div>
                                        )}
                                        {room.roomPrice && (
                                            <div className="grid gap-2">
                                                <Label>Room Price</Label>
                                                <div className="flex items-center">
                                                    <p className="mt-4">{room.roomPrice}</p>
                                                </div>
                                            </div>
                                        )}
                                        <Button type="button" onClick={() => handleRemoveRoom(index)} className="mt-4">
                                            Remove Room
                                        </Button>
                                    </div>
                                ))}
                                <Button type="button" onClick={handleAddRoom} className="mt-4">
                                    Add Another Room
                                </Button>
                                {selectedRooms.length > 0 && selectedRooms.some(room => room.roomNumber) && (
                                    <div className="grid gap-2">
                                        <Label htmlFor="price">Total Price</Label>
                                        <p id="price" className="mt-1">{price}</p>
                                    </div>
                                )}
                            </div>
                            <div className="flex justify-start mt-4 space-x-4">
                                <Button type="submit" onClick={handleSubmit} disabled={processing}>
                                    Book
                                </Button>
                            </div>
                            <div className="flex justify-start mt-4 space-x-4"><br></br>
                                <Button type="button" className="mt-4">
                                    Pay
                                </Button>
                                <Button type="button" className="mt-4" onClick={handleCancel}>
                                    Cancel
                                </Button>
                            </div>
                        </Card>
                    </div>
                )}

                {/* Layout for "Other" */}
                {data.bookingType === 'Other' && (
                    <>
                        <div className="grid gap-4 md:grid-cols-2">
                            {/* Guest Information Card */}
                            <Card className="p-6">
                                <h2 className="text-lg font-semibold">Guest Information</h2>
                                <div className="space-y-6">
                                    <div className="grid gap-2">
                                        <Label htmlFor="bookingType">Booking Type</Label>
                                        <CustomSelect
                                            id="bookingType"
                                            name="bookingType"
                                            value={data.bookingType}
                                            onChange={handleBookingTypeChange}
                                            options={[
                                                { value: 'Self', label: 'Self' },
                                                { value: 'Other', label: 'Other' },
                                            ]}
                                        />
                                    </div>
                                    <div className="grid gap-2">
                                        <Label htmlFor="nationalId">Guest National ID Number</Label>
                                        <Input
                                            id="nationalId"
                                            name="guestNationalId"
                                            value={data.guestNationalId}
                                            onChange={(e) => handleInputChange(e, 'guestNationalId')}
                                            required
                                            placeholder="Enter National ID Number"
                                            autoComplete="off"
                                        />
                                        {errors.guestNationalId && <InputError message={errors.guestNationalId} />}
                                        {activeInput === 'guestNationalId' && suggestedUsers.length > 0 && (
                                            <UserSuggestions
                                                suggestions={suggestedUsers}
                                                onSelect={handleUserSelect}
                                            />
                                        )}
                                    </div>
                                    <div className="grid gap-2">
                                        <Label htmlFor="guestName">Guest Name</Label>
                                        <Input
                                            id="guestName"
                                            name="guestName"
                                            value={data.guestName}
                                            onChange={(e) => handleInputChange(e, 'guestName')}
                                            required
                                            placeholder="Enter Guest Name"
                                            autoComplete="off"
                                        />
                                        {errors.guestName && <InputError message={errors.guestName} />}
                                        {activeInput === 'guestName' && suggestedUsers.length > 0 && (
                                            <UserSuggestions
                                                suggestions={suggestedUsers}
                                                onSelect={handleUserSelect}
                                            />
                                        )}
                                    </div>
                                    <div className="grid gap-2">
                                        <Label htmlFor="guestSurname">Guest Surname</Label>
                                        <Input
                                            id="guestSurname"
                                            name="guestSurname"
                                            value={data.guestSurname}
                                            onChange={(e) => handleInputChange(e, 'guestSurname')}
                                            required
                                            placeholder="Enter Guest Surname"
                                            autoComplete="off"
                                        />
                                        {errors.guestSurname && <InputError message={errors.guestSurname} />}
                                        {activeInput === 'guestSurname' && suggestedUsers.length > 0 && (
                                            <UserSuggestions
                                                suggestions={suggestedUsers}
                                                onSelect={handleUserSelect}
                                            />
                                        )}
                                    </div>
                                    <div className="grid gap-2">
                                        <Label htmlFor="guestemail">Guest Email</Label>
                                        <Input
                                            id="guestemail"
                                            name="guestemail"
                                            type="email"
                                            value={data.guestemail}
                                            onChange={(e) => handleInputChange(e, 'guestemail')}
                                            required
                                            placeholder="Enter Guest Email"
                                            autoComplete="off"
                                        />
                                        {errors.guestemail && <InputError message={errors.guestemail} />}
                                        {activeInput === 'guestemail' && suggestedUsers.length > 0 && (
                                            <UserSuggestions
                                                suggestions={suggestedUsers}
                                                onSelect={handleUserSelect}
                                            />
                                        )}
                                    </div>
                                    <div className="grid gap-2">
                                        <Label htmlFor="guestphone">Guest Phone</Label>
                                        <Input
                                            id="guestphone"
                                            name="guestphone"
                                            value={data.guestphone}
                                            onChange={(e) => handleInputChange(e, 'guestphone')}
                                            required
                                            placeholder="Enter Guest Phone"
                                            autoComplete="off"
                                        />
                                        {errors.guestphone && <InputError message={errors.guestphone} />}
                                        {activeInput === 'guestphone' && suggestedUsers.length > 0 && (
                                            <UserSuggestions
                                                suggestions={suggestedUsers}
                                                onSelect={handleUserSelect}
                                            />
                                        )}
                                    </div>
                                    <div className="grid gap-2">
                                        <Label htmlFor="guestAddress">Guest Address</Label>
                                        <Input
                                            id="guestAddress"
                                            name="guestAddress"
                                            value={data.guestAddress}
                                            onChange={(e) => handleInputChange(e, 'guestAddress')}
                                            required
                                            placeholder="Enter Guest Address"
                                            autoComplete="off"
                                        />
                                        {errors.guestAddress && <InputError message={errors.guestAddress} />}
                                    </div>
                                </div>
                            </Card>

                            {/* Booker Information Card */}
                            <Card className="p-6">
                                <h2 className="text-lg font-semibold">Booker Information</h2>
                                <div className="space-y-6">
                                    <div className="grid gap-2">
                                        <Label htmlFor="bookerNationalId">Booker National ID Number</Label>
                                        <Input
                                            id="bookerNationalId"
                                            name="bookerNationalId"
                                            value={data.bookerNationalId}
                                            onChange={(e) => handleInputChange(e, 'bookerNationalId')}
                                            required
                                            placeholder="Enter Booker's National ID Number"
                                            autoComplete="off"
                                        />
                                        {errors.bookerNationalId && <InputError message={errors.bookerNationalId} />}
                                        {activeInput === 'bookerNationalId' && suggestedUsers.length > 0 && (
                                            <UserSuggestions
                                                suggestions={suggestedUsers}
                                                onSelect={handleUserSelect}
                                            />
                                        )}
                                    </div>
                                    <div className="grid gap-2">
                                        <Label htmlFor="bookerName">Booker Name</Label>
                                        <Input
                                            id="bookerName"
                                            name="bookerName"
                                            value={data.bookerName}
                                            onChange={(e) => handleInputChange(e, 'bookerName')}
                                            required
                                            placeholder="Enter Booker's Name"
                                            autoComplete="off"
                                        />
                                        {errors.bookerName && <InputError message={errors.bookerName} />}
                                        {activeInput === 'bookerName' && suggestedUsers.length > 0 && (
                                            <UserSuggestions
                                                suggestions={suggestedUsers}
                                                onSelect={handleUserSelect}
                                            />
                                        )}
                                    </div>
                                    <div className="grid gap-2">
                                        <Label htmlFor="bookerSurname">Booker Surname</Label>
                                        <Input
                                            id="bookerSurname"
                                            name="bookerSurname"
                                            value={data.bookerSurname}
                                            onChange={(e) => handleInputChange(e, 'bookerSurname')}
                                            required
                                            placeholder="Enter Booker's Surname"
                                            autoComplete="off"
                                        />
                                        {errors.bookerSurname && <InputError message={errors.bookerSurname} />}
                                        {activeInput === 'bookerSurname' && suggestedUsers.length > 0 && (
                                            <UserSuggestions
                                                suggestions={suggestedUsers}
                                                onSelect={handleUserSelect}
                                            />
                                        )}
                                    </div>
                                    <div className="grid gap-2">
                                        <Label htmlFor="bookeremail">Booker Email</Label>
                                        <Input
                                            id="bookeremail"
                                            name="bookeremail"
                                            type="email"
                                            value={data.bookeremail}
                                            onChange={(e) => handleInputChange(e, 'bookeremail')}
                                            required
                                            placeholder="Enter Booker's Email"
                                            autoComplete="off"
                                        />
                                        {errors.bookeremail && <InputError message={errors.bookeremail} />}
                                        {activeInput === 'bookeremail' && suggestedUsers.length > 0 && (
                                            <UserSuggestions
                                                suggestions={suggestedUsers}
                                                onSelect={handleUserSelect}
                                            />
                                        )}
                                    </div>
                                    <div className="grid gap-2">
                                        <Label htmlFor="bookerphone">Booker Phone</Label>
                                        <Input
                                            id="bookerphone"
                                            name="bookerphone"
                                            value={data.bookerphone}
                                            onChange={(e) => handleInputChange(e, 'bookerphone')}
                                            required
                                            placeholder="Enter Booker's Phone"
                                            autoComplete="off"
                                        />
                                        {errors.bookerphone && <InputError message={errors.bookerphone} />}
                                        {activeInput === 'bookerphone' && suggestedUsers.length > 0 && (
                                            <UserSuggestions
                                                suggestions={suggestedUsers}
                                                onSelect={handleUserSelect}
                                            />
                                        )}
                                    </div>
                                </div>
                            </Card>
                        </div>

                        {/* Selected Rooms Card */}
                        <Card className="p-6 w-full">
                            <h2 className="text-lg font-semibold">Selected Rooms</h2>
                            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                {/* Left Side: Date Inputs */}
                                <div className="space-y-6">
                                    <div className="grid gap-2">
                                        <Label htmlFor="checkInDate">Check-In Date</Label>
                                        <Input
                                            id="checkInDate"
                                            name="checkInDate"
                                            type="date"
                                            value={data.checkInDate}
                                            onChange={(e) => setData('checkInDate', e.target.value)}
                                            required
                                            autoComplete="off"
                                        />
                                        {errors.checkInDate && <InputError message={errors.checkInDate} />}
                                    </div>
                                    <div className="grid gap-2">
                                        <Label htmlFor="checkOutDate">Check-Out Date</Label>
                                        <Input
                                            id="checkOutDate"
                                            name="checkOutDate"
                                            type="date"
                                            value={data.checkOutDate}
                                            onChange={(e) => setData('checkOutDate', e.target.value)}
                                            required
                                            autoComplete="off"
                                        />
                                        {errors.checkOutDate && <InputError message={errors.checkOutDate} />}
                                    </div>
                                    <div className="flex justify-start mt-4 space-x-4">
                                        <Button type="submit" onClick={handleSubmit} disabled={processing}>
                                            Book
                                        </Button>
                                    </div>
                                    <div className="flex justify-start mt-4 space-x-4"><br></br>
                                        <Button type="button" className="mt-4">
                                            Pay
                                        </Button>
                                        <Button type="button" className="mt-4" onClick={handleCancel}>
                                            Cancel
                                        </Button>
                                    </div>
                                </div>

                                {/* Right Side: Room Buttons */}
                                <div className="space-y-6">
                                    {selectedRooms.map((room, index) => (
                                        <div key={index} className="space-y-6">
                                            <div className="grid gap-2">
                                                <Label>Room Type</Label>
                                                <div className="flex flex-wrap gap-2">
                                                    {availableRoomTypes.map(availableRoomType => (
                                                        <Button
                                                            className="mt-4"
                                                            key={availableRoomType.room_type_id}
                                                            type="button"
                                                            onClick={() => handleRoomTypeButtonClick(index, availableRoomType.room_type_id)}
                                                        >
                                                            {availableRoomType.room_type_name} ({availableRoomsCount[availableRoomType.room_type_id] || 0})
                                                        </Button>
                                                    ))}
                                                </div>
                                            </div>
                                            {room.roomNumber && (
                                                <div className="grid gap-2">
                                                    <Label>Room Number</Label>
                                                    <div className="flex items-center">
                                                        <p className="mt-4">{room.roomNumber}</p>
                                                        <Button type="button" onClick={() => handleReselectRoom(index)} className="ml-2">
                                                            Reselect Room
                                                        </Button>
                                                    </div>
                                                </div>
                                            )}
                                            {room.roomPrice && (
                                                <div className="grid gap-2">
                                                    <Label>Room Price</Label>
                                                    <div className="flex items-center">
                                                        <p className="mt-4">{room.roomPrice}</p>
                                                    </div>
                                                </div>
                                            )}
                                            <Button type="button" onClick={() => handleRemoveRoom(index)} className="mt-4">
                                                Remove Room
                                            </Button>
                                        </div>
                                    ))}
                                    <Button type="button" onClick={handleAddRoom} className="mt-4">
                                        Add Another Room
                                    </Button>
                                </div>
                            </div>
                        </Card>
                    </>
                )}
            </div>
        </AppLayout>
    );
}
