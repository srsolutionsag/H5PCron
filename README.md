# H5PCron ILIAS Plugin

Add H5P contents in repository objects

This is an OpenSource project by sr solutions ag, CH-Burgdorf (https://sr.solutions)

This project is licensed under the GPL-3.0-only license

## Requirements

* ILIAS 6.0 - 7.999
* PHP >=7.0

## Installation

Start at your ILIAS root directory

```bash
mkdir -p Customizing/global/plugins/Services/Cron/CronHook
cd Customizing/global/plugins/Services/Cron/CronHook
git clone https://github.com/srsolutionsag/H5PCron.git H5PCron
```

Update, activate and config the plugin in the ILIAS Plugin Administration

## Description

### Base plugin

First you need to install the [H5P](https://github.com/srsolutionsag/H5P) plugin

### Cron jobs

With this plugin you can automatic delete old H5P contents data, that is no longer required, created in repository object or page component editor

You can also automatic refresh the hub list

The plugin consists of the following cron jobs:

- Delete old event logs
- Delete old temp files
- Refresh hub list

![Cron](./doc/images/cron.png)

## Adjustment suggestions

You can report bugs or suggestions at https://plugins.sr.solutions/goto.php?target=uihk_srsu_PLH5P

# ILIAS Plugin SLA
We love and live the philosophy of Open Source Software! Most of our developments, which we develop on behalf of customers or in our own work, we make publicly available to all interested parties free of charge at https://github.com/srsolutionsag.

Do you use one of our plugins professionally? Secure the timely availability of this plugin also for future ILIAS versions by signing an SLA. Find out more about this at https://sr.solutions/plugins.

Please note that we only guarantee support and release maintenance for institutions that sign an SLA.
