<?php

 

namespace App\Services;

 

use App\Models\AlertConfiguration;

use App\Models\Role;

use App\Models\User;

use Illuminate\Notifications\Notification;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Notification as NotificationFacade;

 

class AlertNotifier

{

    /**

     * Send a notification to every user configured to receive a given alert type.

     *

     * $notificationFactory receives whether email delivery is enabled for this

     * alert type and must return the Notification instance to send.

     *

     * $shouldSkip, if given, can skip individual recipients (e.g. to dedupe

     * against an already-existing unread notification).

     *

     * Returns the number of users notified.

     */

    public function notify(string $alertType, \Closure $notificationFactory, ?\Closure $shouldSkip = null): int

    {

        $config = AlertConfiguration::where('alert_type', $alertType)

            ->where('is_enabled', true)

            ->first();

 

        if (!$config) {

            return 0;

        }

 

        $users = $this->resolveUsers($config);
        $extraEmails = $config->notify_via_email ? ($config->always_notify_emails ?? []) : [];

 

        if ($users->isEmpty() && empty($extraEmails)) {

            return 0;

        }

 

        $notification = $notificationFactory((bool) $config->notify_via_email);

 

        $sent = 0;

 

        foreach ($users as $user) {

            if ($shouldSkip && $shouldSkip($user)) {

                continue;

            }

 

            $user->notify($notification);

            $sent++;

        }

        foreach ($extraEmails as $email) {
            NotificationFacade::route('mail', $email)->notify($notification);
            $sent++;
        }

        return $sent;

    }

 

    /**

     * Resolve the users that should receive alerts for a given configuration.

     */

    public function resolveUsers(AlertConfiguration $config): Collection

    {

        $query = User::where('status', 'active');

 

        if (!empty($config->notify_roles)) {

            $roleIds = Role::whereIn('slug', $config->notify_roles)->pluck('id');

            if ($roleIds->isNotEmpty()) {

                $query->whereIn('role_id', $roleIds);

            }

        }

 

        return $query->get();

    }

}