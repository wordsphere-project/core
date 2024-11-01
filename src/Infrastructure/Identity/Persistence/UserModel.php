<?php

declare(strict_types=1);

namespace WordSphere\Core\Infrastructure\Identity\Persistence;

use Althinect\FilamentSpatieRolesPermissions\Concerns\HasSuperAdmin;
use Barryvdh\LaravelIdeHelper\Eloquent;
use Carbon\Carbon;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\DatabaseNotificationCollection;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Traits\HasRoles;
use WordSphere\Core\Database\Factories\UserFactory;
use WordSphere\Core\Infrastructure\Shared\Concerns\HasUuidAttribute;
use WordSphere\Core\Legacy\Enums\SystemRole;

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
 * @method static Builder<static>|UserModel newModelQuery()
 * @method static Builder<static>|UserModel newQuery()
 * @method static Builder<static>|UserModel permission($permissions, $without = false)
 * @method static Builder<static>|UserModel query()
 * @method static Builder<static>|UserModel role($roles, $guard = null, $without = false)
 * @method static Builder<static>|UserModel withoutPermission($permissions)
 * @method static Builder<static>|UserModel withoutRole($roles, $guard = null)
 *
 * @mixin Eloquent
 */
class UserModel extends Authenticatable implements FilamentUser, MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory;
    use HasUuidAttribute;
    use HasRoles;
    use HasSuperAdmin;
    use Notifiable;

    protected $table = 'users';

    protected $fillable = [
        'uuid',
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

    public function guardName(): array
    {
        return ['web', 'api'];
    }

    public function canAccessPanel(Panel $panel): bool
    {

        if ($this->isSuperAdmin()) {
            return true;
        }

        return $this->hasRole(SystemRole::SUPER_ADMIN->value);
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

    protected static function newFactory(): UserFactory
    {
        return UserFactory::new();
    }
}
