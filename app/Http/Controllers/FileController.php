<?php

namespace App\Http\Controllers;

// use League\Csv\Writer;
// use League\Csv\CannotInsertRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

use App\Http\Controllers\Controller;
use App\Http\Controllers\MaintenanceController;



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
    public function currentFileDirectory($directory)
    {
        /*
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
        */

        /*
        $fileDirectory = base_path("build");
        if (File::exists($fileDirectory)) { // production in public_html directory
            $fileDirectory = base_path($directory);
        }
        elseif (File::exists(base_path("public_html/build"))) { // production in public_html directory explicitly
            $fileDirectory = base_path("public_html/{$directory}");
        }
        else { // development environment
            $fileDirectory = base_path("public/{$directory}" );
        }
        */

        $file_data= [];
        $file_data['app_url'] =config('filesystems.disks.public.public_url');
        $fileDirectory = config('filesystems.disks.public.files_path');

        if ($fileDirectory ==="public_html"){
            $fileDirectory =  base_path();
            $root_place= !strpos($fileDirectory,"public_html")? strrpos($fileDirectory,"/") : strpos($fileDirectory,"/public_html");
            $fileDirectory = substr($fileDirectory,0,$root_place) . "/public_html";
            $asset_directory = $file_data['app_url'] ."/{$directory}";   //$asset_directory= env('APP_URL') ."/{$directory}";
        }
        else{
            $asset_directory= asset($directory);
        }

        // $connector= !strrpos($fileDirectory,"/")? "\\" : "/";
        // $fileDirectory = $fileDirectory . $connector . $directory;
        $fileDirectory = "{$fileDirectory}/{$directory}";

        //Log::info("filesystems = " . config('filesystems.disks.public.files_path') );


        $file_data['directory'] = $fileDirectory;
        $file_data['asset_directory'] = $asset_directory;

        Log::info("files = ", $file_data );
        return $file_data;
    }

    public function saveFile($csvData, $fileName){

        if(!$csvData || !$fileName){
            return [
                'message' => "Error: Data or Filename not found",
                'code' => 404,
            ];
        }

        $standardPath = "files";
        $file_data = $this->currentFileDirectory($standardPath);
        $fileDirectory = $file_data['directory'];
        $filePath = "{$fileDirectory}/{$fileName}";

        $csvData = mb_convert_encoding($csvData, 'UTF-8', 'auto');

        File::put($filePath, $csvData); // File::put($filePath, $csvData)

        // Generate the URL for accessing the file
        $fileUrl = $file_data['asset_directory'] . '/' . $fileName; //asset("{$standardPath}/{$fileName}")

        return [
            'message' => "Successfully file creation. Download it!",
            'download_url' => $fileUrl,
            'code' => 200,
        ];
    }



    public function saveArrayToCSV($csvFields, $csvDataArray, $fileName){
        if(!$csvDataArray || !$fileName){
            return [
                'message' => "Error: Data or Filename not found",
                'code' => 404,
            ];
        }

        // Control default process time
        app(MaintenanceController::class)->setExecutionTime(7000);


        $standardPath = "files";
        $fileDirectory = $this->currentFileDirectory($standardPath);

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

        app(MaintenanceController::class)->setExecutionTime();

        return [
            'message' => "Successfully CSV creation. Download it!",
            'download_url' => $fileUrl,
            'code' => 200,
        ];
    }

    public function standardPaths() {
        $data[] = array(
            'app_path' => app_path(),
            'base_path' => base_path(),
            'public_path' => public_path(),
            'resource_path' => resource_path(),
            'storage_path' => storage_path(),
            'current_url' => url()->current(),
            'url_env' => config('filesystems.disks.public.public_url'),
            'files_path' => config('filesystems.disks.public.files_path'),

        );

        return response()->json($data, 200);
    }
}
