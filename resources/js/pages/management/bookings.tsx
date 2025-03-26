import { useEffect, useState } from 'react';
import AppLayout from '@/layouts/app/management/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/react';
import axios from 'axios';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Bookings',
        href: '/bookings',
    },
];

export default function Bookings() {
    const [bookings, setBookings] = useState([]);

    useEffect(() => {
        const fetchBookings = async () => {
            const response = await axios.get('/api/bookings');
            setBookings(response.data);
        };

        fetchBookings();
    }, []);

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Bookings" />
            <div className="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
                <table className="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr>
                            <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Guest Name</th>
                            <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Room</th>
                            <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Check-In Date</th>
                            <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Check-Out Date</th>
                            <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                            <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Receptionist</th>
                        </tr>
                    </thead>
                    <tbody className="bg-white divide-y divide-gray-200">
                        {bookings.map((booking) => (
                            <tr key={booking.id}>
                                <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{booking.guestName}</td>
                                <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{booking.room}</td>
                                <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{booking.checkInDate}</td>
                                <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{booking.checkOutDate}</td>
                                <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{booking.price}</td>
                                <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{booking.receptionist}</td>
                            </tr>
                        ))}
                    </tbody>
                </table>
            </div>
        </AppLayout>
    );
}
