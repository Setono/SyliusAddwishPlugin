<?php

declare(strict_types=1);

namespace Setono\SyliusAddwishPlugin\EventListener;

use Setono\TagBag\Tag\TagInterface;
use Setono\TagBag\Tag\TwigTag;
use Setono\TagBag\TagBagInterface;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Order\Context\CartContextInterface;
use Sylius\Component\Order\Model\OrderInterface as BaseOrderInterface;

final class CartClearedSubscriber extends TagSubscriber
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
            'sylius.order_item.post_remove' => [
                'addScriptWhenCartEmpty',
            ],
            'sylius.order.post_update' => [
                'addScriptWhenCartEmpty',
            ],
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

        $twigTag = new TwigTag(
            '@SetonoSyliusAddwishPlugin/Tag/cart_cleared.html.twig',
            ['cart' => $cart]
        );
        $twigTag->setSection(TagInterface::SECTION_BODY_END);
        $this->tagBag->addTag($twigTag);
    }

    public function addScriptWhenCartRemoved(ResourceControllerEvent $event): void
    {
        $cart = $event->getSubject();
        if (!$cart instanceof OrderInterface) {
            return;
        }

        if (BaseOrderInterface::STATE_CART !== $cart->getState()) {
            return;
        }

        $twigTag = new TwigTag(
            '@SetonoSyliusAddwishPlugin/Tag/cart_cleared.html.twig',
            ['cart' => $cart]
        );
        $twigTag->setSection(TagInterface::SECTION_BODY_END);
        $this->tagBag->addTag($twigTag);
    }
}
