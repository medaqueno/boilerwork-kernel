#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Attributes;

use ReflectionAttribute;
use ReflectionClass;
use function array_merge;
use function file_get_contents;
use function is_array;
use function is_dir;
use function pathinfo;
use function preg_match;
use function scandir;
use function token_get_all;
use const PATHINFO_EXTENSION;
use const T_CLASS;

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
            $tokens = token_get_all($contents);
            $class_token_index = null;

            foreach ($tokens as $index => $token) {
                if (is_array($token) && $token[0] === T_CLASS) {
                    $class_token_index = $index;
                    break;
                }
            }

            if ($class_token_index !== null) {
                $class_name_index = $class_token_index + 2;
                $className = $tokens[$class_name_index][1];
            }

            if ($namespace !== null && $className !== null) {

                $ref = new ReflectionClass($namespace . '\\' . $className);

                $attributesInClass = $ref->getAttributes();

                foreach ($attributesInClass as $attribute) {

                    if ($attribute->getName() === 'Boilerwork\Server\Route') {
                        $attributeClass = $attribute->getName();
                        new $attributeClass(
                            method: $attribute->getArguments()['method'],
                            route: $attribute->getArguments()['route'],
                            authorizations: $attribute->getArguments()['authorizations'],
                            target: $ref->getName(),
                        );
                    }

                    if ($attribute->getName() === 'Boilerwork\Messaging\SubscribesTo') {
                        $attributeClass = $attribute->getName();
                        new $attributeClass(
                            topics: $attribute->getArguments()['topics'],
                            target: $ref->getName(),
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