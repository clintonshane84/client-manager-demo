<?php

namespace app\View\Components\Forms;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use Illuminate\Contracts\Support\Htmlable;

class CreateUser extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Htmlable|Closure|string
    {
        return view('components.forms.create-user', [
            'languages'     => $this->languages,
            'interests'     => $this->interests,
            'identityTypes' => $this->identityTypes,
            'postUrl'       => $this->postUrl,
        ]);
    }
}