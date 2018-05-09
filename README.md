Kyjoukan
========
[![](https://images.microbadger.com/badges/image/abienvenu/kyjoukan.svg)](https://microbadger.com/images/abienvenu/kyjoukan "Docker image")
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/c04af8c2-5229-48b3-884a-2e7aa6b7c9d6/mini.png)](https://insight.sensiolabs.com/projects/c04af8c2-5229-48b3-884a-2e7aa6b7c9d6)

Tournament planner and scheduler

This web application will take as input :
- An event details (name, date...)
- A list of teams that will participate in the said event
- A list of available grounds where to play

Then the user will create the first phase of the game, with two possible rules :
- Round-robin : each team plays against each other team in the same pool
- Brackets : each team plays another one; each winner play another winner, and so on...

Features
--------

Once given the teams, grounds and number of pools (group of teams), the application will:
- Automatically dispatch the teams into pools
- Let the user fine tune manually this dispatch
- Provide a printable schedule
- Provide printable game sheets ready to be filled
- Enable the user to type in results
- Provide printable rankings

Once the first phase is over, the user can create another one (and so on...).

Planned features
----------------
- Ability to add games manually
- Ability to remove games manually
- Ability to order teams
- Dispatch by order

Server Installation
-------------------

* Install docker
* Download and run the application :
```bash
$ docker run -d --name kyjoukan -p 8043:80 -e APP_ENV=prod abienvenu/kyjoukan
```
* Point your browser to http://localhost:8043/

CHANGELOG
---------
* v0.6 : Upgrade to Symfony 3.4, spreading of teams across grounds
* v0.5 : Improved warning display + shuffling bugfixes
* v0.4 : Added the "Cumulative Ranking" rule
* v0.3 : Do not call remote CDN, so that Kyjoukan can work without Internet access
* v0.2 : Dockerization
* v0.1 : First working version
