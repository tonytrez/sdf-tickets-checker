# Stade de France Tickets Resale Checker

Simple php script which crawl an official tickets resale page, check if tickets are available and send an alert SMS (using [Twilio](https://www.twilio.com/fr/docs/sms)).

To use it :
- ``$ copy credentials.dist.php credentials.php``
- Edit the file with your personal credentials for Twilio and the link to the page of the performance ticket resale.
- Deploy in your server and cron the script onReSaleTicketsChecker.php
- That's it !

It will automatically send you a SMS when tickets are on sale.