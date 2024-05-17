<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use League\Csv\Writer;
use League\Csv\CannotInsertRecord;
use App\Http\Controllers\Controller;

class FileController extends Controller
{
    public function guardarCarpeta()
    {
        $directoryUrl = 'https://sedcolombia.com.co/Imagenes_Vtex/';

        try {
            // Obtener el contenido del directorio remoto
            $response = Http::get($directoryUrl);

            if ($response->successful()) {
                $fileNames = $this->parseDirectoryListing($response->body());

                // Guardar los nombres de archivo en un archivo CSV
                $this->saveToCsv($fileNames);

                return response()->json([
                    'status' => 'success',
                    'message' => 'Archivo CSV creado exitosamente.',
                ], 200);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Error al obtener el contenido del directorio remoto.',
                ], $response->status());
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error al procesar la solicitud.',
            ], 500);
        }
    }

    private function parseDirectoryListing($directoryContent)
    {
        // Analizar el contenido del directorio para obtener los nombres de archivo
        // Esto depende del formato del contenido, podría necesitar ajustes según el caso específico
        $fileNames = [];

        preg_match_all('/href="([^"]+)"/', $directoryContent, $matches);

        if (isset($matches[1])) {
            foreach ($matches[1] as $match) {
                // Ignorar los enlaces al directorio padre y directorios
                if ($match !== '../' && !preg_match('/\/$/', $match)) {
                    $fileNames[] = $match;
                }
            }
        }

        return $fileNames;
    }

    private function saveToCsv($fileNames)
    {
        $csv = Writer::createFromPath(storage_path('app/files/directory_files.csv'), 'w+');
        $csv->insertOne(['Nombre de Archivo']);

        try {
            foreach ($fileNames as $fileName) {
                $csv->insertOne([$fileName]);
            }
        } catch (CannotInsertRecord $e) {
            // Manejar cualquier error al insertar el registro en el archivo CSV
            throw new \Exception("Error al insertar registros en el archivo CSV.");
        }
    }
}
