<?php

declare(strict_types=1);

namespace Setono\SyliusAddwishPlugin\EventListener;

use Setono\TagBag\Tag\TagInterface;
use Setono\TagBag\Tag\TwigTag;
use Setono\TagBag\TagBagInterface;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Sylius\Component\Product\Model\ProductInterface;
use Symfony\Bundle\SecurityBundle\Security\FirewallConfig;
use Symfony\Bundle\SecurityBundle\Security\FirewallMap;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Http\FirewallMapInterface;

final class ProductVisitedSubscriber extends TagSubscriber
{
    private ?RequestStack $requestStack;

    private ?FirewallMapInterface $firewallMap;

    public function __construct(
        TagBagInterface $tagBag,
        RequestStack $requestStack = null, // todo should not be nullable in v2
        FirewallMapInterface $firewallMap = null // todo should not be nullable in v2
    ) {
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
        if (null === $this->requestStack || null === $this->firewallMap) {
            return;
        }

        $product = $event->getSubject();
        if (!$product instanceof ProductInterface) {
            return;
        }

        $config = $this->getFirewallConfig($this->requestStack->getCurrentRequest());
        if (null === $config) {
            return;
        }

        // only track event when in shop
        if ($config->getContext() !== 'shop') {
            return;
        }

        $twigTag = new TwigTag(
            '@SetonoSyliusAddwishPlugin/Tag/product_visited.html.twig',
            ['product' => $product]
        );
        $twigTag->setSection(TagInterface::SECTION_BODY_END);
        $this->tagBag->addTag($twigTag);
    }

    private function getFirewallConfig(?Request $request): ?FirewallConfig
    {
        if (!$this->firewallMap instanceof FirewallMap) {
            return null;
        }

        if (null === $request) {
            return null;
        }

        return $this->firewallMap->getFirewallConfig($request);
    }
}
