type Params = number | string | { id?: number | string } | { [key: string]: number | string | undefined };

const id = (params?: Params): string => {
    if (params === undefined) {
        return '';
    }

    if (typeof params === 'string' || typeof params === 'number') {
        return String(params);
    }

    if ('id' in params && params.id !== undefined) {
        return String(params.id);
    }

    const first = Object.values(params).find((value) => value !== undefined);

    return first === undefined ? '' : String(first);
};

export const path = (name: string, params?: Params): string => {
    const routes: Record<string, string> = {
        home: '/',
        login: '/login',
        signup: '/signup',
        'login.store': '/login',
        'signup.store': '/signup',
        logout: '/logout',
        'account.settings.edit': '/account/settings',
        'account.settings.profile.update': '/account/settings/profile',
        'account.settings.password.update': '/account/settings/password',
        'rides.search': '/rides/search',
        'rides.publish': '/rides/publish',
        'rides.publish.store': '/rides/publish',
        'dashboards.admin': '/dashboards/admin',
        'dashboards.admin.driver-verification': '/dashboards/admin/driver-verification',
        'dashboards.admin.users': '/dashboards/admin/users',
        'dashboards.admin.rides': '/dashboards/admin/rides',
        'dashboards.driver': '/dashboards/driver',
        'dashboards.traveler': '/dashboards/traveler',
        'drivers.onboarding.create': '/drivers/onboarding',
        'drivers.onboarding.store': '/drivers/onboarding',
    };

    if (name === 'rides.show') {
        return `/rides/${id(params)}`;
    }

    if (name === 'rides.book') {
        return `/rides/${id(params)}/book`;
    }

    if (name === 'rides.complete') {
        return `/rides/${id(params)}/complete`;
    }

    if (name === 'bookings.confirm') {
        return `/bookings/${id(params)}/confirm`;
    }

    if (name === 'bookings.reject') {
        return `/bookings/${id(params)}/reject`;
    }

    if (name === 'bookings.cancel') {
        return `/bookings/${id(params)}/cancel`;
    }

    if (name === 'bookings.reviews.store') {
        return `/bookings/${id(params)}/reviews`;
    }

    if (name === 'admin.driver-profiles.verify') {
        return `/admin/driver-profiles/${id(params)}/verify`;
    }

    return routes[name] ?? '/';
};

export const asset = (uri: string): string => `/${uri.replace(/^\/+/, '')}`;
