
The following instructions assume that you have already set up a Raspberry Pi with [@peterkvt80](https://github.com/peterkvt80)'s [VBIT2](https://github.com/peterkvt80/vbit2) software (following the instructions [here](https://github.com/peterkvt80/vbit2/wiki#installing-vbit2)) in the default location in your user home directory.

It's also assumed that the Raspberry Pi you're using for teletext, whilst it may have internet access, is on your own private network and not exposed/visible to the outside world. The steps below will set up a basic web server which has the ability to run shell commands on your Pi. Use at your own risk and/or consider a more secure option if, for some reason, your teletext-generating Pi is mission-critical and/or visible to the public internet.

#### Run the VBIT2 Config
In the terminal, enter:
```
vbit-config

```
- Stop VBIT2 if it's already running
- Go to options, and disable 'automatically update selected service' and 'run VBIT2 automatically at boot'


#### Install Apache2, PHP, and ACL
In the terminal, enter:
```
sudo apt-get install apache2 php acl -y

```

#### Remove the original web document root
In the terminal, enter:
```
sudo rm -rf /var/www/html

```

#### Set permissions on the web root
In the terminal, enter:
```
sudo chgrp -R www-data /var/www
sudo chmod -R 2775 /var/www

```
to change the group ownership of the folder to www-data, and allow www-data to read/write/execute the PHP scripts.

Then enter:
```
sudo usermod -a -G www-data <your username>
```
to add yourself to the www-data group.

Then enter:
```
sudo setfacl -d -m group:www-data:rwx /var/www
```
to ensure new files and folders inherit the permissions.

Then enter:
```
newgrp www-data

```
(this just allows you to continue with the following steps, without having to log out and back in again.)

#### Clone the teletext service switcher code to the Apache2 document root
In the terminal, enter:
```
git clone https://github.com/JamieNemeth/teletext-service-switcher.git /var/www/html

```
to remove the original document root, and replace it with the teletext switcher code.

#### Enable the www-data user to run shell commands (from PHP)
In the terminal, enter:
```
sudo su
visudo /etc/sudoers

```
to open the sudoers file.

Append the following line to the end of the sudoers file you opened above:
```
www-data ALL=(ALL) NOPASSWD:ALL
```

- Save and exit by pressing Ctrl-X, then 'Y', then Enter

#### Exit sudo su mode
In the terminal, enter:
```
exit

```

#### Add the www-data user to the video group so that it can control teletext generation
In the terminal, enter:
```
sudo usermod -a -G video www-data

```

#### Reboot your Raspberry Pi to allow the above setting to be applied
In the terminal, enter:
```
sudo reboot now

```

#### Add username and folder settings to the service switcher

Navigate to your Raspberry Pi's IP address in the web browser. Click on the 'settings' tab, and enter the username that you use to log in to the Pi and/or run the teletext software, and the root folder where your local teletext files (in TTI format) are stored. Click the 'save' button to store these values (a 'data.json' file will be created in /var/www/html).

**Each set of local teletext files should be in its own subfolder beneath the root folder,** i.e. the structure should be:
```
- <root folder>
    └ <local service name>
        └ TTI files go here
```

Once the correct username and root folder have been saved, you should see a list of available installed and local services in their respective tabs. Click any 'run service' button (in either tab) to switch the output of VBIT2 to that service. You do not have to click on 'stop output' first.

![image](https://github.com/user-attachments/assets/85be8817-c260-4503-8ec5-e93cac49e4d9)

![image](https://github.com/user-attachments/assets/6f4aba1d-3f57-4dab-ae3c-6ce27367fd14)

![image](https://github.com/user-attachments/assets/6be0a7d9-d350-4759-82ae-e0812885548f)




