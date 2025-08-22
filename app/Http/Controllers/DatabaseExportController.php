<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class DatabaseExportController extends Controller
{
    /**
     * Display the database export form.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('database.export');
    }

    /**
     * Export the database to SQL.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function export(Request $request)
    {
        $connection = Config::get('database.default');
        $config = Config::get('database.connections.' . $connection);

        if ($config['driver'] !== 'mysql') {
            return back()->with('error', 'This feature only supports MySQL databases.');
        }

        $filename = 'database_export_' . date('Y_m_d_His') . '.sql';
        $outputPath = storage_path('app/' . $filename);

        try {
            // Run mysqldump command to export database
            $dbName = $config['database'];
            $dbUser = $config['username'];
            $dbPassword = $config['password'];
            $dbHost = $config['host'];
            $dbPort = $config['port'];

            $command = [
                'mysqldump',
                '-h', $dbHost,
                '-P', $dbPort,
                '-u', $dbUser
            ];

            if (!empty($dbPassword)) {
                $command[] = '-p' . $dbPassword;
            }

            $command[] = $dbName;

            $process = new Process($command);
            $process->run();

            if (!$process->isSuccessful()) {
                throw new ProcessFailedException($process);
            }

            Storage::put($filename, $process->getOutput());

            // Download the file and then delete it
            return response()->download(storage_path('app/' . $filename))->deleteFileAfterSend();
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to export database: ' . $e->getMessage());
        }
    }
}
