# What ist this?
PhilleConnect is an all-new open-source next generation school network solution.

!!! BETA TESTING PHASE !!!
Not recommended for productive systems.
And the client software is not yet published, but will follow soon, so hang on...

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

## THIS SOFTWARE IS STILL IN BETA-STATE, EXPECT BUGS AND MISSING DOCUMENTATION !!!

If you are aware of this feel free to give it a try.
But expect quite a few changes in the next weeks and months, we hope to go stable by christmas 2017!

# How can I use this?
* Install a Linux-System to act as the Server, for examle Ubuntu 17.04 (this will work for the commands below, but works on others like debian as well if you "translate" the description below to your system)
* Type `sudo apt-get install docker docker-compose git` to the command line. Then you have your docker host.
* Type `git clone http://github.com/philleconnect/ServerContainers/` to get all you need to make your system build your server.
* Edit the `settings.env` in the `ServerContainers`-directory to your needs (WARNING: Don't change after going productive!)
* Change with `cd ServerContainers` to the build ddirectory and type `docker-compose up -d` to make your system build your server. Grab a coffe and enjoy watching your system working for you!
* When done, have a look at `http://localhost:84/setup/` with a browser on your system, just click next on the first two screens, for testing just skip the next one and give yourself sccess to the `http://localhost:84/ui/` on the last screen.
* have a look around on `http://localhost:84/setup/`, documentation for this will be here soon! (as I said: we are in beta-state - sorry and have fun expecting the best school-network-solution ever!)
* after a system reboot navigate to the ServerContainers-Folder and fire a `docker-compose start` te get it back up.

## Advanced options
(for those that need a shell to be happy)
* To stop or restart your server type `docker-compose stop` or `docker-compose start` from the ServerContainers-directory.
* A little harder is `docker-compose down` and `docker-compose up --build` - the web will gladly tell you the difference to the commands above.
* start a shell in a running container with `docker exec -ti <containerName> /bin/bash`, with `docker ps` you see which containers are running.
* Enjoy playing around, you won't break anything on your host system, because:
* To clean up your individualized stuff do a
```docker volume rm docker volume rm philleconnect_servercontainers_admin_config philleconnect_servercontainers_admin_mysql philleconnect_servercontainers_ldap_db```
(you can also just remove one of those if you like, enjoy playing around - you can't destroy anything!)
after a `docker-compose down` and you'll find a fresh install after the next `docker-compose up --build`
Maybe I got you fixed to docker by now, habe fun with it!
