<?php

declare(strict_types=1);

$installed = json_decode(file_get_contents(__DIR__ . '/../.phpcq/plugins/installed.json'), true);

require_once __DIR__ . '/../.phpcq/plugins/' . $installed['plugins']['phpunit']['tools']['phpunit']['url'];
