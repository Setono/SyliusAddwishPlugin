<?php

declare(strict_types=1);

namespace Setono\SyliusAddwishPlugin\EventListener;

use Setono\SyliusAddwishPlugin\Tag\Tags;
use Setono\TagBagBundle\Tag\TagInterface;
use Setono\TagBagBundle\Tag\TwigTag;
use Setono\TagBagBundle\TagBag\TagBagInterface;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Sylius\Component\Product\Model\ProductInterface;
use Symfony\Bundle\SecurityBundle\Security\FirewallConfig;
use Symfony\Bundle\SecurityBundle\Security\FirewallMap;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Http\FirewallMapInterface;

final class ProductVisitedSubscriber extends TagSubscriber
{
    /** @var RequestStack */
    private $requestStack;

    /** @var FirewallMapInterface */
    private $firewallMap;

    public function __construct(TagBagInterface $tagBag, RequestStack $requestStack, FirewallMapInterface $firewallMap)
    {
        parent::__construct($tagBag);

        $this->requestStack = $requestStack;
        $this->firewallMap = $firewallMap;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'sylius.product.show' => [
                'addScript',
            ],
        ];
    }

    public function addScript(ResourceControllerEvent $event): void
    {
        $product = $event->getSubject();
        if (!$product instanceof ProductInterface) {
            return;
        }

        $config = $this->getFirewallConfig();
        if (null === $config) {
            return;
        }

        // only track event when in shop
        if ($config->getContext() !== 'shop') {
            return;
        }

        $this->tagBag->add(new TwigTag(
            '@SetonoSyliusAddwishPlugin/Tag/product_visited.html.twig',
            TagInterface::TYPE_HTML,
            Tags::TAG_PRODUCT_VISITED,
            ['product' => $product]
        ), TagBagInterface::SECTION_BODY_END);
    }

    private function getFirewallConfig(): ?FirewallConfig
    {
        if (!$this->firewallMap instanceof FirewallMap) {
            return null;
        }

        $request = $this->requestStack->getCurrentRequest();
        if (null === $request) {
            return null;
        }

        return $this->firewallMap->getFirewallConfig($request);
    }
}
