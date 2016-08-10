# Codeigniter terminal tools
Maintenance controller for Codeigniter3

#### Migration configs
Open /application/config/migration.php and set:
```
$config['migration_enabled'] = TRUE;
$config['migration_type'] = 'timestamp';
```
#### Terminal commands
!Don't forget to use "php index.php" before commands below.
```
Migration commands:
tools migration "name"            Create new migration file.
tools migrate "version"           Run all migrations. The version number is optional.
tools reset "version"             Reset all migrations. The version number is optional.

File commands:
tools controller "name"           Create new controller.
tools model "name"                Create new model.
tools library "name"              Create new library.

--------------------------------------------------------------------------------------
Example:
php index.php tools help
php index.php tools controller Test
```
