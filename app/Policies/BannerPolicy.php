<?php

namespace App\Policies;

use App\Models\Banner;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class BannerPolicy
{
    public function modify(User $user,Banner $banner)
    {
        return $user->id === $banner->user_id?
            Response::allow() : Response::deny('You do not own this banner.');
    }
    
}
