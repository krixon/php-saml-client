<?php

namespace Krixon\SamlClient\Test\Functional\Login;

use Krixon\SamlClient\Document\SamlDocument;
use Krixon\SamlClient\Http\Deflate;
use Krixon\SamlClient\Login\Client;
use Krixon\SamlClient\Login\RequestBuilder;
use Krixon\SamlClient\Login\Response;
use Krixon\SamlClient\Name;
use Krixon\SamlClient\Protocol\AuthnContextClass;
use Krixon\SamlClient\Protocol\Binding;
use Krixon\SamlClient\Protocol\Instant;
use Krixon\SamlClient\Protocol\NameIdFormat;
use Krixon\SamlClient\Protocol\NameIdPolicy;
use Krixon\SamlClient\Protocol\RequestedAuthnContext;
use Krixon\SamlClient\ServiceProvider;
use Krixon\SamlClient\Test\TestCase;
use Psr\Http\Message\ServerRequestInterface;

class LoginTest extends TestCase
{
    public function testHttpRedirectBinding()
    {
        $client = new Client();
        $uri    = $this->createUri('http://example.com/auth/saml?answer=42');
        $sp     = new ServiceProvider('jmc', Name::fromString('Jupiter Mining Corp.'));

        $request = RequestBuilder
            ::for($uri)
            ->issueInstant(Instant::fromString('2018-11-05T12:18:22Z'))
            ->parameters(['name' => 'rimmer', 'type' => 'git'])
            ->relayState('custom state data')
            ->serviceProvider($sp)
            ->passive()
            ->forceAuthn()
            ->nameIdPolicy(new NameIdPolicy('Qualifier', NameIdFormat::emailAddress(), true))
            ->requestedAuthnContext(RequestedAuthnContext::minimum(AuthnContextClass::secureRemotePassword()))
            ->appendAuthnContextClass(AuthnContextClass::passwordProtectedTransport())
            ->build();

        $instruction = $client->login($request);

        static::assertTrue($instruction->binding()->equals(Binding::httpRedirect()));

        // TODO: Check the actual raw xml content once the decoding / inflating code is written.
//        $expectedUri = '<xml>';
//        static::assertSame($expectedUri, (string)$message->getHeader('Location')[0]);
    }


    public function testConsume()
    {
        $client = new Client();

        $xml = $this->fixtureContent('login/response.xml');

        $response = $client->consume($this->authnResponse($xml));

        static::assertInstanceOf(Response::class, $response);
        static::assertTrue($response->isSuccess());

        $status = $response->status();

        static::assertSame('It worked', $status->message());
        static::assertSame('Lots of details', $status->detail());
        static::assertCount(2, $response->attributes());
    }


    private function authnResponse(string $xml) : ServerRequestInterface
    {
        return $this
            ->createServerRequest()
            ->withParsedBody([
                'SAMLResponse' => (new Deflate())->encode(SamlDocument::import($xml))
            ]);
    }
}
