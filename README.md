#Introduction

This is a simple boilerplate for a CodeIgniter private messaging system. It comes with the following functionality:

  - Send messages to multiple users
  - Reply, delete, restore messages
  - Browse messages by status: deleted, unread, not deleted, sent, ...
  - AJAX ready function for auto-completing recipient names
  - ORM like base classes to convert MySQL types to PHP types
  - Sample views to demonstrate usage
  - Database structure and sample content

It is written according to the CI coding guides, but it does not support database prefixes.

#Installation

Grab a fresh CodeIgniter installation and connect it to a MySQL database. Download all ci_pm files and extract them to your "application" CI folder. Be sure to overwrite the "constants.php" file! As next step open the "db.sql" file in the "application" folder and execute its contents in a MySQL db. Delete the file afterwards. Now you should be able to reach the module via ".../index.php/pm".

#Usage

To test the system surf to ".../index.php/pm" on your server. To test the auto-completing of recipient names enter only "Foo" to the recipient field and click "send".

To use the private messaging system with your own application you will want to extend the User_model with your own user authentication system. Therefore you have to replace the "current_id" method in User_model with your own method returning the id of the currently logged in user. Pm_model uses "current_id" to get the user id of the current user.

As next step you will want to replace the views and implement e.g. AJAX calls to auto-complete recipient names or show more of the backend messages to the user. Also you might want to delete the sample contents from the database and implement your own routing. 

#Documentation

A full documentation can be found [here](http://www.morvai.de/ci_pm/).
