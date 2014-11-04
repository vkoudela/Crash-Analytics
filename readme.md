Crash Analytics
=========

Crash Analytics is PHP based system that:

  - Collects crash reports from Android devices
  - Analyizes Java's stack trace
  - Groups reports per stack trace, OS, OS version, device brand name, and etc.


Features
----
   - Its fast (unless you have billion records in database, then MySQL will become slow)
   - It detects similar stack traces and counts them as one
   - Its precalculating data in background, every hour
   - Live feed - monitor reports and preview stack trace in the moment when you get the report
   - E-mail alerts - you can make e-mail trigger that will send you an e-mail when system receives the crash report - it also has the feature that it won't make 1000 mails if you receive 1000 reports in the same minute
   - Browse reports per stack trace, device model, package name, OS version, country, ISP or simply use the search
   - It supports creating user accounts


Requirements for webserver
----
   - Web server
      - if you're using Apache, then make sure you have mod_rewrite enabled
      - if you're using Nginx, make sure you've defined URL rewrite in your configuration
   - PHP 5.3+
   - MySQL 5+


Requirements for your Android app
----
   - get [Crash Analytics Wrapper](https://github.com/zdrascic/library-crash-analytics-wrapper) (demo application can be found here: https://github.com/zdrascic/CrashAnalytics_TestApplication)
   - setup end URL for reporting; the URL is `http://your-virtual-host/crash/add` or `http://your-virtual-host/submit.php` - both will work


Installation
----
  - deploy this project on your server
  - point your virtual host to **public** folder
  - make sure you have **cache** and **log** folders in your **storage** folder; if you don't have storage folder, then go to your project root and run this command: `mkdir -p storage/{cache,log}`
  - make sure your webserver has rights to write in **storage** folder and all of its subfolders
  - import SQL file from **sql** folder to your MySQL server
  - in **application/configs**, configure **application.php** and **database.php**; if you want to enable e-mail alerts and reports, then you also need to configure **mail.php**.
  - setup cron job that will run every hour; command you need to execute is: `/usr/bin/php /path/to/your/public-folder/cli.php precalculate`


First run
----

After installation, go to your host with browser and login with admin@admin.com and password "admin".


Tech
----

Crash Analytics uses a number of open source projects to work properly:

* [Koldy PHP Framework](http://koldy.net) - simple and powerful PHP framework
* [PHPMailer](https://github.com/PHPMailer/PHPMailer) - great lib for sending e-mails from PHP
* [Twitter Bootstrap](http://getbootstrap.com/) - great UI boilerplate for modern web apps
* [HighCharts](http://www.highcharts.com/) - beautiful charts


Screen shots
----
* [Login](http://vlatko.koudela.org/work/crash-analytics/01-login.png)
* [Dashboard](http://vlatko.koudela.org/work/crash-analytics/02-dashboard.png)
* [Dashboard while data calculation is in progress](http://vlatko.koudela.org/work/crash-analytics/03-dashboard-while-calculating.png)
* [Live feed](http://vlatko.koudela.org/work/crash-analytics/04-live-feed.png)
* [Stack trace from live feed](http://vlatko.koudela.org/work/crash-analytics/05-stack-trace-from-live-feed.png)
* [Stack trace list](http://vlatko.koudela.org/work/crash-analytics/06-stack-trace-list.png)
* [Crash report details](http://vlatko.koudela.org/work/crash-analytics/07-crash-report-details.png)
* [List of packages](http://vlatko.koudela.org/work/crash-analytics/08-list-of-packages.png)
* [List of package versions](http://vlatko.koudela.org/work/crash-analytics/09-list-of-package-versions.png)
* [Search results](http://vlatko.koudela.org/work/crash-analytics/10-search-results.png)
* [Search results - preview stack trace](http://vlatko.koudela.org/work/crash-analytics/11-search-result-quick-stack-trace.png)
* [Email triggers](http://vlatko.koudela.org/work/crash-analytics/12-email-triggers.png)
* [User accounts](http://vlatko.koudela.org/work/crash-analytics/13-users.png)


License
----

MIT. You're using this software at your own risk.
