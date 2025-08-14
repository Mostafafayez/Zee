<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/


// Broadcast::channel('courier.{courierId}', function ($user, $courierId) {
//     return (int) $user->id === (int) $courierId; // Only the courier can listen
// });
Broadcast::channel('courier.{courierId}', fn($user, $courierId) => (int)$user->id === (int)$courierId);

