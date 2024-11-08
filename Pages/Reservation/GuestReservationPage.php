<?php

require_once(ROOT_DIR . 'Pages/Reservation/NewReservationPage.php');
require_once(ROOT_DIR . 'Presenters/Reservation/GuestReservationPresenter.php');

interface IGuestReservationPage extends INewReservationPage
{
    /**
     * @return bool
     */
    public function GuestInformationCollected();

    /**
     * @return string
     */
    public function GetEmail();

    /**
     * @return string
     */
    public function GetFirstName();

    /**
     * @return string
     */
    public function GetLastName();

    /**
     * @return string
     */
    public function GetUsername();

    /**
     * @return bool
     */
    public function IsCreatingAccount();

    /**
     * @return bool
     */
    public function GetTermsOfServiceAcknowledgement();

    /**
     *
     */
    public function SetGoogleUrl($URL);
}

class GuestReservationPage extends NewReservationPage implements IGuestReservationPage
{
    public function SetGoogleUrl($googleUrl)
    {
        if (Configuration::Instance()->GetSectionKey(ConfigSection::AUTHENTICATION, ConfigKeys::AUTHENTICATION_ALLOW_GOOGLE, new BooleanConverter())) {
            $this->Set('GoogleUrl', $googleUrl);
        }
    }

    private function SetFacebookErrorMessage()
    {
        if (isset($_SESSION['facebook_error']) && $_SESSION['facebook_error'] == true) {
            $this->Set('facebookError', $_SESSION['facebook_error']);
            unset($_SESSION['facebook_error']);
        }
    }

    public function GetGoogleUrl($uri)
    {
        if (Configuration::Instance()->GetSectionKey(ConfigSection::AUTHENTICATION, ConfigKeys::AUTHENTICATION_ALLOW_GOOGLE, new BooleanConverter())) {
            session_start();
            $_SESSION['redirect_uri'] = $uri;
            $client = new Google\Client();
            $client->setClientId(Configuration::Instance()->GetSectionKey(ConfigSection::AUTHENTICATION, ConfigKeys::GOOGLE_CLIENT_ID));
            $client->setClientSecret(Configuration::Instance()->GetSectionKey(ConfigSection::AUTHENTICATION, ConfigKeys::GOOGLE_CLIENT_SECRET));
            $client->setRedirectUri(
                Configuration::Instance()->GetSectionKey(ConfigSection::AUTHENTICATION, ConfigKeys::GOOGLE_REDIRECT_URI)
            );
            $client->addScope("email");
            $client->addScope("profile");
            $client->setPrompt("select_account");
            $GoogleUrl = $client->createAuthUrl();
            return $GoogleUrl;
        }
    }

    public function PageLoad()
    {
        if (Configuration::Instance()->GetSectionKey(ConfigSection::PRIVACY, ConfigKeys::PRIVACY_ALLOW_GUEST_BOOKING, new BooleanConverter())) {
            $this->presenter = $this->GetPresenter();
            $this->presenter->PageLoad();
            $this->Set('ReturnUrl', Pages::SCHEDULE);
            $this->SetGoogleUrl($this->GetGoogleUrl(ServiceLocator::GetServer()->GetUrl()));
            $this->Display($this->GetTemplateName());
        } else {
            $this->RedirectToError(ErrorMessages::INSUFFICIENT_PERMISSIONS);
        }
    }

    protected function GetPresenter()
    {
        return new GuestReservationPresenter(
            $this,
            new GuestRegistration(new PasswordEncryption(), new UserRepository(), new GuestRegistrationNotificationStrategy(), new GuestReservationPermissionStrategy($this)),
            new WebAuthentication(PluginManager::Instance()->LoadAuthentication()),
            $this->LoadInitializerFactory(),
            new NewReservationPreconditionService()
        );
    }

    protected function GetTemplateName()
    {
        if ($this->GuestInformationCollected()) {
            return parent::GetTemplateName();
        }
        $this->SetFacebookErrorMessage();
        $this->Set('AllowFacebookLogin', Configuration::Instance()->GetSectionKey(ConfigSection::AUTHENTICATION, ConfigKeys::AUTHENTICATION_ALLOW_FACEBOOK, new BooleanConverter()));
        $this->Set('AllowGoogleLogin', Configuration::Instance()->GetSectionKey(ConfigSection::AUTHENTICATION, ConfigKeys::AUTHENTICATION_ALLOW_GOOGLE, new BooleanConverter()));
        $this->Set('AllowMicrosoftLogin', Configuration::Instance()->GetSectionKey(ConfigSection::AUTHENTICATION, ConfigKeys::AUTHENTICATION_ALLOW_MICROSOFT, new BooleanConverter()));
        $this->Set('AllowEmailLogin', Configuration::Instance()->GetSectionKey(ConfigSection::AUTHENTICATION, ConfigKeys::AUTHENTICATION_ALLOW_GUEST_FORM, new BooleanConverter()));


        return 'Reservation/collect-guest.tpl';
    }

    public function GuestInformationCollected()
    {
        return !ServiceLocator::GetServer()->GetUserSession()->IsGuest();
    }

    public function GetEmail()
    {
        return $this->GetForm(FormKeys::EMAIL);
    }

    public function GetFirstName()
    {
        return $this->GetForm(FormKeys::FIRSTNAME);
    }

    public function GetLastName()
    {
        return $this->GetForm(FormKeys::LASTNAME);
    }

    public function GetUsername()
    {
        return $this->GetForm(FormKeys::USERNAME);
    }

    public function IsCreatingAccount()
    {
        return $this->IsPostBack() && !$this->GuestInformationCollected();
    }

    public function GetTermsOfServiceAcknowledgement()
    {
        return $this->GetCheckbox(FormKeys::TOS_ACKNOWLEDGEMENT);
    }
}
