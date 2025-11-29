# dash.snort
Modular web dashboard for analysing Snort IDS alerts.

## Features

- Reads Snort alerts from a MySQL snort database.
- Simple web UI to list recent alerts and show a basic signature distribution graph.
- PHPUnit test suite for DB and reporting logic.
- GitHub Actions CI workflow and optional local pre-commit checks.

## Requirements

- PHP ≥ 7.4 with mysqli, pdo_mysql, gd
- Web server (Apache or compatible)
- MySQL / MariaDB
- Composer
- Snort IDS (installed separately and configured to log to MySQL)

## Usage
### Install PHP dependencies
`composer install`

### Database Schema (Replace as required)
`mysql -u <DB_ROOT_USER> -p < scripts/db_init.sql` <br> 
`mysql -u <DB_ROOT_USER> -p snort < db/snort_schema.sql`<br>

### App Configuration
`define('DBHOST', getenv('DB_HOST') ?: 'localhost');` <br>
`define('DBUSER', getenv('DB_USER') ?: '<SNORT_DB_USER>');` <br>
`define('DBPASS', getenv('DB_PASS') ?: '<SNORT_DB_PASSWORD>');` <br>
`define('DBNAME', getenv('DB_NAME') ?: 'snort');` <br>

### Web Server Configuration
`sudo systemctl restart apache2`

## References 
- [Snort](https://www.snort.org/) - Open source network intrusion detection system
- [ACID](https://sourceforge.net/projects/acid/) - Original Analysis Console for Intrusion Databases
- [PHPUnit](https://phpunit.de/) - PHP testing framework





