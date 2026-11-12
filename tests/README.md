# Integration checks

These checks run against real WordPress APIs and a real database. They cover all four shortcodes, formatting and nested shortcodes, unsafe HTML, settings validation and legacy data, CSS registration and admin scope, and settings-page permissions. An independent sRGB luminance calculation also checks that every built-in text/background color pair has contrast of at least 4.5:1 for normal text.

Use a disposable WordPress installation with this plugin active, WP-CLI installed, and an administrator account:

```sh
MK_ALERT_TESTS=1 wp eval-file /path/to/simple-alert-box/tests/integration.php --use-include --path=/path/to/wordpress
```

The explicit environment variable prevents accidental runs. Tests temporarily change plugin options and create a subscriber; they restore the options and remove that user afterward. Do not use a production site.

GitHub Actions runs the same checks on the declared minimum (WordPress 6.0 / PHP 7.4) and the latest WordPress with PHP 8.3 and 8.5. A newly added workflow must actually run on GitHub before its results can be claimed.

## Local Docker environment

Run from the repository root with Docker Desktop available:

```sh
docker compose -p simple-alert-box-review -f tests/compose.yaml up -d wordpress
docker compose -p simple-alert-box-review -f tests/compose.yaml run --rm cli core install --url=http://localhost:8097 --title='Plugin tests' --admin_user=test-admin --admin_password=local-tests-only --admin_email=tests@example.com --skip-email
docker compose -p simple-alert-box-review -f tests/compose.yaml run --rm cli plugin activate simple-alert-box
docker compose -p simple-alert-box-review -f tests/compose.yaml run --rm -e MK_ALERT_TESTS=1 cli eval-file wp-content/plugins/simple-alert-box/tests/integration.php --use-include
```

The test site is bound to `127.0.0.1:8097`; MariaDB is bound to `127.0.0.1:33317`. These are disposable local credentials. Change the ports if they are already occupied. The repository is mounted read-only in WordPress; generated files and the database use dedicated Docker volumes.

To check PHP 8.5, set `MK_ALERT_PHP=8.5` in the environment before the last command (`$env:MK_ALERT_PHP = '8.5'` in PowerShell). The CLI container uses this version while the HTTP site stays on PHP 8.3.

After testing, remove only this project's containers and test data:

```sh
docker compose -p simple-alert-box-review -f tests/compose.yaml down --volumes
```
