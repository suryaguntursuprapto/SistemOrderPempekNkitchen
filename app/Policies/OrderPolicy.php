<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    public function view(User $user, Order $order)
    {
        return $user->isAdmin() || $user->id === $order->user_id;
    }

    public function update(User $user, Order $order)
    {
        return $user->isAdmin();
    }

    public function delete(User $user, Order $order)
    {
        return $user->isAdmin();
    }
}