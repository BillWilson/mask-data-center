<?php

namespace App\Actions;

use Lorisleiva\Actions\Action;

class Home extends Action
{
    /**
     * Execute the action and return a result.
     *
     * @return mixed
     */
    public function handle()
    {
        return view('welcome');
    }
}
