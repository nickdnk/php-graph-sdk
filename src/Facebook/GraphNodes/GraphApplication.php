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

namespace Facebook\GraphNodes;

use DateTime;

/**
 * Class GraphApplication
 *
 * @package Facebook
 * @link https://developers.facebook.com/docs/graph-api/reference/application/
 */
class GraphApplication extends GraphNode
{

    protected static array $graphObjectMap = [
        'object_store_urls' => GraphApplicationObjectStoreURLs::class,
        'restrictions'      => GraphApplicationRestrictions::class,
        'app_events_config' => GraphApplicationEventsConfig::class
    ];

    /**
     * Returns the ID for the application.
     */
    public function getId(): ?string
    {
        return $this->getField('id');
    }

    public function getAamRules(): ?string
    {
        return $this->getField('aam_rules');
    }

    public function getAnAdSpaceLimit(): ?int
    {
        return $this->getField('an_ad_space_limit');
    }

    /**
     * @return ?string[]
     */
    public function getAppDomains(): ?array
    {
        return $this->getField('app_domains');
    }

    public function getAppEventsConfig(): ?GraphApplicationEventsConfig
    {
        return $this->getField('app_events_config');
    }

    public function isAppInstallTracked(): ?bool
    {
        return $this->getField('app_install_tracked');
    }

    public function getAppName(): ?string
    {
        return $this->getField('app_name');
    }

    public function getAppType(): ?int
    {
        return $this->getField('app_type');
    }

    public function getAuthDialogDataHelpUrl(): ?string
    {
        return $this->getField('auth_dialog_data_help_url');
    }

    public function getAuthDialogHeadline(): ?string
    {
        return $this->getField('auth_dialog_headline');
    }

    public function getAuthDialoguePermissionsExplanation(): ?string
    {
        return $this->getField('auth_dialog_perms_explanation');
    }

    public function getAuthReferralDefaultActivityPrivacy(): ?string
    {
        return $this->getField('auth_referral_default_activity_privacy');
    }

    public function isAuthReferralEnabled(): ?bool
    {
        return $this->getField('auth_referral_enabled');
    }

    /**
     * @return ?string[]
     */
    public function getAuthReferralExtendedPermissions(): ?array
    {
        return $this->getField('auth_referral_extended_perms');
    }

    /**
     * @return ?string[]
     */
    public function getAuthReferralFriendPermissions(): ?array
    {
        return $this->getField('auth_referral_friend_perms');
    }

    public function getAuthReferralResponseType(): ?string
    {
        return $this->getField('auth_referral_response_type');
    }

    /**
     * @return ?string[]
     */
    public function getAuthReferralUserPermissions(): ?array
    {
        return $this->getField('auth_referral_user_perms');
    }

    public function isCanvasFluidHeight(): ?bool
    {
        return $this->getField('canvas_fluid_height');
    }

    public function getCanvasFluidWidth(): ?int
    {
        return $this->getField('canvas_fluid_width');
    }

    public function getCanvasUrl(): ?string
    {
        return $this->getField('canvas_url');
    }

    public function getCategory(): ?string
    {
        return $this->getField('category');
    }

    public function getClientConfig(): ?array
    {
        return $this->getField('client_config');
    }

    public function getCompany(): ?string
    {
        return $this->getField('company');
    }

    public function isConfigurediOSSSO(): ?bool
    {
        return $this->getField('configured_ios_sso');
    }

    public function getContactEmail(): ?string
    {
        return $this->getField('contact_email');
    }

    public function getCreatedTime(): ?DateTime
    {
        return $this->getField('created_time');
    }

    public function getCreatorUid(): ?string
    {
        return $this->getField('creator_uid');
    }

    public function getDailyActiveUsers(): ?string
    {
        return $this->getField('daily_active_users');
    }

    public function getDailyActiveUsersRank(): ?int
    {
        return $this->getField('daily_active_users_rank');
    }

    public function getDeauthCallbackUrl(): ?string
    {
        return $this->getField('deauth_callback_url');
    }

    public function getDefaultShareMode(): ?string
    {
        return $this->getField('default_share_mode');
    }

    public function getDescription(): ?string
    {
        return $this->getField('description');
    }

    public function getFinancialId(): ?string
    {
        return $this->getField('financial_id');
    }

    public function getHostingUrl(): ?string
    {
        return $this->getField('hosting_url');
    }

    public function getIconUrl(): ?string
    {
        return $this->getField('icon_url');
    }

