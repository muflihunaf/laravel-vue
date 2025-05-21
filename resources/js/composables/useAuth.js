import { useAuthStore } from '../store/auth';
import { useRouter } from 'vue-router';
import { computed } from 'vue';

export function useAuth() {
    const authStore = useAuthStore();
    const router = useRouter();

    const isAuthenticated = computed(() => authStore.isAuthenticated);
    const user = computed(() => authStore.user);

    const login = async (credentials) => {
        try {
            await authStore.login(credentials);
            router.push('/dashboard');
        } catch (error) {
            throw error;
        }
    };

    const logout = async () => {
        await authStore.logout();
        router.push('/login');
    };

    const hasRole = (role) => {
        return user.value?.roles?.some(r => r.name === role);
    };

    const hasPermission = (permission) => {
        return user.value?.roles?.some(role => 
            role.permissions?.some(p => p.name === permission)
        );
    };

    return {
        isAuthenticated,
        user,
        login,
        logout,
        hasRole,
        hasPermission,
    };
} 