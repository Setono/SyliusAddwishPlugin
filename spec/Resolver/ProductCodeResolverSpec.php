<?php

namespace spec\Setono\SyliusAddwishPlugin\Resolver;

use Doctrine\Common\Collections\ArrayCollection;
use Setono\SyliusAddwishPlugin\Resolver\ProductCodeResolver;
use Sylius\Component\Core\Model\ProductInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Core\Model\ProductVariantInterface;

class ProductCodeResolverSpec extends ObjectBehavior
{
    public function let(): void
    {
        $this->beConstructedWith(true);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(ProductCodeResolver::class);
    }

    public function it_returns_empty_string_if_variant_collection_is_empty(ProductInterface $product): void
    {
        $product->getVariants()->willReturn(new ArrayCollection());

        $this->resolve($product)->shouldReturn('');
    }

    public function it_returns_first_variant_code(ProductInterface $product, ProductVariantInterface $productVariant): void
    {
        $productVariant->getCode()->willReturn('code');
        $product->getVariants()->willReturn(new ArrayCollection([$productVariant->getWrappedObject()]));

        $this->resolve($product)->shouldReturn('code');
    }

    public function it_returns_product_code(ProductInterface $product): void
    {
        $this->beConstructedWith(false);

        $product->getCode()->willReturn('code');

        $this->resolve($product)->shouldReturn('code');
    }
}
