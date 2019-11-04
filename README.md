# How to setup
## Requirements
* Docker
* docker-compose
* an .env file

## Spinning the setup

We need to create a docker image now. One will be created automatically by the docker-compose, but for good practices it's better to just create one ourselves. Go into the folder, at the level where Dockerfile and docker-compose is and type
```docker build -t flyttilfavrskov_app:latest . ```

After this, all we need to do is create an ```.env``` file containing the is described below

##### ENVIRONMENT VARIABLES

You will need to create an .env file next to the "docker-compose.yaml" file.
Inside it, you will need to add the content on the left of the table, with a format as:
DATABASE_HOST=xxx
DATABASE_NAME=xxx
...


| Variable | Description |
| ------ | ------ |
| DATABASE_HOST | The address of the database (with docker-compose it will be under the service name - in this case 'database') |
| DATABASE_NAME | The schema name |
| DATABASE_USER | User of the database |
| DATABASE_PASSWORD | Password of the user |
| DATABASE_ROOT_PASSWORD | Root password |
| DATABASE_PORT | The port it's running under (if it's with docker-compose it can be reached by the default port 3306) |
| ENV | dev,prod etc... |
| DOMAIN | This value will be dinamically fetched by Settings.php and Sites.php. It's the only place where you need to change it, no need to create settings.local, etc.. |

## Last step
Now we just need to tell docker-compose to spin the image created.
```docker-compose up -d```

#TIPS
If you need to go inside the container, go at the level of docker-compose file and write
```docker-compose exec <APP> bash``` - replace <APP> with the name of the actual application or service (mysql, favrskov, etc..)

If you want to have a simlink/mount of your files into the container, uncomment the lines under app: that are under `volume`
