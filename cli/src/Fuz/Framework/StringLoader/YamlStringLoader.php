<?php

namespace Fuz\Framework\StringLoader;

use Symfony\Component\Yaml\Yaml;
use Fuz\Framework\StringLoader\StringLoaderInterface;

class YamlStringLoader implements StringLoaderInterface
{

    public function load($stream)
    {
        return Yaml::parse($stream);
    }

}