<?php

declare(strict_types=1);

namespace Setono\SyliusAddwishPlugin\EventListener;

use Setono\SyliusAddwishPlugin\Tag\Tags;
use Setono\TagBagBundle\Tag\TagInterface;
use Setono\TagBagBundle\Tag\TwigTag;
use Setono\TagBagBundle\TagBag\TagBagInterface;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Order\Context\CartContextInterface;
use Sylius\Component\Order\Model\OrderInterface as BaseOrderInterface;

final class CartClearedSubscriber extends TagSubscriber
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
            'sylius.order_item.post_remove' => [
                'addScriptWhenCartEmpty',
            ],
            'sylius.order.post_update' => [
                'addScriptWhenCartEmpty',
            ],

            // Before sylius_shop_cart_clear
            'sylius.order.post_delete' => [
                'addScriptWhenCartRemoved',
            ],
        ];
    }

    public function addScriptWhenCartEmpty(): void
    {
        $cart = $this->cartContext->getCart();
        if (!$cart instanceof OrderInterface) {
            return;
        }

        // Track clearing only when cart is empty
        if ($cart->getItems()->count() > 0) {
            return;
        }

        $this->tagBag->add(new TwigTag(
            '@SetonoSyliusAddwishPlugin/Tag/cart_cleared.js.twig',
            TagInterface::TYPE_SCRIPT,
            Tags::TAG_CART_CLEARED,
            ['cart' => $cart]
        ), TagBagInterface::SECTION_BODY_BEGIN);
    }

    /**
     * @param ResourceControllerEvent $event
     */
    public function addScriptWhenCartRemoved(ResourceControllerEvent $event): void
    {
        $cart = $event->getSubject();
        if (!$cart instanceof OrderInterface) {
            return;
        }

        if (BaseOrderInterface::STATE_CART !== $cart->getState()) {
            return;
        }

        $this->tagBag->add(new TwigTag(
            '@SetonoSyliusAddwishPlugin/Tag/cart_cleared.js.twig',
            TagInterface::TYPE_SCRIPT,
            Tags::TAG_CART_CLEARED,
            ['cart' => $cart]
        ), TagBagInterface::SECTION_BODY_END);
    }
}
