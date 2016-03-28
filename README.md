# CI-Tools-Controller
Maintenance controller for Codeigniter3

Migration configuration. Open /application/config/migration.php and set:
```
$config['migration_enabled'] = TRUE;
$config['migration_type'] = 'timestamp';
```
Terminal commands:
```
php index.php tools help                        Display the help menu
php index.php tools migration "file_name"       Create a migration file
php index.php tools migrate "version_number"    Run all migrations. The migration file number is optional.
php index.php tools reset                       Delete all migrations from database
```
