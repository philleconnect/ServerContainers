__Archived: Not actively maintained anymore!__

# What ist this?

PhilleConnect is an open-source school network solution.

## Philosophy

* as much open-souce-standards as possible
* Server-side based on docker containers, because "Server-as-code" is better than "Server-as-Virtual-Image" or even "Server-as-black-software"
* As modular as possible: You don't like a thing / need it different / want it to work with X / ... : Go ahead, make it work your way!

## Basic architecture

* LDAP-Server for authentication (supplied as docker-container), open to attach your authentications needs: LDAP is the standard that almost any software can talk, but it's no fun to get up, so we did the job for you.
* Server-as-code, based on Docker
* All basics, and only the basics, for your School Network: Monitor Control, local cloud storage, share- and template storage Remote Control, Internet Control and Screen lock.
* Integration with open-source Firewall Solution "IpFire"
* Thin client software with no proprietary needs like Windows domains
* Platform-independent

# Who can use this?

PhilleConnect is designed to be used in schools with multiple user groups. If your screnario might match this situation, just give it a try!

# How can I use this?

This section was outdated and will get updated.

# Development

## Requirements
- Node.js
- npm
- Python 3

## Frontend
All frontend code is located at `/ui`.

To setup your development environment, go tu `/ui` and run `npm install`. Then use one of the following commands.

### NPM Scripts

* 🔥 `start` - run development server
* 🔧 `build-prod` - build web app for production

Warning: The admin-frontend is still based on Framework7 version 5, while the self service already uses version 6.

## Backend
The backend code is located at `/pc_admin/api`.

## Releasing
You'll have to use the `createRelease.sh` script to create a release. It will compile the frontend, and package the archives.

<!---
* Install a Linux-System to act as the Server, for example Ubuntu 18.04 (this will work for the commands below, but works on others like debian as well if you "translate" the description below to your system)
* Type `sudo apt install docker docker-compose git` to the command line. Then you have your docker host.
* Type `git clone http://github.com/philleconnect/ServerContainers/` to get all you need to make your system build your server.
* Edit the `settings.env` in the `ServerContainers`-directory to your needs (WARNING: Don't change after going productive!)
* Change with `cd ServerContainers` to the build ddirectory and type `docker-compose up -d` to make your system build your server. Grab a coffe and enjoy watching your system working for you!
* When done, have a look at `http://localhost:84/setup/` with a browser on your system, just click next on the first two screens, for testing just skip the next one and give yourself sccess to the `http://localhost:84/ui/` on the last screen.
* have a look around on `http://localhost:84/setup/`, documentation for this will be here soon! (as I said: we are in beta-state - sorry and have fun expecting the best school-network-solution ever!)
* after a system reboot navigate to the ServerContainers-Folder and fire a `docker-compose start` te get it back up.

## Advanced options

(for those that need a shell to be happy)

* To stop or restart your server-environment type `docker-compose stop` or `docker-compose start` from the ServerContainers-directory.
* A little harder is `docker-compose down` and `docker-compose up --build` - this will delete the old containers and rebuild them. All persistent data will be kept, since they are stored in volumes mounted in the containers (for your backups: the data is stored in `/var/lib/docker/volumes`).
* start a shell in a running container with `docker exec -ti <containerName> /bin/bash`, with `docker ps` you see which containers are running.
* Enjoy playing around, you won't break anything on your host system, because:
* To clean up your individualized stuff do a
```docker volume rm docker volume rm philleconnect_servercontainers_admin_config philleconnect_servercontainers_admin_mysql philleconnect_servercontainers_ldap_db```
(you can also just remove one of those if you like, enjoy playing around - you can't destroy anything!)
after a `docker-compose down` and you'll find a fresh install after the next `docker-compose up --build`
Maybe I got you fixed to docker by now, have fun with it!
--->
