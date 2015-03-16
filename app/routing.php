<?php

$app->get('/', 'app.default_controller:indexAction');
$app->post('/', 'app.default_controller:indexAction');
$app->get('/link/{hash}', 'app.default_controller:viewLinkAction');
$app->get('/pw/{hash}', 'app.default_controller:viewPasswordAction');
$app->post('/delete', 'app.default_controller:deleteAction');
