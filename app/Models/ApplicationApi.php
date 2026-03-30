<?php

namespace App\Models;

use Hidehalo\Nanoid\Client;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApplicationApi extends Model
{
    use HasFactory;

    public const AVAILABLE_SCOPES = [
        'users.read',
        'users.write',
        'servers.read',
        'servers.write',
        'roles.read',
        'roles.write',
        'products.read',
        'products.write',
        'vouchers.read',
        'vouchers.write',
        'notifications.read',
        'notifications.write',
    ];

    protected $fillable = ['memo', 'scopes'];

    protected $primaryKey = 'token';

    public $incrementing = false;

    protected $casts = [
        'last_used' => 'datetime',
        'scopes' => 'array',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function (ApplicationApi $applicationApi) {
            $client = new Client();

            $applicationApi->{$applicationApi->getKeyName()} = $client->generateId(48);
        });
    }

    public function updateLastUsed()
    {
        $this->forceFill(['last_used' => now()])->save();
    }

    public function allowsAbility(string $ability): bool
    {
        $scopes = $this->normalizedScopes();
        if (empty($scopes)) {
            return (bool) config('security.api_scopes.allow_legacy_unscoped_tokens', true);
        }

        if (in_array('*', $scopes, true) || in_array($ability, $scopes, true)) {
            return true;
        }

        if (str_contains($ability, '.')) {
            [$resource] = explode('.', $ability, 2);
            if (in_array($resource . '.*', $scopes, true)) {
                return true;
            }
        }

        return false;
    }

    public function normalizedScopes(): array
    {
        if (empty($this->scopes) || !is_array($this->scopes)) {
            return [];
        }

        return array_values(array_unique(array_filter(array_map(
            static fn (mixed $scope) => is_string($scope) ? trim($scope) : null,
            $this->scopes
        ))));
    }
}
