# Crawler App

### Overview
This is an app to demostrate web crawling and the display of collected stats from the pages crawled using a Reactjs 
client side app and a server side PHP Phalcon app. Bootstrap and Font Awesome are used to enhance styling.

### Install

#### Phalcon

Follow the instructions at the [Phalcon Website](https://phalconphp.com/en/download/linux).

and in the main directory 

```
phalcon serve
```

to serve locally

#### Reactjs

In the react-app directory, run

```
npm install
npm serve
```

To serve locally.

#### Production

After making changes to the react-app run

```
npm run build
```

and copy the 

/react-app/build/static
 
 directory to the 
 
/public/

directory

and

copy the content from

/react-app/build/index.html

to the 

/app/views/index.phtml

file

### Apache Setup

Create a virtualhost with (modifying the below code for you server and domain)

```
<VirtualHost *:80>

    ServerAdmin admin@example.host
    DocumentRoot "/var/vhosts/crawler/public"
    DirectoryIndex index.php
    ServerName example.host
    ServerAlias www.example.host

    <Directory "/var/vhosts/crawler/public">
        Options All
        AllowOverride All
        Allow from all
    </Directory>

</VirtualHost>

```

