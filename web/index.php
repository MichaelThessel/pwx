<?php

$app = require __DIR__ . '/../app/app.php';
if ($app instanceof Silex\Application) {
        $app->run();
} else {
        echo 'Failed to initialize application.';
}

