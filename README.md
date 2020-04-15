# Forge Monitor

Laravel Forge monitoring built with Laravel.

## Running

You can run Forge Monitor with:

```bash
php artisan stat:mem
```

The available stat commands are:

- `stat:disk`
- `stat:load`
- `stat:mem`

## Stats

Forge Monitor provides alerting for several monitor types:

- `cpu_load` - CPU Load (%)
- `disk` - Free Disk Space (%)
- `free_memory` - Free Memory (%)
- `used_memory` - Used Memory (%)

Monitors work by checking for a threshold being met for a consecutive amount of time. Once the threshold has been met over a specified time period, Forge will be notified.

For the `disk` monitors, this threshold will only need to be met once at any given interval as the change rate is less frequent.

## Data Storage

Forge Monitor writes to a local SQLite database at `./database/database.db`, giving us a history of recent stat points.

After a sample of the stat has been taken, we will check whether each configured monitor of the stat type has been met. This can be done with SQL queries executed against the DB.

At the end of collecting each sample, we will clear out data older than a pre-determined period of time.

## Config File Description

Forge Monitor uses a TOML based file for configuring monitors named `.monitor`. The monitor file needs to live either in the user's home directory or the root of the application.

Example configuration:

```toml
[monitor-1]
type = "disk"
operator = "gte"
threshold = 10 # %
token = "foobarbaz"

[monitor-2]
type = "free_memory"
operator = "lte"
threshold = 25
minutes = 5
token = "foobarbaz"
```

Forge will add a new `[monitor-{id}]` section for each configured monitor. Monitors are made up of:

- Type
- Operator
- Threshold
- Minutes (*)
- Token

The `minutes` value is ignored for the disk monitor.
