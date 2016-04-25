Kyjoukan
========

Tournament planner and scheduler

This web application will take as input :
- An event
- A list of teams that will participate in the event
- A list of available grounds

Then you will create the first phase of the game, with two possible rules :
- Round-robin : each team plays against each other team in the same pool
- Brackets : each team plays another one; each winner play another winner, and so on...

Finally, you will create pools (team groups), and assign teams into them.

During the tournament, you will enter results.

Planned features
----------------

Once given the teams, grounds and pools, the application will compute a printable schedule, that you can fine tune if necessary. You can publish the schedule on the web.
If will also provide you with printable match sheets.
As soon as you enter the results, you will get a printable hall of fame, available on the web.

Once the first phase is over, you can create another one (and so on...).

Server Installation
-------------------

If you want to install and run Kyjoukan on your own server :

* Install Symfony 2.8
 Download the bundle :
```bash
$ php composer.phar require "abienvenu/kyjoukan":"dev-master"
```
* Add the bundle in your AppKernel.php, and also load StofDoctrineExtensionsBundle :
```php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new Abienvenu\KyjoukanBundle\KyjoukanBundle(),
        new Stof\DoctrineExtensionsBundle\StofDoctrineExtensionsBundle(),
    );
}
```
* Include the route from your app/config/routing.yml :
```YAML
kyjoukan:
    resource: "@KyjoukanBundle/Resources/config/routing.yml"
    prefix: /kyjoukan
```
* Include the config from your app/config/config.yml :
```YAML
    imports:
	- { resource: "@KyjoukanBundle/Resources/config/config.yml" }
```
* Also activate sluggable in your app/config/config.yml :
```YAML
stof_doctrine_extensions:
    orm:
        default:
            sluggable: true
```
