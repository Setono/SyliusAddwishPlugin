<?php

declare(strict_types=1);

namespace Setono\SyliusAddwishPlugin\Resolver;

use Sylius\Component\Core\Model\ProductInterface;

interface ProductCodeResolverInterface
{
    public function resolve(ProductInterface $product): string;
}
