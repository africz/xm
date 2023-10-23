# Xm Building a Real-time Stock Price Aggregator #

## prerequisites ##

- Docker 
preferable Docker Desktop this is provide compatibility on all major systems 
Windows, Mac, Linux 
Application is developed on Linux, but ready to switch to Mac M2


## How to start ##

Clone the project from github to any folder.
git@github.com:africz/xm.git

### You will get the following folder structure ###

- docker  - all docker files 
- root    - xm task source code 
- sql     - database backup
- postman - postman backup 
- tmp     - used by xdebug for profiling 
- .vscode - Configured VSCode to debug

Go to docker folder 

- scripts - helper shell scripts
- traefik - load balancer 
allow to use names, https protocoll instead of localhost:8080 etc only.
- ubuntu 
you can use many operating system configurations etc and run with the same
volumes. This is very handy in work, everybody in the team can improve docker
configuration without conflict in the Git repository. Easy to develop a new idea
and try it out and preserve the current working configuration. 
Here you can make many as you want like Debian, work with Apache or Nginx or Php7 or PHP8 etc
None of them will pollute the project repository or make it to unsafe due latest changes may brake 
the system. Reason why I work on this way, at my previous companies was often problem 
latest changes in docker brake the system and give hard time to developers.
- .env 

PLATFORM=amd64 # amd64 for Linux | arm64  or arm64v8| for M2, M1
set here the host operating system of your computer

CONFIG=ubuntu
docker configuration that you willing to use

DOCKER_STACK_SSL=true
set here to use https or http 

TIMEZONE=Europe/Madrid
Your time zone

PROJECT_NAME=xm
This name will be used in container and images names to seperate
the project parts well. Due volumes are shared with all configuration
these name are not reflected there

PROJECT_PATH=/projects/xm

Project root folder

VERSION=1.0.0

### Build ###

make build

It will build all images

apache
mysql
redis
redis-commander

### Run ###

make up
This will run the system

Url is https://xm.localhost or http:// depend from your choice in docker/.env

Mount any containers

make mount/apache
make mount/mysql 
etc

run redis commander 

in your browser open http://redis-commander.localhost (this is not on https)


### How to use Xm project ###

edit root/.env firs in case of needs or just review the default settings


#mysql
DB_CONNECTION=mysql
DB_HOST=xm-ubuntu-mysql
DB_PORT=3306
DB_DATABASE=xm
DB_USERNAME=xm
DB_PASSWORD=xm

#redis
REDIS_HOST=xm-ubuntu-redis
REDIS_PORT=6379

SYMBOLS_USA=IBM,ORCL,AXP,SEDG,ENPH,MARA,SLB,ARM,LIPO,RIG


ALPHA_VANTAGE_API_MODE=LOCALTEST #LOCALTEST or something else

!!! If this parameter is LOCALTEST it will use emulation process 
files from tests/Data/IntraDay not connect to real server. 
put something else here RUN, SERVER etc and it will connect to
alpha vantage server !!!


ALPHA_VANTAGE_API_MARKET=USA #Market name
ALPHA_VANTAGE_API_INTERVAL=5min #1min,5min 
ALPHA_VANTAGE_API_KEY=SC1P82M9R2IDSILX
ALPHA_VANTAGE_API_URL=https://www.alphavantage.co/query?function=%s&symbol=%s&interval=%s&outputsize=%s&apikey=%s
ALPHA_VANTAGE_API_TEST_URL=http://xm.localhost/api/test/symbols?function=%s&symbol=%s
ALPHA_VANTAGE_API_TEST_DATA=tests/Data #emulate server response locally
ALPHA_VANTAGE_API_TRY_TO_RECONNECT=3 #3x try to reconnect
ALPHA_VANTAGE_API_RECONNECT_INTERVAL=20 #try to reconnect in ever 20 seconds
ALPHA_VANTAGE_API_REDIS_KEY=AVAPI_
ALPHA_VANTAGE_API_REDIS_CACHE_EXPIRE=600 #key expired after 600 sec
ALPHA_VANTAGE_API_WAITING=12 #sec waiting beetwen each calls to not reach the free version rate limit 5 calls per minute

REPORTS_RATE_LIMIT=5 # 5 calls per minute

...

Prepare the application data

in docker folder issue make artisan/fresh this will create all tables and indexes.
You can use it for reset the data any time.

Retrieve the stock data is root/app/Console/Commands/GetStockData.php

This is scheduled in Laravel scheduler to execute periodically in root/app/Console/Kernel.php
 
 $schedule->command('app:get-stock-data')->everyMinute()
          ->weekdays()
          ->timezone('America/New York')
          ->between('4:00', '20:00'); //extended trading time
 
To run manually issue in docker folder make artisan/stock


