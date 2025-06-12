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

/**
 * Class GraphUser
 *
 * @package Facebook
 * @link https://developers.facebook.com/docs/graph-api/reference/user/
 */
class GraphUser extends GraphNode
{

    protected static array $graphObjectMap = [
        'age_range'            => GraphAgeRange::class,
        'hometown'             => GraphPage::class,
        'location'             => GraphPage::class,
        'picture'              => GraphPicture::class,
        'significant_other'    => GraphUser::class,
        'sports'               => GraphExperience::class,
        'favorite_teams'       => GraphExperience::class,
        'favorite_athletes'    => GraphExperience::class,
        'languages'            => GraphExperience::class,
        'inspirational_people' => GraphExperience::class,
        'video_upload_limits'  => GraphVideoUploadLimits::class,
        'permissions'          => GraphPermission::class,
        'payment_pricepoints'  => GraphPaymentPricePoints::class,
        'albums'               => GraphAlbum::class
    ];

    public function getId(): ?string
    {
        return $this->getField('id');
    }

    public function getName(): ?string
    {
        return $this->getField('name');
    }

    public function getProfilePicture(): ?string
    {
        return $this->getField('profile_pic');
    }

    public function getClientBusinessId(): ?string
    {
        return $this->getField('client_business_id');
    }

    public function getTokenForBusiness(): ?string
    {
        return $this->getField('token_for_business');
    }

    public function getIdForAvatars(): ?string
    {
        return $this->getField('id_for_avatars');
    }

    /**
     * @return ?GraphExperience[]
     */
    public function getFavoriteAthletes(): ?array
    {
        return $this->getField('favorite_athletes');
    }

    /**
     * @return ?GraphExperience[]
     */
    public function getFavoriteTeams(): ?array
    {
        return $this->getField('favorite_teams');
    }

    /**
     * @return ?GraphExperience[]
     */
    public function getLanguages(): ?array
    {
        return $this->getField('languages');
    }

    /**
     * @return ?GraphExperience[]
     */
    public function getInspirationalPeople(): ?array
    {
        return $this->getField('inspirational_people');
    }

    public function getAbout(): ?string
    {
        return $this->getField('about');
    }

    public function getNameFormat(): ?string
    {
        return $this->getField('name_format');
    }

    public function getShortName(): ?string
    {
        return $this->getField('short_name');
    }

    public function getFirstName(): ?string
    {
        return $this->getField('first_name');
    }

    public function getMiddleName(): ?string
    {
        return $this->getField('middle_name');
    }

    public function getLastName(): ?string
    {
        return $this->getField('last_name');
    }

    public function getEmail(): ?string
    {
        return $this->getField('email');
    }

    public function getGender(): ?string
    {
        return $this->getField('gender');
    }

    public function getLink(): ?string
    {
        return $this->getField('link');
    }

    public function getBirthday(): ?Birthday
    {
        return $this->getField('birthday');
    }

    public function getLocation(): ?GraphPage
    {
        return $this->getField('location');
    }

    /**
     * Returns the current location of the user as a GraphPage.
     */
    public function getHometown(): ?GraphPage
    {
        return $this->getField('hometown');
    }

    /**
     * Returns the current location of the user as a GraphUser.
     */
    public function getSignificantOther(): ?GraphUser
    {
        return $this->getField('significant_other');
    }

    /**
     * Returns the picture of the user as a GraphPicture
     */
    public function getPicture(): ?GraphPicture
    {
        return $this->getField('picture');
    }

    public function supportsDonateButtonInLiveVideos(): ?bool
    {
        return $this->getField('supports_donate_button_in_live_video');
    }

    public function getAgeRange(): ?GraphAgeRange
    {
        return $this->getField('age_range');
    }

    public function isInstalled(): ?bool
    {
        return $this->getField('installed');
    }

    /**
     * @return ?string[]
     */
    public function getMeetingFor(): ?array
    {
        return $this->getField('meeting_for');
    }

    /**
     * @return ?GraphExperience[]
     */
    public function getSports(): ?array
    {
        return $this->getField('sports');
    }

    public function getVideoUploadLimits(): ?GraphVideoUploadLimits
    {
        return $this->getField('video_upload_limits');
    }

    public function getPermissions(): ?GraphEdge
    {
        return $this->getField('permissions');
    }

    public function getAlbums(): ?GraphEdge
    {
        return $this->getField('albums');
    }

    public function getPaymentPricePoints(): ?GraphPaymentPricePoints
    {
        return $this->getField('payment_pricepoints');
    }
}