    /**
     * @return ?string[]
     */
    public function getiOSBundleIds(): ?array
    {
        return $this->getField('ios_bundle_id');
    }

    public function iOSSupportsNativeProxyAuthFlow(): ?bool
    {
        return $this->getField('ios_supports_native_proxy_auth_flow');
    }

    public function iOSSupportsSystemAuth(): ?bool
    {
        return $this->getField('ios_supports_system_auth');
    }

    public function getiPadAppStoreId(): ?string
    {
        return $this->getField('ipad_app_store_id');
    }

    public function getiPhoneAppStoreId(): ?string
    {
        return $this->getField('iphone_app_store_id');
    }

    public function getLink(): ?string
    {
        return $this->getField('link');
    }

    public function getLoggingToken(): ?string
    {
        return $this->getField('logging_token');
    }

    public function getLogoUrl(): ?string
    {
        return $this->getField('logo_url');
    }

    public function getMobileProfileSectionUrl(): ?string
    {
        return $this->getField('mobile_profile_section_url');
    }

    public function getMobileWebUrl(): ?string
    {
        return $this->getField('mobile_web_url');
    }

    public function getMonthlyActiveUsers(): ?string
    {
        return $this->getField('monthly_active_users');
    }

    public function getMonthlyActiveUsersRank(): ?int
    {
        return $this->getField('monthly_active_users_rank');
    }

    /**
     * Seems to be identical to `getAppName()`.
     */
    public function getName(): ?string
    {
        return $this->getField('name');
    }

    public function getNamespace(): ?string
    {
        return $this->getField('namespace');
    }

    public function getObjectStoreUrls(): ?GraphApplicationObjectStoreURLs
    {
        return $this->getField('object_store_urls');
    }

    public function getPageTabDefaultName(): ?string
    {
        return $this->getField('page_tab_default_name');
    }

    public function getPageTabUrl(): ?string
    {
        return $this->getField('page_tab_url');
    }

    public function getPhotoUrl(): ?string
    {
        return $this->getField('photo_url');
    }

    public function getPrivacyPolicyUrl(): ?string
    {
        return $this->getField('privacy_policy_url');
    }

    public function getProfileSectionUrl(): ?string
    {
        return $this->getField('profile_section_url');
    }

    public function getPropertyId(): ?string
    {
        return $this->getField('property_id');
    }

    /**
     * @return ?string[]
     */
    public function getRealTimeModeDevices(): ?array
    {
        return $this->getField('real_time_mode_devices');
    }

    public function getRestrictions(): ?GraphApplicationRestrictions
    {
        return $this->getField('restrictions');
    }

    public function getRestrictiveDataFilterParams(): ?string
    {
        return $this->getField('restrictive_data_filter_params');
    }

    public function getSecureCanvasUrl(): ?string
    {
        return $this->getField('secure_canvas_url');
    }

    public function getSecurePageTabUrl(): ?string
    {
        return $this->getField('secure_page_tab_url');
    }

    public function getServerIPWhitelist(): ?string
    {
        return $this->getField('server_ip_whitelist');
    }

    public function isSocialDiscovery(): ?bool
    {
        return $this->getField('social_discovery');
    }

    public function getSubcategory(): ?string
    {
        return $this->getField('subcategory');
    }

    public function getSuggestedEventSettings(): ?string
    {
        return $this->getField('suggested_event_settings');
    }

    /**
     * Array of: `WEB`,`CANVAS`,`MOBILE_WEB`,`IPHONE`,`IPAD`,`ANDROID`,`WINDOWS`,`AMAZON`,`SUPPLEMENTARY_IMAGES`,
     * `GAMEROOM`,`INSTANT_GAME`,`OCULUS`,`SAMSUNG`,`XIAOMI`.
     * @return ?string[]
     */
    public function getSupportedPlatforms(): ?array
    {
        return $this->getField('supported_platforms');
    }

    public function getTermsOfServiceUrl(): ?string
    {
        return $this->getField('terms_of_service_url');
    }

    public function getUrlSchemeSuffix(): ?string
    {
        return $this->getField('url_scheme_suffix');
    }

    public function getUserSupportEmail(): ?string
    {
        return $this->getField('user_support_email');
    }

    public function getUserSupportUrl(): ?string
    {
        return $this->getField('user_support_url');
    }

    public function getWebsiteUrl(): ?string
    {
        return $this->getField('website_url');
    }

    public function getWeeklyActiveUsers(): ?string
    {
        return $this->getField('weekly_active_users');
    }
}
