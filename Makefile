PHP=/usr/local/php/bin/php
COMPOSER=$(PHP) composer.phar
LESS=lessc

update:
	$(COMPOSER) self-update
	$(COMPOSER) update

css:
	$(LESS) less/style.less > web/css/style.css
