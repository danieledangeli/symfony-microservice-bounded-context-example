install: install_vendors cache_warm ci_phpunit
quality: phpcs phpmd
tests: tests_unit tests_functional tests_behat

install_vendors:
	curl -sS https://getcomposer.org/installer | php
	php composer.phar install --no-interaction

# Run by CI server
ci_phpunit: install_vendors tests_unit

# Run by CI server
ci_phpfunctional: install_vendors tests_functional tests_behat

tests_unit:
	./bin/phpunit --exclude-group=functional

tests_functional:
	./bin/phpunit --group=functional

tests_behat:
	./bin/behat