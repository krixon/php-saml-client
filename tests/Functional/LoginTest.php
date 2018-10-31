<?php

namespace Krixon\SamlClient\Test\Functional;

use Krixon\SamlClient\Login\Client;
use Krixon\SamlClient\Login\RequestBuilder;
use Krixon\SamlClient\Name;
use Krixon\SamlClient\Protocol\AuthnContextClass;
use Krixon\SamlClient\Protocol\Instant;
use Krixon\SamlClient\Protocol\NameFormat;
use Krixon\SamlClient\Protocol\NameIdPolicy;
use Krixon\SamlClient\Protocol\RequestedAuthnContext;
use Krixon\SamlClient\Test\TestCase;
use Psr\Http\Message\ResponseInterface;

class LoginTest extends TestCase
{
    public function testHttpRedirectBinding()
    {
        $client   = new Client($this->httpFactory());
        $uri      = $this->createUri('http://example.com/auth/saml?answer=42');

        $request = RequestBuilder
            ::for($uri)
            ->issueInstant(Instant::fromString('2018-11-05T12:18:22Z'))
            ->parameters(['name' => 'rimmer', 'type' => 'git'])
            ->relayState('custom state data')
            ->providerName(Name::fromString('Jupiter Mining Corp.'))
            ->passive()
            ->forceAuthn()
            ->nameIdPolicy(new NameIdPolicy('Qualifier', NameFormat::emailAddress(), true))
            ->requestedAuthnContext(RequestedAuthnContext::minimum(AuthnContextClass::secureRemotePassword()))
            ->appendAuthnContextClass(AuthnContextClass::passwordProtectedTransport())
            ->build();

        $message = $client->login($request);

        static::assertInstanceOf(ResponseInterface::class, $message);

        /** @var ResponseInterface $message */

        static::assertSame(1, count($message->getHeader('Location')));
        static::assertSame(302, $message->getStatusCode());

        // TODO: Check the actual raw xml content once the decoding / inflating code is written.
//        $expectedUri = '<xml>';
//        static::assertSame($expectedUri, (string)$message->getHeader('Location')[0]);
    }
}
