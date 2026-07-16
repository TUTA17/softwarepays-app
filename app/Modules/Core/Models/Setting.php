<?php

namespace App\Modules\Core\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $table = 'settings';

    public $timestamps = false;

    protected $fillable = ['name', 'value', 'type'];

    public static function getValue(string $name, $default = null)
    {
        $row = static::where('name', $name)->first();
        return $row ? $row->value : $default;
    }

    public static function setValue(string $name, $value)
    {
        return static::updateOrCreate(['name' => $name], ['value' => $value]);
    }

    /**
     * Get all settings grouped by type
     */
    public static function getAllGrouped(): array
    {
        $tabs = [];
        foreach (static::all() as $item) {
            $tabs[$item->type][$item->name] = $item->value;
        }
        return $tabs;
    }
}
