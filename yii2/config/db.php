<?php

return [
    'class' => 'yii\db\Connection',
    'dsn' => 'pgsql:host=postgres_container_yii2;dbname=pizzario',
    'username' => 'pizzario',
    'password' => 'pizzario',
    'charset' => 'utf8',

    // Schema cache options (for production environment)
    //'enableSchemaCache' => true,
    //'schemaCacheDuration' => 60,
    //'schemaCache' => 'cache',
];
