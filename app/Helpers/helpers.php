<?php

if (!function_exists('shipment_transitions')) {
    function shipment_transitions(): array {
        return [
            'pending' => [
                'merchant' => ['approved', 'canceled'],
            ],
            'approved' => [
                'merchant' => ['ready_for_pickup', 'canceled'],
            ],
            'ready_for_pickup' => [
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
