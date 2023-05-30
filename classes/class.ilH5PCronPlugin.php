<?php

declare(strict_types=1);

use srag\Plugins\H5P\IContainer;

/**
 * @author       Thibeau Fuhrer <thibeau@sr.solutions>
 * @noinspection AutoloadingIssuesInspection
 */
class ilH5PCronPlugin extends ilCronHookPlugin
{
    public const PLUGIN_ID = "h5pcron";

    /**
     * @var IContainer
     */
    protected $h5p_container;

    /**
     * @throws LogicException if the main plugin is not installed.
     */
    public function __construct()
    {
        parent::__construct();

        if (!class_exists('ilH5PPlugin')) {
            throw new LogicException("You cannot use this plugin without installing the main plugin first.");
        }

        $this->h5p_container = ilH5PPlugin::getInstance()->getContainer();
    }

    /**
     * @inheritDoc
     */
    public function getCronJobInstances(): array
    {
        if (!$this->isActive()) {
            return [];
        }

        return [
            $this->getCronJobInstance(ilH5PDeleteOldTmpFilesJob::CRON_JOB_ID),
            $this->getCronJobInstance(ilH5PDeleteOldEventsJob::CRON_JOB_ID),
            $this->getCronJobInstance(ilH5PRefreshLibrariesJob::CRON_JOB_ID),
        ];
    }

    /**
     * @param string $a_job_id
     * @inheritDoc
     */
    public function getCronJobInstance($a_job_id): ?ilCronJob
    {
        if (!$this->isActive()) {
            return null;
        }

        switch ($a_job_id) {
            case ilH5PDeleteOldTmpFilesJob::CRON_JOB_ID:
                return new ilH5PDeleteOldTmpFilesJob(
                    $this->h5p_container->getTranslator(),
                    $this->h5p_container->getRepositoryFactory()->file()
                );
            case ilH5PDeleteOldEventsJob::CRON_JOB_ID:
                return new ilH5PDeleteOldEventsJob(
                    $this->h5p_container->getTranslator(),
                    $this->h5p_container->getRepositoryFactory()->event()
                );
            case ilH5PRefreshLibrariesJob::CRON_JOB_ID:
                return new ilH5PRefreshLibrariesJob(
                    $this->h5p_container->getTranslator(),
                    $this->h5p_container->getKernel()
                );

            default:
                return null;
        }
    }

    /**
     * @inheritDoc
     */
    public function getPluginName(): string
    {
        return "H5PCron";
    }

    public function getId(): string
    {
        return self::PLUGIN_ID;
    }
}
