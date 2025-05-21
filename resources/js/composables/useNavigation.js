import { ref, computed } from 'vue';
import { usePermissions } from './usePermissions';

export function useNavigation() {
    const { hasPermission } = usePermissions();
    
    const navigationItems = computed(() => [
        {
            name: 'Dashboard',
            route: '/dashboard',
            icon: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6',
            permission: 'view_dashboard'
        },
        {
            name: 'Books',
            route: '/books',
            icon: 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253',
            permission: 'view_books'
        },
        {
            name: 'Authors',
            route: '/authors',
            icon: 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',
            permission: 'view_authors'
        },
        {
            name: 'Categories',
            route: '/categories',
            icon: 'M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z',
            permission: 'view_categories'
        },
        {
            name: 'Users',
            route: '/users',
            icon: 'M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87M16 3.13a4 4 0 010 7.75M8 3.13a4 4 0 000 7.75',
            permission: 'view_users'
        },
        {
            name: 'Roles',
            route: '/roles',
            icon: 'M12 8c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 10c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z',
            permission: 'view_roles'
        },
        {
            name: 'Export/Import History',
            route: '/export-import-history',
            icon: 'M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12',
            permission: 'view_export_import_history'
        }
    ]);

    const filteredNavigation = computed(() => {
        return navigationItems.value.filter(item => hasPermission(item.permission));
    });

    const isActiveRoute = (route) => {
        return window.location.pathname === route;
    };

    return {
        navigationItems,
        filteredNavigation,
        isActiveRoute
    };
} 