{include file='globalheader.tpl'}
<div class="page-guest-collect">

    {validation_group class="alert alert-danger"}
    {validator id="emailformat" key="ValidEmailRequired"}
    {validator id="uniqueemail" key="UniqueEmailRequired"}
    {/validation_group}

    <div class="card shadow col-12 col-sm-8 mx-auto">
        <div class="card-body">
            <h2 class="text-center">{translate key=WeNeedYourEmailAddress}</h2>

            <form method="post" id="form-guest-collect" action="{$smarty.server.REQUEST_URI|escape:'html'}" role="form">
                {if $AllowEmailLogin}
                    <div class="mb-3">
                        <div class="form-group">
                            <label class="reg fw-bold" for="email">{translate key="Email"}</label>
                            {textbox type="email" name="EMAIL" class="input" value="Email" required="required"}
                        </div>
                        <div class="form-group">
                            <label class="reg fw-bold" for="firstname">{translate key="FirstName"}</label>
                            {textbox type="firstname" name="FIRSTNAME" class="input" value="FirstName" required="required"}
                        </div>
                        <div class="form-group">
                            <label class="reg fw-bold" for="lastname">{translate key="LastName"}</label>
                            {textbox type="lastname" name="LASTNAME" class="input" value="LastName" required="required"}
                        </div>
                        <div class="form-group">
                            <label class="reg fw-bold" for="username">{translate key="Username"}</label>
                            {textbox type="username" name="USERNAME" class="input" value="Username" required="required"}
                        </div>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="update btn btn-primary" name="" id="btnUpdate">
                            {translate key='Continue'}
                        </button>
                    </div>
                {/if}
                <div class="d-grid">
                    <section class="d-flex justify-content-center flex-wrap gap-2 my-3 social-login">
                        {if $AllowGoogleLogin}
                            <a type="button" href="{$GoogleUrl}" class="btn btn-outline-primary"><i
                                    class="bi bi-google me-1"></i>{translate key='SignInWith'}<span class="fw-medium">
                                    Google</span></a>
                        {/if}
                        {if $AllowMicrosoftLogin}
                            <a type="button" href="{$MicrosoftUrl}" class="btn btn-outline-primary"><i
                                    class="bi bi-microsoft me-1"></i>{translate key='SignInWith'}<span class="fw-medium">
                                    Microsoft</span></a>
                        {/if}
                        {if $AllowFacebookLogin}
                            <a type="button" href="{$FacebookUrl}" class="btn btn-outline-primary"><i
                                    class="bi bi-facebook me-1"></i>{translate key='SignInWith'}<span class="fw-medium">
                                    Facebook</span></a>
                        {/if}
                    </section>
                </div>
            </form>

        </div>
    </div>
    {setfocus key='EMAIL'}

    {include file="javascript-includes.tpl"}
    {jsfile src="ajax-helpers.js"}

    <div id="wait-box" class="wait-box">
        {include file="wait-box.tpl"}
    </div>

</div>
{include file='globalfooter.tpl'}
