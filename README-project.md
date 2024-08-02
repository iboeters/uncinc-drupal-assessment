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
[preflight] Commandfile search paths: <deprecated>/UncInc-drupal-assessment-2021/drupal/vendor/drush/drush/src
 [debug] Starting bootstrap to site [0.07 sec, 8.15 MB]
 [debug] Drush bootstrap phase 2 [0.07 sec, 8.15 MB]
 [debug] Try to validate bootstrap phase 2 [0.07 sec, 8.15 MB]
 [debug] Try to validate bootstrap phase 2 [0.07 sec, 8.15 MB]
 [debug] Try to bootstrap at phase 2 [0.07 sec, 8.15 MB]
 [debug] Drush bootstrap phase: bootstrapDrupalRoot() [0.07 sec, 8.15 MB]
 [debug] Change working directory to <deprecated>/UncInc-drupal-assessment-2021/drupal/docroot [0.07 sec, 8.15 MB]
 [debug] Initialized Drupal 8.8.10 root directory at <deprecated>/UncInc-drupal-assessment-2021/drupal/docroot [0.08 sec, 8.35 MB]
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
- [x] First figure out what they mean by nodes
- [x] Figure out the location of nodes
- [x] Then apparently there are no nodes yet, so I have to create some nodes myself, of different content types (min. of 3 and one being a long text). I should also give them fields
- [x] Figure out how to give back all these nodes, independent of the content type of a node
- [x] Include pagination
- [ ] (optional type filter)
### Thoughts along the way
- This is my first time using Drupal so I find it harder to come up with an initial plan
- The endpoint is sitting in `docroot/modules/custom/nodes_rest/src/Plugin/rest/resource`. I'm not sure how to activate changes I make there -> `drush rs` does not seem to do it but `drush cr` does.
- There is a `/node/add` endpoint, but it says there are no content types yet-> first create content types at `/admin/structure/types/add`
- I added 3 different content types: `article`, `event` and `job_listing`. I also added a node for each of them and when I browse the database using `sqlitebrowser` I can indeed see them in the `node` table.
- 1 thing I did have to do run to fix some errors: `vendor/drush/drush/drush theme:enable stark`
- By searching online, I think I need to get the node IDs and then load these nodes and then probably loop over them to put them into the output of the endpoint
- To add pagination, I probably only want to give back part of the nodes, depending on the page requested. I will use the Pager class of Drupal and add a limit of 1 for now to see if pagination works, and I want to request only these nodes from the database.
- Pagination now works, I can now request different pages by asking for: `http://127.0.0.1:8000/nodes/list?page=2`, if none of the page parameter is not set in the request, it will default to page 1
- I also want to give the amount of pages and the total amount of pages back in the response so that links can be made to other pages.  
- A limit of 2 also seems to work
