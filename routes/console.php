<?php

use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function (): void {
    $this->comment('Support is a product feature, not an afterthought.');
})->purpose('Display an inspiring quote');
