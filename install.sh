printf "Installing project ....\n"

printf "Installing dependencies\n"
composer install

printf "Creating database\n"
./bin/console doctrine:database:create -n --if-not-exists

printf "Creating schema\n"
./bin/console doctrine:schema:create -n

printf "Creating fixtures\n"
./bin/console hautelook:fixtures:load -n


printf "Instalation complete \o/"
