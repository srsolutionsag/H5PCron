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
     * @throws LogicException if the main plugin is not installed.
     */
    public function __construct()
    {
        parent::__construct();

        if (!file_exists(self::H5P_MAIN_AUTOLOAD)) {
            throw new LogicException("You cannot use this plugin without installing the main plugin first.");
        }

        if (!$this->isMainPluginLoaded()) {
            require_once self::H5P_MAIN_AUTOLOAD;
        }

        $this->h5p_container = ilH5PPlugin::getInstance()->getContainer();
    }

    /**
     * @inheritDoc
     */
    public function getPluginName(): string
    {
        return self::PLUGIN_NAME;
    }

    public function getId(): string
    {
        return self::PLUGIN_ID;
    }

    /**
     * @inheritDoc
     */
    public function getCronJobInstances(): array
    {
        if (!$this->isMainPluginInstalled()) {
            ilUtil::sendQuestion('You must install the H5P plugin before you can use the according cron jobs.');
            return [];
        }

        return $this->h5p_container->getCronJobFactory()->getAll();
    }

    /**
     * @param string $a_job_id
     * @inheritDoc
     */
    public function getCronJobInstance($a_job_id): ?ilCronJob
    {
        if (!$this->isMainPluginInstalled()) {
            ilUtil::sendFailure('You must install the H5P plugin before you can use the according cron jobs.');
            return null;
        }

        return $this->h5p_container->getCronJobFactory()->getInstance((string) $a_job_id);
    }

    private function isMainPluginLoaded(): bool
    {
        return class_exists('ilH5PPlugin');
    }

    private function isMainPluginInstalled(): bool
    {
        return $this->h5p_container->getRepositoryFactory()->general()->isMainPluginInstalled();
    }
}
