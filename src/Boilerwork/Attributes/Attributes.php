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
            $namespace = $className = null;
            $contents = file_get_contents($file);
            // buscar el namespace
            if (preg_match('/namespace ([^;]+);/', $contents, $matches)) {
                $namespace = $matches[1];
            }
            // buscar el nombre de la clase
            if (preg_match('/class ([^\s]+)/', $contents, $matches)) {
                $className = $matches[1];
            }
            if ($namespace !== null && $className !== null) {

                $class = new ReflectionClass($namespace . '\\' . $className);
                $className = $class->getName();

                $attributesInClass = $class->getAttributes();
                // if ($class->getName() !== 'App\Core\Authentication\Application\DeactivateUser\DeactivateUserCommandHandler') {
                //     continue;
                // }

                // TODO: ADD More Attributes
                foreach ($attributesInClass as $attr) {
                    $attributeClass = $attr->getName();
                    match ($attributeClass) {
                        // 'Boilerwork\Messaging\SubscribesTo' => (new $attributeClass(
                        //     topics: $attr->getArguments() ?? '',
                        // ))(subscriber: $className),
                        // 'Boilerwork\Server\Route' => (new $attributeClass(
                        //     method: $attr->getArguments()['method'] ?? '',
                        //     route: $attr->getArguments()['route'] ?? '',
                        //     authorizations: $attr->getArguments()['authorizations'] ?? [],
                        // ))(target: $className),
                    };
                    // $attrs->newInstance();
                }

                // Para utilizar con un Attribute como: #[\Binds(abstract: ReadModelInterface::class, concrete: PostgreSqlReadModels::class)]
                foreach ($class->getMethods() as $method) {

                    $attributesInMethods = $method->getAttributes();
                    foreach ($attributesInMethods as $attribute) {
                        $attribute->newInstance();
                    }

                    $parameters = $method->getParameters();
                    foreach ($parameters as $param) {

                        // El tipado del atributo
                        // $paramType = $param->getType()->getName();
                        $param->getAttributes();
                        $attributesInParams = $param->getAttributes();

                        foreach ($attributesInParams as $attribute) {
                            $attributeClass = $attribute->getName();
                            new $attributeClass(...$attribute->getArguments());
                            // $attribute->newInstance();
                        }
                    }
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
            } else {
                $files[] = $item;
            }
        }
        return $files;
    }
}
