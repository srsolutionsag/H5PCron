<?php

declare(strict_types=1);

require_once __DIR__ . "/../vendor/autoload.php";

use srag\Plugins\H5P\IContainer;

/**
 * @author       Thibeau Fuhrer <thibeau@sr.solutions>
 * @noinspection AutoloadingIssuesInspection
 */
class ilH5PCronPlugin extends ilCronHookPlugin
{
    public const PLUGIN_NAME = "H5PCron";
    public const PLUGIN_ID = "h5pcron";

    protected const H5P_MAIN_AUTOLOAD = __DIR__ . "/../../../../Repository/RepositoryObject/H5P/vendor/autoload.php";

    /**
     * @var IContainer
     */
    protected $h5p_container;

    /**
     * @var ilCronManager
     */
    protected $cron_manager;
    /**
     * @throws LogicException if the main plugin is not installed.
     */
    public function __construct(
        \ilDBInterface $db,
        \ilComponentRepositoryWrite $component_repository,
        string $id
    ) {
        global $DIC;
        parent::__construct($db, $component_repository, $id);

        if (!file_exists(self::H5P_MAIN_AUTOLOAD)) {
            throw new LogicException("You cannot use this plugin without installing the main plugin first.");
        }

        require_once self::H5P_MAIN_AUTOLOAD;

        /** @var $component_factory ilComponentFactory */
        $component_factory = $DIC['component.factory'];
        /** @var $plugin ilH5PPlugin */
        $plugin = $component_factory->getPlugin(ilH5PPlugin::PLUGIN_ID);

        $this->h5p_container = $plugin->getContainer();
        $this->cron_manager = $DIC->cron()->manager();
    }

    /**
     * @inheritDoc
     */
    public function getCronJobInstances(): array
    {
        if (!$this->isMainPluginInstalled()) {
            $this->informUserAboutMissingCronJobs();
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
    public function getCronJobInstance(string $jobId): ilCronJob
    {
        if (!$this->isMainPluginInstalled()) {
            $this->informUserAboutMissingCronJobs();
        }

        switch ($jobId) {
            case ilH5PDeleteOldTmpFilesJob::CRON_JOB_ID:
                return new ilH5PDeleteOldTmpFilesJob(
                    $this->h5p_container->getTranslator(),
                    $this->h5p_container->getRepositoryFactory()->file(),
                    new ilStrictCliCronManager($this->cron_manager),
                );
            case ilH5PDeleteOldEventsJob::CRON_JOB_ID:
                return new ilH5PDeleteOldEventsJob(
                    $this->h5p_container->getTranslator(),
                    $this->h5p_container->getRepositoryFactory()->event(),
                    new ilStrictCliCronManager($this->cron_manager)
                );
            case ilH5PRefreshLibrariesJob::CRON_JOB_ID:
                return new ilH5PRefreshLibrariesJob(
                    $this->h5p_container->getTranslator(),
                    $this->h5p_container->getKernel()
                );

            default:
                throw new OutOfBoundsException("Unknown cron job ID: $jobId");
        }
    }

    private function informUserAboutMissingCronJobs(): void
    {
        global $DIC;

        $DIC->ui()->mainTemplate()->setOnScreenMessage(
            ilGlobalTemplateInterface::MESSAGE_TYPE_QUESTION,
            'You must install the H5P plugin before you can use the according cron jobs.'
        );
    }

    private function isMainPluginInstalled(): bool
    {
        return $this->h5p_container->getRepositoryFactory()->general()->isMainPluginInstalled();
    }
}
