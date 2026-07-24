<?php

namespace App\Modules\Core\Events;

use App\Modules\Core\Models\Person;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PersonCreated
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly Person $person
    ) {}
}
