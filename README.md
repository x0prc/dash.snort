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


