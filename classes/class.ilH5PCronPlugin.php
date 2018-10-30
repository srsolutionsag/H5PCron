<?php

require_once __DIR__ . "/../../../../Repository/RepositoryObject/H5P/vendor/autoload.php";
require_once __DIR__ . "/../vendor/autoload.php";

use srag\Plugins\H5P\Job\DeleteOldEventsJob;
use srag\Plugins\H5P\Job\DeleteOldTmpFilesJob;
use srag\Plugins\H5P\Job\RefreshHubJob;
use srag\Plugins\H5P\Utils\H5PTrait;
use srag\Plugins\H5PPageComponent\Job\PageComponentJob;
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
	const PLUGIN_CLASS_NAME = ilH5PPlugin::class;
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
		$jobs = [ new RefreshHubJob(), new DeleteOldTmpFilesJob(), new DeleteOldEventsJob() ];

		try {
			include_once __DIR__ . "/../../../../COPage/PageComponent/H5PPageComponent/vendor/autoload.php";

			if (class_exists(PageComponentJob::class)) {
				array_push($jobs, new PageComponentJob());
			}
		} catch (Throwable $ex) {
		}

		return $jobs;
	}


	/**
	 * @param string $a_job_id
	 *
	 * @return ilCronJob|null
	 */
	public function getCronJobInstance($a_job_id) {
		try {
			include_once __DIR__ . "/../../../../COPage/PageComponent/H5PPageComponent/vendor/autoload.php";

			if (class_exists(PageComponentJob::class)) {
				if ($a_job_id === PageComponentJob::CRON_JOB_ID) {
					return new PageComponentJob();
				}
			}
		} catch (Throwable $ex) {
		}

		switch ($a_job_id) {
			case RefreshHubJob::CRON_JOB_ID:
				return new RefreshHubJob();

			case DeleteOldTmpFilesJob::CRON_JOB_ID:
				return new DeleteOldTmpFilesJob();

			case DeleteOldEventsJob::CRON_JOB_ID:
				return new DeleteOldEventsJob();

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
