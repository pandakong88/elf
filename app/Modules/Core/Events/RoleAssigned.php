<?php

namespace App\Modules\Core\Events;

use App\Modules\Core\Models\Person;
use App\Modules\Core\Models\PersonRole;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RoleAssigned
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly Person $person,
        public readonly PersonRole $role
    ) {}
}
