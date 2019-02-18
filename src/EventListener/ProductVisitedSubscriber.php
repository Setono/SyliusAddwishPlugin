<?php

declare(strict_types=1);

namespace Setono\SyliusAddwishPlugin\EventListener;

use Setono\TagBagBundle\Factory\TwigTagFactory;
use Setono\TagBagBundle\HttpFoundation\Session\Tag\TagBagInterface;
use Setono\TagBagBundle\Tag\TagInterface;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Sylius\Component\Product\Model\ProductInterface;

final class ProductVisitedSubscriber extends TagSubscriber
{
    /**
     * @var TwigTagFactory
     */
    private $twigTagFactory;

    public function __construct(TagBagInterface $tagBag, TwigTagFactory $twigTagFactory)
    {
        parent::__construct($tagBag);

        $this->twigTagFactory = $twigTagFactory;
    }

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
     *
     * @throws \Twig\Error\Error
     */
    public function addScript(ResourceControllerEvent $event): void
    {
        $product = $event->getSubject();

        if (!$product instanceof ProductInterface) {
            return;
        }

        $tag = $this->twigTagFactory->create('@SetonoSyliusAddwishPlugin/Tag/product_visited.html.twig', TagInterface::TYPE_NONE, [
            'product' => $product,
        ]);

        $this->tagBag->add($tag, TagBagInterface::SECTION_BODY_BEGIN);
    }
}
