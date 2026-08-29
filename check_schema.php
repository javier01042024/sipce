<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$cols = \Illuminate\Support\Facades\DB::select('SHOW COLUMNS FROM sesiones');
foreach ($cols as $c) {
    echo $c->Field.' | '.$c->Type.' | Null:'.$c->Null.' | Key:'.$c->Key.PHP_EOL;
}
