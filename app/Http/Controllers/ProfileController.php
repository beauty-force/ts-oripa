<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Str;
use DB;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Inertia\Response
     */
    public function edit(Request $request)
    {
        $line_id = env('LINE_ID');
        $user = auth()->user();
        if ($user->invite_code == null) {
            $user->invite_code = Str::random(8);
            $user->save();
        }

        $invitations = DB::table('invitations')
            ->leftjoin('users', 'users.id', '=', 'invitations.user_id')
            ->leftjoin('profiles', 'profiles.user_id', '=', 'invitations.user_id')
            ->select('invitations.id', 'users.name', 'invitations.status')
            ->where('invitations.inviter', $user->id)
            ->get();

        return Inertia::render('Profile/Edit', [
            'mustVerifyEmail' => $user instanceof MustVerifyEmail,
            'invitations' => $invitations,
            'status' => session('status'),
            'hide_cat_bar' => 1,
            'invite_url' => 'https://test.ts-oripa.com/register?invitation_code='.$user->invite_code,
            'invite_bonus' => getOption('invite_bonus'),
            'invited_bonus' => getOption('invited_bonus'),
            'line_link_url' => 'https://line.me/R/ti/p/'.$line_id,
            'line_invite_text' => "✨トレしるオリパからの特別なご招待✨

友達からトレしるオリパへの招待状が届いています！新規登録時に下記の招待コードを入力すると、紹介者とあなたの両方に特典ポイントがプレゼントされます！🎁

🔐 招待コード: 【$user->invite_code"."】
📱 登録はこちらから: https://test.ts-oripa.com/register?invitation_code=$user->invite_code

トレしるオリパで素敵な体験を始めましょう！🌟",
            'twitter_invite_text' => "【トレしるオリパ 特別招待キャンペーン🎯】

友達紹介特典実施中🎊
新規登録時に下記の招待コードを入力すると、紹介者とあなたの両方に特典ポイントがプレゼントされます！🎁

🔐 招待コード: 【$user->invite_code"."】
📱 登録はこちらから
（https://test.ts-oripa.com/register?invitation_code=$user->invite_code)"

        ]);
    }

    /**
     * Update the user's profile information.
     *
     * @param  \App\Http\Requests\ProfileUpdateRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(ProfileUpdateRequest $request)
    {
        $user = $request->user();
        $user->fill($request->validated());

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        if ($request->avatar) {
            if (!file_exists('images/avatars')) {
                mkdir('images/avatars', 0777, true);
            }
            if ($user->avatar && file_exists(public_path('images/avatars/'.$user->avatar))) unlink(public_path('images/avatars/'.$user->avatar));
            $url = saveImage('images/avatars', $request->file('avatar'), false);
            $user->update(['avatar' => $url]);
        }
        return Redirect::route('profile.edit')->with('data', ['user' => $user]);
    }

    /**
     * Delete the user's account.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {
        $request->validate([
            'password' => ['required', 'current-password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->update(['status' => 2]);

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
