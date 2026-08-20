<?php

namespace App\Exceptions;

use InvalidArgumentException;

/**
 * Raised when an action would consume more stock than an inventory item has.
 *
 * Extends InvalidArgumentException on purpose: stock shortages were already
 * reported that way, and existing handlers catch that type. Widening to a
 * dedicated class adds the per-item detail needed for a useful message and an
 * alert, without breaking anything already catching the old type.
 */
class InsufficientStockException extends InvalidArgumentException
{
    /**
     * @param array<int, array{name: string, sku: ?string, available: float, needed: float, unit: ?string}> $shortages
     */
    public function __construct(
        public readonly array $shortages,
        public readonly ?string $action = null
    ) {
        parent::__construct(static::buildMessage(
            $shortages,
            $action ? $action . ' failed - not enough stock' : null
        ));
    }

    /**
     * A single sentence naming every short item with its figures, so the user
     * can see what to restock rather than only that something failed.
     */
    public static function buildMessage(array $shortages, ?string $context = null): string
    {
        $parts = array_map(
            fn ($s) => sprintf(
                '%s (available %s, needed %s%s)',
                $s['name'],
                rtrim(rtrim(number_format($s['available'], 2, '.', ''), '0'), '.'),
                rtrim(rtrim(number_format($s['needed'], 2, '.', ''), '0'), '.'),
                !empty($s['unit']) ? ' ' . $s['unit'] : ''
            ),
            $shortages
        );

        $lead = $context ?: 'Not enough stock to complete this action';

        return $lead . ': ' . implode('; ', $parts) . '.';
    }
}
