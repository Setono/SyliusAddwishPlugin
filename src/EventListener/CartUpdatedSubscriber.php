<?php

declare(strict_types=1);

namespace Setono\SyliusAddwishPlugin\EventListener;

use Setono\TagBag\Tag\TagInterface;
use Setono\TagBag\Tag\TwigTag;
use Setono\TagBag\TagBagInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Order\Context\CartContextInterface;
use Sylius\Component\Order\Model\OrderInterface as BaseOrderInterface;

final class CartUpdatedSubscriber extends TagSubscriber
{
    /** @var CartContextInterface */
    private $cartContext;

    public function __construct(TagBagInterface $tagBag, CartContextInterface $cartContext)
    {
        parent::__construct($tagBag);

        $this->cartContext = $cartContext;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'sylius.order_item.post_add' => [
                'addScript',
            ],
            'sylius.order_item.post_remove' => [
                'addScript',
            ],
            'sylius.order.post_update' => [
                'addScript',
            ],
        ];
    }

    public function addScript(): void
    {
        $cart = $this->cartContext->getCart();
        if (!$cart instanceof OrderInterface) {
            return;
        }

        if (BaseOrderInterface::STATE_CART !== $cart->getState()) {
            return;
        }

        // If cart is empty - we have CartClearedSubscriber for this
        if ($cart->getItems()->count() <= 0) {
            return;
        }

        $twigTag = new TwigTag(
            '@SetonoSyliusAddwishPlugin/Tag/cart_updated.html.twig',
            ['cart' => $cart]
        );
        $twigTag->setSection(TagInterface::SECTION_BODY_BEGIN);
        $this->tagBag->addTag($twigTag);
    }
}
