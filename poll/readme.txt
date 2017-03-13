How to install:

Open up connect.php and admin/connect.php, and insert your sql username, password, and the selected databasename
Upload all files
run install.php and install the SQL tables and then DELETE install.php
run admin/register.php and register yourself a admin username and then DELETE register and reguser
Thats it!
Poll.php is the actual page of the poll.
To log in to admin, to do admin/index.php


Other
-----
To put the poll inside a web template, copy and paste the code from the index.php file. For
this to work the admin and image files of the poll have to be the same relative path to the page the poll is on
as it originally was to poll.php.


You may use this poll and Modify it in any way as long as you do not take the link to chipmunk scripts 
off of the "see results" page.

Upgrade from pervious to 1.3
-----------------------------
Just upload the admin folder again except register and reguser and you are upgraded.

1.3 or before to encrypted admin password
-----------------------------------------
1. Go into phpmyadmin find the table P_admin and changed the password field from length 15 to length 255
2. Empty P_admin table
3. Now upload register and reguser again and register yourself an admin name(don't worry, you poll stats are still there)
4. upload admin/authenticate.php again
5. Delete admin/register.php and admin/reguser.php

