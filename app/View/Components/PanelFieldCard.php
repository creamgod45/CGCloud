<?php

namespace App\View\Components;

use App\Lib\I18N\I18N;
use App\Models\Member;
use App\Models\ShareTable;
use App\Models\VirtualFile;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\View\Component;

class PanelFieldCard extends Component
{
    /**
     * @var VirtualFile[]|Collection|null $virtualFiles
     */
    public array|null|Collection $virtualFiles = null;

    public bool $isGuestUser;
    public bool $isAuthUser;

    public function __construct(
        public ShareTable $shareTable,
        public string $popoverid,
        public I18N $i18N,
    )
    {
        $this->isGuestUser = auth()->guest();
        $this->isAuthUser = auth()->check();

        if($this->isAuthUser) {
            $this->virtualFiles = $this->shareTable->getAllVirtualFiles();
        }
    }

    public function isGuest(): bool
    {
        return auth()->guest();
    }

    public function isAuth(): bool
    {
        return auth()->check();
    }

    public function isOwner(Member $member): bool
    {
        return $this->shareTable->isOwner($member);
    }

    public function render(): View
    {
        return view('components.panel-field-card');
    }
}
