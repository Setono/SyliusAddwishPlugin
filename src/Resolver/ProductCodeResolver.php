<?php

declare(strict_types=1);

namespace Setono\SyliusAddwishPlugin\Resolver;

use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;

final class ProductCodeResolver implements ProductCodeResolverInterface
{
    private $variantBased;

    public function __construct(bool $variantBased)
    {
        $this->variantBased = $variantBased;
    }

    public function resolve(ProductInterface $product): string
    {
        if ($this->variantBased) {
            /** @var ProductVariantInterface|null $variant */
            $variant = $product->getVariants()->first();

            if (null === $variant) {
                return '';
            }

            return (string) $variant->getCode();
        }

        if (null === $product->getCode()) {
            return '';
        }

        return (string) $product->getCode();
    }
}
