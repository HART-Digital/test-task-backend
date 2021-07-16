<?php

namespace App\Models;

use App\Events\Plan\PlanCreatedEvent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

/**
 * Class Plan
 *
 * @package App\Models
 * @property string $id
 * @property $paths
 * @property $options
 * @property $status
 * @property $additional
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Plan newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Plan newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Plan query()
 * @method static \Illuminate\Database\Eloquent\Builder|Plan whereAdditional($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Plan whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Plan whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Plan whereOptions($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Plan wherePaths($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Plan whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Plan whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property int|null $user_id
 * @method static \Illuminate\Database\Eloquent\Builder|Plan whereUserId($value)
 * @property-read \App\Models\User|null $user
 * @property bool $public
 * @method static \Illuminate\Database\Eloquent\Builder|Plan wherePublic($value)
 * @property array $deprecated_paths
 * @property-read int|null $paths_count
 * @method static \Illuminate\Database\Eloquent\Builder|Plan whereDeprecatedPaths($value)
 * @property array $hidden_paths
 * @method static \Illuminate\Database\Eloquent\Builder|Plan whereHiddenPaths($value)
 */
class Plan extends Model
{
    use HasFactory;

    protected $table = 'plans';

    protected $fillable = [
        'options',
        'status',
        'additional',
        'user_id',
        'public',
        'paths',
        'hidden_paths',
    ];

    protected $casts = [
        'paths' => 'array',
        'options' => 'array',
        'status' => 'array',
        'additional' => 'array',
        'hidden_paths' => 'array',
        'public' => 'bool',
    ];

    protected $dispatchesEvents = [
        'created' => PlanCreatedEvent::class,
    ];

    public function __construct(array $attributes = [])
    {
        $this->attributes['id'] = Str::orderedUuid()->toString();
        $this->paths = static::initialPaths();

        parent::__construct($attributes);
    }

    public static function initialPaths(): array
    {
        return [
            'plan' => null,
            'mask' => null,
            'furniture' => null,
            'neural' => null,
            'unreal' => [],
            'logs' => [],
        ];
    }

    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            $model->id = Str::uuid()->toString();
        });
    }

    public function getIncrementing(): bool
    {
        return false;
    }

    public function getKeyType(): string
    {
        return 'string';
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function setCurrentStep(?string $step): void
    {
        $this->setAdditionalKey('currentStep', $step);
    }

    public function getCurrentStep(): ?string
    {
        return $this->additional['currentStep'];
    }

    public function setAdditionalKey(string $key, $value): void
    {
        $additional = $this->additional;
        $additional[$key] = $value;
        $this->additional = $additional;
    }

    public function setPathsKey(string $key, $value): void
    {
        $paths = $this->paths;
        $paths[$key] = $value;
        $this->paths = $paths;
    }

    public function setOptionsKey(string $key, $value): void
    {
        $options = $this->options;
        $options[$key] = $value;
        $this->options = $options;
    }

    public function setStatusKey(string $key, int $value): void
    {
        $status = $this->status;
        $status[$key] = $value;
        $this->status = $status;
    }
}
