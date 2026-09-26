<?php
use Illuminate\Support\Facades\Artisan;
Artisan::command('business:summary', function () {
    $this->info('Car Business Management System is ready.');
})->purpose('Display system status');
