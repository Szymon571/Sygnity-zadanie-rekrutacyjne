<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Fortify\TwoFactorAuthenticatable;

#[Fillable(['name', 'first_name', 'last_name', 'nickname', 'email', 'password'])]
#[Hidden(['password', 'two_factor_secret', 'two_factor_recovery_codes', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, TwoFactorAuthenticatable;

    /**
     * Keep the legacy "name" column synchronized with first and last name.
     */
    protected static function booted(): void
    {
        static::saving(function (self $user): void {
            $user->name = $user->fullName();
        });
    }

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
        ];
    }

    /**
     * Get the user's full name.
     */
    public function fullName(): string
    {
        return trim(implode(' ', array_filter([
            $this->first_name,
            $this->last_name,
        ])));
    }

    /**
     * Get the user's initials.
     */
    public function initials(): string
    {
        return Str::of($this->fullName() ?: $this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn ($word) => Str::substr($word, 0, 1))
            ->implode('');
    }

    public function watchedCurrencies(): HasMany
    {
        return $this->hasMany(WatchedCurrency::class);
    }

    public function getAvatarUrlAttribute(): string
    {
        $palette = ['1d4ed8', '0f766e', 'b45309', 'be123c', '7c3aed', '0369a1', '15803d', 'a16207'];
        $hash = crc32(Str::lower($this->email ?? $this->name ?? 'user'));
        $background = $palette[$hash % count($palette)];

        return 'https://ui-avatars.com/api/?' . http_build_query([
                'name' => $this->name,
                'size' => 96,
                'background' => $background,
                'color' => 'ffffff',
                'bold' => 'true',
            ]);
    }
}
