<?php
/**
 * @file
 * Keep track of some of the default values
 */

/**
 * @defgroup quest
 * @{
 *
 * Default quest types
 */
class QuestType {
	const TASK            = 'task';
	const COLLEGE_SUBJECT = 'college_subject';
	const COLLEGE_COURSE  = 'college_course';
	const COLLEGE_SECTION = 'college_section';
	const COLLEGE_EXAM    = 'college_exam';
	const COLLEGE_READING = 'college_reading';
	const COLLEGE_LAB     = 'college_lab';
	const COLLEGE_ESSAY   = 'college_essay';
	const COLLEGE_QUIZ    = 'college_quiz';
}

/**
 * Default quest attribute types
 */
class QuestAttributeType {
	const COLLEGE_SESSION_NUM  = 'college_session_num';
	const COLLEGE_COURSE_NUM   = 'college_course_num';
	const COLLEGE_SUBJECT_ABBR = 'college_subject_abbr';
}

/**
 * @} End of "defgroup quest"
 */

/**
 * @defgroup date
 * @{
 *
 * Default date types
 */
class DateType {
	const END_DATE   = 'end_date';
	const BEGIN_DATE = 'begin_date';
	const CHECKPOINT = 'checkpoint';
}

/**
 * @} End of "defgroup date"
 */

/**
 * @defgroup item
 * @{
 *
 * Default item type
 */
class ItemType {
	const BOOK          = 'book';
	const LAB_MATERIAL  = 'lab_material';
	const LAB_EQUIPMENT = 'lab_equipment';
}

/**
 * Default item attribute type
 */
class ItemAttributeType {
	const ISBN      = 'isbn';
	const WEIGHT    = 'weight';
	const DIMENSION = 'dimension';
}

/**
 * @} End of "defgroup item"
 */


/**
 * @defgroup affiliation
 * @{
 *
 * Default affiliation type
 */
class AffiliationType {
	const COLLEGE            = 'college';
	const STUDY_GROUP        = 'study_group';
	const COLLEGE_FRATERNITY = 'college_fraternity';
	const COLLEGE_SORORITY   = 'college_sorority';
}

/**
 * @} End of "defgroup affiliation"
 */

/**
 * @defgroup location
 * @{
 *
 * Default location type
 */
class LocationType {
	const CAMPUS = 'campus';
	const BUILDING = 'building';
	const CITY = 'city';
}

/**
 * @} End of "defgroup location"
 */

/**
 * @defgroup merchant
 *
 * Default merhant type
 */
class MerchantType {
	const RETAIL_ONLINE = 'retail_online';
	const RETAIL_LOCAL  = 'retail_local';
	const RESALE        = 'resale';
}

/**
 * @} End of "defgroup merchant"
 */

/**
 * @defgroup statistic
 *
 * Default statistic type
 */
class StatisticType {
	const BEST_REPLY    = 'best_reply';
	const COMMENT_COUNT = 'comment_count';
	const VISIT         = 'visit';
	const LIKE          = 'like';
	const KARMA         = 'karma';
}
/**
 * @} End of "defgroup statistic"
 */

/**
 * @defgroup achievement
 *
 * Default achievement type
 */
class AchievementType{
	const HIGHEST_KARMA   = 'highest_karma';
	const MOST_COMMENT    = 'most_comment';
	const MOST_BEST_REPLY = 'most_best_reply';
}
/**
 * @{ End of "defgroup achievement"
 */

/**
 * @defgroup file
 *
 * Default file type
 */
class FileType{
	const PROFILE_IMAGE = 'profile_image';
	const SYLLABUS      = 'syllabus';
}
/**
 * @{ End of "defgroup file"
 */
