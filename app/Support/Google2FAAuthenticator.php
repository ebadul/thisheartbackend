<?php
namespace App\Support;
use PragmaRX\Google2FALaravel\Support\Authenticator;

class Google2FAAuthenticator extends Authenticator
{
    protected function canPassWithoutCheckingOTP($user)
    {
        if(!count($user->passwordSecurity))
            return true;
        
        return !$user->passwordSecurity->google2fa_enable || !$this->isEnabled() || $this->noUserIsAuthenticated() || $this->twoFactorAuthStillValid();
    }

    protected function getGoogle2FASecretKey($user)
    {
        $secret = $user->passwordSecurity->{$this->config('otp_secret_column')};

        if (is_null($secret) || empty($secret)) {
            throw new InvalidSecretKey('Secret key cannot be empty.');
        }

        return $secret;
    }
}