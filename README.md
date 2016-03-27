# CI-Tools-Controller
Maintenance controller for Codeigniter3
## Migrations
Migration configuration. Open /application/config/migration.php and set:
```
$config['migration_enabled'] = TRUE;
$config['migration_type'] = 'timestamp';
```
Terminal commands:
```
!use php index.php

tools help                        Display the help menu
tools migration "file_name"       Create a migration file
tools migrate "version_number"    Run all migrations. The migration file number is optional.
tools reset                       Delete all migrations from database
```
