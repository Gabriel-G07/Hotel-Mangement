import React, { useEffect } from 'react';
import { type BreadcrumbItem, type SharedData } from '@/types';
import { Transition } from '@headlessui/react';
import { Head, Link, useForm, usePage } from '@inertiajs/react';
import { FormEventHandler } from 'react';
import DeleteCurrency from '@/components/delete-currency';
import HeadingSmall from '@/components/heading-small';
import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/app-layout';
import SettingsLayout from '@/layouts/settings/management_layout';
import { ActionMessage } from '@/components/ui/action-message';
import { Table, TableHead, TableCell, TableBody, TableRow } from '@/components/ui/table';
import { Dialog, DialogClose, DialogContent, DialogDescription, DialogFooter, DialogTitle, DialogTrigger } from '@/components/ui/dialog';

interface CurrenciesProps {
    currencies: any[];
}

export default function Currencies({ currencies }: CurrenciesProps) {
    const { data, setData, post, patch, delete: destroy, errors, processing, recentlySuccessful } = useForm({
        currency_code: '',
        currency_name: '',
        exchange_rate: '',
        is_base_currency: false,
        currency_id: '',
    });

    const [isEditing, setIsEditing] = React.useState(false);
    const [selectedCurrency, setSelectedCurrency] = React.useState<any>(null);

    useEffect(() => {
        if (currencies.length > 0 && !currencies.some(currency => currency.is_base_currency)) {
            setData('is_base_currency', true);
        }
    }, [currencies]);

    const submit: FormEventHandler = (e) => {
        e.preventDefault();

        if (isEditing) {
            patch(route('management.settings.currencies.update', selectedCurrency?.currency_id), {
                data,
                preserveScroll: true,
                onSuccess: () => {
                    setIsEditing(false);
                    setSelectedCurrency(null);
                    setData({
                        currency_code: '',
                        currency_name: '',
                        exchange_rate: '',
                        is_base_currency: false,
                        currency_id: '',
                    });
                },
                onError: (errors) => {
                    console.error('Error updating currency:', errors);
                },
            });
        } else {
            post(route('management.settings.currencies.store'), {
                data,
                preserveScroll: true,
                onSuccess: () => {
                    setData({
                        currency_code: '',
                        currency_name: '',
                        exchange_rate: '',
                        is_base_currency: false,
                        currency_id: '',
                    });
                },
                onError: (errors) => {
                    console.error('Error creating currency:', errors);
                },
            });
        }
    };

    const handleEdit = (e: React.MouseEvent, currency: any) => {
        e.preventDefault();
        setIsEditing(true);
        setSelectedCurrency(currency);
        setData({
            currency_code: currency.currency_code,
            currency_name: currency.currency_name,
            exchange_rate: currency.exchange_rate,
            is_base_currency: currency.is_base_currency,
            currency_id: currency.currency_id,
        });
    };

    const handleCancel = () => {
        setIsEditing(false);
        setSelectedCurrency(null);
        setData({
            currency_code: '',
            currency_name: '',
            exchange_rate: '',
            is_base_currency: false,
            currency_id: '',
        });
    };

    const handleBaseCurrencyChange = (e: React.ChangeEvent<HTMLSelectElement>) => {
        const selectedCurrencyId = e.target.value;
        patch(route('management.settings.currencies.update', selectedCurrencyId), {
            data: { is_base_currency: true },
            preserveScroll: true,
            onSuccess: () => {
                window.location.reload();
            },
            onError: (errors) => {
                console.error('Error changing base currency:', errors);
            },
        });
    };

    return (
        <AppLayout breadcrumbs={[{ title: 'Currencies settings', href: route('management.settings.currencies.index') }]}>
            <Head title="Currencies settings" />

            <SettingsLayout>
                <div className="space-y-6 flex flex-col lg:flex-row">
                    <div className="w-full lg:w-1/2">
                        <HeadingSmall title="Currencies information" description="Add, Edit or Remove Currencies" />
                        <div className="md:max-w-2xl max-w-xl">
                        <form onSubmit={submit} className="space-y-6">
                            <div className="grid gap-2">
                                <Label htmlFor="currency_code">Currency Code</Label>
                                <Input
                                    id="currency_code"
                                    className="mt-1 block w-full"
                                    value={data.currency_code}
                                    onChange={(e) => setData('currency_code', e.target.value)}
                                    required
                                    autoComplete="currency_code"
                                    placeholder="Currency Code"
                                />
                                {errors.currency_code && <InputError message={errors.currency_code} />}
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="currency_name">Currency Name</Label>
                                <Input
                                    id="currency_name"
                                    className="mt-1 block w-full"
                                    value={data.currency_name}
                                    onChange={(e) => setData('currency_name', e.target.value)}
                                    required
                                    autoComplete="currency_name"
                                    placeholder="Currency Name"
                                />
                                {errors.currency_name && <InputError message={errors.currency_name} />}
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="exchange_rate">Exchange Rate</Label>
                                <Input
                                    id="exchange_rate"
                                    className="mt-1 block w-full"
                                    value={data.exchange_rate}
                                    onChange={(e) => setData('exchange_rate', e.target.value)}
                                    required
                                    autoComplete="exchange_rate"
                                    placeholder="Exchange Rate"
                                />
                                {errors.exchange_rate && <InputError message={errors.exchange_rate} />}
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="is_base_currency">Is Base Currency</Label>
                                <Input
                                    id="is_base_currency"
                                    type="checkbox"
                                    className="mt-1 block"
                                    checked={data.is_base_currency}
                                    onChange={(e) => setData('is_base_currency', e.target.checked)}
                                />
                                {errors.is_base_currency && <InputError message={errors.is_base_currency} />}
                            </div>

                            {isEditing ? (
                                <div className="flex items-center gap-4">
                                    <Button type="submit" disabled={processing}>
                                        Update Currency
                                    </Button>
                                    <Button type="button" onClick={handleCancel}>
                                        Cancel
                                    </Button>
                                </div>
                            ) : (
                                <div className="flex items-center gap-4">
                                    <Button type="submit" disabled={processing}>
                                        Add Currency
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
                        <Table data={currencies}>
                            <TableHead>
                                <TableRow>
                                    <TableCell>Currency Code</TableCell>
                                    <TableCell>Currency Name</TableCell>
                                    <TableCell>Exchange Rate</TableCell>
                                    <TableCell>Is Base Currency</TableCell>
                                    <TableCell>Actions</TableCell>
                                </TableRow>
                            </TableHead>
                            <TableBody>
                                {currencies.map((currency, index) => (
                                    <TableRow key={currency.currency_id} index={index}>
                                        <TableCell>{currency.currency_code}</TableCell>
                                        <TableCell>{currency.currency_name}</TableCell>
                                        <TableCell>{currency.exchange_rate}</TableCell>
                                        <TableCell>{currency.is_base_currency ? 'Yes' : 'No'}</TableCell>
                                        <TableCell className="flex flex-col items-center">
                                            <Button variant="link" onClick={(e) => handleEdit(e, currency)}>Edit</Button>
                                            <DeleteCurrency currency={currency} />
                                        </TableCell>
                                    </TableRow>
                                ))}
                            </TableBody>
                        </Table>
                    </div>
                </div>

                <Dialog>
                    <DialogTrigger asChild>
                        <Button variant="primary">Change Base Currency</Button>
                    </DialogTrigger>
                    <DialogContent>
                        <DialogTitle>Change Base Currency</DialogTitle>
                        <DialogDescription>
                            Select a new base currency from the list below.
                        </DialogDescription>
                        <select
                            className="mt-1 block w-full"
                            onChange={handleBaseCurrencyChange}
                        >
                            <option value="">Select Base Currency</option>
                            {currencies.map((currency) => (
                                <option key={currency.currency_id} value={currency.currency_id}>
                                    {currency.currency_code} - {currency.currency_name}
                                </option>
                            ))}
                        </select>
                        <DialogFooter className="gap-4 flex justify-center">
                            <DialogClose asChild>
                                <Button variant="secondary">Cancel</Button>
                            </DialogClose>
                        </DialogFooter>
                    </DialogContent>
                </Dialog>
            </SettingsLayout>
        </AppLayout>
    );
}
