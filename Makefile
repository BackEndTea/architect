.PHONY: check
check: vendor
	bin/architect
	vendor/bin/phpcs
	vendor/bin/phpstan
	vendor/bin/phpunit

vendor:
	composer update