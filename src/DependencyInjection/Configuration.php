<?php

declare(strict_types=1);

namespace Setono\SyliusAddwishPlugin\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('setono_sylius_addwish');

        /** @var ArrayNodeDefinition $rootNode */
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->scalarNode('partner_id')
                    ->isRequired()
                    ->cannotBeEmpty()
                    ->info('This can be found here: https://addwish.com/company/signin.html')
                ->end()
                ->booleanNode('variant_based')
                    ->defaultFalse()
                    ->info('If true the various injections will inject variant codes instead of product codes.')
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
