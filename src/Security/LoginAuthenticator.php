<?php
namespace App\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use App\Repository\UsersRepository;

class LoginAuthenticator extends AbstractAuthenticator
{
    private RouterInterface $router;

    public function __construct(RouterInterface $router, UsersRepository $usersRepository)
    {
        $this->router = $router;
        $this->usersRepository = $usersRepository;
    }

    public function supports(Request $request): bool
    {
        return $request->attributes->get('_route') === 'app_conexion' && $request->isMethod('POST');
    }

    public function authenticate(Request $request): Passport
    {
        $pseudo = $request->request->get('pseudo');
        $password = $request->request->get('password');

        $user = $this->usersRepository->findOneBy(['pseudo' => $pseudo]);

        if (!$user || !password_verify($password, $user->getPassword())) {
            throw new AuthenticationException('mot de passe ou pseudo incorrects.');
        }

        return new SelfValidatingPassport(new UserBadge($pseudo));
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        $user = $token->getUser();
        return new RedirectResponse($this->router->generate('user_dashboard', ['id' => $user->getId()]));
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): Response
    {
        return new RedirectResponse($this->router->generate('app_conexion'));
    }
}

