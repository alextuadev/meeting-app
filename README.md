### Into your folder run:
Copy the .env.example file into .env in the root directory and customize your variables and run:

``` 
docker run --rm \
-u "$(id -u):$(id -g)" \
-v $(pwd):/var/www/html \
-w /var/www/html \
laravelsail/php82-composer:latest \
composer install --ignore-platform-reqs 
```


Migrate database:
```
./vendor/bin/sail artisan migrate
```

Run seeders ( to populate  users in database ):
``` 
./vendor/bin/sail artisan db:seed
```


Url:
```
http://localhost/api/schedule-meeting
````

Body to sent to create a meeting:
```
{
    "meetingName": "Meeting 1",
    "startDateTime": "2023-09-29 21:00:00",
    "endDateTime": "2023-09-29 21:30:00",
    "userIDs": "1,2,3"
}
```
