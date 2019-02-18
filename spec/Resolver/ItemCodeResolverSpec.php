<?php

namespace spec\Setono\SyliusAddwishPlugin\Resolver;

use Setono\SyliusAddwishPlugin\Resolver\ItemCodeResolver;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;

class ItemCodeResolverSpec extends ObjectBehavior
{
    public function let(): void
    {
        $this->beConstructedWith(true);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(ItemCodeResolver::class);
    }

    public function it_returns_empty_string_if_variant_is_null(OrderItemInterface $item): void
    {
        $item->getVariant()->willReturn(null);
        $this->resolve($item)->shouldReturn('');
    }

    public function it_returns_variant_code(OrderItemInterface $item, ProductVariantInterface $productVariant): void
    {
        $productVariant->getCode()->willReturn('code');
        $item->getVariant()->willReturn($productVariant);
        $this->resolve($item)->shouldReturn('code');
    }

    public function it_returns_empty_string_if_product_is_null(OrderItemInterface $item): void
    {
        $this->beConstructedWith(false);
        $item->getProduct()->willReturn(null);
        $this->resolve($item)->shouldReturn('');
    }

    public function it_returns_product_code(OrderItemInterface $item, ProductInterface $product): void
    {
        $this->beConstructedWith(false);
        $product->getCode()->willReturn('code');
        $item->getProduct()->willReturn($product);
        $this->resolve($item)->shouldReturn('code');
    }
}
