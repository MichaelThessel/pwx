<?php

$app->get('/', 'app.default_controller:indexAction');
$app->post('/submit', 'app.default_controller:submitAction');
