<?php

namespace App\Http\Controllers;

// use League\Csv\Writer;
// use League\Csv\CannotInsertRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use App\Http\Controllers\Controller;




class FileController extends Controller
{

    public function saveVtexImagesFileName()
    {
        $directoryUrl = config('filesystems.disks.public.public_images','https://sedvirtual.sedcolombia.com.co/stockimages/');

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

    public function folderPublicPath( string $standardPath = ""): string
    {
        $publicPath = public_path();

        // Check if public_path() starts with "/home/" and does not include "/public_html/"
        if (str_starts_with($publicPath, "/home/") && !str_contains($publicPath, "/public_html/")) {
            // Extract the username (next word after "/home/")
            $parts = explode('/', $publicPath);
            $username = $parts[2] ?? '';

            // If username length is 0, set default path
            if (strlen($username) === 0) {
                $publicPath = "/home/public_html";
            }
            else {
                $publicPath = "/home/{$username}/public_html";
            }
        }

        return $publicPath . '/' . trim($standardPath);
    }

    public function saveFile($csvData, $fileName){

        if(!$csvData || !$fileName){
            return [
                'message' => "Error: Data or Filename not found",
                'code' => 404,
            ];
        }

        $standardPath = config('filesystems.disks.public.exported_files','files');
        $filePath = "" .$this->folderPublicPath($standardPath) ."/" .$fileName;
        //log::info("Path. dir = " . $filePath);

        $csvData = mb_convert_encoding($csvData, 'UTF-8', 'auto');

        File::put($filePath, $csvData); // File::put($filePath, $csvData)


        return [
            'message' => "Successfully file creation. Download it!",
            'download_url' => url($standardPath) . "/" .$fileName, 
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
        if (count($csvDataArray) == 0) {
            return [
                'message' => "No records found to export.",
                'code' => 204,
            ];
        }
        // Control default process time
        app(MaintenanceController::class)->setExecutionTime(7000);

        $standardPath = config('filesystems.disks.public.exported_files','files');
        
        $filePath = "" .$this->folderPublicPath ($standardPath) ."/" .$fileName;    


        $fp = fopen($filePath, 'w');

        fputcsv($fp, $csvFields);
        foreach ($csvDataArray as $fields) {
            fputcsv($fp, $fields);
        }

        fclose($fp);

        // Control default process time restored
        app(MaintenanceController::class)->setExecutionTime();

        return [
            'message' => "Generación de archivo CSV Exitosa.",
            'download_url' => url($standardPath) . "/" .$fileName, 
            'code' => 200,
        ];
    }

    public function standardPaths() {
        $data[] = array(
            'app_path' => app_path(),
            'base_path' => base_path(),
            'public_path' => $this->folderPublicPath(),
            'resource_path' => resource_path(),
            'storage_path' => storage_path(),
            'current_url' => url()->current(),
            'exported_files' =>config('filesystems.disks.public.exported_files'),
            'public_images' => config('filesystems.disks.public.public_images'),
            'conexion_epicor' => env('API_PROD_URL'),
        );

        return response()->json($data, 200);
    }
}
