<?php

declare(strict_types=1);

namespace Vcampitelli\Framework\Core\Application\Middleware;

use Vcampitelli\Framework\Core\Domain\User\User;
use Vcampitelli\Framework\Core\Infrastructure\Persistence\User\UserRepository;
use Exception;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface as ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface as Middleware;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;
use Slim\Psr7\Factory\StreamFactory;

/**
 * Middleware responsible for the entire authentication process.
 * It will try differente mechanisms from the request (like Basic Auth or Bearer Token) and store it in a session.
 * It will fallback to the current session if it is a valid one.
 */
class AuthenticationMiddleware implements Middleware
{
    /**
     * Index to store the user info in $_SESSION
     *
     * @var string
     */
    private const SESSION_INDEX_USER = '__USER__';

    /**
     * @var UserRepository
     */
    private UserRepository $userRepository;

    /**
     * AuthenticationMiddleware constructor.
     *
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @param Request        $request
     * @param RequestHandler $handler
     *
     * @return ResponseInterface
     */
    public function process(Request $request, RequestHandler $handler): ResponseInterface
    {
        try {
            $this->bootstrapSession($request);
        } catch (Exception $exception) {
            $message = $exception->getMessage();
            return new Response(
                StatusCodeInterface::STATUS_UNAUTHORIZED,
                null,
                ($message) ? (new StreamFactory())->createStream($message) : null
            );
        }

        return $handler->handle($request);
    }

    /**
     * Starts session and checks current user
     *
     * @param Request $request
     *
     * @throws Exception
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    protected function bootstrapSession(Request $request): void
    {
        if (\session_status() === \PHP_SESSION_NONE) {
            \session_start();
        }

        $exception = null;
        $user = null;
        try {
            $user = $this->loginFromRequest($request);
        } catch (\Exception $exception) {
        }

        if (!$user) {
            if ((!empty($_SESSION)) && (!empty($_SESSION[self::SESSION_INDEX_USER]))) {
                $user = $this->userRepository->loginById($_SESSION[self::SESSION_INDEX_USER]);
            }

            if (!$user) {
                throw $exception ?? new Exception('Authentication failed');
            }
        }

        \session_regenerate_id(true);
        $_SESSION[self::SESSION_INDEX_USER] = $user->getId();
    }

    /**
     * Returns the User ID from current request
     *
     * @param Request $request Current request
     *
     * @return User
     *
     * @throws Exception
     */
    protected function loginFromRequest(Request $request): User
    {
        $authorization = $request->getServerParams()['HTTP_AUTHORIZATION'] ?? '';
        if (empty($authorization)) {
            throw new Exception('Missing Authorization header');
        }

        if (\stripos($authorization, 'Basic ') !== false) {
            $basic = \substr($authorization, 6);
            if (!empty($basic)) {
                $basic = \base64_decode($basic);
                if (!empty($basic)) {
                    list($username, $password) = \explode(':', $basic, 2);
                    $user = $this->userRepository->loginByUsernameAndPassword($username, $password);
                    if ($user) {
                        return $user;
                    }
                }
            }
            throw new Exception('Invalid credentials');
        }

        if (\stripos($authorization, 'Bearer ') !== false) {
            $token = \substr($authorization, 7);
            if (!empty($token)) {
                $user = $this->userRepository->loginByToken($token);
                if ($user) {
                    return $user;
                }
            }
            throw new Exception('Invalid credentials');
        }

        throw new Exception('No valid authentication process found');
    }
}
