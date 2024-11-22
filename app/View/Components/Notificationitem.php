<?php

namespace App\View\Components;

use App\Lib\Utils\ENotificationType;
use App\Lib\Utils\Utils;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Notificationitem extends Component
{
    public string $title;
    public string $description;
    public mixed $type;
    public int $line;
    public int $millisecond;
    public string $id;

    public function __construct($line, $title, $description, $type, $millisecond = 4900, $id = "")
    {
        $this->line = Utils::default($line, 0);
        $this->title = Utils::default($title, "");
        $this->description = Utils::default($description, "");
        $this->type = Utils::default($type, ENotificationType::info);
        $this->millisecond = Utils::default($millisecond, 4900);
        $this->id = $id;
    }

    public function render(): View|Closure|string
    {
        return view('components.notificationitem');
    }
}
