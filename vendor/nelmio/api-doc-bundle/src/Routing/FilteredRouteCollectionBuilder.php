<?php

/*
 * This file is part of the NelmioApiDocBundle package.
 *
 * (c) Nelmio
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nelmio\ApiDocBundle\Routing;

use Doctrine\Common\Annotations\Reader;
use Nelmio\ApiDocBundle\Annotation\Areas as LegacyAreas;
use Nelmio\ApiDocBundle\Attribute\Areas;
use Nelmio\ApiDocBundle\Util\ControllerReflector;
use OpenApi\Annotations\AbstractAnnotation;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

final class FilteredRouteCollectionBuilder
{
    private ?Reader $annotationReader;

    private ControllerReflector $controllerReflector;

    private string $area;

    /**
     * @var array<string, mixed>
     */
    private array $options;

    /**
     * @param array<mixed> $options
     */
    public function __construct(
        ?Reader $annotationReader,
        ControllerReflector $controllerReflector,
        string $area,
        array $options = []
    ) {
        $resolver = new OptionsResolver();
        $resolver
            ->setDefaults([
                'path_patterns' => [],
                'host_patterns' => [],
                'name_patterns' => [],
                'with_annotation' => false,
                'disable_default_routes' => false,
            ])
            ->setAllowedTypes('path_patterns', 'string[]')
            ->setAllowedTypes('host_patterns', 'string[]')
            ->setAllowedTypes('name_patterns', 'string[]')
            ->setAllowedTypes('with_annotation', 'boolean')
            ->setAllowedTypes('disable_default_routes', 'boolean')
        ;

        if (array_key_exists(0, $options)) {
            trigger_deprecation('nelmio/api-doc-bundle', '3.2', 'Passing an indexed array with a collection of path patterns as argument 1 for `%s()` is deprecated since 3.2.0, expected structure is an array containing parameterized options.', __METHOD__);

            $normalizedOptions = ['path_patterns' => $options];
            $options = $normalizedOptions;
        }

        $this->annotationReader = $annotationReader;
        $this->controllerReflector = $controllerReflector;
        $this->area = $area;
        $this->options = $resolver->resolve($options);
    }

    public function filter(RouteCollection $routes): RouteCollection
    {
        $filteredRoutes = new RouteCollection();
        foreach ($routes->all() as $name => $route) {
            if ($this->matchPath($route)
                && $this->matchHost($route)
                && $this->matchAnnotation($route)
                && $this->matchName($name)
                && $this->defaultRouteDisabled($route)
            ) {
                $filteredRoutes->add($name, $route);
            }
        }

        return $filteredRoutes;
    }

    private function matchPath(Route $route): bool
    {
        foreach ($this->options['path_patterns'] as $pathPattern) {
            if (preg_match('{'.$pathPattern.'}', $route->getPath())) {
                return true;
            }
        }

        return 0 === count($this->options['path_patterns']);
    }

    private function matchHost(Route $route): bool
    {
        foreach ($this->options['host_patterns'] as $hostPattern) {
            if (preg_match('{'.$hostPattern.'}', $route->getHost())) {
                return true;
            }
        }

        return 0 === count($this->options['host_patterns']);
    }

    private function matchName(string $name): bool
    {
        foreach ($this->options['name_patterns'] as $namePattern) {
            if (preg_match('{'.$namePattern.'}', $name)) {
                return true;
            }
        }

        return 0 === count($this->options['name_patterns']);
    }

    private function matchAnnotation(Route $route): bool
    {
        if (false === $this->options['with_annotation']) {
            return true;
        }

        $reflectionMethod = $this->controllerReflector->getReflectionMethod($route->getDefault('_controller'));

        if (null === $reflectionMethod) {
            return false;
        }

        /** @var Areas|null $areas */
        $areas = $this->getAttributesAsAnnotation($reflectionMethod, Areas::class)[0] ?? null;

        if (null === $areas) {
            /** @var Areas|null $areas */
            $areas = $this->getAttributesAsAnnotation($reflectionMethod->getDeclaringClass(), Areas::class)[0] ?? null;

            if (null === $areas && null !== $this->annotationReader) {
                /** @var Areas|null $areas */
                $areas = $this->annotationReader->getMethodAnnotation(
                    $reflectionMethod,
                    LegacyAreas::class
                );

                if (null === $areas) {
                    $areas = $this->annotationReader->getClassAnnotation($reflectionMethod->getDeclaringClass(), LegacyAreas::class);
                }
            }
        }

        return (null !== $areas) ? $areas->has($this->area) : false;
    }

    private function defaultRouteDisabled(Route $route): bool
    {
        if (false === $this->options['disable_default_routes']) {
            return true;
        }

        $method = $this->controllerReflector->getReflectionMethod(
            $route->getDefault('_controller') ?? ''
        );

        if (null === $method) {
            return false;
        }

        $annotations = null !== $this->annotationReader
            ? $this->annotationReader->getMethodAnnotations($method)
            : [];

        if (\PHP_VERSION_ID >= 80100) {
            $annotations = array_merge($annotations, array_map(function (\ReflectionAttribute $attribute) {
                return $attribute->newInstance();
            }, $method->getAttributes(AbstractAnnotation::class, \ReflectionAttribute::IS_INSTANCEOF)));
        }

        foreach ($annotations as $annotation) {
            if (false !== strpos(get_class($annotation), 'Nelmio\\ApiDocBundle\\Annotation')
                || false !== strpos(get_class($annotation), 'Nelmio\\ApiDocBundle\\Attribute')
                || false !== strpos(get_class($annotation), 'OpenApi\\Annotations')
                || false !== strpos(get_class($annotation), 'OpenApi\\Attributes')
            ) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param \ReflectionClass|\ReflectionMethod $reflection
     *
     * @return Areas[]
     */
    private function getAttributesAsAnnotation($reflection, string $className): array
    {
        $annotations = [];
        if (\PHP_VERSION_ID < 80100) {
            return $annotations;
        }

        foreach ($reflection->getAttributes($className, \ReflectionAttribute::IS_INSTANCEOF) as $attribute) {
            $annotations[] = $attribute->newInstance();
        }

        return $annotations;
    }
}
