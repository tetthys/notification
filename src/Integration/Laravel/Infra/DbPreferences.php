<?php

namespace Tetthys\Notification\Integration\Laravel\Infra;

use Tetthys\Notification\Core\Contracts\Preferences;
use Illuminate\Support\Facades\DB;

final class DbPreferences implements Preferences
{
    public function disabledChannelsFor(
        string $userId,
        string $notificationType,
    ): array {
        $row = DB::table("user_notification_prefs")
            ->where("user_id", $userId)
            ->where("type", $notificationType)
            ->first();

        if (!$row) {
            return [];
        }
        $arr = json_decode($row->disabled_channels ?? "[]", true);
        return is_array($arr) ? $arr : [];
    }
}
