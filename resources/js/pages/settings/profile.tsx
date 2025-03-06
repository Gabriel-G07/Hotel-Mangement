import { type BreadcrumbItem, type SharedData } from '@/types';
import { Transition } from '@headlessui/react';
import { Head, Link, useForm, usePage } from '@inertiajs/react';
import { FormEventHandler } from 'react';

import DeleteUser from '@/components/delete-user';
import HeadingSmall from '@/components/heading-small';
import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/app-layout';
import SettingsLayout from '@/layouts/settings/layout';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Profile settings',
        href: '/settings/profile',
    },
];

interface ProfileForm {
    username: string;
    first_name: string;
    last_name: string;
    national_id_number: string;
    address: string;
    phone_number: string;
    profile_picture: string;
    email: string;
    password: string; // For verification
}

export default function Profile({ mustVerifyEmail, status }: { mustVerifyEmail: boolean; status?: string }) {
    const { auth } = usePage<SharedData>().props;

    const { data, setData, patch, errors, processing, recentlySuccessful } = useForm<Required<ProfileForm>>({
        username: auth.user.username,
        first_name: auth.user.first_name,
        last_name: auth.user.last_name,
        national_id_number: auth.user.national_id_number,
        address: auth.user.address,
        phone_number: auth.user.phone_number,
        profile_picture: auth.user.profile_picture,
        email: auth.user.email,
        password: '', // For verification
    });

    const submit: FormEventHandler = (e) => {
        e.preventDefault();

        patch(route('profile.update'), {
            preserveScroll: true,
        });
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Profile settings" />

            <SettingsLayout>
                <div className="space-y-6">
                    <HeadingSmall title="Profile information" description="Update your profile information" />

                    <form onSubmit={submit} className="space-y-6">
                        {/* Username (Read-only) */}
                        <div className="grid gap-2">
                            <Label htmlFor="username">Username</Label>
                            <Input
                                id="username"
                                className="mt-1 block w-full"
                                value={data.username}
                                readOnly
                                placeholder="Username"
                            />
                        </div>

                        {/* First Name */}
                        <div className="grid gap-2">
                            <Label htmlFor="first_name">First Name</Label>
                            <Input
                                id="first_name"
                                className="mt-1 block w-full"
                                value={data.first_name}
                                onChange={(e) => setData('first_name', e.target.value)}
                                required
                                placeholder="First Name"
                            />
                            <InputError className="mt-2" message={errors.first_name} />
                        </div>

                        {/* Last Name */}
                        <div className="grid gap-2">
                            <Label htmlFor="last_name">Last Name</Label>
                            <Input
                                id="last_name"
                                className="mt-1 block w-full"
                                value={data.last_name}
                                onChange={(e) => setData('last_name', e.target.value)}
                                required
                                placeholder="Last Name"
                            />
                            <InputError className="mt-2" message={errors.last_name} />
                        </div>

                        {/* National ID Number */}
                        <div className="grid gap-2">
                            <Label htmlFor="national_id_number">National ID Number</Label>
                            <Input
                                id="national_id_number"
                                className="mt-1 block w-full"
                                value={data.national_id_number}
                                onChange={(e) => setData('national_id_number', e.target.value)}
                                placeholder="National ID Number"
                            />
                            <InputError className="mt-2" message={errors.national_id_number} />
                        </div>

                        {/* Address */}
                        <div className="grid gap-2">
                            <Label htmlFor="address">Address</Label>
                            <Input
                                id="address"
                                className="mt-1 block w-full"
                                value={data.address}
                                onChange={(e) => setData('address', e.target.value)}
                                placeholder="Address"
                            />
                            <InputError className="mt-2" message={errors.address} />
                        </div>

                        {/* Phone Number */}
                        <div className="grid gap-2">
                            <Label htmlFor="phone_number">Phone Number</Label>
                            <Input
                                id="phone_number"
                                className="mt-1 block w-full"
                                value={data.phone_number}
                                onChange={(e) => setData('phone_number', e.target.value)}
                                placeholder="Phone Number"
                            />
                            <InputError className="mt-2" message={errors.phone_number} />
                        </div>

                        {/* Profile Picture */}
                        <div className="grid gap-2">
                            <Label htmlFor="profile_picture">Profile Picture URL</Label>
                            <Input
                                id="profile_picture"
                                className="mt-1 block w-full"
                                value={data.profile_picture}
                                onChange={(e) => setData('profile_picture', e.target.value)}
                                placeholder="Profile Picture URL"
                            />
                            <InputError className="mt-2" message={errors.profile_picture} />
                        </div>

                        {/* Email */}
                        <div className="grid gap-2">
                            <Label htmlFor="email">Email address</Label>
                            <Input
                                id="email"
                                type="email"
                                className="mt-1 block w-full"
                                value={data.email}
                                onChange={(e) => setData('email', e.target.value)}
                                required
                                autoComplete="username"
                                placeholder="Email address"
                            />
                            <InputError className="mt-2" message={errors.email} />
                        </div>

                        {/* Password (for verification) */}
                        <div className="grid gap-2">
                            <Label htmlFor="password">Password</Label>
                            <Input
                                id="password"
                                type="password"
                                className="mt-1 block w-full"
                                value={data.password}
                                onChange={(e) => setData('password', e.target.value)}
                                required
                                placeholder="Password"
                            />
                            <InputError className="mt-2" message={errors.password} />
                        </div>

                        {mustVerifyEmail && auth.user.email_verified_at === null && (
                            <div>
                                <p className="text-muted-foreground -mt-4 text-sm">
                                    Your email address is unverified.{' '}
                                    <Link
                                        href={route('verification.send')}
                                        method="post"
                                        as="button"
                                        className="text-foreground underline decoration-neutral-300 underline-offset-4 transition-colors duration-300 ease-out hover:decoration-current! dark:decoration-neutral-500"
                                    >
                                        Click here to resend the verification email.
                                    </Link>
                                </p>

                                {status === 'verification-link-sent' && (
                                    <div className="mt-2 text-sm font-medium text-green-600">
                                        A new verification link has been sent to your email address.
                                    </div>
                                )}
                            </div>
                        )}

                        <div className="flex items-center gap-4">
                            <Button disabled={processing}>Save</Button>

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
                    </form>
                </div>

                <DeleteUser />
            </SettingsLayout>
        </AppLayout>
    );
}
