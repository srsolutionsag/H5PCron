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
 * Class DeleteOldEventsJob
 *
 * @package srag\Plugins\H5PCron\Job
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class DeleteOldEventsJob extends ilCronJob {

	use DICTrait;
	use H5PTrait;
	const CRON_JOB_ID = ilH5PCronPlugin::PLUGIN_ID . "_delete_old_events";
	const PLUGIN_CLASS_NAME = ilH5PCronPlugin::class;


	/**
	 * DeleteOldEventsJob constructor
	 */
	public function __construct() {

	}


	/**
	 * Get id
	 *
	 * @return string
	 */
	public function getId() {
		return self::CRON_JOB_ID;
	}


	/**
	 * @return string
	 */
	public function getTitle() {
		return ilH5PCronPlugin::PLUGIN_NAME . ": " . DICStatic::plugin(ilH5PPlugin::PLUGIN_CLASS_NAME)
				->translate("delete_old_events", Cron::CRON_LANG_MODULE);
	}


	/**
	 * @return string
	 */
	public function getDescription() {
		return DICStatic::plugin(ilH5PPlugin::PLUGIN_CLASS_NAME)->translate("delete_old_events_description", Cron::CRON_LANG_MODULE);
	}


	/**
	 * Is to be activated on "installation"
	 *
	 * @return boolean
	 */
	public function hasAutoActivation() {
		return true;
	}


	/**
	 * Can the schedule be configured?
	 *
	 * @return boolean
	 */
	public function hasFlexibleSchedule() {
		return true;
	}


	/**
	 * Get schedule type
	 *
	 * @return int
	 */
	public function getDefaultScheduleType() {
		return self::SCHEDULE_TYPE_DAILY;
	}


	/**
	 * Get schedule value
	 *
	 * @return int|array
	 */
	public function getDefaultScheduleValue() {
		return NULL;
	}


	/**
	 * Run job
	 *
	 * @return ilCronJobResult
	 */
	public function run() {
		$cron = new Cron();

		$result = $cron->deleteOldEvents();

		return $result;
	}
}
