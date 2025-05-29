
The following instructions assume that you have already set up a Raspberry Pi with [@peterkvt80](https://github.com/peterkvt80)'s [VBIT2](https://github.com/peterkvt80/vbit2) software (following the instructions [here](https://github.com/peterkvt80/vbit2/wiki#installing-vbit2)) in the default location in your user home directory.

It's also assumed that the Raspberry Pi you're using for teletext, whilst it may have internet access, is on your own private network and not exposed/visible to the outside world. The steps below will set up a basic web server which has the ability to run shell commands on your Pi. Use at your own risk and/or consider a more secure option if, for some reason, your teletext-generating Pi is mission-critical and/or visible to the public internet.

Open a terminal, and run the VBIT2 Config:
```
vbit-config
```
Go to options, and disable 'automatically update selected service' and 'run VBIT2 automatically at boot'.


Install Apache2 and PHP:
```
sudo apt-get install apache2
sudo apt-get install php
```

Copy the teletext recovery switcher code to the Apache2 document root:
```
cd /var/www/html
git clone https://github.com/JamieNemeth/teletext-recovery-switcher.git
```

Open the sudoers file:
```
sudo su
visudo /etc/sudoers
```

Append the following line to the sudoers file to enable the www-data user to run shell commands (from PHP):
```
www-data ALL=(ALL) NOPASSWD:ALL
```

Save and exit by pressing Ctrl-X, then 'Y', then Enter.

Exit sudo su mode:
```
exit
```

Add the www-data user to the video group so that it can control teletext generation:
```
sudo usermod -a -G video www-data
```

Reboot your Raspberry Pi to allow the above setting to be applied.

Navigate to your Raspberry Pi's IP address in the web browser. Enter the username that you use to log in to the Pi and/or run the teletext software, and the root folder where your teletext recoveries (in TTI format) are stored. Click the 'save' button to store these values (a 'data.json' file will be created in /var/www/html).

**Each recovery should be in its own subfolder beneath the root folder,** i.e. the structure should be \<root folder\>/\<recovery name\>/\<TTI files go here\>.

Once the correct username and root recoveries folder have been saved, you should see a list of available recoveries. Click any 'run service' button to switch the output of VBIT2 to that service.
