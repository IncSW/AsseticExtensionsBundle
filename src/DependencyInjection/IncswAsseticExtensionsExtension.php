<?php

declare(strict_types = 1);

namespace Incsw\AsseticExtensionsBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;

/**
 * Class IncswAsseticExtensionsExtension
 *
 * @package Incsw\AsseticExtensionsBundle\DependencyInjection
 */
final class IncswAsseticExtensionsExtension extends Extension
{

    /**
     *
     * @param array $configs
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     *
     * @return void
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $processor = new Processor();
        $configuration = new Configuration();
        $config = $processor->processConfiguration($configuration, $configs);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('babel.yml');

        $filters = [
            'babel' => [
                'bin' => $container->hasParameter('assetic.filter.babel.bin') ? $container->getParameter('assetic.filter.babel.bin') : '/usr/bin/babel',
                'config' => $container->hasParameter('assetic.filter.babel.config') ? $container->getParameter('assetic.filter.babel.config') : $container->getParameter('kernel.root_dir') . '/../.babelrc'
            ]
        ];

        if (array_key_exists('filters', $config)) {
            foreach ($config['filters'] as $name => $filter) {
                foreach ($filter as $key => $value) {
                    $filters[$name][$key] = $value;
                }
            }
        }

        foreach ($filters as $name => $filter) {
            foreach ($filter as $key => $value) {
                $container->setParameter('incsw_assetic_extensions.filter.' . $name . '.' . $key, $value);
            }
        }
    }
}
