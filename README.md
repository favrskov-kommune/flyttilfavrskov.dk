# How to setup
## Requirements
* Docker
* docker-compose
* an .env file

## Spinning the setup

We need to create a docker image now. One will be created automatically by the docker-compose, but for good practices it's better to just create one ourselves. Go into the folder, at the level where Dockerfile and docker-compose is and type
```docker build --build-arg=env=dev --target http -t flyttilfavrskov:dev .``` or
```docker build --build-arg=env=staging --target https -t flyttilfavrskov:staging . ``` or
```docker build --build-arg=env=prod --target https -t flyttilfavrskov:prod .```

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
```docker-compose exec <APP> bash``` - replace <APP> with the name of the actual application or service (mysql, app, etc..)

If you want to have a simlink/mount of your files into the container, uncomment the lines under app: that are under `volume`

#Deployment

We now need to tag the created image with the container registry name. (In order to tell docker where to push the image)
Run a ```docker images``` in order to see the imageId of the newly created.
After you see the hash, we need to ```docker tag XXXXXX favrskov.azurecr.io/flyttilfavrskov:[dev/staging/prod]``` (In the brackets is the types of environments variables. choose one)
After the tagging is completed, just ```docker push favrskov.azurecr.io/flyttilfavrskov:[dev/staging/prod]``` (In case it doesn't work, do ```docker login favrskov.azurecr.io``` with the credentials on 1Pass - Registry Access Keys)

Next step is fetching the ip an ssh into the machine (1Pass). You should then cd into the grundsalg folder, and run ```docker pull favrskov.azurecr.io/flyttilfavrskov:[dev/staging/prod]```. After some time, it will completed and we just need to ```docker-compose up -d```
