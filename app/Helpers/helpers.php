<?php

if (!function_exists('shipment_transitions')) {
    function shipment_transitions(): array {
         return [

         'pending' => [
                'admin' => ['approved', 'canceled'],
            ],

            'approved' => [
                'admin' => ['assigned'],
            ],

            'assigned' => [
                'courier' => ['courier_approved', 'courier_canceled'],
            ],


            'courier_canceled' => [
                'admin' => ['assigned', 'canceled'],
            ],

            'courier_approved' => [
                'courier' => ['picked_up'],
            ],

            'picked_up' => [
                'courier' => ['in_transit'],
            ],

            'in_transit' => [
                'courier' => ['delivered', 'failed_delivery'],
            ],

            'failed_delivery' => [
                'courier' => ['retry_delivery', 'return_initiated'],
            ],

            'retry_delivery' => [
                'courier' => ['in_transit', 'delivered', 'failed_delivery'],
            ],

            'return_initiated' => [
                'courier' => ['returned'],
            ],

            'delivered' => [
                'admin' => ['confirmed'],
            ],

            // Optional: courier cancels after assigned
            'courier_canceled' => [
                'admin' => ['reassigned', 'canceled'],
            ],
        ];
    }
}

if (!function_exists('can_transition')) {
    function can_transition(string $currentStatus, string $newStatus, string $role): bool
    {
        $transitions = shipment_transitions();
        $allowed = $transitions[$currentStatus][$role] ?? [];
        return in_array($newStatus, $allowed);
    }
}
