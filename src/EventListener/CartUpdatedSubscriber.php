<?php

declare(strict_types=1);

namespace Setono\SyliusAddwishPlugin\EventListener;

use Setono\SyliusAddwishPlugin\Tag\Tags;
use Setono\TagBagBundle\Tag\TagInterface;
use Setono\TagBagBundle\Tag\TwigTag;
use Setono\TagBagBundle\TagBag\TagBagInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Order\Context\CartContextInterface;
use Sylius\Component\Order\Model\OrderInterface as BaseOrderInterface;

final class CartUpdatedSubscriber extends TagSubscriber
{
    /**
     * @var CartContextInterface
     */
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

        $this->tagBag->add(new TwigTag(
            '@SetonoSyliusAddwishPlugin/Tag/cart_updated.js.twig',
            TagInterface::TYPE_SCRIPT,
            Tags::TAG_CART_UPDATED,
            ['cart' => $cart]
        ), TagBagInterface::SECTION_BODY_END);
    }
}
