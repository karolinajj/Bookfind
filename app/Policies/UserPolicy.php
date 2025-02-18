<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Auth;

class UserPolicy
{
    use HandlesAuthorization;

    public function viewAccount(User $user)
    {
        return Auth::check() && Auth::user()->id == $user->id && !$user->isBlocked();
    }

    public function accessSearchPage()
    {
        return Auth::check();
    }

    public function editProfile(User $user)
    {
        return Auth::check() && Auth::user()->id == $user->id;
    }

    public function deleteAccount(User $user)
    {
        return Auth::check() && (Auth::user()->id == $user->id || Auth::user()->isAdmin());
    }

    public function manageUsers()
    {
        return Auth::check() && Auth::user()->isAdmin();
    }

    public function addToCart()
    {
        return Auth::check();
    }

    public function viewCart()
    {
        return Auth::check();
    }

    public function checkout()
    {
        return Auth::check();
    }

    public function viewOrders(User $user)
    {
        return Auth::check() && Auth::user()->id == $user->id;
    }

    public function manageCatalog()
    {
        return Auth::check() && Auth::user()->isAdmin();
    }

    public function leaveReview()
    {
        return Auth::check();
    }
    
}