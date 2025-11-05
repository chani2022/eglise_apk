<?php

namespace App\Service;

use ReCaptcha\ReCaptcha;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class RecaptchaService
{
    private $recaptcha;

    public function __construct($google_recaptcha)
    {
        $this->recaptcha = new ReCaptcha($google_recaptcha);
    }
    public function verify(Request $request, $value_response_recaptcha): bool
    {
        $isResponseOk = false;
        $remote_addr = $request->server->get('REMOTE_ADDR');
        $server_name = $request->server->get('SERVER_NAME');
        // dump($request);
        /**
         * pour le test en local
         */
        if (preg_match('/:/', $server_name)) {
            $server_name = explode(':', $request->server->get('SERVER_NAME'))[0];
        }
        $resp = $this->recaptcha->setExpectedHostname($server_name)
            ->verify($value_response_recaptcha, $remote_addr);
        if ($resp->isSuccess()) {
            $isResponseOk = true;
        }
        // dd($resp);
        return $isResponseOk;
    }
}
