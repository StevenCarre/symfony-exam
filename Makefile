install:
	composer install
	php bin/console doc:data:create --if-not-exists
	php bin/console doc:mig:mig
	php bin/console hau:fix:load