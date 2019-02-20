<?php

declare(strict_types=1);

namespace Setono\SyliusAddwishPlugin\Tag;

final class Tags
{
    public const TAG_LIBRARY = 'setono_sylius_addwish_library';
    public const TAG_EVENT_HANDLER = 'setono_sylius_addwish_event_handler';
    public const TAG_CART_CLEARED = 'setono_sylius_addwish_cart_cleared';
    public const TAG_CART_UPDATED = 'setono_sylius_addwish_cart_updated';
    public const TAG_ORDER_COMPLETED = 'setono_sylius_addwish_order_completed';
    public const TAG_PRODUCT_VISITED = 'setono_sylius_addwish_product_visited';

    private function __construct()
    {
    }
}
