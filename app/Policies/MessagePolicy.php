<?php

namespace App\Policies;

use App\Models\Message;
use App\Models\User;

class MessagePolicy
{
    public function view(User $user, Message $message)
    {
        return $user->isAdmin() || $user->id === $message->user_id;
    }

    public function reply(User $user, Message $message)
    {
        return $user->isAdmin();
    }

    public function delete(User $user, Message $message)
    {
        return $user->isAdmin();
    }
}