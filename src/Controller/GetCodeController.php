<?php

namespace App\Controller;

use App\Service\GetCodeService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class GetCodeController
{
    public function handle(Request $request, Response $response, $args): Response
    {
        $email = $request->getQueryParams()['email'];
        $password = $request->getQueryParams()['password'];
        $emailServer = $request->getQueryParams()['emailServer'];
        $socialMedia = $request->getQueryParams()['sm'];
        $getCodeService = new GetCodeService($email, $password, $emailServer, $socialMedia);

        $response->getBody()->write($getCodeService->getCode(1, 0));

        return $response;
    }
}
