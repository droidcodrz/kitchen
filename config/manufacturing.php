<?php

return [
    'project_statuses' => [
        'draft'         => ['label' => 'Draft',         'color' => '#6B7280', 'next' => ['confirmed']],
        'confirmed'     => ['label' => 'Confirmed',     'color' => '#3B82F6', 'next' => ['in_production']],
        'in_production' => ['label' => 'In Production', 'color' => '#F59E0B', 'next' => ['delayed', 'finished']],
        'delayed'       => ['label' => 'Delayed',       'color' => '#EF4444', 'next' => ['in_production', 'finished']],
        'finished'      => ['label' => 'Finished',      'color' => '#10B981', 'next' => ['delivered']],
        'delivered'     => ['label' => 'Delivered',      'color' => '#8B5CF6', 'next' => []],
    ],

    'inventory_types' => [
        'raw_material'  => 'Raw Materials',
        'consumable'    => 'Consumables',
        'part'          => 'Parts',
        'finished_good' => 'Finished Goods',
    ],

    'alerts' => [
        'deadline_warning_days' => 7,
        'low_stock_check_interval' => 60,
    ],

    'order_no_prefix' => 'ORD-',
    'order_no_padding' => 6,
];
