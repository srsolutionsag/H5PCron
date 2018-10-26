<?php

require_once __DIR__ . "/../../../../Repository/RepositoryObject/H5P/vendor/autoload.php";
require_once __DIR__ . "/../vendor/autoload.php";

use srag\Plugins\H5P\Utils\H5PTrait;
use srag\Plugins\H5PCron\Job\DeleteOldEventsJob;
use srag\Plugins\H5PCron\Job\DeleteOldTmpFilesJob;
use srag\Plugins\H5PCron\Job\PageComponentJob;
use srag\Plugins\H5PCron\Job\RefreshHubJob;
use srag\RemovePluginDataConfirm\PluginUninstallTrait;

/**
 * Class ilH5PCronPlugin
 *
 * @author studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class ilH5PCronPlugin extends ilCronHookPlugin {

	use PluginUninstallTrait;
	use H5PTrait;
	const PLUGIN_ID = "h5pcron";
	const PLUGIN_NAME = "H5PCron";
	const PLUGIN_CLASS_NAME = self::class;
	const REMOVE_PLUGIN_DATA_CONFIRM = false;
	const REMOVE_PLUGIN_DATA_CONFIRM_CLASS_NAME = H5PRemoveDataConfirm::class;
	/**
	 * @var self|null
	 */
	protected static $instance = NULL;


	/**
	 * @return self
	 */
	public static function getInstance() {
		if (self::$instance === NULL) {
			self::$instance = new self();
		}

		return self::$instance;
	}


	/**
	 * ilH5PCronPlugin constructor
	 */
	public function __construct() {
		parent::__construct();
	}


	/**
	 * @return string
	 */
	public function getPluginName() {
		return self::PLUGIN_NAME;
	}


	/**
	 * @return ilCronJob[]
	 */
	public function getCronJobInstances() {
		return [ new RefreshHubJob(), new DeleteOldTmpFilesJob(), new DeleteOldEventsJob(), new PageComponentJob() ];
	}


	/**
	 * @param string $a_job_id
	 *
	 * @return ilCronJob|null
	 */
	public function getCronJobInstance($a_job_id) {
		switch ($a_job_id) {
			case RefreshHubJob::CRON_JOB_ID:
				return new RefreshHubJob();

			case DeleteOldTmpFilesJob::CRON_JOB_ID:
				return new DeleteOldTmpFilesJob();

			case DeleteOldEventsJob::CRON_JOB_ID:
				return new DeleteOldEventsJob();

			case PageComponentJob::CRON_JOB_ID:
				return new PageComponentJob();

			default:
				return NULL;
		}
	}


	/**
	 * @inheritdoc
	 */
	protected function deleteData()/*: void*/ {
		// Nothing to delete
	}
}
