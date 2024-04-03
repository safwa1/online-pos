<?php


namespace App\Traits;

trait BackToIndexView {

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

}
