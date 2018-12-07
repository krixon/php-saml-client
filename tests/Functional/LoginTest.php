<?php

namespace Krixon\SamlClient\Test\Functional;

use Krixon\SamlClient\Document\SamlDocument;
use Krixon\SamlClient\Http\DocumentCodec;
use Krixon\SamlClient\Login\Client;
use Krixon\SamlClient\Login\RequestBuilder;
use Krixon\SamlClient\Login\Response;
use Krixon\SamlClient\Name;
use Krixon\SamlClient\Protocol\AuthnContextClass;
use Krixon\SamlClient\Protocol\Binding;
use Krixon\SamlClient\Protocol\Instant;
use Krixon\SamlClient\Protocol\NameFormat;
use Krixon\SamlClient\Protocol\NameIdPolicy;
use Krixon\SamlClient\Protocol\RequestedAuthnContext;
use Krixon\SamlClient\Test\TestCase;
use Psr\Http\Message\ServerRequestInterface;

class LoginTest extends TestCase
{
    public function testHttpRedirectBinding()
    {
        $client = new Client();
        $uri    = $this->createUri('http://example.com/auth/saml?answer=42');

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

        $instruction = $client->login($request);

        static::assertTrue($instruction->binding()->equals(Binding::httpRedirect()));

        // TODO: Check the actual raw xml content once the decoding / inflating code is written.
//        $expectedUri = '<xml>';
//        static::assertSame($expectedUri, (string)$message->getHeader('Location')[0]);
    }


    public function testConsume()
    {
        $client = new Client();

        $xml = /** @lang XML */'
            <samlp:Response
              xmlns:samlp="urn:oasis:names:tc:SAML:2.0:protocol"
              xmlns:saml="urn:oasis:names:tc:SAML:2.0:assertion"
              ID="xyz987"
              InResponseTo="identifier_1"
              Version="2.0"
              IssueInstant="2004-12-05T09:22:05Z"
              Destination="https://sp.example.com/SAML2/SSO/POST"
            >
                <saml:Issuer>https://idp.example.org/SAML2</saml:Issuer>
                <samlp:Status>
                    <samlp:StatusCode Value="urn:oasis:names:tc:SAML:2.0:status:Success"/>
                    <samlp:StatusMessage>It worked</samlp:StatusMessage>
                    <samlp:StatusDetail>Lots of details</samlp:StatusDetail>
                </samlp:Status>
                <saml:Assertion
                  xmlns:saml="urn:oasis:names:tc:SAML:2.0:assertion"
                  ID="identifier_3"
                  Version="2.0"
                  IssueInstant="2004-12-05T09:22:05Z"
                >
                    <saml:Issuer>https://idp.example.org/SAML2</saml:Issuer>
                    <!-- a POSTed assertion MUST be signed -->
                    <ds:Signature xmlns:ds="http://www.w3.org/2000/09/xmldsig#">...</ds:Signature>
                    <saml:Subject>
                        <saml:NameID Format="urn:oasis:names:tc:SAML:2.0:nameid-format:transient">
                            3f7b3dcf-1674-4ecd-92c8-1544f346baf8
                        </saml:NameID>
                        <saml:SubjectConfirmation Method="urn:oasis:names:tc:SAML:2.0:cm:bearer">
                            <saml:SubjectConfirmationData
                              InResponseTo="identifier_1"
                              Recipient="https://sp.example.com/SAML2/SSO/POST"
                              NotOnOrAfter="2004-12-05T09:27:05Z"
                            />
                        </saml:SubjectConfirmation>
                    </saml:Subject>
                    <saml:Conditions NotBefore="2004-12-05T09:17:05Z" NotOnOrAfter="2004-12-05T09:27:05Z">
                        <saml:AudienceRestriction>
                          <saml:Audience>https://sp.example.com/SAML2</saml:Audience>
                        </saml:AudienceRestriction>
                    </saml:Conditions>
                    <saml:AuthnStatement AuthnInstant="2004-12-05T09:22:00Z" SessionIndex="identifier_3">
                        <saml:AuthnContext>
                            <saml:AuthnContextClassRef>
                                urn:oasis:names:tc:SAML:2.0:ac:classes:PasswordProtectedTransport
                            </saml:AuthnContextClassRef>
                        </saml:AuthnContext>
                    </saml:AuthnStatement>
                </saml:Assertion>
            </samlp:Response>
            ';

        $response = $client->consume($this->authnResponse($xml));

        static::assertInstanceOf(Response::class, $response);
        static::assertTrue($response->isSuccess());

        $status = $response->status();

        static::assertSame('It worked', $status->message());
        static::assertSame('Lots of details', $status->detail());
    }


    private function authnResponse(string $xml) : ServerRequestInterface
    {
        return $this
            ->createServerRequest()
            ->withParsedBody([
                'SAMLResponse' => (new DocumentCodec())->toPayload(SamlDocument::import($xml))
            ]);
    }
}
