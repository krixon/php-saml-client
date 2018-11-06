<?php

namespace Krixon\SamlClient\Exception;

use Krixon\SamlClient\Protocol\Binding;

class UnsupportedBinding extends \InvalidArgumentException implements SamlClientException
{
    public function __construct(Binding $binding = null, string $context = null, Binding ...$supported)
    {
        $message = 'Unsupported binding';

        if ($context) {
            $message .= "for $context";
        }

        if ($binding) {
            $message .= sprintf(": '%s'", $binding->toString());
        }

        if ($supported) {
            $message .= sprintf(
                ". Supported bindings: [%s]",
                self::stringifyBindingArray($supported)
            );
        }

        $message .= '.';

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
