<?php

declare(strict_types=1);

namespace Kopling\Contact\Command;

use Illuminate\Console\Command;
use Kopling\Contact\Config;
use Kopling\Contact\Message;

class PruneCommand extends Command
{
    protected $signature = 'kopling:contact:prune {--dry-run : Only count the messages that would go}';

    protected $description = 'Delete contact messages older than the retention setting';

    public function handle(): int
    {
        $days = Config::retentionDays();
        $query = Message::query()->where('created_at', '<', now()->subDays($days));

        if ($this->option('dry-run')) {
            $this->components->info("{$query->count()} message(s) older than {$days} day(s) would be deleted.");

            return self::SUCCESS;
        }

        $this->components->info("Deleted {$query->delete()} message(s) older than {$days} day(s).");

        return self::SUCCESS;
    }
}
