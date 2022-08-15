## Project setup ##
In the main project directory please run:
```sh
$ ./build.sh
```
After that the project should be properly running on the given IP.

## Working on available endpoints ##
1. Run a POSTMAN (or similar) application.
2. Set request method to POST
3. Paste your page's IP following by `/oauth/token`
4. Fill the `body` tab with following key => value pairs:
```text
client_id => ec55c94f-b297-45ba-8de3-1ca6b0e2d07e
grant_type => password
username => tokenuser
password => 123
client_secret => 1234
```
5. Copy the `access_token` value.
6. Go to another POSTMAN tab.
7.  Paste your page's IP following by `/api/articles`
8. Set authorization as `Bearer <coppied_token>`
9. After sending the request you will receieve a list of articles in JSON format.
10. Access token is valid for 60s, after that you will need to generate a new token by `refresh_token` form the request you did on `/oauth/token`.
11. Change the data of `/oauth/token` request to:
```text
client_id => ec55c94f-b297-45ba-8de3-1ca6b0e2d07e
grant_type => refresh_token
client_secret => 1234
refresh_token => <the token you've coppied>
```

## Setup oauth keys (in case they would not work) ##
Run the following command to generate admin login link:
```sh
$ docker-compose run php-dev drush uli
```
Copy the part after `default` and paste it to your browser prefixed by the page's IP.
Example:
`http://192.168.32.3/user/reset/1/1660573228/RxKmcVZYKOIfEpbkW51ASKVz5Qh-ftGezF98MWpQJzc/login`
You should be logged in as an admin.

Please visit
`/admin/config/people/simple_oauth`
Then click `Generate keys` button.
Place `../oauth-keys/` path, click `Generate` and `Save configuration` after that.


## Helpful commands ##
Using drush:
```sh
$ docker-compose run php-dev drush [command name]
```
Database import:
```sh
$ docker-compose run php-dev drush sql-cli < database/database.sql
```
Restart containers:
```sh
$ docker-compose down
$ docker-compose up -d
```

