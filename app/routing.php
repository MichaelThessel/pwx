<?php

$app->get('/', 'app.default_controller:indexAction');
$app->post('/', 'app.default_controller:indexAction');
$app->get('/pw/{hash}', 'app.default_controller:viewAction');
