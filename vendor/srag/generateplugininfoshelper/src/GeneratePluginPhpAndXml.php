<?php

namespace srag\GeneratePluginInfosHelper\H5PCron;

use Closure;
use Composer\Config;
use Composer\Script\Event;
use stdClass;

/**
 * Class GeneratePluginPhpAndXml
 *
 * @package srag\GeneratePluginInfosHelper\H5PCron
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 *
 * @internal
 */
final class GeneratePluginPhpAndXml
{

    const AUTOGENERATED_COMMENT = "Autogenerated from " . self::PLUGIN_COMPOSER_JSON . " - All changes will be overridden if generated again!";
    const LUCENE_OBJECT_DEFINITION_XML = "LuceneObjectDefinition.xml";
    const PLUGIN_COMPOSER_JSON = "composer.json";
    const PLUGIN_PHP = "plugin.php";
    const PLUGIN_README = "README.md";
    const PLUGIN_XML = "plugin.xml";
    /**
     * @var self|null
     */
    private static $instance = null;
    /**
     * @var string
     */
    private static $plugin_root = "";
    /**
     * @var Event
     */
    private $event;
    /**
     * @var stdClass
     */
    private $plugin_composer_json;


    /**
     * GeneratePluginPhpAndXml constructor
     *
     * @param Event $event
     */
    private function __construct(Event $event)
    {
        $this->event = $event;
    }


    /**
     * @param Event $event
     *
     * @internal
     */
    public static function generatePluginPhpAndXml(Event $event)/*: void*/
    {
        self::$plugin_root = rtrim(Closure::bind(function () : string {
            return $this->baseDir;
        }, $event->getComposer()->getConfig(), Config::class)(), "/");

        self::getInstance($event)->doGeneratePluginPhpAndXml();
    }


    /**
     * @param Event $event
     *
     * @return self
     */
    private static function getInstance(Event $event) : self
    {
        if (self::$instance === null) {
            self::$instance = new self($event);
        }

        return self::$instance;
    }


    /**
     *
     */
    private function doGeneratePluginPhpAndXml()/*: void*/
    {
        $this->plugin_composer_json = json_decode(file_get_contents(self::$plugin_root . "/" . self::PLUGIN_COMPOSER_JSON));

        $this->updateMissingVariablesComposerJson();

        $this->generatePluginPhp();

        $this->generatePluginXml();

        $this->generateLuceneObjectDefinitionXml();
    }


