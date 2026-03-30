<?php

namespace App\Models;

use Illuminate\Notifications\DatabaseNotification;

class Notification extends DatabaseNotification
{
    // Extends Laravel's built-in DatabaseNotification model
    // which handles the uuid pk, morphs, data (json), read_at
}
