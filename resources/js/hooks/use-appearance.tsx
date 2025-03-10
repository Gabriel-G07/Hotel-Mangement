import { useCallback, useEffect, useState } from 'react';

export type Appearance = 'light' | 'dark' | 'system';

const prefersDark = () => window.matchMedia('(prefers-color-scheme: dark)').matches;

const applyTheme = (appearance: Appearance) => {
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

export function useAppearance() {
    const [appearance, setAppearance] = useState<Appearance>('system');

    const fetchAppearanceFromBackend = async () => {
        try {
            const url = route('reception.settings.appearance.get');
            console.log("Fetching URL:", url);
            const response = await fetch(url);
            const data = await response.json();
            setAppearance(data.theme);
            localStorage.setItem('appearance', data.theme);
            applyTheme(data.theme);
        } catch (error) {
            console.error('Error fetching theme:', error);
        }
    };

    const updateAppearanceInBackend = async (mode: Appearance) => {
        try {
            await fetch(route('reception.settings.appearance.update'), {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement).content,
                },
                body: JSON.stringify({ theme: mode }),
            });
        } catch (error) {
            console.error('Error updating theme:', error);
        }
    };

    const updateAppearance = useCallback((mode: Appearance) => {
        setAppearance(mode);
        localStorage.setItem('appearance', mode);
        applyTheme(mode);
        updateAppearanceInBackend(mode);
    }, []);

    useEffect(() => {
        fetchAppearanceFromBackend();

        return () => mediaQuery.removeEventListener('change', handleSystemThemeChange);
    }, [updateAppearance]);

    return { appearance, updateAppearance } as const;
}
