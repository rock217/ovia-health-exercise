test: composer
	phpunit --configuration phpunit.xml

composer:
	composer install
