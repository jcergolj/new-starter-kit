<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Laravel\Fortify\Actions\DisableTwoFactorAuthentication;
use Laravel\Fortify\Actions\EnableTwoFactorAuthentication;
use Jcergolj\InAppNotifications\Facades\InAppNotification;

class TwoFactorController extends Controller
{
    public function edit(Request $request)
    {
        return view('settings.two-factor.edit', [
            'user' => $request->user(),
        ]);
    }

    public function update(Request $request, EnableTwoFactorAuthentication $enableTwoFactor)
    {
        $enableTwoFactor($request->user());

        return to_route('settings.confirmed-two-factor.edit');
    }

    public function destroy(Request $request, DisableTwoFactorAuthentication $disableTwoFactor)
    {
        $disableTwoFactor($request->user());

        InAppNotification::success(__('Two-factor disabled.'));

        return back(fallback: route('settings.two-factor.edit'));
    }
}
