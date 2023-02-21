#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Attributes;

use ReflectionAttribute;
use ReflectionClass;

final class Attributes
{
    private string $dir;

    public function __construct(string $dir)
    {
        $this->dir = $dir;
    }

    public function scan()
    {
        $files = $this->scanDir($this->dir);

        foreach ($files as $file) {
            $classes = get_declared_classes();
            include $file;
            $new_classes = array_diff(get_declared_classes(), $classes);

            foreach ($new_classes as $class) {
                $ref = new ReflectionClass($class);

                $attributesInClass = $ref->getAttributes();

                foreach ($attributesInClass as $attribute) {

                    if ($attribute->getName() === 'Boilerwork\Server\Route') {
                        $attributeClass = $attribute->getName();
                        new $attributeClass(
                            method: $attribute->getArguments()['method'],
                            target: $ref->getName(),
                            route: $attribute->getArguments()['route'],
                            authorizations: $attribute->getArguments()['authorizations'],
                        );
                    }
                }

                $methods = $ref->getMethods();
                foreach ($methods as $method) {
                    $attributes = $method->getAttributes();
                    foreach ($attributes as $attribute) {

                        if ($attribute->getName() === 'Boilerwork\Container\Bind') {
                            $attributeClass = $attribute->getName();
                            new $attributeClass(...$attribute->getArguments());
                        }
                    }

                    // $parameters = $method->getParameters();
                    // foreach ($parameters as $param) {

                    //     // El tipado del atributo
                    //     // $paramType = $param->getType()->getName();
                    //     $param->getAttributes();
                    //     $attributesInParams = $param->getAttributes();

                    //     foreach ($attributesInParams as $attribute) {
                    //         $attributeClass = $attribute->getName();
                    //         new $attributeClass(...$attribute->getArguments());
                    //         // $attribute->newInstance();
                    //     }
                    // }
                }
            }
        }
    }

    private function scanDir($dir)
    {
        $files = array();
        $scan = scandir($dir);
        foreach ($scan as $item) {
            if ($item === '.' || $item === '..') continue;
            $item = $dir . '/' . $item;
            if (is_dir($item)) {
                $files = array_merge($files, $this->scanDir($item));
            } else if (pathinfo($item, PATHINFO_EXTENSION) == "php") {
                $files[] = $item;
            }
        }
        return $files;
    }
}
