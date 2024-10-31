<?php

namespace App\Security\Sanitizer;

// require '../../global_config.php';

use HTMLPurifier;
use HTMLPurifier_Config;

class Sanitizer
{
    static function tmpFolder() : string {
        $dir = dirname(__FILE__, 2);
        $dir .= '/HTMLPurifier/DefinitionCache/Serializer';
        return $dir;
    }

    static function purifyDOM(string $content) : string
    {
        $config = HTMLPurifier_Config::createDefault();
        $config->set('HTML.AllowedElements', SanitizerConfig::$allowedElements);
        $config->set('CSS.AllowedProperties', SanitizerConfig::$allowedProperties);
        $config->set('Attr.AllowedClasses', SanitizerConfig::$allowedClasses);
        $config->set('Attr.AllowedFrameTargets', SanitizerConfig::$allowedFrameTargets);
        $config->set('HTML.AllowedAttributes', SanitizerConfig::$allowedAttributes);
        $config->set('URI.AllowedSchemes', SanitizerConfig::$allowedSchemes);
        $config->set('Cache.SerializerPath', self::tmpFolder());

        //allow iframes from trusted sources
        $config->set('HTML.SafeIframe', true);
        //allow YouTube and Vimeo
        $config->set('URI.SafeIframeRegexp', '%^(https?:)?//(www\.youtube(?:-nocookie)?\.com/embed/|player\.vimeo\.com/video/)%');

        return (new HTMLPurifier())->purify($content, $config);
    }

    static function purifyInput(string $content) : string
    {
        $config = HTMLPurifier_Config::createDefault();
        $config->set('Cache.SerializerPath', self::tmpFolder());
        return (new HTMLPurifier())->purify($content, $config);
    }
}
