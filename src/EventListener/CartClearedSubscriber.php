<?php

declare(strict_types=1);

namespace Setono\SyliusAddwishPlugin\EventListener;

use Setono\TagBagBundle\Factory\TwigTagFactory;
use Setono\TagBagBundle\HttpFoundation\Session\Tag\TagBagInterface;
use Setono\TagBagBundle\Tag\TagInterface;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Order\Context\CartContextInterface;
use Sylius\Component\Order\Model\OrderInterface as BaseOrderInterface;

final class CartClearedSubscriber extends TagSubscriber
{
    /**
     * @var TwigTagFactory
     */
    private $twigTagFactory;

    /**
     * @var CartContextInterface
     */
    private $cartContext;

    /**
     * @param TagBagInterface $tagBag
     * @param TwigTagFactory $twigTagFactory
     */
    public function __construct(
        TagBagInterface $tagBag,
        TwigTagFactory $twigTagFactory,
        CartContextInterface $cartContext
    ) {
        parent::__construct($tagBag);

        $this->twigTagFactory = $twigTagFactory;
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
            'sylius.order.pre_remove' => [
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

        $tag = $this->twigTagFactory->create('@SetonoSyliusAddwishPlugin/Tag/cart_cleared.js.twig', TagInterface::TYPE_SCRIPT, [
            'cart' => $cart,
        ]);

        $this->tagBag->add($tag, TagBagInterface::SECTION_BODY_BEGIN);
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

        $tag = $this->twigTagFactory->create('@SetonoSyliusAddwishPlugin/Tag/cart_cleared.js.twig', TagInterface::TYPE_SCRIPT, [
            'cart' => $cart,
        ]);

        $this->tagBag->add($tag, TagBagInterface::SECTION_BODY_BEGIN);
    }
}
