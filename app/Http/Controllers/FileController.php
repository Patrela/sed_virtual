<?php

namespace App\Http\Controllers;

use League\Csv\Writer;
use Illuminate\Http\Request;
use League\Csv\CannotInsertRecord;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;


class FileController extends Controller
{
    public function saveVtexImagesFileName()
    {
        $directoryUrl = 'https://sedcolombia.com.co/stockimages/';

        try {
            // Obtener el contenido del directorio remoto
            $response = Http::get($directoryUrl);

            if ($response->successful()) {
                $fileNameList = $this->parseDirectoryListing($response->body());

                $result= $this->saveCsv($fileNameList, "vtex_images.csv");
                if (!is_array($result) || !isset($result['code']) || !isset($result['message'])) {
                    return response()->json([
                        'message' => 'Internal Server Error - Invalid response from saveCsv method.',
                        'code' => 500,
                    ], 500);
                }
                return response()->json($result, $result['code']);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Error fetching the directory',
                ], $response->status());
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Internal Server Error',
                'code' => 500,
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


    public function exportCsv(Request $request, string $name)
    {

        if(!$name){
            return response()->json([
                'message' => 'Unauthorized - Access is denied due to invalid credentials.',
                'code' => 401,
            ], 401);
        }
        $name = preg_split('/\s+/', trim($name))[0];
        $fileName =  trim($name) ."000.csv";
        $csvData = $request->input('prod_csv_text');
        if (strpos($csvData, "\n") === false) {
            $csvData= str_replace("dimension_weight", "dimension_weight\r\n", $csvData);
            //Log::info("Enter excluded");
        }
        $result = $this->saveCsv($csvData, $fileName);
        if (!is_array($result) || !isset($result['code']) || !isset($result['message'])) {
            return response()->json([
                'message' => 'Internal Server Error - Invalid response from saveCsv method.',
                'code' => 500,
            ], 500);
        }
        return response()->json($result, $result['code']);
    }


    private function saveCsv($csvData, $fileName){
        if(!$csvData || !$fileName){
            return [
                'message' => "Error Data or Filename not found",
                'code' => 404,
            ];
        }
        $standardPath = "files";
        if (!File::exists(public_path( $standardPath))) {
            File::makeDirectory(public_path( $standardPath), 0755, true);
        }
        $standardPath =  $standardPath . "/";
        $filePath = public_path( $standardPath . $fileName);

        $csvData = mb_convert_encoding($csvData, 'UTF-8', 'auto');
        File::put($filePath, $csvData);

        $fileUrl = asset($standardPath . $fileName);
        return [
            'message' => "Successfully CSV creation. ¡Download it! ",
            'download_url' => $fileUrl,
            'code' => 200,
        ];
    }

}
