# OpenCart File system monitoring \[FSMonitor\]
[![License: GPLv3](https://img.shields.io/badge/license-GPL%20V3-green?style=plastic)](LICENSE)

The extension keeps track of new, modified and deleted site files and specified server directories. Automatic scanning (cron) will automate checks and notify the administrator by email in case of any changes in the files.

Does not require third-party libraries. Does not create copies of files.

## Other Languages

* [Russian](README_RU.md)

## Change Log

* [CHANGELOG.md](docs/CHANGELOG.md)

## Screenshots

* [SCREENSHOTS.md](docs/SCREENSHOTS.md)

## Features

The extension allows:

* track new, changed, deleted site files;
* configure extensions of scanned files (by default - executable files of systems based on the Linux kernel);
* add additional readable server directories for monitoring;
* exclude directories and files from monitoring by mask;
* view the contents of files with syntax highlighting, without the possibility of editing;
* run an automatic scan using the cron scheduler (wget/curl/cli), followed by an Email/WhatsApp/Telegram notifications to the administrator if there are changes;
* it is possible to add a nice widget to the main page of the administrative panel to view the last scan, as well as to manually start a new one.

## Compatibility

* OpenCart 1.5, 2.x, 3.x, 4.x versions.

## Dependencies

* For version 1.5, vqmod is used to add a widget to the main page.
* For versions 2.0 to 2.2 inclusive, ocmod is used to add a widget to the home page.

## Demo

Admin

* [https://fs-monitor.shtt.blog/admin/](https://fs-monitor.shtt.blog/admin/) (auto login)

## Installation

* Install the module through the standard extension installation section.
* Install module "FSMonitor" in the modules section. 
* Go to the module page. This step will start the initial scan.
* To display the widget, go to "Modules > Control Panel", select FSMonitor. Specify a width of 12 and a sort order of 0 in the settings. This will display the full-width module at the very top.
* For versions prior to 2.3, the widget is rendered using vqmod / ocmod.

## Management

* To enable automatic scanning with cron, go to the module settings section and copy one of the three code options to start scanning (wget/curl/cli). Paste this code in the scheduler, in the hosting control panel and run a task to check if it works.
* The module does not heavily load the server, so checks can be carried out several times a day.
* You can rename a scan on the preview page by clicking on the name of the scan.

## License

* [GPL v3.0](LICENSE.MD)

## Thank You for Using My Extensions!

I have decided to make all my OpenCart extensions free and open-source to benefit the community. Developing, maintaining, and updating these extensions takes time and effort.

If my extensions have been helpful for your project and you’d like to support my work, any donation is greatly appreciated.

### 💙 You can support me via:

* [PayPal](https://paypal.me/TalgatShashakhmetov?country.x=US&locale.x=en_US)
* [CashApp](https://cash.app/$TalgatShashakhmetov)

Your support inspires me to keep improving and developing these tools. Thank you!
