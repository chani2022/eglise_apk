<?php

namespace App\Security;

use App\Service\RecaptchaService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use Symfony\Contracts\Translation\TranslatorInterface;

class AppAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'app_login';

    public function __construct(private UrlGeneratorInterface $urlGenerator, private RecaptchaService $recaptcha, private TranslatorInterface $trans)
    {
    }

    public function supports(Request $request): bool
    {

        return $request->isMethod('POST') && $this->getLoginUrl($request) === $request->getBaseUrl() . $request->getPathInfo();
    }
    public function authenticate(Request $request): Passport
    {
        /**
         * verification recaptcha
         */
        $value_response_recaptcha = $request->request->get('g-recaptcha-response');

        if (!$this->recaptcha->verify($request, $value_response_recaptcha)) {
            throw new CustomUserMessageAuthenticationException($this->trans->trans('Veuillez valider le recaptcha!'));
        }

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

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($targetPath);
        }


        $route_name = "app_dashboard";

        if ($token->getUser()->getRoles()[0] == "ROLE_USER") {
            $route_name = "app_home";
        } else if ($token->getUser()->getRoles()[0] == "ROLE_REDACTEUR") {
            $route_name = "app_article";
        }
        // For example:
        return new RedirectResponse($this->urlGenerator->generate($route_name));
        // throw new \Exception('TODO: provide a valid redirect inside '.__FILE__);
    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}
