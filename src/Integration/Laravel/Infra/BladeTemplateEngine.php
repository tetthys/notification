<?php

namespace Tetthys\Notification\Integration\Laravel\Infra;

use Tetthys\Notification\Core\Contracts\TemplateEngine;
use Illuminate\Support\Facades\View;

final class BladeTemplateEngine implements TemplateEngine
{
    public function render(string $type, string $channel, array $data): array
    {
        $view = "notifications.$type.$channel";
        if (View::exists($view)) {
            return View::make($view, ['data' => $data])->renderData();
        }

        return match ($channel) {
            'email' => ['subject' => "[$type] ".($data['subject'] ?? ''), 'body' => $data['body'] ?? ''],
            'inApp' => ['title' => $data['title'] ?? ($data['subject'] ?? ''), 'body' => $data['body'] ?? ''],
            default => $data,
        };
    }
}