import { useCallback, useEffect, useState } from 'react';

export type Appearance = 'light' | 'dark' | 'system';

const prefersDark = () => window.matchMedia('(prefers-color-scheme: dark)').matches;

export const applyTheme = (appearance: Appearance) => {
    const isDark = appearance === 'dark' || (appearance === 'system' && prefersDark());
    document.documentElement.classList.toggle('dark', isDark);
};

const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');

const handleSystemThemeChange = () => {
    const currentAppearance = localStorage.getItem('appearance') as Appearance;
    applyTheme(currentAppearance || 'system');
};

export function initializeTheme() {
    const savedAppearance = (localStorage.getItem('appearance') as Appearance) || 'system';
    applyTheme(savedAppearance);
    mediaQuery.addEventListener('change', handleSystemThemeChange);
}

export const fetchAppearanceFromBackend = async (): Promise<Appearance | null> => {
    try {
        const url = route('reception.settings.appearance.get');
        const response = await fetch(url);
        const data = await response.json();
        localStorage.setItem('appearance', data.theme);
        return data.theme;
    } catch (error) {
        console.error('Error fetching theme:', error);
        return null;
    }
};

export function useAppearance() {
    const [appearance, setAppearance] = useState<Appearance>('system');

    const updateAppearanceInBackend = async (newTheme: Appearance) => {
        try {
            const csrfTokenElement = document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement;
            if (!csrfTokenElement) {
                throw new Error('CSRF token not found');
            }
            const csrfToken = csrfTokenElement.content;

            const response = await fetch(route('reception.settings.appearance.update'), {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify({ theme: newTheme }),
            });

            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(errorData.error);
            }
            const data = await response.json();
        } catch (error) {
            console.error('Error updating theme:', error);
        }
    };

    const updateAppearance = useCallback((newTheme: Appearance) => {
        setAppearance(newTheme);
        localStorage.setItem('appearance', newTheme);
        applyTheme(newTheme);
        updateAppearanceInBackend(newTheme).catch((error) => {
            console.error('Error updating theme:', error);
        });
    }, []);

    useEffect(() => {
        fetchAppearanceFromBackend().then((theme) => {
            if (theme) {
                setAppearance(theme);
                applyTheme(theme);
            }
        });

        return () => mediaQuery.removeEventListener('change', handleSystemThemeChange);
    }, [updateAppearance]);

    return { appearance, updateAppearance } as const;
}
