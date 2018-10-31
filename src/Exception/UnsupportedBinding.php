<?php

namespace Krixon\SamlClient\Exception;

use Krixon\SamlClient\Protocol\Binding;

class UnsupportedBinding extends \InvalidArgumentException implements SamlClientException
{
    public function __construct(Binding $binding, string $context, Binding ...$supported)
    {
        $message = sprintf(
            "Unsupported binding for %s: '%s'. Supported bindings: [%s]",
            $binding->toString(),
            $context,
            self::stringifyBindingArray($supported)
        );

        parent::__construct($message);
    }


    private static function stringifyBindingArray(array $bindings) : string
    {
        $strings = array_map(
            function (Binding $binding) {
                return $binding->toString();
            },
            $bindings
        );

        return implode(', ', $strings);
    }
}
