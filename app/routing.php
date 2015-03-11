<?php

$app->get('/', 'app.default_controller:indexAction');
$app->post('/submit', 'app.default_controller:submitAction');
$app->get('/pw/{hash}', 'app.default_controller:viewAction');
