<?php
namespace App\Authorization\JWTLocal;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
class AuthJWTComponent
{
    public function creatJwtToken($jwt_user_id, $expires_in, $token_type = 'access_token') : string {
        try {
            $privateKey = file_get_contents("config/jwt.key");
            $iat = time();
            $expires = $iat + $expires_in;
            $payload = [
                'iss' => 'example.com',
                'aud' => 'example.com',
                'iat' => $iat,
                'sub' => $jwt_user_id . '_' . uniqid(),
                'exp' => $expires,
                'token_type' => $token_type,
            ];
            $jwt = JWT::encode($payload, $privateKey, 'RS256');
            return $jwt;
        }
        catch (\Exception $e) {

        }
        return '';
    }

    public function accessTokenExpiresIn() {
        return 60 * 10;
    }

    public function refreshTokenExpiresIn() {
        return 60 * 60 * 24 * 30;
    }

    public function getAuthenticateUserId() {
        $auth = TableRegistry::getTableLocator()->get("AuthJwt")->find()->select('id')->where([
            'username' => $_SERVER['PHP_AUTH_USER'],
            'password' => $_SERVER['PHP_AUTH_PW']
        ])->first();
        return $auth['id'] ?? '';
    }
    public function isValidBasicAuthentication() : bool {
        $auth_username = $_SERVER['PHP_AUTH_USER'] ?? '';
        $auth_password = $_SERVER['PHP_AUTH_PW'] ?? '';
        if (empty($auth_username) || empty($auth_password)) {
            return false;
        }
        $auth = TableRegistry::getTableLocator()->get("AuthJwt")->find()->where([
            'username' => $auth_username,
            'password' => $auth_password
        ])->first();

        if (empty($auth) || ($auth_username != $auth['username'] || $auth_password != $auth['password'])) {
            return false;
        }
        return true;
    }

    public function authenticateRoute(ServerRequestInterface $request) : bool {
        $authorizationString = $request->getHeaderLine("Authorization");
        if (empty($authorizationString)) {
            $authorizationString = $request->getHeaderLine("authorization");
        }
        if (!(str_starts_with($authorizationString, "bearer ") || str_starts_with($authorizationString, "Bearer "))) {
            return false;
        }
        $authorizationToken = substr($authorizationString, 7, strlen($authorizationString));
        $is_valid = $this->validateAccessToken($authorizationToken);
        if (!$is_valid) {
            return false;
        }
        return true;
    }

    public function validateAccessToken($jwt) : bool {
        try {
            $publicKey = file_get_contents('/jwt.pem');
            $decoded = JWT::decode($jwt, new Key($publicKey, 'RS256'));
            return true;
        }
        catch (\LogicException $e) {
            // errors having to do with environmental setup or malformed JWT Keys
        }
        catch (\UnexpectedValueException $e) {
            // errors having to do with JWT signature and claims
        }
        return false;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if ($request->getAttribute('params')['_matchedRoute'] == '/api/v1/auth/token') {
            if (!$this->isValidBasicAuthentication()) {
                return $this->unAuthorizedResponse();
            }
        }
        else {
            if (!$this->authenticateRoute($request)) {
                return $this->unAuthorizedResponse();
            }
        }

        $response = $handler->handle($request);
        return $response;
        // return $this->setCORSHeaders($response);
    }

    public function unAuthorizedResponse() : ResponseInterface {
        return new Response([
            'type' => 'application/json',
            'body' => json_encode([
                "statusCode" => ApiResponseUtil::UNAUTHORIZED_HTTP_CODE,
                "result" => [
                    'status' => 'error',
                    'msg' => 'Unauthenticated'
                ]
            ]),
            'status' => ApiResponseUtil::UNAUTHORIZED_HTTP_CODE
        ]);
    }

}
