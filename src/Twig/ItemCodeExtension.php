<?php

declare(strict_types=1);

namespace Setono\SyliusAddwishPlugin\Twig;

use Setono\SyliusAddwishPlugin\Resolver\ItemCodeResolverInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

final class ItemCodeExtension extends AbstractExtension
{
    private ItemCodeResolverInterface $itemCodeResolver;

    public function __construct(ItemCodeResolverInterface $itemCodeResolver)
    {
        $this->itemCodeResolver = $itemCodeResolver;
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('setono_sylius_addwish_item_code', [$this, 'itemCode'], ['is_safe' => ['html']]),
        ];
    }

    public function itemCode(OrderItemInterface $item): string
    {
        return $this->itemCodeResolver->resolve($item);
    }
}
