export type Role = 'admin' | 'driver' | 'traveler';

export type City = {
    id: number;
    name: string;
};

export type Vehicle = {
    id: number;
    brand: string;
    model: string;
};

export type UserSummary = {
    id: number;
    first_name: string;
    last_name: string;
    name: string;
    email: string;
    phone?: string;
    role: Role;
    account_status?: string;
    dashboard_route: string;
    initials?: string;
    joined_date?: string;
    email_verified?: boolean;
    phone_verified?: boolean;
    suspended_at?: string | null;
    driver_profile?: DriverProfile | null;
};

export type PhotoFile = {
    path: string | null;
    url: string | null;
    exists: boolean;
};

export type DriverProfile = {
    id: number;
    cin_number: string;
    cin_verified: boolean;
    avg_rating: string;
    total_trips: number;
    cin_front_photo: PhotoFile;
    cin_back_photo: PhotoFile;
    vehicle: Vehicle | null;
    submitted_at: string | null;
    photos_complete: boolean;
};

export type PublicDriverSummary = {
    id: number;
    first_name: string;
    last_name: string;
    name: string;
    initials?: string;
    profile: {
        avg_rating: string;
        total_trips: number;
        cin_verified: boolean;
    } | null;
};

export type Ride = {
    id: number;
    status: string;
    departure_city: City | null;
    arrival_city: City | null;
    departure_time: string;
    departure_date: string;
    departure_time_label: string;
    arrival_time_label: string;
    departure_day_label: string;
    departure_full_label: string;
    departure_datetime_label: string;
    price_per_seat: number;
    price_label: string;
    total_seats: number;
    available_seats: number;
    available_seats_label: string;
    meeting_point: string | null;
    notes: string | null;
    admin_note?: string | null;
    vehicle: Vehicle | null;
    driver: PublicDriverSummary | null;
    can_request: boolean;
    can_complete: boolean;
    can_edit?: boolean;
    can_cancel?: boolean;
};

export type Booking = {
    id: number;
    ride_id: number;
    seats_reserved: number;
    status: string;
    ride: Ride | null;
    traveler: UserSummary | null;
    can_cancel: boolean;
    can_review?: boolean;
    reviewed?: boolean;
};

export type Notification = {
    id: number;
    title: string;
    message: string;
    is_read: boolean;
    created_label: string | null;
};

export type SharedProps = {
    auth: {
        user: UserSummary | null;
    };
    flash: {
        status?: string | null;
    };
    errors: Record<string, string>;
};
