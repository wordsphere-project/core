<?php

declare(strict_types=1);

namespace WordSphere\Core\Infrastructure\Identity\Persistence;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Althinect\FilamentSpatieRolesPermissions\Concerns\HasSuperAdmin;
use Barryvdh\LaravelIdeHelper\Eloquent;
use Carbon\Carbon;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Contracts\Auth;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\DatabaseNotificationCollection;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use WordSphere\Core\Database\Factories\UserFactory;
use WordSphere\Core\Legacy\Enums\SystemRole;

use function DI\string;

/**
 * @property int $id
 * @property string $uuid
 * @property string $name
 * @property string $email
 * @property Carbon $email_verified_at
 * @property string $password
 * @property string $remember_token
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read DatabaseNotificationCollection<int, DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read Collection<int, Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read Collection<int, Role> $roles
 * @property-read int|null $roles_count
 *
 * @method static Builder<static>|EloquentUser newModelQuery()
 * @method static Builder<static>|EloquentUser newQuery()
 * @method static Builder<static>|EloquentUser permission($permissions, $without = false)
 * @method static Builder<static>|EloquentUser query()
 * @method static Builder<static>|EloquentUser role($roles, $guard = null, $without = false)
 * @method static Builder<static>|EloquentUser withoutPermission($permissions)
 * @method static Builder<static>|EloquentUser withoutRole($roles, $guard = null)
 *
 * @mixin Eloquent
 */
class EloquentUser extends Authenticatable implements Auth\MustVerifyEmail, FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory;

    use HasSuperAdmin;
    use Notifiable;

    protected string $guard_name = 'web';

    protected $table = 'users';

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function canAccessPanel(Panel $panel): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        return $this->hasRole(SystemRole::USER->value);
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

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function (EloquentUser $user) {
            $user->uuid = (string) Str::uuid();
        });
    }

    protected static function newFactory(): UserFactory
    {
        return UserFactory::new();
    }
}
