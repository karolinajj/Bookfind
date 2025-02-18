<?php

namespace App\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Auth;

class NotificationPolicy
{
    use HandlesAuthorization;

    public function delete()
    {
        return Auth::check();
    }

    public function getNotifications()
    {
        return Auth::check();
    }

    public function context()
    {
        return Auth::check();
    }

    public function update()
    {
        return Auth::check();
    }
    
}