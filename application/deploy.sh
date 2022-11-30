#!/bin/bash

# Deploy dev 
# deploy dev environment application

# ---------------------------------- 
# install php and symfony dependencies
composer install

# install node modules and build styles and js
npm install
npm run dev

# Create the database
php bin/console doctrine:database:create --if-not-exists
php bin/console doctrine:migrations:migrate

# Insert dev data
php bin/console hautelook:fixtures:load