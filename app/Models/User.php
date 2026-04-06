<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    const ROLE_SUPER_ADMIN = 'super_admin';
    const ROLE_ADMIN = 'admin';
    const ROLE_MODERATOR = 'moderator';
    const ROLE_SUPPORT = 'support';
    const ROLE_MERCHANT = 'merchant';
    const ROLE_CUSTOMER = 'customer';

    public function isSuperAdmin() { return $this->role === self::ROLE_SUPER_ADMIN; }
    public function isAdmin() { return $this->role === self::ROLE_ADMIN || $this->role === self::ROLE_SUPER_ADMIN; }
    public function isModerator() { return $this->role === self::ROLE_MODERATOR || $this->isAdmin(); }
    public function isSupport() { return $this->role === self::ROLE_SUPPORT || $this->isAdmin(); }
    public function isStaff() { return in_array($this->role, [self::ROLE_SUPER_ADMIN, self::ROLE_ADMIN, self::ROLE_MODERATOR, self::ROLE_SUPPORT]); }
    
    // Permission Helpers for Dashboard Staff
    public function canAdd() { return in_array($this->role, [self::ROLE_SUPER_ADMIN, self::ROLE_ADMIN, self::ROLE_MODERATOR]); }
    public function canEdit() { return in_array($this->role, [self::ROLE_SUPER_ADMIN, self::ROLE_ADMIN, self::ROLE_MODERATOR]); }
    public function canDelete() { return in_array($this->role, [self::ROLE_SUPER_ADMIN, self::ROLE_ADMIN]); }

    public function merchant()
    {
        return $this->hasOne(Merchant::class);
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function permissions()
    {
        return $this->hasMany(UserPermission::class);
    }

    /**
     * Check if user has permission for a specific screen and action
     */
    public function hasPermission($screen, $action = 'view')
    {
        // Super admins have all permissions
        if ($this->role === self::ROLE_SUPER_ADMIN) {
            return true;
        }

        // Only admins, moderators, support roles use the permission system
        if (!$this->isStaff()) {
            return false;
        }

        $permission = $this->permissions->where('screen', $screen)->first();

        if (!$permission) {
            return false;
        }

        $column = "can_{$action}";
        return (bool) ($permission->$column ?? false);
    }


    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'phone',
        'email',
        'password',
        'password_plain',
        'role',
        'status',
        'profile_image',
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
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    protected $appends = ['is_verified', 'merchant_id'];

    public function getIsVerifiedAttribute()
    {
        if ($this->role !== self::ROLE_MERCHANT) {
            return false;
        }
        
        // Use relationship if already loaded to avoid extra queries
        if ($this->relationLoaded('merchant')) {
            return $this->merchant ? (bool) $this->merchant->approved : false;
        }

        // Otherwise use a lightweight query to avoid circular appends
        return \DB::table('merchants')
            ->where('user_id', $this->id)
            ->where('approved', true)
            ->exists();
    }

    public function getMerchantIdAttribute()
    {
        if ($this->relationLoaded('merchant')) {
            return $this->merchant ? $this->merchant->id : null;
        }

        return \DB::table('merchants')
            ->where('user_id', $this->id)
            ->value('id');
    }

    public function getProfileImageUrlAttribute()
    {
        return $this->profile_image ? \image_url($this->profile_image) : asset('images/logo.jpg');
    }

    protected $attributes = [
        'status' => 'active',
    ];
}
