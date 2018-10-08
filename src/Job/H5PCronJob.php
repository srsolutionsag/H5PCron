<?php

namespace srag\Plugins\H5PCron\Job;

use ilCronJob;
use ilCronJobResult;
use ilH5PCronPlugin;
use srag\DIC\DICTrait;
use srag\Plugins\H5P\Cron\H5PCron;
use srag\Plugins\H5P\Utils\H5PTrait;

/**
 * Class H5PCronJob
 *
 * @package srag\Plugins\H5PCron\Job
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class H5PCronJob extends ilCronJob {

	use DICTrait;
	use H5PTrait;
	const CRON_JOB_ID = ilH5PCronPlugin::PLUGIN_ID;
	const PLUGIN_CLASS_NAME = ilH5PCronPlugin::class;


	/**
	 * H5PCronJob constructor
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
		return ilH5PCronPlugin::PLUGIN_NAME;
	}


	/**
	 * @return string
	 */
	public function getDescription() {
		return "";
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
		$cron = new H5PCron();

		$result = $cron->run();

		return $result;
	}
}
