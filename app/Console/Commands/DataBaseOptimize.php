<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Exception;

class DataBaseOptimize extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:optimize
                        {--table=* : Defaulting to all tables in the default database.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Optimize table/s of the database';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(Builder $builder)
    {
        $this->db = $builder;
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     * @throws Exception
     */
    public function handle(): void
    {
        $this->info('Starting Optimization.');

        $this->getTables()
            ->tap(function($collection) {
                $this->progress = $this->output->createProgressBar($collection->count());
            })
            ->each(function($table){
                $this->optimize($table);
            });

        $this->info(PHP_EOL.'Optimization Completed');
    }

    /**
     * Get database which need optimization
     *
     * @return string
     * @throws Exception
     */
    protected function getDatabase(): string
    {
        $database = config('database.database');

        // Check if the database exists
        if (is_string($database) && $this->existsDatabase($database)) {
            return $database;
        }
        throw new Exception("This database {$database} doesn't exists.");
    }

    /**
     * Check if the database exists
     *
     * @param  string $databaseName
     * @return bool
     */
    private function existsDatabase(string &$databaseName): bool
    {
        return $this->db
            ->newQuery()
            ->selectRaw('SCHEMA_NAME')
            ->fromRaw('INFORMATION_SCHEMA.SCHEMATA')
            ->whereRaw("SCHEMA_NAME = '{$databaseName}'")
            ->count();
    }

    /**
     * Get all the tables that need to the optimized
     *
     * @return Collection
     * @throws Exception
     */
    private function getTables(): Collection
    {
        $tableList = collect($this->option('table'));
        if ($tableList->isEmpty()) {
            $tableList = $this->db
                ->newQuery()
                ->selectRaw('TABLE_NAME')
                ->fromRaw('INFORMATION_SCHEMA.TABLES')
                ->whereRaw("TABLE_SCHEMA = '{$this->getDatabase()}'")
                ->get();
            return $tableList->pluck('TABLE_NAME');
        }
        // Check if the table exists
        if ($this->existsTables($tableList)) {
            return $tableList;
        }
        throw new Exception("One or more tables provided doesn't exists.");
    }

    /**
     * Check if the table exists
     *
     * @param Collection $tables
     * @return bool
     * @throws Exception
     */
    private function existsTables(Collection $tables): bool
    {
        return $this->db
                ->newQuery()
                ->fromRaw('INFORMATION_SCHEMA.TABLES')
                ->whereRaw("TABLE_SCHEMA = '{$this->getDatabase()}'")
                ->whereRaw('TABLE_NAME IN (\'' . $tables->implode("','") . '\')')
                ->count() == $tables->count();
    }

    /**
     * Optimize the table
     *
     * @param  string $table
     * @return void
     */
    protected function optimize(string $table): void
    {
        $result = $this->db->getConnection()->select("OPTIMIZE TABLE `{$table}`");
        if (collect($result)->pluck('Msg_text')->contains('OK')) {
            $this->progress->advance();
        }
    }
}
