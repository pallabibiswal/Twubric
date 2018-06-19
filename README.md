# Twrubric
- need php5,git and composer if not present then please install
	composer : https://getcomposer.org/doc/00-intro.md#manual-installation
	git : https://git-scm.com/book/en/v2/Getting-Started-Installing-Git
- check for git and composer installation.
	- go to the terminal
	- git --version
	- composer -v
- create twitter devlopper account.
	-Go to ount https://apps.twitter.com/ and login then click on create new app to create an application.
  	-Add mandatory fields and also add url to Callback URLs as http://localhost:8000/app/followers
	-copy your consumer key and consumer secret key and generate access token that will be needed during installation of application.

- go to the terminal
- git clone https://github.com/pallabibiswal/Twubric.git
- go to the clonned folder and run below command
    - composer install
- while installing you will be asked to provide some informations out of which you must provide informations for these:
	# consumer_key:
	# consumer_secret:
	# access_key:
	# access_secret:
	# oauth_callback:
- These informations are needed for Authentication process.
- now check your application
	- php bin\console
- if all okay then run below commands
    - bin/console cache:clear OR php bin/console cache:clear
- php bin/console cache:clear --env=prod --no-debug
- Symfony will create a server ,default port is 8000
- run application in browser as : localhost:8000
