<?php

namespace Inium\Laraboard\App\Board;

use Illuminate\Support\Facades\Session;

trait AlertSessionFlashTrait
{
    /**
     * Session Flash message 중 alert danger message를 설정한다.
     *
     * @param string $message   메시지
     * @return void
     */
    private function flashAlertDanger(string $message)
    {
        Session::flash('alert-class', 'alert-danger');
        Session::flash('message', $message);
    }
}
