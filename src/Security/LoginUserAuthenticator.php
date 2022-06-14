<?php

namespace App\Security;

use Exception;
use Symfony\Component\HttpFoundation\{Request, Response, RedirectResponse};
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\{Authentication\Token\TokenInterface, Security};
use Symfony\Component\Security\Http\{
    Util\TargetPathTrait,
    Authenticator\AbstractLoginFormAuthenticator,
    Authenticator\Passport\Badge\CsrfTokenBadge,
    Authenticator\Passport\Badge\UserBadge,
    Authenticator\Passport\Credentials\PasswordCredentials,
    Authenticator\Passport\Passport,
};

class LoginUserAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'app_login';

    private UrlGeneratorInterface $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    public function authenticate(Request $request): Passport
    {
        $email = $request->request->get('email', '');

        $request->getSession()->set(Security::LAST_USERNAME, $email);

        return new Passport(
            new UserBadge($email),
            new PasswordCredentials($request->request->get('password', '')),
            [
                new CsrfTokenBadge('authenticate', $request->request->get('_csrf_token')),
            ]
        );
    }

    /**
     * @throws Exception
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response {
        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($targetPath);
        }
         return new RedirectResponse(
             match($token->getUser()->getRoles()['roles']){
                 'ROLE_ADMIN' => $this->urlGenerator->generate('admin_home'),
                 'ROLE_USER' => $this->urlGenerator->generate('profil_home'),
             }
         );
    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}
