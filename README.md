# cgrd-test-task

## Start project locally

For this project you nedd to have installed and configured docker on your maschine.

First open the terminal and make sure that you are in root folder of the project.
After making sure, in command line type:

```
docker-compose -d server
```

To stop all cotainer type:

```
docker-compose down
```

For working application you will need to import `database-dump.sql` file into running in container database.
To do that you have 2 options:

1. Enter database container and import database from command line
2. Install mysql client and setup connection for running mysql container

#### Describe above options

1. To import container without installing database client, you should follow these steps:

- Check running containers by typing in CMD:

```
docker ps
```

If this is the only application which you are runninig then you should see 3 running containers.
It should look something like that:

| CONTAINER ID | IMAGE                 | COMMAND                | CREATED    | STATUS      | PORTS                             | NAMES                   |
| ------------ | --------------------- | ---------------------- | ---------- | ----------- | --------------------------------- | ----------------------- |
| 817e52fb3e3a | cgrd-test-task-server | "/docker-entrypoint.…" | 2 days ago | Up 11 hours | 0.0.0.0:8000->80/tcp              | cgrd-test-task-server-1 |
| 0344ab00dac3 | cgrd-test-task-php    | "docker-php-entrypoi…" | 2 days ago | Up 11 hours | 9000/tcp                          | cgrd-test-task-php-1    |
| 12637364e45a | mysql                 | "docker-entrypoint.s…" | 2 days ago | Up 11 hours | 0.0.0.0:3306->3306/tcp, 33060 tcp | cgrd-test-task-mysql-1  |

So in this case you should get last row name and go inside this container. To do that, in CMD type:

```
docker exec -it cgrd-test-task-mysql-1 /bin/sh
```

This command will take you inside the container and `-it` flags will give you ability to run commands inside of it.

So now run this command:

```
mysql -u homestead -p homestead < /home/database-dump.sql
```

Command line will ask for mysql massword of homstead user. Type `secret`.

By the way all the mysql credentials are located in `./env/mysql.env` file. So if you will change anything you have to change the commands regarding to your changes.

Thats it! Tables shold be created and you are ready to use website.

2. To setup database via client you should follow steps of dedicated client. So describing it depends on the clien of your choice. Here are required credentials for connecting to the database service:

```
HOST=localhost
DATABASE=homestead
PORT=3306
USERNAME=homestead
PASSWORD=secret
```

After connecting to the server import `sqldump/database-dump.sql` file into `homstead` database and you are ready to use website.

Website will be available on [http://localhost:8000](http://localhost:8000)
