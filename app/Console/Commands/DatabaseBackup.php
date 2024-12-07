<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class DatabaseBackup extends Command
{
    protected $signature = 'db:backup';
    protected $description = 'Crear un backup de la base de datos';

    public function handle()
    {
        $this->info('Iniciando backup...');

        // Obtener fecha y hora para el nombre del archivo
        $filename = 'backup_' . Carbon::now()->format('Y_m_d_H_i_s') . '.sql';

        // Configuración de la base de datos
        $database = config('database.connections.mysql.database');
        $user = config('database.connections.mysql.username');
        $password = config('database.connections.mysql.password');

        // Ruta a mysqldump en Laragon
        $mysqldump = 'C:\laragon\bin\mysql\mysql-8.0.30-winx64\bin\mysqldump.exe';

        // Comando para el backup
        $command = "\"{$mysqldump}\" -u {$user} -p{$password} {$database} > " . storage_path("app/backups/{$filename}");

        // Crear directorio si no existe
        if (!Storage::exists('backups')) {
            Storage::makeDirectory('backups');
        }

        // Ejecutar comando
        $output = [];
        $resultCode = 0;
        exec($command, $output, $resultCode);

        if ($resultCode !== 0) {
            $this->error('Error al crear el backup: ' . implode("\n", $output));
            return 1;
        }

        $this->info('Backup creado exitosamente: ' . $filename);
        return 0;
    }
}