    /**
     *
     */
    private function generateLuceneObjectDefinitionXml()/* : void*/
    {
        if (!empty($this->plugin_composer_json->extra->ilias_plugin->lucene_search)) {
            echo "(Re)generate " . self::LUCENE_OBJECT_DEFINITION_XML . "
";

            file_put_contents(self::$plugin_root . "/" . self::LUCENE_OBJECT_DEFINITION_XML, '<?xml version="1.0" encoding="UTF-8"?>
<!-- ' . htmlspecialchars(self::AUTOGENERATED_COMMENT) . ' -->
<ObjectDefinition xmlns:xi="http://www.w3.org/2001/XInclude" type="' . htmlspecialchars(strval($this->plugin_composer_json->extra->ilias_plugin->id)) . '">
	<Document type="default">
		<xi:include href="../../../../../../../Services/Object/LuceneDataSource.xml" />
	</Document>
</ObjectDefinition>
');
        }
    }


    /**
     *
     */
    private function generatePluginPhp()/* : void*/
    {
        echo "(Re)generate " . self::PLUGIN_PHP . "
";

        $plugins_vars = [
            "id"                => strval($this->plugin_composer_json->extra->ilias_plugin->id),
            "version"           => strval($this->plugin_composer_json->version),
            "ilias_min_version" => strval($this->plugin_composer_json->extra->ilias_plugin->ilias_min_version),
            "ilias_max_version" => strval($this->plugin_composer_json->extra->ilias_plugin->ilias_max_version),
            "responsible"       => strval($this->plugin_composer_json->authors[0]->name),
            "responsible_mail"  => strval($this->plugin_composer_json->authors[0]->email)
        ];

        if (!empty($this->plugin_composer_json->extra->ilias_plugin->learning_progress)) {
            $plugins_vars["learning_progress"] = true;
        }

        if (!empty($this->plugin_composer_json->extra->ilias_plugin->supports_export)) {
            $plugins_vars["supports_export"] = true;
        }

        file_put_contents(self::$plugin_root . "/" . self::PLUGIN_PHP, '<?php
// ' . self::AUTOGENERATED_COMMENT . '

require_once __DIR__ . "/vendor/autoload.php";

' . implode('
', array_map(function (string $name, $value) : string {
                return '$' . $name . ' = ' . json_encode($value, JSON_UNESCAPED_SLASHES) . ';';
            }, array_keys($plugins_vars), $plugins_vars)) . '
');
    }


    /**
     *
     */
    private function generatePluginXml()/* : void*/
    {
        echo "(Re)generate " . self::PLUGIN_XML . "
";

        file_put_contents(self::$plugin_root . "/" . self::PLUGIN_XML, '<?php xml version = "1.0" encoding = "UTF-8"?>
<!-- ' . htmlspecialchars(self::AUTOGENERATED_COMMENT) . ' -->
<plugin id="' . htmlspecialchars(strval($this->plugin_composer_json->extra->ilias_plugin->id)) . '">
	' . (!empty($this->plugin_composer_json->extra->ilias_plugin->events) ? '<events>
		' . implode('
		', array_map(function (stdClass $event) : string {
                    return '<event id="' . htmlspecialchars($event->id) . '" type="' . htmlspecialchars($event->type) . '" />';
                }, (array) $this->plugin_composer_json->extra->ilias_plugin->events)) . '
	</events>' : '') . '
