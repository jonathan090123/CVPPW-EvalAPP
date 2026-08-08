<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /** Palet warna untuk badge jabatan (hex). */
    public const POSITION_PALETTE = [
        '#0d6efd', // biru
        '#198754', // hijau
        '#dc3545', // merah
        '#6f42c1', // ungu
        '#fd7e14', // oranye
        '#20c997', // teal
        '#d63384', // pink
        '#0dcaf0', // cyan (teks gelap)
    ];

    /** Indeks palet yang butuh teks gelap. */
    public const LIGHT_TEXT_INDICES = [7];

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Blade::directive('positionBadge', function ($position) {
            return "<?php
                \$__pos = (string) ({$position});
                \$__palette = \App\Providers\AppServiceProvider::POSITION_PALETTE;
                \$__light = \App\Providers\AppServiceProvider::LIGHT_TEXT_INDICES;
                \$__idx = abs(crc32(strtolower(trim(\$__pos)))) % count(\$__palette);
                \$__color = \$__palette[\$__idx];
                \$__txtColor = in_array(\$__idx, \$__light, true) ? '#212529' : '#fff';
                echo '<span class=\"badge rounded-pill ms-1\" style=\"background-color:'.\$__color.';color:'.\$__txtColor.'\">'.e(\$__pos).'</span>';
            ?>";
        });
    }
}