<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class FacebookController extends Controller
{
    public function redirectToFacebook()
    {
        return Socialite::driver('facebook')->redirect();
    }

    public function handleFacebookCallback()
    {
    $facebookUser = Socialite::driver('facebook')->fields([
        'name', 'email'
    ])->user();

    $user = User::updateOrCreate(
        ['email' => $facebookUser->getEmail()],
        [
            'name' => $facebookUser->getName(),
            'facebook_id' => $facebookUser->getId(),
            'avatar' => $facebookUser->getAvatar(),
            'password' => bcrypt('facebook_login'),
        ]
    );

    Auth::login($user);
    return redirect('/home');
    }

}
