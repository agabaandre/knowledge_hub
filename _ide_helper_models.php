<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * App\Models\AccessLevel
 *
 * @property int $id
 * @property string $level_name
 * @property string|null $level_description
 * @property string|null $created_at
 * @property string|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|AccessLevel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AccessLevel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AccessLevel query()
 * @method static \Illuminate\Database\Eloquent\Builder|AccessLevel whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccessLevel whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccessLevel whereLevelDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccessLevel whereLevelName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccessLevel whereUpdatedAt($value)
 */
	class AccessLevel extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\AccessLog
 *
 * @property int $id
 * @property string $ip_address
 * @property string $country
 * @property string|null $city
 * @property string|null $publication_id
 * @property string|null $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $lat
 * @property string $long
 * @method static \Illuminate\Database\Eloquent\Builder|AccessLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AccessLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AccessLog query()
 * @method static \Illuminate\Database\Eloquent\Builder|AccessLog whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccessLog whereCountry($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccessLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccessLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccessLog whereIpAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccessLog whereLat($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccessLog whereLong($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccessLog wherePublicationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccessLog whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccessLog whereUserId($value)
 */
	class AccessLog extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\AdministrativeUnitTymon\JWTAuth
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property int|null $parent_id
 * @property string|null $code
 * @property string|null $alternate_code
 * @property string|null $logo
 * @property string|null $icon
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read AdministrativeUnit|null $parent
 * @method static \Illuminate\Database\Eloquent\Builder|AdministrativeUnit newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AdministrativeUnit newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AdministrativeUnit query()
 * @method static \Illuminate\Database\Eloquent\Builder|AdministrativeUnit whereAlternateCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdministrativeUnit whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdministrativeUnit whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdministrativeUnit whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdministrativeUnit whereIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdministrativeUnit whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdministrativeUnit whereLogo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdministrativeUnit whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdministrativeUnit whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdministrativeUnit whereUpdatedAt($value)
 */
	class AdministrativeUnit extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Answer
 *
 * @property int $id
 * @property int $question_id
 * @property string $answer_text
 * @property int $is_correct
 * @property string|null $answer_explanation
 * @property-read \App\Models\Question|null $question
 * @method static \Illuminate\Database\Eloquent\Builder|Answer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Answer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Answer query()
 * @method static \Illuminate\Database\Eloquent\Builder|Answer whereAnswerExplanation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Answer whereAnswerText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Answer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Answer whereIsCorrect($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Answer whereQuestionId($value)
 */
	class Answer extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\ApiClient
 *
 * @property int $id
 * @property int|null $country_id
 * @property string $client_name
 * @property string|null $client_description
 * @property string|null $client_flag
 * @property int|null $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $api_key
 * @property string|null $api_secret
 * @method static \Illuminate\Database\Eloquent\Builder|ApiClient newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ApiClient newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ApiClient query()
 * @method static \Illuminate\Database\Eloquent\Builder|ApiClient whereApiKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApiClient whereApiSecret($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApiClient whereClientDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApiClient whereClientFlag($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApiClient whereClientName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApiClient whereCountryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApiClient whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApiClient whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApiClient whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApiClient whereUserId($value)
 */
	//class ApiClient extends \Eloquent implements \Illuminate\Contracts\Auth\Authenticatable {}
}

namespace App\Models{
/**
 * App\Models\Asset
 *
 * @property-read \App\Models\AssetType|null $type
 * @method static \Illuminate\Database\Eloquent\Builder|Asset newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Asset newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Asset query()
 */
	class Asset extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\AssetType
 *
 * @property int $id
 * @property string $type_name
 * @property string|null $type_desc
 * @property string|null $slug
 * @method static \Illuminate\Database\Eloquent\Builder|AssetType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AssetType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AssetType query()
 * @method static \Illuminate\Database\Eloquent\Builder|AssetType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssetType whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssetType whereTypeDesc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssetType whereTypeName($value)
 */
	class AssetType extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\AuditTrail
 *
 * @property int $id
 * @property int $user_id
 * @property string $action
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $old_data
 * @property string|null $new_data
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|AuditTrail newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AuditTrail newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AuditTrail query()
 * @method static \Illuminate\Database\Eloquent\Builder|AuditTrail whereAction($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AuditTrail whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AuditTrail whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AuditTrail whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AuditTrail whereNewData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AuditTrail whereOldData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AuditTrail whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AuditTrail whereUserId($value)
 */
	class AuditTrail extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Author
 *
 * @property int $id
 * @property string $name
 * @property string $icon
 * @property string $is_organsiation
 * @property string $address
 * @property string $telephone
 * @property string $email
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property string $logo
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Publication> $publications
 * @property-read int|null $publications_count
 * @method static \Illuminate\Database\Eloquent\Builder|Author newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Author newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Author query()
 * @method static \Illuminate\Database\Eloquent\Builder|Author whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Author whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Author whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Author whereIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Author whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Author whereIsOrgansiation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Author whereLogo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Author whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Author whereTelephone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Author whereUpdatedAt($value)
 */
	class Author extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\CommunityOfPractice
 *
 * @property int $id
 * @property string $community_name
 * @property string|null $description
 * @property int $created_by
 * @property int $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|CommunityOfPractice newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CommunityOfPractice newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CommunityOfPractice query()
 * @method static \Illuminate\Database\Eloquent\Builder|CommunityOfPractice whereCommunityName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CommunityOfPractice whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CommunityOfPractice whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CommunityOfPractice whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CommunityOfPractice whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CommunityOfPractice whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CommunityOfPractice whereUpdatedAt($value)
 */
	class CommunityOfPractice extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\CommunityOfPracticeMembers
 *
 * @property int $id
 * @property int $community_of_practice_id
 * @property int $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|CommunityOfPracticeMembers newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CommunityOfPracticeMembers newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CommunityOfPracticeMembers query()
 * @method static \Illuminate\Database\Eloquent\Builder|CommunityOfPracticeMembers whereCommunityOfPracticeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CommunityOfPracticeMembers whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CommunityOfPracticeMembers whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CommunityOfPracticeMembers whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CommunityOfPracticeMembers whereUserId($value)
 */
	class CommunityOfPracticeMembers extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Country
 *
 * @property int $id
 * @property string $national
 * @property string $name
 * @property string|null $iso_code
 * @property string|null $iso3_code
 * @property int|null $numcode
 * @property string|null $phonecode
 * @property int $region_id
 * @property float $longitude
 * @property float $latitude
 * @property int $population
 * @property string $color
 * @property string|null $svg_path
 * @property string|null $flag
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Publication> $publications
 * @property-read int|null $publications_count
 * @property-read \App\Models\Region|null $region
 * @method static \Illuminate\Database\Eloquent\Builder|Country newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Country newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Country query()
 * @method static \Illuminate\Database\Eloquent\Builder|Country whereColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Country whereFlag($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Country whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Country whereIso3Code($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Country whereIsoCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Country whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Country whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Country whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Country whereNational($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Country whereNumcode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Country wherePhonecode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Country wherePopulation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Country whereRegionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Country whereSvgPath($value)
 */
	class Country extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\DataCategory
 *
 * @property int $id
 * @property string $category_name
 * @property string|null $url_path
 * @property string|null $slug
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $show_on_menu
 * @property string|null $required_permission
 * @property int $is_special
 * @property int $is_dashboard
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\DataSubCategory> $sub_categories
 * @property-read int|null $sub_categories_count
 * @method static \Illuminate\Database\Eloquent\Builder|DataCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DataCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DataCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder|DataCategory whereCategoryName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DataCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DataCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DataCategory whereIsDashboard($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DataCategory whereIsSpecial($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DataCategory whereRequiredPermission($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DataCategory whereShowOnMenu($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DataCategory whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DataCategory whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DataCategory whereUrlPath($value)
 */
	class DataCategory extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\DataRecord
 *
 * @property int $id
 * @property string $title
 * @property string $description
 * @property string $url
 * @property int $file_type_id
 * @property int|null $data_sub_category_id
 * @property int $country_id
 * @property string $cover_image
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $is_embedded
 * @property int $data_category_id
 * @property-read \App\Models\DataCategory|null $category
 * @property-read \App\Models\Country|null $country
 * @property-read \App\Models\PublicationType|null $file_type
 * @property-read \App\Models\DataSubCategory|null $sub_category
 * @method static \Illuminate\Database\Eloquent\Builder|DataRecord newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DataRecord newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DataRecord query()
 * @method static \Illuminate\Database\Eloquent\Builder|DataRecord whereCountryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DataRecord whereCoverImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DataRecord whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DataRecord whereDataCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DataRecord whereDataSubCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DataRecord whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DataRecord whereFileTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DataRecord whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DataRecord whereIsEmbedded($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DataRecord whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DataRecord whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DataRecord whereUrl($value)
 */
	class DataRecord extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\DataSubCategory
 *
 * @property int $id
 * @property string $sub_catgeory_name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $data_category_id
 * @property-read \App\Models\DataCategory|null $category
 * @method static \Illuminate\Database\Eloquent\Builder|DataSubCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DataSubCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DataSubCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder|DataSubCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DataSubCategory whereDataCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DataSubCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DataSubCategory whereSubCatgeoryName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DataSubCategory whereUpdatedAt($value)
 */
	class DataSubCategory extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Expert
 *
 * @property int $id
 * @property string $first_name
 * @property string $last_name
 * @property string $job_title
 * @property string $occupation
 * @property string|null $email
 * @property string|null $phone_number
 * @property string $expert_type_id
 * @property string|null $photo
 * @property int|null $country_id
 * @property string|null $url
 * @property int|null $created_by
 * @property string $created_at
 * @property-read \App\Models\Country|null $country
 * @property-read \App\Models\ExpertType|null $type
 * @method static \Illuminate\Database\Eloquent\Builder|Expert newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Expert newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Expert query()
 * @method static \Illuminate\Database\Eloquent\Builder|Expert whereCountryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Expert whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Expert whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Expert whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Expert whereExpertTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Expert whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Expert whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Expert whereJobTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Expert whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Expert whereOccupation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Expert wherePhoneNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Expert wherePhoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Expert whereUrl($value)
 */
	class Expert extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\ExpertType
 *
 * @property int $id
 * @property int|null $isco_id
 * @property string|null $name
 * @method static \Illuminate\Database\Eloquent\Builder|ExpertType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ExpertType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ExpertType query()
 * @method static \Illuminate\Database\Eloquent\Builder|ExpertType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExpertType whereIscoId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExpertType whereName($value)
 */
	class ExpertType extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Fact
 *
 * @property int $id
 * @property string $fact_title
 * @property string $fact_summary
 * @property string|null $fact_description
 * @property string|null $fact_image
 * @property string|null $resource_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Fact newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Fact newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Fact query()
 * @method static \Illuminate\Database\Eloquent\Builder|Fact whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Fact whereFactDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Fact whereFactImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Fact whereFactSummary($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Fact whereFactTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Fact whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Fact whereResourceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Fact whereUpdatedAt($value)
 */
	class Fact extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Faq
 *
 * @property int $id
 * @property string $question
 * @property string $answer
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Faq newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Faq newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Faq query()
 * @method static \Illuminate\Database\Eloquent\Builder|Faq whereAnswer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Faq whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Faq whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Faq whereQuestion($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Faq whereUpdatedAt($value)
 */
	class Faq extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Favourite
 *
 * @property int $id
 * @property int $publication_id
 * @property int $user_id
 * @method static \Illuminate\Database\Eloquent\Builder|Favourite newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Favourite newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Favourite query()
 * @method static \Illuminate\Database\Eloquent\Builder|Favourite whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Favourite wherePublicationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Favourite whereUserId($value)
 */
	class Favourite extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Forum
 *
 * @property int $id
 * @property string $forum_title
 * @property string $forum_image
 * @property string|null $forum_description
 * @property int $created_by
 * @property string $created_at
 * @property string $status
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ForumComment> $comments
 * @property-read int|null $comments_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CommunityOfPractice> $communities
 * @property-read int|null $communities_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ForumTag> $tags
 * @property-read int|null $tags_count
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|Forum newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Forum newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Forum query()
 * @method static \Illuminate\Database\Eloquent\Builder|Forum whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Forum whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Forum whereForumDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Forum whereForumImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Forum whereForumTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Forum whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Forum whereStatus($value)
 */
	class Forum extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\ForumComment
 *
 * @property int $id
 * @property int $forum_id
 * @property string $comment
 * @property int $created_by
 * @property string $created_at
 * @property int|null $parent_id
 * @property string $status
 * @property-read mixed $comment_replies
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|ForumComment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ForumComment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ForumComment query()
 * @method static \Illuminate\Database\Eloquent\Builder|ForumComment whereComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ForumComment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ForumComment whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ForumComment whereForumId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ForumComment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ForumComment whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ForumComment whereStatus($value)
 */
	class ForumComment extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\ForumCommunityOfPractice
 *
 * @property int $id
 * @property int $forum_id
 * @property int $community_of_practice_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|ForumCommunityOfPractice newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ForumCommunityOfPractice newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ForumCommunityOfPractice query()
 * @method static \Illuminate\Database\Eloquent\Builder|ForumCommunityOfPractice whereCommunityOfPracticeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ForumCommunityOfPractice whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ForumCommunityOfPractice whereForumId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ForumCommunityOfPractice whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ForumCommunityOfPractice whereUpdatedAt($value)
 */
	class ForumCommunityOfPractice extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\ForumSubscription
 *
 * @property int $id
 * @property int $user_id
 * @property int $forum_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|ForumSubscription newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ForumSubscription newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ForumSubscription query()
 * @method static \Illuminate\Database\Eloquent\Builder|ForumSubscription whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ForumSubscription whereForumId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ForumSubscription whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ForumSubscription whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ForumSubscription whereUserId($value)
 */
	class ForumSubscription extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\ForumTag
 *
 * @property int $id
 * @property int $forum_id
 * @property string $tag
 * @method static \Illuminate\Database\Eloquent\Builder|ForumTag newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ForumTag newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ForumTag query()
 * @method static \Illuminate\Database\Eloquent\Builder|ForumTag whereForumId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ForumTag whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ForumTag whereTag($value)
 */
	class ForumTag extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\GeoCoverage
 *
 * @property int $id
 * @property string $national
 * @property string $name
 * @property string|null $iso_code
 * @property string|null $iso3_code
 * @property int|null $numcode
 * @property string|null $phonecode
 * @property int $region_id
 * @property float $longitude
 * @property float $latitude
 * @property int $population
 * @property string $color
 * @property string|null $svg_path
 * @property string|null $flag
 * @method static \Illuminate\Database\Eloquent\Builder|GeoCoverage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GeoCoverage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GeoCoverage query()
 * @method static \Illuminate\Database\Eloquent\Builder|GeoCoverage whereColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GeoCoverage whereFlag($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GeoCoverage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GeoCoverage whereIso3Code($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GeoCoverage whereIsoCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GeoCoverage whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GeoCoverage whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GeoCoverage whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GeoCoverage whereNational($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GeoCoverage whereNumcode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GeoCoverage wherePhonecode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GeoCoverage wherePopulation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GeoCoverage whereRegionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GeoCoverage whereSvgPath($value)
 */
	class GeoCoverage extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\HealthAsset
 *
 * @property int $id
 * @property string $asset_name
 * @property string $asset_desc
 * @property int $asset_type_id
 * @property string|null $url
 * @property float|null $baseline_value
 * @property int $country_id
 * @property string|null $geo_code
 * @property int $created_by
 * @property \Illuminate\Support\Carbon $created_at
 * @property-read \App\Models\Country|null $country
 * @property-read \App\Models\AssetType|null $type
 * @method static \Illuminate\Database\Eloquent\Builder|HealthAsset newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|HealthAsset newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|HealthAsset query()
 * @method static \Illuminate\Database\Eloquent\Builder|HealthAsset whereAssetDesc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HealthAsset whereAssetName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HealthAsset whereAssetTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HealthAsset whereBaselineValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HealthAsset whereCountryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HealthAsset whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HealthAsset whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HealthAsset whereGeoCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HealthAsset whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HealthAsset whereUrl($value)
 */
	class HealthAsset extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\IscoClassification
 *
 * @property int $id
 * @property int|null $isco_id
 * @property string|null $name
 * @method static \Illuminate\Database\Eloquent\Builder|IscoClassification newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|IscoClassification newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|IscoClassification query()
 * @method static \Illuminate\Database\Eloquent\Builder|IscoClassification whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IscoClassification whereIscoId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IscoClassification whereName($value)
 */
	class IscoClassification extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\JobTitle
 *
 * @property int $id
 * @property int|null $job_id
 * @property int|null $classification_id
 * @property int|null $isco_id
 * @property string|null $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|JobTitle newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|JobTitle newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|JobTitle query()
 * @method static \Illuminate\Database\Eloquent\Builder|JobTitle whereClassificationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JobTitle whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JobTitle whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JobTitle whereIscoId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JobTitle whereJobId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JobTitle whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JobTitle whereUpdatedAt($value)
 */
	class JobTitle extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Kpi
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property int $subject_area
 * @property string $computation_method
 * @property string $frequency
 * @property-read \App\Models\SubjectArea|null $subjectArea
 * @method static \Illuminate\Database\Eloquent\Builder|Kpi newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Kpi newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Kpi query()
 * @method static \Illuminate\Database\Eloquent\Builder|Kpi whereComputationMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Kpi whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Kpi whereFrequency($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Kpi whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Kpi whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Kpi whereSubjectArea($value)
 */
	class Kpi extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\KpiData
 *
 * @property-read \App\Models\Kpi|null $kpi
 * @method static \Illuminate\Database\Eloquent\Builder|KpiData newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|KpiData newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|KpiData query()
 */
	class KpiData extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\KpiDataRecord
 *
 * @property int $id
 * @property int $kpi_id
 * @property int $value
 * @property string $period
 * @property int $country_id
 * @property int $data_source
 * @property-read \App\Models\Country|null $country
 * @method static \Illuminate\Database\Eloquent\Builder|KpiDataRecord newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|KpiDataRecord newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|KpiDataRecord query()
 * @method static \Illuminate\Database\Eloquent\Builder|KpiDataRecord whereCountryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|KpiDataRecord whereDataSource($value)
 * @method static \Illuminate\Database\Eloquent\Builder|KpiDataRecord whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|KpiDataRecord whereKpiId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|KpiDataRecord wherePeriod($value)
 * @method static \Illuminate\Database\Eloquent\Builder|KpiDataRecord whereValue($value)
 */
	class KpiDataRecord extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Publication
 *
 * @property int $id
 * @property int $author_id
 * @property int $sub_thematic_area_id
 * @property string|null $publication
 * @property string $title
 * @property string $description
 * @property int $publication_catgory_id
 * @property int $geographical_coverage_id
 * @property string|null $cover
 * @property string $is_active
 * @property int $visits
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property int $is_featured
 * @property int $is_video
 * @property int $is_version
 * @property string|null $version_no
 * @property int|null $parent_id
 * @property int|null $file_type_id
 * @property string|null $citation_link
 * @property string|null $citation_authors
 * @property int|null $user_id
 * @property int $is_approved
 * @property int $is_rejected
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\UserAccessGroup> $accessgroups
 * @property-read int|null $accessgroups_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PublicationAttachment> $attachments
 * @property-read int|null $attachments_count
 * @property-read \App\Models\Author|null $author
 * @property-read \App\Models\PublicationCategory|null $category
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PublicationComment> $comments
 * @property-read int|null $comments_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CommunityOfPractice> $communities
 * @property-read int|null $communities_count
 * @property-read \App\Models\PublicationType|null $file_type
 * @property-read mixed $approved_comments
 * @property-read mixed $has_attachments
 * @property-read mixed $is_favourite
 * @property-read mixed $label
 * @property-read mixed $pending_comments
 * @property-read mixed $tag_ids
 * @property-read mixed $theme
 * @property-read mixed $value
 * @property-read Publication|null $parent
 * @property-read \App\Models\SubThemeticArea|null $sub_theme
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PublicationSummary> $summaries
 * @property-read int|null $summaries_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PublicationTag> $tags
 * @property-read int|null $tags_count
 * @property-read \App\Models\User|null $user
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Publication> $versioning
 * @property-read int|null $versioning_count
 * @method static \Illuminate\Database\Eloquent\Builder|Publication newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Publication newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Publication query()
 * @method static \Illuminate\Database\Eloquent\Builder|Publication whereAuthorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Publication whereCitationAuthors($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Publication whereCitationLink($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Publication whereCover($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Publication whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Publication whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Publication whereFileTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Publication whereGeographicalCoverageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Publication whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Publication whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Publication whereIsApproved($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Publication whereIsFeatured($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Publication whereIsRejected($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Publication whereIsVersion($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Publication whereIsVideo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Publication whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Publication wherePublication($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Publication wherePublicationCatgoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Publication whereSubThematicAreaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Publication whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Publication whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Publication whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Publication whereVersionNo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Publication whereVisits($value)
 */
	class Publication extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\PublicationAccessGroup
 *
 * @property int $id
 * @property int $user_access_group_id
 * @property int $publication_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|PublicationAccessGroup newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PublicationAccessGroup newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PublicationAccessGroup query()
 * @method static \Illuminate\Database\Eloquent\Builder|PublicationAccessGroup whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PublicationAccessGroup whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PublicationAccessGroup wherePublicationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PublicationAccessGroup whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PublicationAccessGroup whereUserAccessGroupId($value)
 */
	class PublicationAccessGroup extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\PublicationAttachment
 *
 * @property int $id
 * @property int $publication_id
 * @property string $file
 * @property string|null $description
 * @method static \Illuminate\Database\Eloquent\Builder|PublicationAttachment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PublicationAttachment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PublicationAttachment query()
 * @method static \Illuminate\Database\Eloquent\Builder|PublicationAttachment whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PublicationAttachment whereFile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PublicationAttachment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PublicationAttachment wherePublicationId($value)
 */
	class PublicationAttachment extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\PublicationCategory
 *
 * @property int $id
 * @property string $category_name
 * @property string|null $category_desc
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|PublicationCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PublicationCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PublicationCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder|PublicationCategory whereCategoryDesc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PublicationCategory whereCategoryName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PublicationCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PublicationCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PublicationCategory whereUpdatedAt($value)
 */
	class PublicationCategory extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\PublicationComment
 *
 * @property int $id
 * @property int $user_id
 * @property int $publication_id
 * @property string $comment
 * @property string $created_at
 * @property string $status
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|PublicationComment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PublicationComment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PublicationComment query()
 * @method static \Illuminate\Database\Eloquent\Builder|PublicationComment whereComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PublicationComment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PublicationComment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PublicationComment wherePublicationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PublicationComment whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PublicationComment whereUserId($value)
 */
	class PublicationComment extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\PublicationCommunityOfPractice
 *
 * @property int $id
 * @property int $publication_id
 * @property int $community_of_practice_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|PublicationCommunityOfPractice newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PublicationCommunityOfPractice newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PublicationCommunityOfPractice query()
 * @method static \Illuminate\Database\Eloquent\Builder|PublicationCommunityOfPractice whereCommunityOfPracticeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PublicationCommunityOfPractice whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PublicationCommunityOfPractice whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PublicationCommunityOfPractice wherePublicationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PublicationCommunityOfPractice whereUpdatedAt($value)
 */
	class PublicationCommunityOfPractice extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\PublicationSummary
 *
 * @property int $id
 * @property int $resource_id
 * @property string|null $file_path
 * @property string $title
 * @property string|null $description
 * @property int $is_link
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $is_approved
 * @property int $author_id
 * @property int $is_rejected
 * @property int|null $user_id
 * @property-read \App\Models\Author|null $author
 * @property-read \App\Models\Publication|null $publication
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|PublicationSummary newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PublicationSummary newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PublicationSummary query()
 * @method static \Illuminate\Database\Eloquent\Builder|PublicationSummary whereAuthorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PublicationSummary whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PublicationSummary whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PublicationSummary whereFilePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PublicationSummary whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PublicationSummary whereIsApproved($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PublicationSummary whereIsLink($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PublicationSummary whereIsRejected($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PublicationSummary whereResourceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PublicationSummary whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PublicationSummary whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PublicationSummary whereUserId($value)
 */
	class PublicationSummary extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\PublicationTag
 *
 * @property int $id
 * @property int $tag_id
 * @property int $publication_id
 * @property-read \App\Models\Tag|null $tag
 * @method static \Illuminate\Database\Eloquent\Builder|PublicationTag newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PublicationTag newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PublicationTag query()
 * @method static \Illuminate\Database\Eloquent\Builder|PublicationTag whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PublicationTag wherePublicationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PublicationTag whereTagId($value)
 */
	class PublicationTag extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\PublicationType
 *
 * @property int $id
 * @property string $name
 * @property string $icon
 * @property int $is_downloadable
 * @method static \Illuminate\Database\Eloquent\Builder|PublicationType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PublicationType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PublicationType query()
 * @method static \Illuminate\Database\Eloquent\Builder|PublicationType whereIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PublicationType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PublicationType whereIsDownloadable($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PublicationType whereName($value)
 */
	class PublicationType extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Question
 *
 * @property int $id
 * @property string $question_text
 * @property int $enabled
 * @property \Illuminate\Support\Carbon $created_at
 * @property int|null $resource_id
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Answer> $answers
 * @property-read int|null $answers_count
 * @property-read mixed $right_answers
 * @property-read mixed $wrong_answers
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\QuestionStats> $responses
 * @property-read int|null $responses_count
 * @method static \Illuminate\Database\Eloquent\Builder|Question newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Question newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Question query()
 * @method static \Illuminate\Database\Eloquent\Builder|Question whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Question whereEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Question whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Question whereQuestionText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Question whereResourceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Question whereUpdatedAt($value)
 */
	class Question extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\QuestionStats
 *
 * @property int $id
 * @property int $question_id
 * @property int $answer_id
 * @property int $user_id
 * @property int $is_correct
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionStats newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionStats newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionStats query()
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionStats whereAnswerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionStats whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionStats whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionStats whereIsCorrect($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionStats whereQuestionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionStats whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionStats whereUserId($value)
 */
	class QuestionStats extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Quote
 *
 * @property int $id
 * @property string $quote
 * @property int $is_active
 * @method static \Illuminate\Database\Eloquent\Builder|Quote newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Quote newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Quote query()
 * @method static \Illuminate\Database\Eloquent\Builder|Quote whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Quote whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Quote whereQuote($value)
 */
	class Quote extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Region
 *
 * @property int $id
 * @property string $region_name
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Country> $countries
 * @property-read int|null $countries_count
 * @method static \Illuminate\Database\Eloquent\Builder|Region newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Region newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Region query()
 * @method static \Illuminate\Database\Eloquent\Builder|Region whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Region whereRegionName($value)
 */
	class Region extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Setting
 *
 * @property int $id
 * @property string|null $title
 * @property string $site_name
 * @property string $seo_keywords
 * @property string $site_description
 * @property string|null $address
 * @property string|null $email
 * @property string|null $phone
 * @property string|null $language
 * @property string $timezone
 * @property string|null $default_password
 * @property string|null $primary_color
 * @property string|null $secondary_color
 * @property string|null $logo
 * @property string|null $favicon
 * @property string $default_primary_color
 * @property string $default_secondary_color
 * @property string|null $analytics_script
 * @property string $icon_font_color
 * @property string|null $khub_logo
 * @property string|null $primary_text_color
 * @property string|null $links_active_color
 * @property string|null $spotlight_banner
 * @property string|null $banner_text
 * @property string|null $slogan
 * @property string|null $banner_image
 * @property string|null $facebook
 * @property string|null $twitter
 * @property string|null $linkedin
 * @property string|null $academia
 * @property string|null $researchgate
 * @method static \Illuminate\Database\Eloquent\Builder|Setting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Setting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Setting query()
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereAcademia($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereAnalyticsScript($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereBannerImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereBannerText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereDefaultPassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereDefaultPrimaryColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereDefaultSecondaryColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereFacebook($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereFavicon($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereIconFontColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereKhubLogo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereLanguage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereLinkedin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereLinksActiveColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereLogo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting wherePrimaryColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting wherePrimaryTextColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereResearchgate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereSecondaryColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereSeoKeywords($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereSiteDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereSiteName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereSlogan($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereSpotlightBanner($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereTimezone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereTwitter($value)
 */
	class Setting extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\SubThemeticArea
 *
 * @property int $id
 * @property string $description
 * @property string $icon
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $thematic_area_id
 * @property-read \App\Models\ThemeticArea|null $theme
 * @method static \Illuminate\Database\Eloquent\Builder|SubThemeticArea newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SubThemeticArea newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SubThemeticArea query()
 * @method static \Illuminate\Database\Eloquent\Builder|SubThemeticArea whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubThemeticArea whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubThemeticArea whereIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubThemeticArea whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubThemeticArea whereThematicAreaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubThemeticArea whereUpdatedAt($value)
 */
	class SubThemeticArea extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\SubjectArea
 *
 * @property int $id
 * @property string $name
 * @property string $created
 * @property string $updated
 * @property-read mixed $description
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Kpi> $kpis
 * @property-read int|null $kpis_count
 * @method static \Illuminate\Database\Eloquent\Builder|SubjectArea newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SubjectArea newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SubjectArea query()
 * @method static \Illuminate\Database\Eloquent\Builder|SubjectArea whereCreated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubjectArea whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubjectArea whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubjectArea whereUpdated($value)
 */
	class SubjectArea extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Subscribe
 *
 * @property int $id
 * @property string $email
 * @property string|null $name
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Database\Factories\SubscribeFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscribe newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Subscribe newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Subscribe query()
 * @method static \Illuminate\Database\Eloquent\Builder|Subscribe whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscribe whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscribe whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscribe whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscribe whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscribe whereUpdatedAt($value)
 */
	class Subscribe extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Tag
 *
 * @property int $id
 * @property string $tag_text
 * @method static \Illuminate\Database\Eloquent\Builder|Tag newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Tag newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Tag query()
 * @method static \Illuminate\Database\Eloquent\Builder|Tag whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tag whereTagText($value)
 */
	class Tag extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\ThemeticArea
 *
 * @property int $id
 * @property string $description
 * @property string $icon
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $display_index
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\SubThemeticArea> $subthemes
 * @property-read int|null $subthemes_count
 * @method static \Illuminate\Database\Eloquent\Builder|ThemeticArea newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ThemeticArea newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ThemeticArea query()
 * @method static \Illuminate\Database\Eloquent\Builder|ThemeticArea whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ThemeticArea whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ThemeticArea whereDisplayIndex($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ThemeticArea whereIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ThemeticArea whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ThemeticArea whereUpdatedAt($value)
 */
	class ThemeticArea extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\User
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $author_id
 * @property string $photo
 * @property string|null $first_name
 * @property string $last_name
 * @property string|null $organization_name
 * @property int $is_approved
 * @property int $is_verified
 * @property int $is_changed
 * @property int $status
 * @property string|null $verification_token
 * @property string|null $job_title
 * @property string|null $phone_number
 * @property int|null $country_id
 * @property int $is_subscribed
 * @property int $access_level_id
 * @property int $pwd_changed
 * @property int|null $administrative_unit_id
 * @property-read \App\Models\AccessLevel|null $access_level
 * @property-read \App\Models\Country|null $country
 * @property-read mixed $area
 * @property-read mixed $names
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\UserPreference> $preferences
 * @property-read int|null $preferences_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Role> $roles
 * @property-read int|null $roles_count
 * @method static \Database\Factories\UserFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User permission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User role($roles, $guard = null)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereAccessLevelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereAdministrativeUnitId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereAuthorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCountryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereIsApproved($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereIsChanged($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereIsSubscribed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereIsVerified($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereJobTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereOrganizationName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePhoneNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePhoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePwdChanged($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereVerificationToken($value)
 */
	class User extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\UserAccessGroup
 *
 * @property int $id
 * @property string $group_name
 * @property string $group_description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|UserAccessGroup newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserAccessGroup newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserAccessGroup query()
 * @method static \Illuminate\Database\Eloquent\Builder|UserAccessGroup whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAccessGroup whereGroupDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAccessGroup whereGroupName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAccessGroup whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAccessGroup whereUpdatedAt($value)
 */
	class UserAccessGroup extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\UserAccessGrouping
 *
 * @property int $id
 * @property int $user_id
 * @property int $user_access_group_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|UserAccessGrouping newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserAccessGrouping newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserAccessGrouping query()
 * @method static \Illuminate\Database\Eloquent\Builder|UserAccessGrouping whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAccessGrouping whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAccessGrouping whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAccessGrouping whereUserAccessGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAccessGrouping whereUserId($value)
 */
	class UserAccessGrouping extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\UserPreference
 *
 * @property int $id
 * @property int $user_id
 * @property int $tag_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|UserPreference newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserPreference newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserPreference query()
 * @method static \Illuminate\Database\Eloquent\Builder|UserPreference whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserPreference whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserPreference whereTagId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserPreference whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserPreference whereUserId($value)
 */
	class UserPreference extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\UserRole
 *
 * @property int $id
 * @property string $model_type
 * @property int $model_id
 * @property int $role_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|UserRole newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserRole newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserRole query()
 * @method static \Illuminate\Database\Eloquent\Builder|UserRole whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserRole whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserRole whereModelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserRole whereModelType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserRole whereRoleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserRole whereUpdatedAt($value)
 */
	class UserRole extends \Eloquent {}
}

