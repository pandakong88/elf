<?php

namespace App\Modules\Core\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class LandingPageContent extends Model
{
    use HasUuids;

    protected $table = 'landing_page_contents';

    protected $fillable = [
        'key',
        'value',
        'type',
        'section',
        'title',
    ];
}
