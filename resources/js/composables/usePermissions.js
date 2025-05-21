import { useAuth } from './useAuth';

export function usePermissions() {
    const { user } = useAuth();

    const hasPermission = (permission) => {
        return user.value?.roles?.some(role => 
            role.permissions?.some(p => p.name === permission)
        );
    };

    const hasAnyPermission = (permissions) => {
        return permissions.some(permission => hasPermission(permission));
    };

    const hasAllPermissions = (permissions) => {
        return permissions.every(permission => hasPermission(permission));
    };

    return {
        hasPermission,
        hasAnyPermission,
        hasAllPermissions,
    };
} 