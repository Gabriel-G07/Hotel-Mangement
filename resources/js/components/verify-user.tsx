import { useForm, usePage } from '@inertiajs/react';
import { FormEventHandler, useRef, useState } from 'react';

import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

import { Dialog, DialogClose, DialogContent, DialogDescription, DialogFooter, DialogTitle, DialogTrigger } from '@/components/ui/dialog';

interface AuthenticateUserProps {
    onSuccess: () => void;
    title?: string;
    description?: string;
    buttonText?: string;
    route: string;
}

export default function AuthenticateUser({
    onSuccess,
    title = 'Verify your identity',
    description = 'Please enter your password to confirm your identity.',
    buttonText = 'Verify',
    route,
}: AuthenticateUserProps) {
    const passwordInput = useRef<HTMLInputElement>(null);
    const { auth } = usePage().props;
    const { data, setData, patch, processing, reset, errors, clearErrors } = useForm<Required<{ password: string }>>({ password: '' });
    const [open, setOpen] = useState(false);

    const authenticateUser: FormEventHandler = (e) => {
        e.preventDefault();

        patch(route, { // Use the route prop directly
            preserveScroll: true,
            onSuccess: () => {
                closeModal();
                onSuccess();
            },
            onError: () => passwordInput.current?.focus(),
            onFinish: () => reset(),
        });
    };

    const closeModal = () => {
        clearErrors();
        reset();
        setOpen(false);
    };

    const openModal = () => {
        setOpen(true);
    };

    return (
        <div className="space-y-6">
            <Dialog open={open} onOpenChange={setOpen}>
                <DialogTrigger asChild>
                    <Button onClick={openModal}>{buttonText}</Button>
                </DialogTrigger>
                <DialogContent>
                    <DialogTitle>{title}</DialogTitle>
                    <DialogDescription>{description}</DialogDescription>
                    <form className="space-y-6" onSubmit={authenticateUser}>
                        <input type="hidden" name="username" value={auth.user?.username || ''} />
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

                        <DialogFooter className="gap-2">
                            <DialogClose asChild>
                                <Button variant="secondary" onClick={closeModal}>
                                    Cancel
                                </Button>
                            </DialogClose>

                            <Button variant="primary" disabled={processing} asChild>
                                <button type="submit">Verify</button>
                            </Button>
                        </DialogFooter>
                    </form>
                </DialogContent>
            </Dialog>
        </div>
    );
}
