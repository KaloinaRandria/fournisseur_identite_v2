<?php

namespace Container3VyBCAa;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class get_ServiceLocator_KAFmOxiService extends App_KernelDevDebugContainer
{
    /**
     * Gets the private '.service_locator.KAFmOxi' shared service.
     *
     * @return \Symfony\Component\DependencyInjection\ServiceLocator
     */
    public static function do($container, $lazyLoad = true)
    {
        return $container->privates['.service_locator.KAFmOxi'] = new \Symfony\Component\DependencyInjection\Argument\ServiceLocator($container->getService ??= $container->getService(...), [
            'repository' => ['privates', 'App\\Repository\\StudentRepository', 'getStudentRepositoryService', true],
        ], [
            'repository' => 'App\\Repository\\StudentRepository',
        ]);
    }
}
