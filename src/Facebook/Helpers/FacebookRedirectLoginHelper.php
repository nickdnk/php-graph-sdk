<?php
/**
 * Copyright 2017 Facebook, Inc.
 *
 * You are hereby granted a non-exclusive, worldwide, royalty-free license to
 * use, copy, modify, and distribute this software in source code or binary
 * form for use in connection with the web services and APIs provided by
 * Facebook.
 *
 * As with any software that integrates with the Facebook platform, your use
 * of this software is subject to the Facebook Developer Principles and
 * Policies [http://developers.facebook.com/policy/]. This copyright notice
 * shall be included in all copies or substantial portions of the software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL
 * THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER
 * DEALINGS IN THE SOFTWARE.
 *
 */

namespace Facebook\Helpers;

use Facebook\Authentication\AccessToken;
use Facebook\Authentication\OAuth2Client;
use Facebook\Exceptions\FacebookSDKException;
use Facebook\PersistentData\FacebookSessionPersistentDataHandler;
use Facebook\PersistentData\PersistentDataInterface;
use Facebook\PseudoRandomString\PseudoRandomStringGeneratorFactory;
use Facebook\PseudoRandomString\PseudoRandomStringGeneratorInterface;
use Facebook\Url\FacebookUrlDetectionHandler;
use Facebook\Url\FacebookUrlManipulator;
use Facebook\Url\UrlDetectionInterface;
use function hash_equals;

/**
 * Class FacebookRedirectLoginHelper
 *
 * @package Facebook
 */
class FacebookRedirectLoginHelper
{
    /**
     * @const int The length of CSRF string to validate the login link.
     */
    const CSRF_LENGTH = 32;

    /**
     * @var OAuth2Client The OAuth 2.0 client service.
     */
    protected OAuth2Client $oAuth2Client;

    /**
     * @var UrlDetectionInterface The URL detection handler.
     */
    protected UrlDetectionInterface $urlDetectionHandler;

    /**
     * @var PersistentDataInterface The persistent data handler.
     */
    protected PersistentDataInterface $persistentDataHandler;

    /**
     * @var PseudoRandomStringGeneratorInterface The cryptographically secure pseudo-random string generator.
     */
    protected PseudoRandomStringGeneratorInterface $pseudoRandomStringGenerator;

    /**
     * @param OAuth2Client $oAuth2Client The OAuth 2.0 client service.
     * @param PersistentDataInterface|null $persistentDataHandler The persistent data handler.
     * @param UrlDetectionInterface|null $urlHandler The URL detection handler.
     * @param PseudoRandomStringGeneratorInterface|null $prsg The cryptographically secure pseudo-random string generator.
     */
    public function __construct(OAuth2Client $oAuth2Client, ?PersistentDataInterface $persistentDataHandler = null, ?UrlDetectionInterface $urlHandler = null, ?PseudoRandomStringGeneratorInterface $prsg = null)
    {
        $this->oAuth2Client = $oAuth2Client;
        $this->persistentDataHandler = $persistentDataHandler ?: new FacebookSessionPersistentDataHandler();
        $this->urlDetectionHandler = $urlHandler ?: new FacebookUrlDetectionHandler();
        $this->pseudoRandomStringGenerator = PseudoRandomStringGeneratorFactory::createPseudoRandomStringGenerator($prsg);
    }

    /**
     * Returns the persistent data handler.
     */
    public function getPersistentDataHandler(): PersistentDataInterface
    {
        return $this->persistentDataHandler;
    }

    /**
     * Returns the URL detection handler.
     */
    public function getUrlDetectionHandler(): UrlDetectionInterface
    {
        return $this->urlDetectionHandler;
    }

    /**
     * Returns the cryptographically secure pseudo-random string generator.
     */
    public function getPseudoRandomStringGenerator(): PseudoRandomStringGeneratorInterface
    {
        return $this->pseudoRandomStringGenerator;
    }

    /**
     * Stores CSRF state and returns a URL to which the user should be sent to in order to continue the login process with Facebook.
     *
     * @param string $redirectUrl The URL Facebook should redirect users to after login.
     * @param array $scope List of permissions to request during login.
     * @param array $params An array of parameters to generate URL.
     * @param string $separator The separator to use in http_build_query().
     * @return string
     */
    private function makeUrl(string $redirectUrl, array $scope, array $params = [], string $separator = '&'): string
    {
        $state = $this->persistentDataHandler->get('state') ?: $this->pseudoRandomStringGenerator->getPseudoRandomString(static::CSRF_LENGTH);
        $this->persistentDataHandler->set('state', $state);

        return $this->oAuth2Client->getAuthorizationUrl($redirectUrl, $state, $scope, $params, $separator);
    }

