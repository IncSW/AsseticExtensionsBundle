<?php

declare(strict_types = 1);

namespace Incsw\AsseticExtensionsBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\Definition\Processor;

/**
 * Class IncswAsseticExtensionsExtension
 *
 * @package Incsw\AsseticExtensionsBundle\DependencyInjection
 */
class AppExtension extends Extension
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

        $filters = [
            'babel' => [
                'bin' => '/usr/bin/babel',
                'config' => $container->getParameter('kernel.root_dir') . '/../.babelrc'
            ]
        ];

        foreach ($config['filters'] as $name => $filter) {
            foreach ($filter as $key => $value) {
                $filters[$name][$key] = $value;
            }
        }

        foreach ($filters as $name => $filter) {
            foreach ($filter as $key => $value) {
                $container->setParameter('incsw_assetic_extensions.filter.' . $name . '.' . $key, $value);
            }
        }
    }
}
