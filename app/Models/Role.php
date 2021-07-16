<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * App\Models\Role
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\User[] $users
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder|Role newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Role newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Role query()
 * @mixin \Eloquent
 */
class Role extends Model
{
    protected $table = 'roles';

    protected $fillable = [
        'id',
        'name',
        'permissions',
    ];

    protected $casts = [
        'permissions' => 'array',
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_role', 'role_id', 'user_id');
    }

    public function hasAccess(array $permissions): bool
    {
        $errors = array_reduce(
            $permissions,
            function ($errors, $permission) {
                if (!$this->hasPermission($permission)) {
                    $errors += 1;
                }
                return $errors;
            },
            0
        );
        return $errors === 0;
    }


    private function hasPermission(string $permission): bool
    {
        return $this->permissions[$permission] ?? false;
    }
}
