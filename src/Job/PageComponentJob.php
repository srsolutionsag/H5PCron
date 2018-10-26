<?php

namespace srag\Plugins\H5PCron\Job;

use ilCronJob;
use ilCronJobResult;
use ilH5PCronPlugin;
use ilH5PPlugin;
use srag\DIC\DICStatic;
use srag\DIC\DICTrait;
use srag\Plugins\H5P\Cron\Cron;
use srag\Plugins\H5P\Utils\H5PTrait;

/**
 * Class PageComponentJob
 *
 * @package    srag\Plugins\H5PCron\Job
 *
 * @author     studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 *
 * @deprecated since ILIAS 5.3
 */
class PageComponentJob extends ilCronJob {

	use DICTrait;
	use H5PTrait;
	/**
	 * @var string
	 *
	 * @deprecated since ILIAS 5.3
	 */
	const CRON_JOB_ID = ilH5PCronPlugin::PLUGIN_ID . "_page_component";
	/**
	 * @var string
	 *
	 * @deprecated since ILIAS 5.3
	 */
	const PLUGIN_CLASS_NAME = ilH5PCronPlugin::class;


	/**
	 * PageComponentJob constructor
	 *
	 * @deprecated since ILIAS 5.3
	 */
	public function __construct() {

	}


	/**
	 * Get id
	 *
	 * @return string
	 *
	 * @deprecated since ILIAS 5.3
	 */
	public function getId() {
		return self::CRON_JOB_ID;
	}


	/**
	 * @return string
	 *
	 * @deprecated since ILIAS 5.3
	 */
	public function getTitle() {
		return ilH5PCronPlugin::PLUGIN_NAME . ": " . DICStatic::plugin(ilH5PPlugin::PLUGIN_CLASS_NAME)
				->translate("page_component", Cron::CRON_LANG_MODULE);
	}


	/**
	 * @return string
	 *
	 * @deprecated since ILIAS 5.3
	 */
	public function getDescription() {
		return DICStatic::plugin(ilH5PPlugin::PLUGIN_CLASS_NAME)->translate("page_component_description", Cron::CRON_LANG_MODULE) . "<br><br>"
			. DICStatic::plugin(ilH5PPlugin::PLUGIN_CLASS_NAME)->translate("page_component_description_deprecated", Cron::CRON_LANG_MODULE);
	}


	/**
	 * Is to be activated on "installation"
	 *
	 * @return boolean
	 *
	 * @deprecated since ILIAS 5.3
	 */
	public function hasAutoActivation() {
		return (!self::version()->is53());
	}


	/**
	 * Can the schedule be configured?
	 *
	 * @return boolean
	 *
	 * @deprecated since ILIAS 5.3
	 */
	public function hasFlexibleSchedule() {
		return true;
	}


	/**
	 * Get schedule type
	 *
	 * @return int
	 *
	 * @deprecated since ILIAS 5.3
	 */
	public function getDefaultScheduleType() {
		return self::SCHEDULE_TYPE_DAILY;
	}


	/**
	 * Get schedule value
	 *
	 * @return int|array
	 *
	 * @deprecated since ILIAS 5.3
	 */
	public function getDefaultScheduleValue() {
		return NULL;
	}


	/**
	 * Run job
	 *
	 * @return ilCronJobResult
	 *
	 * @deprecated since ILIAS 5.3
	 */
	public function run() {
		$cron = new Cron();

		$result = $cron->pageComponentCron();

		return $result;
	}
}
