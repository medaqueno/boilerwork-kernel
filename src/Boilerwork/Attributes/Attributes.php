#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Attributes;

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

                $reflection = new ReflectionClass($namespace . '\\' . $className);

                // Para utilizar con un Attribute como: #[\Binds(abstract: ReadModelInterface::class, concrete: PostgreSqlReadModels::class)]
                foreach ($reflection->getMethods() as $method) {

                    // $attributes = $method->getAttributes(Bind::class, ReflectionAttribute::IS_INSTANCEOF);
                    $attributes = $method->getAttributes();
                    foreach ($attributes as $attribute) {
                        $attribute->newInstance();
                    }

                    // Para utilizar con un Attribute como: @Implements("interface"="\App\Core\ItemDomainName\Application\Shared\ReadModelInterface","repository"="\App\Core\ItemDomainName\Infra\Persistence\PostgreSqlReadModels")
                    // obtener el doc block del método
                    /*    $docBlock = $method->getDocComment();
                    if ($docBlock === false) continue;
                    // var_dump($docBlock);
                    if (preg_match('/@Implements\("interface"="([^"]+)","repository"="([^"]+)"\)/', $docBlock, $matches)) {
                        $interface = $matches[1];
                        $repository = $matches[2];
                        var_dump($interface, $repository);
                        // ahora tienes el nombre de la interfaz en $interface
                        // y el nombre del repositorio en $repository
                    }
                    */
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
