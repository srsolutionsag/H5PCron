# H5PCron ILIAS Plugin

Clean old data from H5P contents that is no longer required

This is an OpenSource project by studer + raimann ag, CH-Burgdorf (https://studer-raimann.ch)

This project is licensed under the GPL-3.0-only license

## Requirements

* ILIAS 6.0 - 7.999
* PHP >=7.0

## Installation

Start at your ILIAS root directory

```bash
mkdir -p Customizing/global/plugins/Services/Cron/CronHook
cd Customizing/global/plugins/Services/Cron/CronHook
git clone https://github.com/studer-raimann/H5PCron.git H5PCron
```

Update, activate and config the plugin in the ILIAS Plugin Administration

## Description

### Base plugin

First you need to install the [H5P](https://github.com/studer-raimann/H5P) plugin

### Cron jobs

With this plugin you can automatic delete old H5P contents data, that is no longer required, created in repository object or page component editor

You can also automatic refresh the hub list

The plugin consists of the following cron jobs:

- Delete old event logs
- Delete old temp files
- Refresh hub list

![Cron](./doc/images/cron.png)

## Adjustment suggestions

You can report bugs or suggestions at https://plugins.studer-raimann.ch/goto.php?target=uihk_srsu_PLH5P

## ILIAS Plugin SLA

We love and live the philosophy of Open Source Software! Most of our developments, which we develop on behalf of customers or on our own account, are publicly available free of charge to all interested parties at https://github.com/studer-raimann.

Do you use one of our plugins professionally? Secure the timely availability of this plugin for the upcoming ILIAS versions via SLA. Please inform yourself under https://studer-raimann.ch/produkte/ilias-plugins/plugin-sla.

Please note that we only guarantee support and release maintenance for institutions that sign a SLA.
