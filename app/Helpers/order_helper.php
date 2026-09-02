<?php

if (! function_exists('order_number')) {
    /**
     * Return the stable customer-facing order number without changing the
     * numeric primary key used by payments and database relationships.
     */
    function order_number(array|int|string $order): string
    {
        $id = is_array($order) ? ($order['id'] ?? 0) : $order;

        return 'PICK' . str_pad((string) max(0, (int) $id), 8, '0', STR_PAD_LEFT);
    }
}
