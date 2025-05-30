
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
sudo setfacl -R -d -m group:www-data:rwx /var/www

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
to replace the original document root with the teletext switcher code.

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



## Set up a network file share (NFS) as your local service directory

If you want to go the whole hog, as I have, you can set up a network share as your "local" service root folder. I find this useful because I have one single source of in-progress teletext recoveries, that I can access and edit from any PC or laptop on the same network, and then display any in-progress recovery (after converting to TTI format) via any Raspberry Pi using the teletext switcher.

These instructions assume you have already followed all of the steps above, particularly the ones related to file permissions.

#### Install AutoFS

In the terminal, enter:
```
sudo apt-get install autofs

```

#### Create a NFS root folder in the web root
In the terminal, enter:
```
mkdir /var/www/nfs

```

#### Set up AutoFS with your NFS
In the terminal, enter:
```
sudo nano /etc/auto.master

```

Append this line to the end of the file:
```
/var/www/nfs    /etc/auto.nfs --timeout=60
```
ensuring there is a tab between 'nfs' and '/etc', and a space between 'auto.nfs' and '--timeout=60'.

- Press Ctrl-X, Y, then Enter to save.


Then, in the terminal, enter:
```
sudo nano /etc/auto.nfs

```

Append the line:
```
<folder name>    -fstype=nfs    <NFS server name>:/<NFS folder path>
```
where 'folder name' is the name of the folder that will appear inside /var/www/nfs. Ensure there are tabs between the three parts of the line.

- Press Ctrl-X, Y, then Enter to save.

For example: I want the folder name to be *wdmycloudmirror*, and my network share is at *wdmycloudmirror:/nfs/Public*, so my line in auto.nfs is:
```
wdmycloudmirror    -fstype=nfs    wdmycloudmirror:/nfs/Public
```

In the terminal, enter:
```
sudo systemctl start autofs
sudo systemctl enable autofs

```
to run AutoFS, and set it to automatically run on startup.

Note: if you look inside the */var/www/nfs* directory, you won't see the network share until it's loaded on demand (when you try to access it directly). For example, running *dir* or *ls* will return nothing. However, if you then run *cd \<folder name\>*, after a couple of seconds you will be able to browse your network share at */var/www/nfs/\<folder name\>*.

#### Add your local NFS path to the teletext switcher

Navigate to your Raspberry Pi's IP address in the web browser. Click on the 'settings' tab, and enter */var/www/nfs/\<folder name\>* in the 'local services root folder' input. Click to save these settings.

If you've followed the folder structure above, i.e.
```
- <root folder>
    └ <local service name>
        └ TTI files go here
```
then you should see a list of available services in the 'local services' tab.

For example, I've stored my local services in *wdmycloudmirror:/nfs/Public/Teletext/Restorations/tti-teletext-restorations*, which means that they appear locally on the Pi mapped to */var/www/nfs/wdmycloudmirror/Teletext/Restorations/tti-teletext-restorations*, so this is the path I've used in 'local services root folder'.
