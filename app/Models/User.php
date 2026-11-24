<?php

namespace App\Models;

use Arrocy\Saas\Models\Order;
use Arrocy\Saas\Models\Subscription;
use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Lab404\Impersonate\Models\Impersonate;

#[Fillable(['id', 'name', 'email', 'phone', 'password', 'role', 'socialite', 'status', 'timezone', 'avatar', 'email_verified_at'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Impersonate, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'socialite' => 'array',
    ];

    public function scopeSearch(Builder $query, ?string $search): Builder
    {
        $search = trim((string) $search);

        if ($search === '') {
            return $query;
        }

        $pattern = '%'.$search.'%';

        return $query->where(fn (Builder $query): Builder => $query
            ->where('name', 'like', $pattern)
            ->orWhere('email', 'like', $pattern)
            ->orWhere('phone', 'like', $pattern));
    }

    public function hasRole($roles): bool
    {
        if (is_string($roles)) {
            $roles = explode('|', $roles);
        }

        return in_array($this->role, $roles);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function subscription(): HasOne
    {
        return $this->hasOne(Subscription::class);
    }

    // Get the user's initials.
    public function initials(): string
    {
        $initials = Str::initials($this->name, true);

        return Str::length($initials) > 1
            ? Str::substr($initials, 0, 1).Str::substr($initials, -1)
            : $initials;
    }

    public function canImpersonate()
    {
        return $this->hasRole('admin');
    }

    protected static function booted(): void
    {
        static::creating(function (User $user) {
            if (empty($user->id)) {
                $timestamp = now()->getPreciseTimestamp();
                $baseTime = floor($timestamp / 100);
                $randomDigits = random_int(10, 99);
                $user->id = (int) ($baseTime.$randomDigits);
            }
            if (empty($user->password)) {
                $user->password = Hash::make((string) $user->id);
            }
            if (empty($user->role)) {
                $user->role = 'user';
            }
        });

        static::created(function (User $user) {
            \Helper::afterNewUserCreated($user);
        });
    }
}
