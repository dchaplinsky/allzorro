# allzorro

```
composer install

export DATABASE_HOST=localhost
export DATABASE_PASSWORD=
export DATABASE_USER=allzorro
export DATABASE_NAME=allzorro

vendor/bin/doctrine orm:schema-tool:update --force --dump-sql

php sync.php -h
```

