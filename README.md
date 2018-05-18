# Connexion

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## Installation

1. Install laravel using Composer (eg: to create a project named connexion: `laravel new methodist`)
2. Change to the project folder created and fix permissions on bootstrap and storage folders: 
```
sudo chmod -R 777 storage
sudo chmod -R 777 bootstrap
```
3. Check the Laravel installation is running properly before proceeding. 
4. Add the methodist package to composer.json (also, note the minimum-stability setting is just until tymon/jwt-auth reaches v 1.0.0 - at the moment it is a RC:
```
"require": {
   ...
   "bishopm/methodist": "dev-master"
},
"minimum-stability": "RC",
```
5. Run *composer update* in the project folder, which will pull in the package and its dependencies
6. Add your database credentials to .env
7. Add Bishopm\Churchnet\Providers\MethodistServiceProvider::class at the bottom of the list of providers in config/app.php (We're not using Laravel's package auto-discovery at the moment because it creates problems with some of the package routes)