<?php

// app/Providers/AppServiceProvider.php

namespace App\Providers;

use Illuminate\Support\Facades\URL; // <-- Tambahkan baris ini
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    // ...
    public function boot(): void
    {
        if (str_contains(request()->server('HTTP_HOST'), 'ngrok') || env('APP_ENV') !== 'local') {
            URL::forceScheme('https'); 
        }
    }
}
