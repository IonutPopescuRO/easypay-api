<?php

use Slim\Http\Request;
use Slim\Http\Response;
use GuzzleHttp\Client;
use Integrations\CynicoIntegration as CynicoIntegration;

// Routes

$app->get('/', function (Request $request, Response $response, array $args) {
    // Sample log message
    $this->logger->info("EasyPay '/' route");

    // Render index view
    //return $this->renderer->render($response, 'index.phtml', $args);
    $array = [];
    $cynico = new CynicoIntegration($array);
    return $response->withJson($cynico->getEasyPayStatus());
});

$app->post('/pay', function (Request $request, Response $response, array $args) {
    $posts = $request->getParsedBody();

    $parameters = [
        "recipient" => null,
        "prefix" => null,
        "license_plate" => null
    ];
    $api_response = [
        "success" => 0,
        "message_id" => null
    ];

    foreach($parameters as $key => $value)
        $parameters[$key] = $posts[$key] ?? null;

    if (in_array(null, $parameters))
        return $response->withJson(['error'=>'All parameters needs to be declared.']);
	
	if (!ctype_digit($parameters['recipient']))
        return $response->withJson(['error'=>'Wrong number phone.']);

    $array = [];
    $cynico = new CynicoIntegration($array);
    $sms_response = $cynico->sendSMS($parameters);

    $api_response['message_id'] = $sms_response->id ?? null;
    if($api_response['message_id']!=null)
        $api_response['success']=1;

    return $response->withJson($api_response);
});


$app->get('/test', function (Request $request, Response $response, array $args) {
    // Sample log message
    $this->logger->info("EasyPay '/' route");

    // Render index view
    return $this->renderer->render($response, 'test.phtml', $args);
});