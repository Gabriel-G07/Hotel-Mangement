import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { useInitials } from '@/hooks/use-initials';
import { type User } from '@/types';

export function UserInfo({ user, showEmail = false }: { user: User; showEmail?: boolean }) {
    const getInitials = useInitials();

    return (
        <>
            <Avatar className="h-8 w-8 overflow-hidden rounded-full">
                {user.profile_picture ? (
                    <AvatarImage src={user.profile_picture} />
                ) : (
                    <AvatarFallback className="rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white">
                        {getInitials(`${user.first_name} ${user.last_name}`)}
                    </AvatarFallback>
                )}
            </Avatar>
            <div className="grid flex-1 text-left text-sm leading-tight">
                <span className="truncate font-medium">{`${user.first_name} ${user.last_name}`}</span>
                {showEmail && (
                    <span className="text-muted-foreground truncate text-xs">
                        @{user.username} - {user.email}
                    </span>
                )}
            </div>
        </>
    );
}
