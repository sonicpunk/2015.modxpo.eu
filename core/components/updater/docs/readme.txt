UPDATER - the universal update notifier

"keeps you up to date with your MODX version information"

The Updater package contains
- a mail notifier which sends notifications about core updates, package updates and installs and an information digest about your system on a regular base
- a dashboard widget which shows if there is an update for the MODX core available and if there are package updates available for download or install.
- a system broadcast messager to inform you at every manager page about core updates with a small red bar at the bottom of the page


==
Thanks to all attendees of the MODX CCC 2015 for their support and help. This package is one of the 
many outcomes of this hacking event and part of Team Updates' efforts to ease the update, monitoring, 
maintaining aspects of MODX. See https://github.com/modx-ccc-2015/whishlist for more details.                                      
==

Version:    0.2.4-beta
Date:       20150312
Authors:    Jens Külzer (inreti GmbH) <jens.kuelzer@inreti.de>
            
            ****
            ** *   inreti GmbH
            *#**   Die IT-Mediatoren
            ****
            
Forum:      http://forums.modx.com/thread/?thread=96613
Support:    modx-updater@inreti.de


How to use the notifier:
============================
By default no notification is active after installation of Updater. You have various options to activate the notifier of updater. Tweaking the system settings allow you to receive mails about
core updates, package updates or even just a digest mail with a summary of your installed components.
At the moment you can provide email-addresses there for SysAdmins and PackageAdmins. Digests are send to SysAdmins.
In the future you will have certain permissions for your "normal" users to manage reception of notifications.

E.g. to receive notifications about core updates:
- set 'updater.core_notifications_mail' to your desired mail address
- activate 'updater.send_core_notifications' to 'true' to activate it
- reload the page or browse to any of your sites pages (no matter which context) to trigger sending

If emails are send you will notice values in system settings under area 'Persistance'.

How to use the widget:
============================
Just install the package and add the Updater widget "Update status" to your dashboard. It will then show you information about the MODX core status and the installed packages.
Note: only sudo users or users with the permission "perform_maintenance_tasks" will be able to see the widget.

How to use the broadcast bar:
=============================
Activate system broadcast messages in the system settings with 'updater. You need either 'sudo' rights or to add the permission 'system_perform_maintenance_tasks' to your user to see anything.

Remember: this is a beta version - although it has been tested intensively, consider not to install it in production environments without testing it yourself.


Other System settings to use:
=============================

* updater.show_broadcast_messages = yes/[no]
Enable if you want to be warned on every manager page and not only by the dashboard. Warning: Deletion of the message bar does not work persistently at the moment. 
(this is experimental at the moment: you will not get rid of that message until you update to the latest version AND every manager user will see that message).

* cache_expires_core = [86400]
Cache expiration time in seconds. Per default only search once a day for new core updates. Updater uses its own cache partition, to clearing the cache in the manager has no effect on that. Please be aware that values less than one day aka 86400 seconds will not be accepted to safe github for massive api calls.

*github_timeout = [1500]
A timeout for looking up new version tags at github. You can adjust this according to your servers connection - keep as low as possible.
