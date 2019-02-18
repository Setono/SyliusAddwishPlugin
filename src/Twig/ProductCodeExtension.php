<?php

declare(strict_types=1);

namespace Setono\SyliusAddwishPlugin\Twig;

use Setono\SyliusAddwishPlugin\Resolver\ProductCodeResolverInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

final class ProductCodeExtension extends AbstractExtension
{
    /**
     * @var ProductCodeResolverInterface
     */
    private $productCodeResolver;

    public function __construct(ProductCodeResolverInterface $productCodeResolver)
    {
        $this->productCodeResolver = $productCodeResolver;
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('setono_sylius_addwish_product_code', [$this, 'productCode'], ['is_safe' => ['html']]),
        ];
    }

    public function productCode(ProductInterface $product): string
    {
        return $this->productCodeResolver->resolve($product);
    }
}
