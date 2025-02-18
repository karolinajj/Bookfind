<?php

namespace App\Policies;

use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Access\HandlesAuthorization;

class AdminPolicy
{
    use HandlesAuthorization;

    public function show()
    {
        return Auth::check() && Auth::user()->isAdmin();
    }

    public function blockUser()
    {
        return Auth::check() && Auth::user()->isAdmin();
    }

    public function unblockUser()
    {
        return Auth::check() && Auth::user()->isAdmin();
    }

    public function deleteBookPost()
    {
        return Auth::check() && Auth::user()->isAdmin();
    }

    public function manageBookCatalog()
    {
        return Auth::check() && Auth::user()->isAdmin();
    }

    public function viewUserOrders()
    {
        return Auth::check() && Auth::user()->isAdmin();
    }   
}