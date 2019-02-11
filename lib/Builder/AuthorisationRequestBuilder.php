<?php
/**
 * Created by PhpStorm.
 * Date: 9/15/17
 */

namespace OK\Builder;


use OK\Builder\Extensions\BuildsAttributes;
use OK\Model\Attribute;
use OK\Model\Attributes;
use OK\Model\Location;
use OK\Model\Open\AuthorisationRequest;

/**
 * Class AuthorisationRequestBuilder
 * @package OK\Builder
 *
 * @api
 * @method AuthorisationRequestBuilder setAction(string $action)
 * @method AuthorisationRequestBuilder setReference(string $reference)
 * @method AuthorisationRequestBuilder setPermissions(string $permissions)
 * @method AuthorisationRequestBuilder setRedirectUrl(string $redirectUrl)
 * @method AuthorisationRequestBuilder setLocation(Location $location)
 * @method AuthorisationRequestBuilder setToken(string $token)
 * @method AuthorisationRequestBuilder setPhoneNumber(string $phoneNumber)
 * @method AuthorisationRequestBuilder setTriggerPeriod(string $triggerPeriod)
 * @method AuthorisationRequestBuilder setAttributes(Attributes $attrs)
 * @method AuthorisationRequest build()
 */
class AuthorisationRequestBuilder extends GenericBuilder
{
    use BuildsAttributes;

    protected function getObject() {
        return new AuthorisationRequest;
    }
}