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
        $directoryUrl = 'https://sedvirtual.sedcolombia.com.co/stockimages/';

        try {
            // Obtener el contenido del directorio remoto
            $response = Http::get($directoryUrl);

            if ($response->successful()) {
                $fileNameList = $this->parseDirectoryListing($response->body());

                $result= $this->saveFile($fileNameList, "vtex_images.csv");
                if (!is_array($result) || !isset($result['code']) || !isset($result['message'])) {
                    return response()->json([
                        'message' => 'Internal Server Error - Invalid response from saveFile method.',
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
        $result = $this->saveFile($csvData, $fileName);
        if (!is_array($result) || !isset($result['code']) || !isset($result['message'])) {
            return response()->json([
                'message' => 'Internal Server Error - Invalid response from saveFile method.',
                'code' => 500,
            ], 500);
        }
        return response()->json($result, $result['code']);
    }

    public function saveFile($csvData, $fileName){
        if(!$csvData || !$fileName){
            return [
                'message' => "Error: Data or Filename not found",
                'code' => 404,
            ];
        }

        $standardPath = "files";
        $fileDirectory = base_path("public/{$standardPath}");
        // Create the directory if it doesn't exist, or, redirect to /public_html
        if (!File::exists($fileDirectory)) {
             ////$fileDirectory =public_path( $standardPath);
            $fileDirectory = base_path();
            $startRoot= strrpos($fileDirectory,"/");
            $fileDirectory =  substr($fileDirectory,0,$startRoot) . "/public_html/{$standardPath}";
            //File::makeDirectory($fileDirectory, 0755, true);
        }
        Log::info("paths...", ["fileDirectory= ",$fileDirectory, " base_path= ", base_path(), " storage_path= ", storage_path(), " app_path= ", app_path(), " public_path= ", public_path() , " resource_path= ", resource_path()]);

        $filePath = "{$fileDirectory}/{$fileName}";

        $csvData = mb_convert_encoding($csvData, 'UTF-8', 'auto');

        File::put($filePath, $csvData);

        // Generate the URL for accessing the file
        $fileUrl = asset("{$standardPath}/{$fileName}");

        return [
            'message' => "Successfully file creation. Download it!",
            'download_url' => $fileUrl,
            'code' => 200,
        ];
    }

    public function setExecutionTime($default_time = 60){
        ini_set('max_execution_time', $default_time);
    }

    public function saveArrayToCSV($csvFields, $csvDataArray, $fileName){
        if(!$csvDataArray || !$fileName){
            return [
                'message' => "Error: Data or Filename not found",
                'code' => 404,
            ];
        }

        // Control default process time
        $this->setExecutionTime(7000);


        $standardPath = "files";
        $fileDirectory = base_path("public/{$standardPath}");
        // Create the directory if it doesn't exist, or, redirect to /public_html
        if (!File::exists($fileDirectory)) {
             ////$fileDirectory =public_path( $standardPath);
            $fileDirectory = base_path();
            $startRoot= strrpos($fileDirectory,"/");
            $fileDirectory =  substr($fileDirectory,0,$startRoot) . "/public_html/{$standardPath}";
            //File::makeDirectory($fileDirectory, 0755, true);
        }
        Log::info("paths...", ["fileDirectory= ",$fileDirectory, " base_path= ", base_path(), " storage_path= ", storage_path(), " app_path= ", app_path(), " public_path= ", public_path() , " resource_path= ", resource_path()]);

        $filePath = "{$fileDirectory}/{$fileName}";

        //$csvDataArray = mb_convert_encoding($csvDataArray, 'UTF-8', 'auto');

        //File::put($filePath, $csvDataArray);

        $fp = fopen($filePath, 'w');

        fputcsv($fp, $csvFields);
        foreach ($csvDataArray as $fields) {
            fputcsv($fp, $fields);
        }

        fclose($fp);

        // Generate the URL for accessing the file
        $fileUrl = asset("{$standardPath}/{$fileName}");

        // Control default process time restored
        $this->setExecutionTime();

        return [
            'message' => "Successfully CSV creation. Download it!",
            'download_url' => $fileUrl,
            'code' => 200,
        ];
    }

}
