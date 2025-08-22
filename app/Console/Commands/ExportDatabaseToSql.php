<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ExportDatabaseToSql extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:export {--filename= : The name of the output file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export the database to a SQL file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $connection = Config::get('database.default');
        $config = Config::get('database.connections.' . $connection);

        if ($config['driver'] !== 'mysql') {
            $this->error('This command only supports MySQL databases.');
            return 1;
        }

        $dbName = $config['database'];
        $dbUser = $config['username'];
        $dbPassword = $config['password'] ? '-p' . $config['password'] : '';
        $dbHost = $config['host'];
        $dbPort = $config['port'];

        $filename = $this->option('filename') ?: 'database_export_' . date('Y_m_d_His') . '.sql';
        $outputPath = storage_path('app/' . $filename);

        $this->info('Exporting database to ' . $outputPath);

        // Check if mysqldump is available
        exec('mysqldump --version', $output, $returnVar);
        
        if ($returnVar !== 0) {
            $this->error('mysqldump command not available. Make sure MySQL is installed and in your PATH.');
            return 1;
        }

        // Construct the mysqldump command
        $command = sprintf(
            'mysqldump -h %s -P %s -u %s %s %s > %s',
            escapeshellarg($dbHost),
            escapeshellarg($dbPort),
            escapeshellarg($dbUser),
            $dbPassword,
            escapeshellarg($dbName),
            escapeshellarg($outputPath)
        );

        $this->info('Running export command...');
        exec($command, $output, $returnVar);

        if ($returnVar !== 0) {
            $this->error('Failed to export database. Error code: ' . $returnVar);
            return 1;
        }

        $this->info('Database exported successfully to: ' . $outputPath);
        $this->info('File size: ' . round(filesize($outputPath) / 1024, 2) . ' KB');
        
        return 0;
    }
}
