<?php

namespace App\Modules\Client\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['name', 'steam_app_id', 'price', 'seo_title', 'seo_description', 'is_active', 'description', 'genres', 'original_price', 'header_image', 'aliases'];

    public function keys()
    {
        return $this->hasMany(GameKey::class);
    }

    public function getAvailableKeysAttribute()
    {
        $count = $this->keys()->whereNull('sold_to_user_id')->count();
        if ($count > 0) {
            return $count;
        }

        // Check auto-provisioning setting (Mock mode or Real API Key configured)
        $mockMode = \App\Modules\Core\Models\Setting::where('name', 'wholesale_mock_mode')->value('value') ?? '1';
        $apiKey = \App\Modules\Core\Models\Setting::where('name', 'wholesale_api_key')->value('value');
        
        if ($mockMode === '1' || !empty($apiKey)) {
            return 999;
        }

        return 0;
    }
}
