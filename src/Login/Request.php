<?php

namespace Krixon\SamlClient\Login;

use Krixon\SamlClient\Name;
use Krixon\SamlClient\Protocol\Binding;
use Krixon\SamlClient\Protocol\Instant;
use Krixon\SamlClient\Protocol\NameIdPolicy;
use Krixon\SamlClient\Protocol\RequestedAuthnContext;
use Krixon\SamlClient\Protocol\RequestId;
use Krixon\SamlClient\Document\SamlDocument;
use Psr\Http\Message\UriInterface;

class Request
{
    private $id;
    private $uri;
    private $binding;
    private $parameters;
    private $relayState;
    private $document;


    public function __construct(
        RequestId $id,
        UriInterface $uri,
        Instant $issueInstant,
        Binding $binding,
        bool $forceAuthn = false,
        bool $passive = false,
        RequestedAuthnContext $authnContext = null,
        NameIdPolicy $nameIdPolicy = null,
        string $relayState = null,
        Name $providerName = null,
        array $parameters = []
    ) {

        $this->uri        = $uri;
        $this->id         = $id;
        $this->binding    = $binding;
        $this->parameters = $parameters;
        $this->relayState = $relayState;

        $document = SamlDocument::create('samlp:AuthnRequest');
        $root     = $document->root();

        $root->setAttribute('Version', '2.0');
        $root->setAttribute('ID', $this->id->toString());
        $root->setAttribute('IssueInstant', $issueInstant->toString());

        if ($providerName) {
            $root->setAttribute('ProviderName', $providerName->toString());
        }

        if ($forceAuthn) {
            $root->setAttribute('ForceAuthn', 'true');
        }

        if ($passive) {
            $root->setAttribute('IsPassive', 'true');
        }

        if ($nameIdPolicy) {
            $nameIdPolicy->appendTo($document, $root);
        }

        if ($authnContext) {
            $authnContext->appendTo($document, $root);
        }

        $this->document = $document;
    }


    public function toDocument() : SamlDocument
    {
        return $this->document;
    }


    public function id() : RequestId
    {
        return $this->id;
    }


    /**
     * The endpoint to which the request should be made.
     */
    public function uri() : UriInterface
    {
        return $this->uri;
    }


    /**
     * The protocol binding to use for this request.
     */
    public function binding() : Binding
    {
        return $this->binding;
    }


    /**
     * The RelayState token is an opaque reference to state information maintained at the SP.
     *
     * The use of this mechanism in an initial request places requirements on the selection and use of the binding
     * subsequently used to convey the response. Namely, if a SAML request message is accompanied by RelayState data,
     * then the SAML responder MUST return its SAML protocol response using a binding that also supports a RelayState
     * mechanism, and it MUST place the exact RelayState data it received with the request into the corresponding
     * RelayState parameter in the response.
     *
     * Some bindings that define a "RelayState" mechanism do not provide for end to end origin
     * authentication or integrity protection of the RelayState value. Most such bindings are defined in
     * conjunction with HTTP, and RelayState is often involved in the preservation of HTTP resource state that
     * may involve the use of HTTP redirects, or embedding of RelayState information in HTTP responses,
     * HTML content, etc. In such cases, implementations need to beware of Cross-Site Scripting (XSS) and
     * other attack vectors (e.g., Cross-Site Request Forgery, CSRF) that are common to such scenarios.
     *
     * Section 3.1.1 https://www.oasis-open.org/committees/download.php/56780/sstc-saml-bindings-errata-2.0-wd-06-diff.pdf
     *
     * There is also another, de facto standard use for RelayState when using IdP-initiated log on. In that case,
     * there is no incoming request from the SP, so there can be no state to be relayed back. Instead, the RelayState
     * is used by the IDP to signal to the SP what URL the SP should redirect to after successful sign on. That
     * is not part of the SAML2 standard, but is supported but this library.
     *
     * @return string|null
     */
    public function relayState() : ?string
    {
        return $this->relayState;
    }


    /**
     * Additional parameters to be included in the HTTP request.
     */
    public function parameters() : array
    {
        return $this->parameters;
    }
}
