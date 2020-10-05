<?php
/**
 * SignupController
 * @package site-profile-signup
 * @version 0.0.1
 */

namespace SiteProfileSignup\Controller;

use Profile\Model\Profile;

use LibForm\Library\Form;
use SiteProfileLogin\Library\Meta;

class SignupController extends \Site\Controller
{
    public function registerAction(){
        $next = $this->req->getQuery('next');
        if(!$next)
            $next = $this->router->to('siteHome');

        if($this->profile->isLogin())
            return $this->res->redirect($next);

        $form = new Form('site.profile.signup');

        $params = [
            'error' => false,
            'form'  => $form,
            'meta' => Meta::single((object)[
                'title' => 'Register',
                'description' => 'Profile register page'
            ])
        ];

        if(!($valid = $form->validate())){
            $this->res->render('profile/auth/signup', $params);
            return $this->res->send();
        }

        $valid->password = password_hash($valid->password, PASSWORD_DEFAULT);

        $valid->educations = '[]';
        $valid->profession = '[]';
        $valid->contact    = '[]';
        $valid->socials    = '[]';

        if($this->user->isLogin())
            $valid->user = $this->user->id;

        if(!($id = Profile::create((array)$valid)))
            deb(Profile::lastError());

        $this->res->render('profile/auth/signup-success', $params);
        return $this->res->send();
    }
}