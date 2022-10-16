install:
	composer install
	php bin/console doc:data:create --if-not-exists
	php bin/console doc:mig:mig --no-interaction
	php bin/console hau:fix:load --no-interaction