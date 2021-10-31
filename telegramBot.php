<?php

try {
    file_put_contents(__DIR__ . '/log.txt', file_put_contents('php://input'));
} catch (Exception $exception) {
    \Illuminate\Support\Facades\Log::debug('tberror', [$exception]);
}
