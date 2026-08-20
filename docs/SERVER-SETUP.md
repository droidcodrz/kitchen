# Server Setup

One-time configuration for the background services. Once these are in place
everything runs on its own — no manual commands, no scripts to trigger.

Only two things ever need doing by hand afterwards: running `./deploy.sh`
when new code is pushed, and checking the services are alive if something
looks stuck.

---

## What runs automatically, once this is configured

| What | How often | Driven by |
|---|---|---|
| Sending notifications and emails | Within seconds of the event | Queue worker |
| Low-stock check across all inventory | Hourly | Scheduler |
| Marking overdue projects as delayed | Daily | Scheduler |
| Approaching-deadline warnings | Daily | Scheduler |

Nothing above needs a person to press anything. The application queues the
work; these two services carry it out.

---

## 1. Queue worker — sends every notification and email

**Without this, no notification or email is ever delivered.** The application
writes them to a `jobs` table and something has to pick them up. That
something is the worker.

The worker is a process that must run permanently and restart itself if it
dies or the server reboots. That is a process manager's job, not a cron job
and not a shell script.

### On Cloudways

**Application Settings → Supervisor Jobs → Add**

```
Command:      php /home/master/applications/YOUR_APP/public_html/artisan queue:work --sleep=3 --tries=3 --max-time=3600
Directory:    /home/master/applications/YOUR_APP/public_html
Autostart:    true
Autorestart:  true
Processes:    1
```

Replace `YOUR_APP` with the real application folder name. If Supervisor Jobs
is not available on the plan, Cloudways support will add it on request.

Why these flags:

- `--tries=3` — retry a failed send three times instead of discarding it.
- `--max-time=3600` — the worker exits after an hour and Supervisor starts a
  fresh one. Long-running PHP processes accumulate memory; this recycles them.
- `--sleep=3` — wait three seconds when the queue is empty rather than
  hammering the database.

### Verifying it works

```bash
php artisan queue:monitor default
```

Queued work should stay near zero. A number that climbs and never falls means
the worker is not running.

### If Supervisor is unavailable

A cron entry every minute is a workable fallback, though notifications may
then be delayed by up to a minute:

```
* * * * * cd /home/master/applications/YOUR_APP/public_html && php artisan queue:work --stop-when-empty >> /dev/null 2>&1
```

Prefer Supervisor where possible: it delivers immediately and survives
reboots.

---

## 2. Scheduler — runs the recurring checks

Separate from the queue. The worker delivers messages; the scheduler decides
when the recurring checks happen. **One cron entry drives all three tasks** —
never add a cron entry per task.

```
* * * * * cd /home/master/applications/YOUR_APP/public_html && php artisan schedule:run >> /dev/null 2>&1
```

On Cloudways: **Application Settings → Cron Job Management**.

This runs every minute by design. Laravel decides internally which tasks are
due, so most runs do nothing at all.

### Verifying it works

```bash
php artisan schedule:list
```

Lists all three tasks with their next run times.

---

## 3. Mail configuration

The worker delivers email, but only if a real mail transport is configured.
With `MAIL_MAILER=log`, messages are written to `storage/logs/laravel.log`
and never sent — a worker will not change that.

In `.env`:

```
MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_USERNAME=your-username
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="Kitchen Manufacturing"
```

After changing `.env`:

```bash
php artisan config:clear
php artisan queue:restart
```

Both matter: the worker holds the old configuration in memory until restarted.

---

## 4. Upload size limit

Project attachments are allowed up to 100 MB by the application.
`public/.user.ini` raises PHP's own ceiling to match:

```ini
upload_max_filesize = 110M
post_max_size = 110M
```

Some hosts ignore `.user.ini`. Confirm after deploying by uploading a large
file, or by checking `phpinfo()`. If the values have not taken effect, set
them from the hosting control panel instead.

---

## First-time setup on a new machine

Two directories are deliberately not in git — `vendor/` and `public/build/` —
so a fresh clone has neither, and the application cannot run until both are
generated:

```bash
composer install          # without this: "Failed to open stream: vendor/autoload.php"
npm ci && npm run build   # without this: every page returns 500 (no Vite manifest)
cp .env.example .env      # then fill in database and mail settings
php artisan key:generate
php artisan migrate
```

Both failures look alarming and neither is a code fault — they are simply
build steps that have not been run yet.

## Deploying new code

```bash
./deploy.sh
```

Pulls, installs PHP dependencies, rebuilds frontend assets, migrates, clears
the caches and restarts the workers.

`queue:restart` is the step worth understanding: a running worker keeps the
old code in memory and never picks up a deploy on its own. Skip it and your
changes appear not to have worked at all, which is confusing to diagnose. The
script always does it.

---

## When something looks stuck

| Symptom | Check |
|---|---|
| No notifications or emails at all | Is the worker running? `php artisan queue:monitor default` |
| Notifications appear, emails do not | `MAIL_MAILER` — likely still `log` |
| Low stock or delayed checks never fire | Is the `schedule:run` cron entry present? |
| A deployed fix seems to do nothing | `php artisan queue:restart` and `php artisan view:clear` |
| Uploads fail on large files | Did `.user.ini` take effect on this host? |

Failed jobs are kept rather than discarded:

```bash
php artisan queue:failed     # list them, with the error
php artisan queue:retry all  # try them again once the cause is fixed
```
