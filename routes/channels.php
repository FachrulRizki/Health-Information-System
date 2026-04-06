<?php

use Illuminate\Support\Facades\Broadcast;

// Queue display channel — public, no auth required
Broadcast::channel('poli.{poliId}', function () {
    return true;
});

// Bed status channel
Broadcast::channel('beds', function ($user) {
    return $user !== null;
});

// Private user channel — for doctor notifications (lab/radiology results)
Broadcast::channel('user.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});

// Private user channel — for doctor notifications (lab/radiology results)
Broadcast::channel('user.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});
