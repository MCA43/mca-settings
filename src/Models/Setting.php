<?php

namespace Mca\Settings\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'group',
        'key',
        'value',
        'type',
        'label',
        'description',
        'sort',
        'is_locked',
    ];

    protected function casts(): array
    {
        return [
            'label' => 'array',
            'description' => 'array',
            'is_locked' => 'boolean',
            'sort' => 'integer',
        ];
    }

    public function getTable(): string
    {
        return (string) config('settings.table', 'mca_settings');
    }
}