</plugin>
');
    }


    /**
     * @param string $variable
     *
     * @return string
     */
    private function getOldPluginVar(string $variable) : string
    {
        $plugin_php = file_get_contents(self::$plugin_root . "/" . self::PLUGIN_PHP);

        $text = [];

        preg_match('/\\$' . $variable . '\\s*=\\s*["\']?([^"\']+)["\']?\\s*;/', $plugin_php, $text);

        if (is_array($text) && count($text) > 1) {
            $text = $text[1];

            if (is_string($text) && !empty($text)) {
                return $text;
            }
        }

        return "";
    }


    /**
     *
     */
    private function updateMissingVariablesComposerJson()/* : void*/
    {
        $updated_composer_json = false;

        $old_version = $this->getOldPluginVar("version");
        if (empty($this->plugin_composer_json->version) || (!empty($old_version) && version_compare($old_version, $this->plugin_composer_json->version, ">"))) {
            echo "Update missing or older " . self::PLUGIN_COMPOSER_JSON . " > version (" . ($this->plugin_composer_json->version ?? null) . ") from " . self::PLUGIN_PHP . " > version ("
                . $old_version . ")
";

            $this->plugin_composer_json->version = $old_version;

            $updated_composer_json = true;
        }

        if (empty($this->plugin_composer_json->extra)) {
            $this->plugin_composer_json->extra = (object) [];

            $updated_composer_json = true;
        }

        if (isset($this->plugin_composer_json->ilias_plugin)) {
            echo "Migrate " . self::PLUGIN_COMPOSER_JSON . " > ilias_plugin to " . self::PLUGIN_COMPOSER_JSON . " > extra > ilias_plugin
";

            $this->plugin_composer_json->extra->ilias_plugin = $this->plugin_composer_json->ilias_plugin;

            unset($this->plugin_composer_json->ilias_plugin);

            $updated_composer_json = true;
        }

        if (empty($this->plugin_composer_json->extra->ilias_plugin)) {
            $this->plugin_composer_json->extra->ilias_plugin = (object) [];

            $updated_composer_json = true;
        }

        $id = $this->getOldPluginVar("id");
        if (empty($this->plugin_composer_json->extra->ilias_plugin->id)) {
            echo "Update missing " . self::PLUGIN_COMPOSER_JSON . " > ilias_plugin > id (" . ($this->plugin_composer_json->extra->ilias_plugin->id ?? null) . ") from " . self::PLUGIN_PHP . " > id ("
                . $id . ")
";

            $this->plugin_composer_json->extra->ilias_plugin->id = $id;

            $updated_composer_json = true;
        }

        $name = basename(self::$plugin_root);
        if (empty($this->plugin_composer_json->extra->ilias_plugin->name)) {
            echo "Update missing " . self::PLUGIN_COMPOSER_JSON . " > ilias_plugin > name (" . ($this->plugin_composer_json->extra->ilias_plugin->name ?? null) . ") from current folder ("
                . $name . ")
";

            $this->plugin_composer_json->extra->ilias_plugin->name = $name;

            $updated_composer_json = true;
        }

        $old_ilias_min_version = $this->getOldPluginVar("ilias_min_version");
        if (empty($this->plugin_composer_json->extra->ilias_plugin->ilias_min_version)
            || (!empty($old_ilias_min_version)
                && version_compare($old_ilias_min_version, $this->plugin_composer_json->extra->ilias_plugin->ilias_min_version, ">"))
        ) {
            echo "Update missing or older " . self::PLUGIN_COMPOSER_JSON . " > ilias_plugin > ilias_min_version (" . ($this->plugin_composer_json->extra->ilias_plugin->ilias_min_version ?? null)
                . ") from " . self::PLUGIN_PHP . " > ilias_min_version ("
                . $old_ilias_min_version . ")
";

            $this->plugin_composer_json->extra->ilias_plugin->ilias_min_version = $old_ilias_min_version;

            $updated_composer_json = true;
        }

        $old_ilias_max_version = $this->getOldPluginVar("ilias_max_version");
        if (empty($this->plugin_composer_json->extra->ilias_plugin->ilias_max_version)
            || (!empty($old_ilias_max_version)
                && version_compare($old_ilias_max_version, $this->plugin_composer_json->extra->ilias_plugin->ilias_max_version, ">"))
        ) {
            echo "Update missing or older " . self::PLUGIN_COMPOSER_JSON . " > ilias_plugin > ilias_max_version (" . ($this->plugin_composer_json->extra->ilias_plugin->ilias_max_version ?? null)
                . ") from " . self::PLUGIN_PHP . " > ilias_max_version ("
                . $old_ilias_max_version . ")
";

            $this->plugin_composer_json->extra->ilias_plugin->ilias_max_version = $old_ilias_max_version;

            $updated_composer_json = true;
        }

        if (empty($this->plugin_composer_json->extra->ilias_plugin->slot)) {
            $plugin_class = "classes/class.il" . $this->plugin_composer_json->extra->ilias_plugin->name . "Plugin.php";

            $plugin_class_code = file_get_contents(self::$plugin_root . "/" . $plugin_class);

            $matches = [];
            preg_match("/Plugin\s+extends\s+il([A-Za-z]+)Plugin/", $plugin_class_code, $matches);
            $hook = $matches[1];

            $matches = [];
            $readme = file_get_contents(self::$plugin_root . "/" . self::PLUGIN_README);
            preg_match("/Customizing\/global\/plugins\/([A-Za-z]+)\/([A-Za-z]+)\/" . $hook . "/", $readme, $matches);

            $component = implode("/", [
                $matches[1],
                $matches[2]
            ]);

            $slot = implode("/", [
                $component,
                $hook
            ]);

            echo "Update missing " . self::PLUGIN_COMPOSER_JSON . " > ilias_plugin > slot (" . ($this->plugin_composer_json->extra->ilias_plugin->slot ?? null) . ") from " . $plugin_class . " ("
                . $hook
                . ") and "
                . self::PLUGIN_README . " ("
                . $component . ")
";

            $this->plugin_composer_json->extra->ilias_plugin->slot = $slot;

            $updated_composer_json = true;
        }

        if (empty($this->plugin_composer_json->extra->ilias_plugin->learning_progress)) {
            $learning_progress = $this->getOldPluginVar("learning_progress");

            if ($learning_progress === "true") {
                echo "Update missing " . self::PLUGIN_COMPOSER_JSON . " > ilias_plugin > learning_progress (" . ($this->plugin_composer_json->extra->ilias_plugin->learning_progress ?? null)
                    . ") from "
                    . self::PLUGIN_PHP . " > learning_progress (" . $learning_progress . ")
";

                $this->plugin_composer_json->extra->ilias_plugin->learning_progress = true;

                $updated_composer_json = true;
            }
        }

        if (empty($this->plugin_composer_json->extra->ilias_plugin->lucene_search)) {
            $lucene_search = json_encode(file_exists(self::$plugin_root . "/" . self::LUCENE_OBJECT_DEFINITION_XML));

            if ($lucene_search === "true") {
                echo "Update missing " . self::PLUGIN_COMPOSER_JSON . " > ilias_plugin > lucene_search (" . ($this->plugin_composer_json->extra->ilias_plugin->lucene_search ?? null) . ") from "
                    . self::LUCENE_OBJECT_DEFINITION_XML . " (" . $lucene_search . ")
";

                $this->plugin_composer_json->extra->ilias_plugin->lucene_search = true;

                $updated_composer_json = true;
            }
        }

        if (empty($this->plugin_composer_json->extra->ilias_plugin->supports_export)) {
            $supports_export = $this->getOldPluginVar("supports_export");

            if ($supports_export === "true") {
                echo "Update missing " . self::PLUGIN_COMPOSER_JSON . " > ilias_plugin > supports_export (" . ($this->plugin_composer_json->extra->ilias_plugin->supports_export ?? null) . ") from "
                    . self::PLUGIN_PHP . " > supports_export (" . $supports_export . ")
";

                $this->plugin_composer_json->extra->ilias_plugin->supports_export = true;

                $updated_composer_json = true;
            }
        }

        if (empty($this->plugin_composer_json->authors)) {
            $responsible = $this->getOldPluginVar("responsible");
            $responsible_mail = $this->getOldPluginVar("responsible_mail");

            echo "Update missing " . self::PLUGIN_COMPOSER_JSON . " > authors (" . ($this->plugin_composer_json->authors ?? null) . ") from " . self::PLUGIN_PHP . " > responsible (" . $responsible
                . ") and " . self::PLUGIN_PHP . " > responsible_mail ("
                . $responsible_mail . ")
";

            $this->plugin_composer_json->authors = [
                (object) [
                    "name"     => $responsible,
                    "email"    => $responsible_mail,
                    "homepage" => "",
                    "role"     => "Developer"
                ]
            ];

            $updated_composer_json = true;
        }

        if (empty($this->plugin_composer_json->extra->ilias_plugin->events)) {
            if (file_exists(self::$plugin_root . "/" . self::PLUGIN_XML)) {
                $plugin_xml = json_decode(json_encode(simpleXML_load_file(self::$plugin_root . "/" . self::PLUGIN_XML)));

                if (!empty($plugin_xml->events) && !empty($plugin_xml->events->event)) {
                    echo "Update missing " . self::PLUGIN_COMPOSER_JSON . " > ilias_plugin > events (" . ($this->plugin_composer_json->extra->ilias_plugin->events ?? null) . ") from "
                        . self::PLUGIN_XML . " > events
";

                    $this->plugin_composer_json->extra->ilias_plugin->events = array_map(function (stdClass $event) : stdClass {
                        return (object) [
                            "id"   => $event->{"@attributes"}->id,
                            "type" => $event->{"@attributes"}->type
                        ];
                    }, $plugin_xml->events->event);

                    $updated_composer_json = true;
                }
            }
        }

        if ($updated_composer_json) {
            echo "Store updated changes in " . self::PLUGIN_COMPOSER_JSON . "
";

            file_put_contents(self::$plugin_root . "/" . self::PLUGIN_COMPOSER_JSON, preg_replace_callback("/\n( +)/", function (array $matches) : string {
                    return "
" . str_repeat(" ", (strlen($matches[1]) / 2));
                }, json_encode($this->plugin_composer_json, JSON_UNESCAPED_SLASHES + JSON_PRETTY_PRINT)) . "
");
        }
    }
}
