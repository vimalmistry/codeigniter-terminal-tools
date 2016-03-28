# CI-Tools-Controller
Maintenance controller for Codeigniter3

Migration configs
-----------------
Open /application/config/migration.php and set:
```
$config['migration_enabled'] = TRUE;
$config['migration_type'] = 'timestamp';
```
Terminal commands
-----------------
```
CI migration commands:
php index.php tools migration "name"            Create new migration file.
php index.php tools migrate "version"           Run all migrations. The version number is optional.
php index.php tools reset                       Reset all migrations.

CI file commands:      
php index.php tools controller "name"           Create new controller.
php index.php tools model "name"                Create new model.
php index.php tools library "name"              Create new library.
php index.php tools "type" "file" "name" -rm    Delete file. Use "-rm" key to delete your created file.
```
