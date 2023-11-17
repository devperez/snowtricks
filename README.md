
# Snowtricks [![Codacy Badge](https://app.codacy.com/project/badge/Grade/08d1895363874a17b29955f9dec6cd03)](https://app.codacy.com/gh/devperez/snowtricks/dashboard?utm_source=gh&utm_medium=referral&utm_content=&utm_campaign=Badge_grade)

This is project number 6 in my OpenClassRooms path to become a symfony developer.

## Environment Variables

To run this project, you will need to add the following environment variables to your .env file

`MAILER_DSN=smtp://mailhog:1025`

`JWT_SECRET='S3cr3tC0D3'`


## Installation

Although it wasn't a requirement, this project was developed using a docker environment.

1. First of all, clone the project.

```bash
git clone https://github.com/devperez/snowtricks.git
```
2. If Docker is not installed on your machine, get Docker [here](https://docs.docker.com/get-docker/).

3.  Then, run the docker image :

```bash
    docker compose -d
```
4. Once the project is running :
```bash
    npm run dev
```

5. Finally, run the fixtures :
```bash
    php bin/console doctrine:fixtures:load
```
Now the project should be running fine.
Here are some more pieces of relevant information:

You will need to access the mailhog interface to check your emails and this can be done on port 8025 :
http://localhost:8025

Phpmyadmin can be reached at this address :
http://localhost:8080/

You have to use these credentials to get in :

Utilisateur : snowtrick

Mot de passe : password

You can create new accounts but if you want to use one that already exists, you have to use 123456 as a password.

Enjoy!