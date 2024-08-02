# Unc Inc assessment - Drupal

### Setting up the webserver
- Setting up the Drupal webserver took quite some time. First of all installing the old versions of software, f.e. to be able to access and download older php versions and do `sudo apt-get install php-7.2`. After that I got a lot of errors that php packages were missing, like `php7.2-xml`. After installing 4 of these I was able to continue and finally run composer install without errors. However, that got me in this problem running `vendor/drush/drush/drush rs 8000`: 
```
[debug] Drush bootstrap phase: bootstrapDrupalSite() [0.06 sec, 8.63 MB]
 [debug] Initialized Drupal site default at sites/default [0.06 sec, 8.87 MB]
 [debug] Try to validate bootstrap phase 5 [0.06 sec, 8.87 MB]
 [debug] Try to bootstrap at phase 5 [0.06 sec, 8.87 MB]
 [debug] Drush bootstrap phase: bootstrapDrupalConfiguration() [0.06 sec, 8.87 MB]
 [debug] Add service modifier [0.06 sec, 9.06 MB]
 [debug] Try to validate bootstrap phase 5 [0.06 sec, 9.06 MB]
 [debug] Unable to connect to database. More information may be available by running `drush status`. This may occur when Drush is trying to bootstrap a site that has not been installed or does not have a configured database. In this case you can select another site with a working database setup by specifying the URI to use with the --uri parameter on the command line. See `drush topic docs-aliases` for details. [0.07 sec, 9.25 MB]
 [debug] Could not bootstrap at phase 5 [0.07 sec, 9.23 MB]

In BootstrapHook.php line 32:
                                                                      
  [Exception]                                                         
  Bootstrap failed. Run your command with -vvv for more information.
```
- The bootstrap failed as it was unable to connect to the database.
- After being lost in possible fixes online for quite some time, running `vendor/drush/drush/drush cr` (cache rebuild), finally got me to this error, saying that the driver could not be found:

```
[preflight] Commandfile search paths: /home/gent/Downloads/UncInc-drupal-assessment-2021/drupal/vendor/drush/drush/src
 [debug] Starting bootstrap to site [0.07 sec, 8.15 MB]
 [debug] Drush bootstrap phase 2 [0.07 sec, 8.15 MB]
 [debug] Try to validate bootstrap phase 2 [0.07 sec, 8.15 MB]
 [debug] Try to validate bootstrap phase 2 [0.07 sec, 8.15 MB]
 [debug] Try to bootstrap at phase 2 [0.07 sec, 8.15 MB]
 [debug] Drush bootstrap phase: bootstrapDrupalRoot() [0.07 sec, 8.15 MB]
 [debug] Change working directory to /home/gent/Downloads/UncInc-drupal-assessment-2021/drupal/docroot [0.07 sec, 8.15 MB]
 [debug] Initialized Drupal 8.8.10 root directory at /home/gent/Downloads/UncInc-drupal-assessment-2021/drupal/docroot [0.08 sec, 8.35 MB]
 [debug] Try to validate bootstrap phase 2 [0.08 sec, 8.34 MB]
 [debug] Try to bootstrap at phase 2 [0.08 sec, 8.63 MB]
 [debug] Drush bootstrap phase: bootstrapDrupalSite() [0.08 sec, 8.63 MB]
 [debug] Initialized Drupal site default at sites/default [0.08 sec, 8.87 MB]

In Connection.php line 116:
                         
  [PDOException]         
  could not find driver
```
- running `sudo apt-get install php-sqlite3` fixed everything

### The initial plan

#### 1. Project overview
The objective of the assignment:
- Edit the existing endpoint so it returns nodes of all types including paginating (and optional type filter).
- Currently, this install contains no nodes and no content types. It is up to you to create them and give them fields (minimum of 3 with one being a long text)
#### 2. Steps
Now for the assignment, I have to implement the `/nodes/list` endpoint, so that it returns nodes of all types. Steps I will take to do this:
- [ ] First figure out what they mean by nodes
- [ ] Figure out the location of nodes
- [ ] Then apparently there are no nodes yet, so I have to create some nodes myself, of different content types (min. of 3 and one being a long text). I should also give them fields
- [ ] Figure out how to give back all these nodes, independent of the content type of a node
- [ ] Include pagination
- [ ] (optional type filter)
### Thoughts along the way
- 
