<?php

$app->get('/', 'app.default_controller:indexAction');
$app->post('/', 'app.default_controller:indexPostAction');
$app->get('/link/{hash}', 'app.default_controller:viewLinkAction');
$app->get('/pw/{hash}', 'app.default_controller:viewPasswordAction');
$app->post('/delete', 'app.default_controller:deleteAction');

// API
$app->post('/api/', 'app.default_api_controller:apiPostAction');
$app->get('/api/{hash}', 'app.default_api_controller:apiGetAction');
$app->delete('/api/{hash}', 'app.default_api_controller:apiDeleteAction');
