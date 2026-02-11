<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'address',
        'bio',
        'avatar',
        'is_active',
        'last_login'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'last_login' => 'datetime'
        ];
    }

    // Role constants
    const ROLE_CLIENT = 'client';
    const ROLE_OWNER = 'owner';
    const ROLE_ADMIN = 'admin';

    // Relationships
    public function accommodations()
    {
        return $this->hasMany(Accommodation::class, 'owner_id');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'client_id');
    }

    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function receivedMessages()
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }

    // Scopes
    public function scopeClients($query)
    {
        return $query->where('role', self::ROLE_CLIENT);
    }

    public function scopeOwners($query)
    {
        return $query->where('role', self::ROLE_OWNER);
    }

    public function scopeAdmins($query)
    {
        return $query->where('role', self::ROLE_ADMIN);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeRecentLogins($query, $days = 30)
    {
        return $query->where('last_login', '>=', now()->subDays($days));
    }

    // Accessors
    public function getRoleLabelAttribute()
    {
        $labels = [
            self::ROLE_CLIENT => 'Client',
            self::ROLE_OWNER => 'Accommodation Owner',
            self::ROLE_ADMIN => 'Administrator'
        ];
        
        return $labels[$this->role] ?? $this->role;
    }

    public function getAvatarUrlAttribute()
    {
        if ($this->avatar) {
            return asset('storage/avatars/' . $this->avatar);
        }
        
        // Return default avatar based on initials
        $initials = strtoupper(substr($this->name, 0, 2));
        return "https://ui-avatars.com/api/?name={$initials}&background=2E7D32&color=fff&size=128";
    }

    public function getIsClientAttribute()
    {
        return $this->role === self::ROLE_CLIENT;
    }

    public function getIsOwnerAttribute()
    {
        return $this->role === self::ROLE_OWNER;
    }

    public function getIsAdminAttribute()
    {
        return $this->role === self::ROLE_ADMIN;
    }

    // Methods
    public function isClient()
    {
        return $this->role === self::ROLE_CLIENT;
    }

    public function isOwner()
    {
        return $this->role === self::ROLE_OWNER;
    }

    public function isAdmin()
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function hasRole($role)
    {
        return $this->role === $role;
    }

    public function updateLastLogin()
    {
        $this->update(['last_login' => now()]);
    }

    public function getDashboardRoute()
    {
        switch ($this->role) {
            case self::ROLE_ADMIN:
                return route('admin.dashboard');
            case self::ROLE_OWNER:
                return route('owner.dashboard');
            default:
                return route('dashboard');
        }
    }

    // Dashboard statistics methods
    public function getClientBookingsCount()
    {
        return $this->bookings()->count();
    }

    public function getOwnerAccommodationsCount()
    {
        return $this->accommodations()->count();
    }

    public function getUnreadMessagesCount()
    {
        return $this->receivedMessages()->unread()->count();
    }

    public function getPendingBookingsCount()
    {
        if ($this->isOwner()) {
            return Booking::forOwner($this->id)->pending()->count();
        }
        return $this->bookings()->pending()->count();
    }
}

