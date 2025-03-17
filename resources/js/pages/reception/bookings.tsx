import { useState, useEffect } from 'react';
import AppLayout from '@/layouts/app/reception/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/react';
import axios from 'axios';
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

export default function Bookings() {
    const [nationalId, setNationalId] = useState('');
    const [guestName, setGuestName] = useState('');
    const [surname, setSurname] = useState('');
    const [email, setEmail] = useState('');
    const [phone, setPhone] = useState('');
    const [homeAddress, setHomeAddress] = useState('');
    const [roomTypes, setRoomTypes] = useState([]);
    const [rooms, setRooms] = useState([]);
    const [selectedRooms, setSelectedRooms] = useState([{ roomType: '', roomNumber: '', roomPrice: 0 }]);
    const [checkInDate, setCheckInDate] = useState('');
    const [checkOutDate, setCheckOutDate] = useState('');
    const [price, setPrice] = useState(0);
    const [roomOptions, setRoomOptions] = useState([]);
    const [suggestedUsers, setSuggestedUsers] = useState([]);
    const [users, setUsers] = useState([]);
    const [activeInput, setActiveInput] = useState('');
    const [availableRoomsCount, setAvailableRoomsCount] = useState({});

    useEffect(() => {
        const fetchRoomsAndTypes = async () => {
            try {
                const response = await axios.get('/reception/rooms-and-types');
                setRoomTypes(response.data.roomTypes);
                setRooms(response.data.rooms);

                // Calculate the number of available rooms for each room type
                const count = {};
                response.data.rooms.forEach(room => {
                    if (room.is_available) {
                        count[room.room_type_id] = (count[room.room_type_id] || 0) + 1;
                    }
                });
                setAvailableRoomsCount(count);
            } catch (error) {
                console.error('Error fetching rooms and types:', error);
                setRoomTypes([]);
                setRooms([]);
            }
        };

        fetchRoomsAndTypes();
    }, []);

    useEffect(() => {
        const fetchUsers = async () => {
            try {
                const response = await axios.get('/reception/users');
                if (Array.isArray(response.data)) {
                    setUsers(response.data);
                } else {
                    setUsers([]);
                    console.error('Unexpected response format:', response.data);
                }
            } catch (error) {
                console.error('Error fetching users:', error);
                setUsers([]);
            }
        };

        fetchUsers();
    }, []);

    const filterSuggestedUsers = (query) => {
        if (query.length === 0) {
            setSuggestedUsers([]);
            return;
        }

        const filteredUsers = users.filter(user =>
            (user.national_id_number && user.national_id_number.includes(query)) ||
            (user.phone_number && user.phone_number.includes(query))
        );
        setSuggestedUsers(filteredUsers);
    };

    const handleInputChange = (e, inputType) => {
        const value = e.target.value;
        setActiveInput(inputType);

        if (inputType === 'nationalId') {
            setNationalId(value);
        } else if (inputType === 'phone') {
            setPhone(value);
        }

        filterSuggestedUsers(value);
    };

    const handleAddRoom = () => {
        setSelectedRooms([...selectedRooms, { roomType: '', roomNumber: '', roomPrice: 0 }]);
    };

    const handleRoomTypeButtonClick = (index, roomTypeId) => {
        const newSelectedRooms = [...selectedRooms];
        newSelectedRooms[index] = { ...newSelectedRooms[index], roomType: roomTypeId };

        // Filter the room options based on the selected room type
        const filteredRooms = rooms.filter(room => room.room_type_id === roomTypeId);
        setRoomOptions(filteredRooms.map(room => ({
            value: room.room_id,
            label: `${room.room_number} - ${room.price_per_night} per night`
        })));

        // Automatically select a room number at random from the filtered list
        if (filteredRooms.length > 0) {
            const randomRoom = filteredRooms[Math.floor(Math.random() * filteredRooms.length)];
            newSelectedRooms[index].roomNumber = randomRoom.room_number;
            newSelectedRooms[index].roomPrice = randomRoom.price_per_night;
        } else {
            newSelectedRooms[index].roomNumber = '';
            newSelectedRooms[index].roomPrice = 0;
        }

        setSelectedRooms(newSelectedRooms);
        calculatePrice(newSelectedRooms);
    };

    const handleReselectRoom = (index) => {
        const newSelectedRooms = [...selectedRooms];
        const roomTypeId = newSelectedRooms[index].roomType;

        // Filter the room options based on the selected room type
        const filteredRooms = rooms.filter(room => room.room_type_id === roomTypeId);

        // Automatically select a room number at random from the filtered list
        if (filteredRooms.length > 0) {
            const randomRoom = filteredRooms[Math.floor(Math.random() * filteredRooms.length)];
            newSelectedRooms[index].roomNumber = randomRoom.room_number;
            newSelectedRooms[index].roomPrice = randomRoom.price_per_night;
        } else {
            newSelectedRooms[index].roomNumber = '';
            newSelectedRooms[index].roomPrice = 0;
        }

        setSelectedRooms(newSelectedRooms);
        calculatePrice(newSelectedRooms);
    };

    const handleRoomChange = (index, value) => {
        const newSelectedRooms = [...selectedRooms];
        newSelectedRooms[index] = { ...newSelectedRooms[index], roomNumber: value };
        setSelectedRooms(newSelectedRooms);
        calculatePrice(newSelectedRooms);
    };

    const calculatePrice = (selectedRooms) => {
        const totalPrice = selectedRooms.reduce((acc, room) => acc + (room.roomPrice || 0), 0);
        setPrice(totalPrice);
    };

    const handleRemoveRoom = (index) => {
        const newSelectedRooms = selectedRooms.filter((_, i) => i !== index);
        setSelectedRooms(newSelectedRooms);
        calculatePrice(newSelectedRooms);
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        try {
            console.log('Submitting booking:', {
                nationalId,
                guestName,
                surname,
                email,
                phone,
                homeAddress,
                selectedRooms,
                checkInDate,
                checkOutDate,
                price,
            });
            const response = await axios.post('/reception/book', {
                nationalId,
                guestName,
                surname,
                email,
                phone,
                homeAddress,
                selectedRooms,
                checkInDate,
                checkOutDate,
                price,
            });
            if (response.status === 200) {
                // Handle successful booking
                alert('Booking successful!');
            }
        } catch (error) {
            console.error('Error submitting booking:', error);
            alert('Failed to submit booking.');
        }
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Bookings On Reception" />
            <div className="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <Card>
                        <form onSubmit={handleSubmit} className="space-y-4" autoComplete="off">
                            <div className="grid gap-2">
                                <Label htmlFor="nationalId">National ID Number</Label>
                                <Input
                                    id="nationalId"
                                    value={nationalId}
                                    onChange={(e) => handleInputChange(e, 'nationalId')}
                                    required
                                    placeholder="Enter National ID Number"
                                    autoComplete="off"
                                />
                                {activeInput === 'nationalId' && suggestedUsers.length > 0 && (
                                    <div className="bg-white dark:bg-gray-800 border rounded shadow p-2 mt-2">
                                        {suggestedUsers.map(user => (
                                            <div key={user.id} className="p-2 hover:bg-gray-200 dark:hover:bg-gray-700 cursor-pointer" onClick={() => {
                                                setNationalId(user.national_id_number || '');
                                                setGuestName(user.first_name || '');
                                                setSurname(user.last_name || '');
                                                setEmail(user.email || '');
                                                setPhone(user.phone_number || '');
                                                setHomeAddress(user.address || '');
                                                setSuggestedUsers([]);
                                            }}>
                                                {user.first_name} {user.last_name}
                                            </div>
                                        ))}
                                    </div>
                                )}
                            </div>
                            <div className="grid gap-2">
                                <Label htmlFor="phone">Phone</Label>
                                <Input
                                    id="phone"
                                    value={phone}
                                    onChange={(e) => handleInputChange(e, 'phone')}
                                    required
                                    placeholder="Enter Phone Number"
                                    autoComplete="off"
                                />
                                {activeInput === 'phone' && suggestedUsers.length > 0 && (
                                    <div className="bg-white dark:bg-gray-800 border rounded shadow p-2 mt-2">
                                        {suggestedUsers.map(user => (
                                            <div key={user.id} className="p-2 hover:bg-gray-200 dark:hover:bg-gray-700 cursor-pointer" onClick={() => {
                                                setNationalId(user.national_id_number || '');
                                                setGuestName(user.first_name || '');
                                                setSurname(user.last_name || '');
                                                setEmail(user.email || '');
                                                setPhone(user.phone_number || '');
                                                setHomeAddress(user.address || '');
                                                setSuggestedUsers([]);
                                            }}>
                                                {user.first_name} {user.last_name}
                                            </div>
                                        ))}
                                    </div>
                                )}
                            </div>
                            <div className="grid gap-2">
                                <Label htmlFor="guestName">Guest Name</Label>
                                <Input
                                    id="guestName"
                                    value={guestName}
                                    onChange={(e) => setGuestName(e.target.value)}
                                    required
                                    placeholder="Enter Guest Name"
                                    autoComplete="off"
                                />
                            </div>
                            <div className="grid gap-2">
                                <Label htmlFor="surname">Surname</Label>
                                <Input
                                    id="surname"
                                    value={surname}
                                    onChange={(e) => setSurname(e.target.value)}
                                    required
                                    placeholder="Enter Surname"
                                    autoComplete="off"
                                />
                            </div>
                            <div className="grid gap-2">
                                <Label htmlFor="email">Email</Label>
                                <Input
                                    id="email"
                                    type="email"
                                    value={email}
                                    onChange={(e) => setEmail(e.target.value)}
                                    required
                                    placeholder="Enter Email"
                                    autoComplete="off"
                                />
                            </div>
                            <div className="grid gap-2">
                                <Label htmlFor="homeAddress">Home Address</Label>
                                <Input
                                    id="homeAddress"
                                    value={homeAddress}
                                    onChange={(e) => setHomeAddress(e.target.value)}
                                    required
                                    placeholder="Enter Home Address"
                                    autoComplete="off"
                                />
                            </div>
                        </form>
                    </Card>
                    <Card>
                    <form onSubmit={handleSubmit} className="space-y-4" autoComplete="off">
                            {selectedRooms.map((room, index) => (
                                <div key={index} className="space-y-4">
                                    <div className="grid gap-2">
                                        <Label>Room Type</Label>
                                        <div className="flex flex-wrap gap-2">
                                            {roomTypes.map(roomType => (
                                                <Button
                                                    key={roomType.room_type_id}
                                                    type="button"
                                                    onClick={() => handleRoomTypeButtonClick(index, roomType.room_type_id)}
                                                    className={room.roomType === roomType.room_type_id ? 'bg-blue-500 text-white' : 'bg-gray-200 dark:bg-gray-700'}
                                                >
                                                    {roomType.room_type_name} ({availableRoomsCount[roomType.room_type_id] || 0})
                                                </Button>
                                            ))}
                                        </div>
                                    </div>
                                    {room.roomNumber && (
                                        <div className="grid gap-2">
                                            <Label>Room Number</Label>
                                            <div className="flex items-center">
                                                <p className="mt-1 block w-full rounded-md border-gray-300 shadow-sm">{room.roomNumber}</p>
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
                                                <p className="mt-1 block w-full rounded-md border-gray-300 shadow-sm">{room.roomPrice}</p>
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
                                    <p id="price" className="mt-1 block w-full rounded-md border-gray-300 shadow-sm">{price}</p>
                                </div>
                            )}
                            <div className="grid gap-2">
                                <Label htmlFor="checkInDate">Check-In Date</Label>
                                <Input
                                    id="checkInDate"
                                    type="date"
                                    value={checkInDate}
                                    onChange={(e) => setCheckInDate(e.target.value)}
                                    required
                                    autoComplete="off"
                                />
                            </div>
                            <div className="grid gap-2">
                                <Label htmlFor="checkOutDate">Check-Out Date</Label>
                                <Input
                                    id="checkOutDate"
                                    type="date"
                                    value={checkOutDate}
                                    onChange={(e) => setCheckOutDate(e.target.value)}
                                    required
                                    autoComplete="off"
                                />
                            </div>
                            <div className="flex space-x-4">
                                <Button type="submit" className="mt-4">
                                    Book
                                </Button>
                                <Button type="button" className="mt-4">
                                    Pay
                                </Button>
                                <Button type="button" className="mt-4">
                                    Cancel
                                </Button>
                            </div>
                        </form>
                    </Card>
                </div>
            </div>
        </AppLayout>
    );
}
