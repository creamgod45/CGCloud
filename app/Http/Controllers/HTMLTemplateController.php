<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\View;

class HTMLTemplateController extends Controller
{
    public function Notification()
    {
        return View::make('components.notificationitem', [
            "type" => "%type%",
            "millisecond" => -1,
            "title" => "%title%",
            "description" => "%description%",
            "id" => "placeholder",
            "line" => 17,
        ]);
    }
}
