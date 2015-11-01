<?php

$app->get('/', 'app.default_controller:indexAction');
$app->post('/', 'app.default_controller:indexPostAction');
$app->get('/link/{hash}', 'app.default_controller:viewLinkAction');
$app->get('/pw/{hash}', 'app.default_controller:viewPasswordAction');
$app->post('/delete', 'app.default_controller:deleteAction');

// Api
$app->put('/api', 'app.default_controller:apiPutAction');
$app->get('/api/{hash}', 'app.default_controller:apiViewAction');
$app->delete('/api/{hash}', 'app.default_controller:apiDeleteAction');
