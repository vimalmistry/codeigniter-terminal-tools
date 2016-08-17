# CodeIgniter Terminal Tools
Maintenance controller for CodeIgniter3 console

#### Migration configs
Open /application/config/migration.php and set:
```
$config['migration_enabled'] = TRUE;
$config['migration_type'] = 'timestamp';
```
#### Terminal commands
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
```
