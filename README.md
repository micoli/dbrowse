# dbrowse
enhanced directory index


* change index.php constructor call to customize paths and path map

* nginx configuration :
```
location ~ /browse {
	include fastcgi_params;
	fastcgi_pass unix:/var/run/php5-fpm.sock;
	fastcgi_index index.php;
	fastcgi_param SCRIPT_FILENAME /var/www/browse/index.php;
}
```
