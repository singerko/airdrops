<?php
// app/Policies/AirdropPolicy.php

namespace App\Policies;

use App\Models\Airdrop;
use App\Models\User;

class AirdropPolicy
{
    public function viewAny(User $user)
    {
        return true;
    }

    public function view(User $user, Airdrop $airdrop)
    {
        return $airdrop->status !== 'draft' || $user->isAdmin();
    }

    public function create(User $user)
    {
        return $user->isAdmin();
    }

    public function update(User $user, Airdrop $airdrop)
    {
        return $user->isAdmin();
    }

    public function delete(User $user, Airdrop $airdrop)
    {
        return $user->isAdmin();
    }

    public function translate(User $user, Airdrop $airdrop)
    {
        return $user->isAdmin();
    }
}
