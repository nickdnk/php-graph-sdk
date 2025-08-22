# CHANGELOG

## 8.x (UNOFFICIAL)

- 8.0.1
    - Removed pinned [DigiCert High Assurance EV Root CA](https://knowledge.digicert.com/general-information/digicert-trusted-root-authority-certificates).
OS-supplied CA is now used by default.

- 8.0.0
    - Requires PHP 8.1.
    - Defaults to Facebook Graph v20.0, instead of v2.10 which is no longer accessible.
    - Added a few more objects/types. Please open a PR if you want to add more types.
        - `GraphAgeRange`
        - `GraphApplicationEventsConfig`
        - `GraphApplicationObjectStoreURLs`
        - `GraphApplicationRestrictions`
        - `GraphArchivedAd`
        - `GraphEngagement`
        - `GraphExperience`
        - `GraphInsightsRangeValue`
        - `GraphMailingAddress`
        - `GraphPaymentPricePoint`
        - `GraphPaymentPricePoints`
        - `GraphPermission`
        - `GraphPhoto`
        - `GraphPlace`
        - `GraphPlatformImageSource`
        - `GraphVideo`
        - `GraphVideoFormat`
        - `GraphVideoUploadLimits`
    - Removed integration tests as Facebook's API does not support it anymore anyway.
    - Deleted the entire `docs` folder as it's too much work to maintain and was full of outdated examples. For
      documentation, see https://developers.facebook.com/docs/graph-api.
    - Removed the custom `autoload.php` file. Please autoload with composer.
    - Removed all `mcrypt` implementations as it was removed in PHP 7.2:
        - Removed `mcrypt` as option for `createPseudoRandomStringGenerator`.
        - Removed `McryptPseudoRandomStringGenerator`.
    - Removed deprecated classes:
        - `GraphList`
        - `GraphObject`
        - `GraphObjectFactory`
    - `AccessTokenMetaData`:
        - Removed deprecated `getProperty()` function. Use `getField()`.
        - `getExpiresAt()` and `getIssuedAt()` now return `null` if the underlying data cannot be converted to a timestamp.
    - `Collection`:
        - Removed deprecated `getProperty()` function. Use `getField()`.
        - Removed deprecated `getPropertyNames()` function. Use `getFieldNames()`.
    - `FacebookResponse`:
        - Removed deprecated `getGraphList()` function. Use `getGraphEdge()`.
    - `FacebookApp`:
        - If PHP runs in 32 bit mode, you *must* provide your app ID as a string.
    - `GraphNodeFactory`:
        - Removed convenience methods as they would require a method per node/object. Use `makeGraphNode(<class>)`
          instead and pass in a subclass of `GraphNode`.
            - `makeGraphAchievment()`
            - `makeGraphAlbum()`
            - `makeGraphPage()`
            - `makeGraphSessionInfo()`
            - `makeGraphUser()`
            - `makeGraphEvent()`
            - `makeGraphGroup()`
        - `castAsGraphNodeOrGraphEdge()` is now private.
        - `safelyMakeGraphEdge()` is now private.
        - `safelyMakeGraphNode()` is now private.
        - `validateResponseAsArray()` is now private.
    - All random string generators now type-check their `length` parameter instead of relying on a validation method and
      user-land exception.
    - `GraphAlbum`:
        - `getPlace()` now correctly returns a `GraphPlace` instead of a `GraphPage`.
    - `GraphEvent`:
        - Removed `getPicture()`. Use `getCover()` instead. 
    - `GraphGroup`:
        - Removed `getLink()`.
        - Removed `getVenue()`.
    - `GraphNode`:
        - `castToBirthday()` is now private.
        - `castToDateTime()` is now private.
        - Removed `isIso8601DateString()`. If a date/time field cannot be parsed as a `DateTime`, it will be omitted
          instead as this would be incompatible with types added to these fields.
    - `GraphPage`:
        - Removed `getGlobalBrandParentPage()`.
        - Removed `getPerms()`.
    - `GraphAchievement`:
        - Removed entirely. See https://developers.facebook.com/docs/graph-api/reference/user/achievements.
    - `FacebookResponse`:
        - Deprecated these type-casting methods. Use `getGraphNode(<class>)` instead. This is similar to the changes made to
          `GraphNodeFactory` for the same reason.
            - `getGraphAlbum()`
            - `getGraphPage()`
            - `getGraphSessionInfo()`
            - `getGraphUser()`
            - `getGraphEvent()`
            - `getGraphGroup()`
        - Removed `getGraphAchivement()`

## 7.x (UNOFFICIAL)

- 7.0.1
    - Add `conflict` section to `composer.json` to prevent incompatible versions of Guzzle.
- 7.0.0 (:warning: Removed from packagist because it was missing the conflict change from 7.0.1)
    - Guzzle now takes priority over cURL as the HTTP client, if available.
    - Removed support for Guzzle 5. Guzzle 6 or 7 is now required if using Guzzle.
    - Fixed additional deprecation warnings on PHP 8.1.
    - Removed deprecated or skipped tests for PHP < 7.3.

## 6.x (UNOFFICIAL)

- 6.0.3
    - Fixed deprecation warnings for PHP 8.1
- 6.0.2
    - Removed PHP 5 polyfills
    - Updated `phpunit.xml`
    - Added GitHub Actions
    - Test against PHP 8.1
- 6.0.1
    - Added `replace` property to `composer.json`
- 6.0.0
    - Works with PHP 8.

## 5.x

Version 5 of the Facebook PHP SDK is a complete refactor of version 4. It comes loaded with lots of new features and a
friendlier API.

- 5.7.1 (2018-XX-XX)
- 5.7.0 (2018-12-12)
    - Add `joined` to list of fields to be cast to `\DateTime` (#950)
    - Add `GraphPage::getFanCount()` to get the number of people who like the page (#815)
    - Fixed HTTP/2 support (#1079)
    - Fixed resumable upload error (#1001)
    - Strip 'enforce_https' param (#1084)
    - Conserve id when next to data key, resolves #700 (#1034)
- 5.6.3 (2018-07-01)
    - Add fix for countable error in PHP 7.2 (originally #969 by @andreybolonin)
- 5.6.2 (2018-02-15)
    - Strip 'code' param (#913)
- 5.6.1 (2017-08-16)
    - Fixed doc block syntax that interfered with Doctrine (#844)
- 5.6.0 (2017-07-23)
    - Bump Graph API version to v2.10 (#829)
- 5.5.0 (2017-04-20)
    - Added support for batch options (#713)
    - Bump Graph API version to v2.9.
- 5.4.4 (2017-01-19)
    - Added the `application/octet-stream` MIME type for SRT files (#734)
- 5.4.3 (2016-12-30)
    - Fixed a bug that would throw a type error in `GraphEdge` in some cases (#715)
- 5.4.2 (2016-11-15)
    - Added check for [PHP 7 CSPRNG](http://php.net/manual/en/function.random-bytes.php) first to keep mcrypt
      deprecation messages from appearing in PHP 7.1 (#692)
- 5.4.1 (2016-10-18)
    - Fixed a bug that was not properly parsing response headers when they contained the colon `:` character. (#679)
- 5.4.0 (2016-10-12)
    - Bump Graph API version to v2.8.
    - Auto-cast `cover` field to `GraphCoverPhoto` and `picture` field to `GraphPicture` in `GraphPage`. (#655)
    - Added `getCover()` and `getPicture()` to `GraphPage`. (#655)
- 5.3.1
    - Fixed a bug where the `polyfills.php` file wasn't being included properly when using the built-in auto loader (
      #633)
- 5.3.0
    - Bump Graph API version to v2.7.
- 5.2.1
    - Fix notice that is raised in `FacebookUrlDetectionHandler` (#626)
    - Fix bug in `FacebookRedirectLoginHelper::getLoginUrl()` where the CSRF token gets overwritten in certain
      scenarios (#613)
    - Fix bug with polyfills not getting loaded when installing the Facebook PHP SDK manually (#599)
- 5.2.0
    - Added new Birthday class to handle Graph API response variations
    - Bumped Graph version to v2.6
    - Added better error checking for app IDs that are cast as int when they are greater than PHP_INT_MAX
- 5.1.5
    - Removed mbstring extension dependency
    - Updated required PHP version syntax in composer.json
- 5.1.4
    - Breaking changes
        - Changes the serialization method of FacebookApp
            - FacebookApps serialized by versions prior 5.1.4 cannot be unserialized by this version
    - Fixed redirect_uri injection vulnerability
- 5.0 (2015-07-09)
    - New features
        - Added the `Facebook\Facebook` super service for an easier API
        - Improved "reauthentication" and "rerequest" support
        - Requests/Responses
            - Added full batch support
            - Added full file upload support for videos & photos
            - Added methods to make pagination easier
            - Added "deep" pagination support so that Graph edges embedded in a Graph node can be paginated over easily
            - Beta support at `graph.beta.facebook.com`
            - Added `getMetaData()` to `GraphEdge` to obtain all the metadata associated with a list of Graph nodes
            - Full nested param support
            - Many improvements to the Graph node subtypes
        - New injectable interfaces
            - Added a `PersistentDataInterface` for custom persistent data handling
            - Added a `PseudoRandomStringGeneratorInterface` for customizable CSPRNG's
            - Added a `UrlDetectionInterface` for custom URL-detection logic
    - Codebase changes
        - Moved exception classes to `Exception\*` directory
        - Moved response collection objects to `GraphNodes\*` directory
        - Moved helpers to `Helpers\*` directory
        - Killed `FacebookSession` in favor of the `AccessToken` entity
        - Added `FacebookClient` service
        - Renamed `FacebookRequestException` to `FacebookResponseException`
        - Renamed `FacebookHttpable` to `FacebookHttpClientInterface`
        - Added `FacebookApp` entity that contains info about the Facebook app
        - Updated the API for the helpers
        - Added `HttpClients`, `PersistentData` and `PseudoRandomString` factories to reduce main class' complexity
    - Tests
        - Added namespaces to the tests
        - Grouped functional tests under `functional` group
    - Other changes
        - Made PSR-2 compliant
        - Adopted SemVer
        - Completely refactored request/response handling
        - Refactored the OAuth 2.0 logic
        - Added `ext-mbstring` to composer require
        - Added this CHANGELOG. Hi! :)

## 4.1-dev

Since the Facebook PHP SDK didn't follow SemVer in version 4.x, the master branch was going to be released as 4.1.
However, the SDK switched to SemVer in v5.0. So any references on the internet to version 4.1 can be assumed to be an
alias to version `5.0.0`

## 4.0.x

Version 4.0 of the Facebook PHP SDK did not follow [SemVer](http://semver.org/). The versioning format used was as
follows: `4.MAJOR.(MINOR|PATCH)`. The `MINOR` and `PATCH` versions were squashed together.

- 4.0.23 (2015-04-03)
    - Added support for new JSON response types in Graph v2.3 when requesting access tokens
- 4.0.22 (2015-04-02)
    - Fixed issues related to multidimensional params
    - **Bumped default fallback Graph version to `v2.3`**
- 4.0.21 (2015-03-31)
    - Added a `FacebookPermissions` class to reference all the Facebook permissions
- 4.0.20 (2015-03-02)
    - Fixed a bug introduced in `4.0.19` related to CSRF comparisons
- 4.0.19 (2015-03-02)
    - Added stricter CSRF comparison checks to `SignedRequest` and `FacebookRedirectLoginHelper`
- 4.0.18 (2015-02-24)
    - [`FacebookHttpable`] Reverted a breaking change from `4.0.17` that changed the method signatures
- 4.0.17 (2015-02-19)
    - [`FacebookRedirectLoginHelper`] Added multiple auth types to `getLoginUrl()`
    - [`GraphUser`] Added `getTimezone()`
    - [`FacebookCurl`] Additional fix for `curl_init()` handling
    - Added support for https://graph-video.facebook.com when path ends with `/videos`
- 4.0.16 (2015-02-03)
    - [`FacebookRedirectLoginHelper`] Added "reauthenticate" functionality to `getLoginUrl()`
    - [`FacebookCurl`] Fixed `curl_init()` issue
- 4.0.15 (2015-01-06)
    - [`FacebookRedirectLoginHelper`] Added guard against accidental exposure of app secret via the logout link
- 4.0.14 (2014-12-29)
    - [`GraphUser`] Added `getGender()`
    - [`FacebookRedirectLoginHelper`] Added CSRF protection for rerequest links
    - [`GraphAlbum`] Fixed bugs in getter methods
- 4.0.13 (2014-12-12)
    - [`FacebookRedirectLoginHelper`] Added `$displayAsPopup` param to `getLoginUrl()`
    - [`FacebookResponse`] Fixed minor pagination bug
    - Removed massive cert bundle and replaced with `DigiCertHighAssuranceEVRootCA` for peer verification
- 4.0.12 (2014-10-30)
    - **Updated default fallback Graph version to `v2.2`**
    - Fixed potential duplicate `type` param in URL's
    - [`FacebookRedirectLoginHelper`] Added `getReRequestUrl()`
    - [`GraphUser`] Added `getEmail()`
- 4.0.11 (2014-08-25)
    - [`FacebookCurlHttpClient`] Added a method to disable IPv6 resolution
- 4.0.10 (2014-08-12)
    - [`GraphObject`] Fixed improper usage of `stdClass`
    - Fixed warnings when `open_basedir` directive set
    - Fixed long lived sessions forgetting the signed request
    - [`CanvasLoginHelper`] Removed GET processing
    - Updated visibility on `FacebookSession::useAppSecretProof`
- 4.0.9 (2014-06-27)
    - [`FacebookPageTabHelper`] Added ability to fetch `app_data`
    - Added `GraphUserPage` Graph node collection
    - Cleaned up test files
    - Decoupled signed request handling
    - Added some stronger type hinting
    - Explicitly added separator in `http_build_query()`
    - [`FacebookCurlHttpClient`] Updated the calculation of the request body size
    - Decoupled access token handling
    - [`FacebookRedirectLoginHelper`] Implemented better CSPRNG
    - Added autoloader for those poor non-composer peeps
- 4.0.8 (2014-06-10)
    - Enabled `appsecret_proof` by default
    - Added stream wrapper and Guzzle HTTP client implementations
- 4.0.7 (2014-05-31)
    - Improved testing environment
    - Added `FacebookPageTabHelper`
    - [`FacebookSession`] Fixed issue where `validateSessionInfo()` would return incorrect results
- 4.0.6 (2014-05-24)
    - Added feature to inject custom HTTP clients
    - [`FacebookCanvasLoginHelper`] Fixed bug that would throw when logging out
    - Removed appToken from test credentials file
    - [`FacebookRequest`] Added `appsecret_proof` handling
- 4.0.5 (2014-05-19)
    - Fixed bug in cURL where proxy headers are not included in header_size
    - Added internal SDK error codes for thrown exceptions
    - Added stream wrapper fallback for hosting environments without cURL
    - Added getter methods for signed requests
    - Fixed warning that showed up in tests
    - Changed SDK error code for stream failure
    - Added `GraphAlbum` Graph node collection
- 4.0.4 (2014-05-15)
    - Added more error codes to accommodate more Graph error responses
    - [`JavaScriptLoginHelper`] Fixed bug that would try to get a new access token when one already existed
- 4.0.3 (2014-05-14)
    - Fixed bug for "Missing client_id parameter" error
    - Fixed bug for eTag support when "Network is unreachable" error occurs
    - Fixed pagination issue related to `sdtClass`
- 4.0.2 (2014-05-07)
    - [`composer.json`] Upgraded to use PSR-4 autoloading instead of Composer's `classmap`
    - [`FacebookCanvasLoginHelper`] Abstracted access to super globals
    - [`FacebookRequest`] Fixed bug that blindly appended params to a url
    - [`FacebookRequest`] Added support for `DELETE` and `PUT` methods
    - Added eTag support to Graph requests
- 4.0.1 (2014-05-05)
    - All exceptions are now extend from `FacebookSDKException`
    - [`FacebookSession`] Signed request parsing will throw on malformed signed request input
    - Excluded test credentials from tests
    - [`FacebookRedirectLoginHelper`] Changed scope on `$state` property
    - [`phpunit.xml`] Normalized
- 4.0.0 (2014-04-30)
    - Initial release. Yay!
