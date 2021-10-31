<?php

try {
    file_put_contents(__DIR__ . '/log.txt', file_put_contents('php://input'), FILE_APPEND);
} catch (Exception $exception) {
    \Illuminate\Support\Facades\Log::debug('tberror', [$exception]);
}