    /**
     * Returns the URL to send the user in order to login to Facebook.
     *
     * @param string $redirectUrl The URL Facebook should redirect users to after login.
     * @param array $scope List of permissions to request during login.
     * @param string $separator The separator to use in http_build_query().
     */
    public function getLoginUrl(string $redirectUrl, array $scope = [], string $separator = '&'): string
    {
        return $this->makeUrl($redirectUrl, $scope, [], $separator);
    }

    /**
     * Returns the URL to send the user in order to log out of Facebook.
     *
     * @param AccessToken|string $accessToken The access token that will be logged out.
     * @param string $next The url Facebook should redirect the user to after a successful logout.
     * @param string $separator The separator to use in http_build_query().
     *
     * @throws FacebookSDKException
     */
    public function getLogoutUrl(AccessToken|string $accessToken, string $next, string $separator = '&'): string
    {
        if (!$accessToken instanceof AccessToken) {
            $accessToken = new AccessToken($accessToken);
        }

        if ($accessToken->isAppAccessToken()) {
            throw new FacebookSDKException('Cannot generate a logout URL with an app access token.', 722);
        }

        $params = [
            'next'         => $next,
            'access_token' => $accessToken->getValue(),
        ];

        return 'https://www.facebook.com/logout.php?' . http_build_query($params, '', $separator);
    }

    /**
     * Returns the URL to send the user in order to login to Facebook with permission(s) to be re-asked.
     *
     * @param string $redirectUrl The URL Facebook should redirect users to after login.
     * @param array $scope List of permissions to request during login.
     * @param string $separator The separator to use in http_build_query().
     */
    public function getReRequestUrl(string $redirectUrl, array $scope = [], string $separator = '&'): string
    {
        $params = ['auth_type' => 'rerequest'];

        return $this->makeUrl($redirectUrl, $scope, $params, $separator);
    }

    /**
     * Returns the URL to send the user in order to login to Facebook with user to be re-authenticated.
     *
     * @param string $redirectUrl The URL Facebook should redirect users to after login.
     * @param array $scope List of permissions to request during login.
     * @param string $separator The separator to use in http_build_query().
     */
    public function getReAuthenticationUrl(string $redirectUrl, array $scope = [], string $separator = '&'): string
    {
        $params = ['auth_type' => 'reauthenticate'];

        return $this->makeUrl($redirectUrl, $scope, $params, $separator);
    }

    /**
     * Takes a valid code from a login redirect, and returns an AccessToken entity.
     *
     * @param string|null $redirectUrl The redirect URL.
     *
     * @return AccessToken|null
     *
     * @throws FacebookSDKException
     */
    public function getAccessToken(?string $redirectUrl = null): ?AccessToken
    {
        if (!$code = $this->getCode()) {
            return null;
        }

        $this->validateCsrf();
        $this->resetCsrf();

        $redirectUrl = $redirectUrl ?: $this->urlDetectionHandler->getCurrentUrl();
        // At minimum we need to remove the 'code', 'enforce_https' and 'state' params
        $redirectUrl = FacebookUrlManipulator::removeParamsFromUrl($redirectUrl, ['code', 'enforce_https', 'state']);

        return $this->oAuth2Client->getAccessTokenFromCode($code, $redirectUrl);
    }

    /**
     * Validate the request against a cross-site request forgery.
     *
     * @throws FacebookSDKException
     */
    protected function validateCsrf(): void
    {
        $state = $this->getState();
        if (!$state) {
            throw new FacebookSDKException('Cross-site request forgery validation failed. Required GET param "state" missing.');
        }
        $savedState = $this->persistentDataHandler->get('state');
        if (!$savedState) {
            throw new FacebookSDKException('Cross-site request forgery validation failed. Required param "state" missing from persistent data.');
        }

        if (hash_equals($savedState, $state)) {
            return;
        }

        throw new FacebookSDKException('Cross-site request forgery validation failed. The "state" param from the URL and session do not match.');
    }

    /**
     * Resets the CSRF so that it doesn't get reused.
     */
    private function resetCsrf(): void
    {
        $this->persistentDataHandler->set('state', null);
    }

    /**
     * Return the code.
     */
    protected function getCode(): ?string
    {
        return $this->getInput('code');
    }

    /**
     * Return the state.
     */
    protected function getState(): ?string
    {
        return $this->getInput('state');
    }

    /**
     * Return the error code.
     */
    public function getErrorCode(): ?string
    {
        return $this->getInput('error_code');
    }

    /**
     * Returns the error.
     */
    public function getError(): ?string
    {
        return $this->getInput('error');
    }

    /**
     * Returns the error reason.
     */
    public function getErrorReason(): ?string
    {
        return $this->getInput('error_reason');
    }

    /**
     * Returns the error description.
     */
    public function getErrorDescription(): ?string
    {
        return $this->getInput('error_description');
    }

    /**
     * Returns a value from a GET param.
     *
     * @param string $key
     * @return string|null
     */
    private function getInput(string $key): ?string
    {
        $v = $_GET[$key] ?? null;
        if (!is_string($v)) {
            return null;
        }
        return $v;
    }
}
