<?php

$data = json_decode(file_get_contents('php://input'), true);
\Illuminate\Support\Facades\Log::debug('telegrambot', $data);
