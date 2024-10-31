<?php


namespace App\Security\Sanitizer;


class SanitizerConfig
{
    static $allowedElements = [
        'a',
        'b',
        'blockquote',
        'br',
        'code',
        'em',
        'h1',
        'h2',
        'h3',
        'h4',
        'h5',
        'h6',
        'hr',
        'i',
        'img',
        'li',
        'ol',
        'p',
        'pre',
        's',
        'span',
        'strong',
        'sub',
        'sup',
        'table',
        'tbody',
        'td',
        'th',
        'thead',
        'tr',
        'u',
        'ul',
        'iframe'
    ];
    static $allowedClasses = [
        'alert',
        'alert-danger',
        'alert-info',
        'alert-success',
        'alert-warning',
        'blockquote',
        'blockquote-footer',
        'img-responsive',
        'table',
        'table-bordered',
        'table-condensed',
        'table-hover',
        'table-responsive',
        'table-striped',
    ];
    static $allowedProperties = [
		'color',
		'background-color',
		'font-size',
		'font-weight',
		'padding-left',
		'text-align',
		'text-decoration',
	];
    static $allowedFrameTargets = [
        '_blank',
    ];
    static $allowedAttributes = [
        'a.href',
        'a.title',
        'a.target',
        'a.rel',
        'code.class',
        'img.class',
        'img.src',
        'ol.start',
        'p.class',
        'p.style',
        'span.style',
        'table.class',
        'td.align',
        'th.align',

        'iframe.src',
        'iframe.title',
        'iframe.allow',
        'iframe.width',
        'iframe.height',
        'iframe.frameborder',
        'iframe.allowfullscreen'
    ];
    static $allowedSchemes = [
        'http' => true,
        'https' => true,
    ];
    
    //static $cacheSerializerPath = TMP.'HTMLPurifier/DefinitionCache/Serializer';
}
