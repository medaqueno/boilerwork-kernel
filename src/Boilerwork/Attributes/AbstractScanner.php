#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Attributes;

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

abstract class AbstractScanner
{
    public function scan(string $directory): void
    {
        $iterator      = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($directory));
        $regexIterator = new \RegexIterator($iterator, '/^.+\.php$/i', \RecursiveRegexIterator::GET_MATCH);

        foreach ($regexIterator as $file) {
            $this->processFile($file[0]);
        }
    }

    private function processFile(string $file): void
    {
        $className = $this->getClassNameFromFile($file);

        if ($className === null) {
            return;
        }

        $reflectionClass = new \ReflectionClass($className);
        $this->processClass($reflectionClass);
    }

    protected function processClass(\ReflectionClass $class): void
    {
        // Procesar atributos a nivel de clase
        $classAttributes = $class->getAttributes(static::ATTRIBUTE_CLASS);
        foreach ($classAttributes as $attribute) {
            $this->processAttribute($attribute, $class);
        }

        // Procesar atributos a nivel de método
        foreach ($class->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
            $this->processMethod($method);
        }
    }

    protected function processMethod(\ReflectionMethod $method): void
    {
        $attributes = $method->getAttributes(static::ATTRIBUTE_CLASS);
        foreach ($attributes as $attribute) {

            $this->processAttribute($attribute);
        }
    }

    protected function getClassNameFromFile(string $file): ?string
    {
        $content = file_get_contents($file);
        if (! $content) {
            return null;
        }

        if (! preg_match('/namespace\s+(.+?);/s', $content, $matches)) {
            return null;
        }
        $namespace = $matches[1];

        if (! preg_match('/class\s+([^\s]+)/', $content, $matches)) {
            return null;
        }
        $className = $matches[1];

        return $namespace . '\\' . $className;
    }


    abstract protected function processAttribute(\ReflectionAttribute $attribute, $parentClass = null): void;
}
