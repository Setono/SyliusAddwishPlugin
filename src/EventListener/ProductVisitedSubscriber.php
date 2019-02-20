<?php

declare(strict_types=1);

namespace Setono\SyliusAddwishPlugin\EventListener;

use Setono\SyliusAddwishPlugin\Tag\Tags;
use Setono\TagBagBundle\Tag\TagInterface;
use Setono\TagBagBundle\Tag\TwigTag;
use Setono\TagBagBundle\TagBag\TagBagInterface;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Sylius\Component\Product\Model\ProductInterface;

final class ProductVisitedSubscriber extends TagSubscriber
{
    public static function getSubscribedEvents(): array
    {
        return [
            'sylius.product.show' => [
                'addScript',
            ],
        ];
    }

    /**
     * @param ResourceControllerEvent $event
     */
    public function addScript(ResourceControllerEvent $event): void
    {
        $product = $event->getSubject();

        if (!$product instanceof ProductInterface) {
            return;
        }

        $this->tagBag->add(new TwigTag(
            '@SetonoSyliusAddwishPlugin/Tag/product_visited.html.twig',
            TagInterface::TYPE_HTML,
            Tags::TAG_PRODUCT_VISITED,
            ['product' => $product]
        ), TagBagInterface::SECTION_BODY_END);
    }
}
