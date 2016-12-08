<?php

$messages = $app['controllers_factory'];

$messages->get('/', function () {
    return 'messages';
});

return $messages;
