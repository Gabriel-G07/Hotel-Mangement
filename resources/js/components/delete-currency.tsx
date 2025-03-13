import { useForm } from '@inertiajs/react';
import { FormEventHandler, useRef } from 'react';
import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Dialog, DialogClose, DialogContent, DialogDescription, DialogFooter, DialogTitle, DialogTrigger } from '@/components/ui/dialog';

interface DeleteCurrencyProps {
    currency: any;
}

export default function DeleteCurrency({ currency }: DeleteCurrencyProps) {
    const passwordInput = useRef<HTMLInputElement>(null);
    const { data, setData, delete: destroy, processing, reset, errors, clearErrors } = useForm<{ password: string }>({ password: '' });

    const deleteCurrency: FormEventHandler = (e) => {
        e.preventDefault();

        destroy(route('management.settings.currencies.destroy', currency.currency_id), {
            preserveScroll: true,
            onSuccess: () => reset(),
            onError: (error) => {
                console.error('Error deleting currency:', error);
                passwordInput.current?.focus();
            },
            onFinish: () => {
                window.location.reload();
            },
        });
    };

    const closeModal = () => {
        clearErrors();
        reset();
    };

    return (
        <Dialog>
            <DialogTrigger asChild>
                <Button variant="destructive">Delete Currency</Button>
            </DialogTrigger>
            <DialogContent>
                <DialogTitle>Are you sure you want to delete this currency?</DialogTitle>
                <DialogDescription>
                    Please enter your password for confirmation.
                </DialogDescription>
                <form onSubmit={deleteCurrency}>
                    <div className="grid gap-2">
                        <Label htmlFor="password" className="sr-only">
                            Password
                        </Label>

                        <Input
                            id="password"
                            type="password"
                            name="password"
                            ref={passwordInput}
                            value={data.password}
                            onChange={(e) => setData('password', e.target.value)}
                            placeholder="Password"
                            autoComplete="current-password"
                        />

                        <InputError message={errors.password} />
                    </div>

                    <div className="mt-6" />

                    <DialogFooter className="gap-4 flex justify-center">
                        <div className="space-y-4 rounded-lg border border-red-100 bg-red-50 p-4 dark:border-red-200/10 dark:bg-red-700/10 w-fit mx-auto text-center">
                            <div className="relative space-y-0.5 text-red-600 dark:text-red-100">
                                <p className="font-medium">Warning</p>
                                <p className="text-sm">Please proceed with caution, this cannot be undone.</p>
                            </div>

                            <div className="flex justify-center gap-4 mt-4">
                                <DialogClose asChild>
                                    <Button variant="secondary" onClick={closeModal}>
                                        Cancel
                                    </Button>
                                </DialogClose>

                                <Button variant="destructive" disabled={processing} type="submit">
                                    Delete Currency
                                </Button>
                            </div>
                        </div>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>
    );
}
