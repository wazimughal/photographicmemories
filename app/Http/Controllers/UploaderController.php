<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Pion\Laravel\ChunkUpload\Exceptions\UploadFailedException;
use Storage;
use Illuminate\Http\UploadedFile;
use Pion\Laravel\ChunkUpload\Exceptions\UploadMissingFileException;
use Pion\Laravel\ChunkUpload\Handler\AbstractHandler;
use Pion\Laravel\ChunkUpload\Handler\HandlerFactory;
use Pion\Laravel\ChunkUpload\Receiver\FileReceiver;

class UploaderController extends Controller
{
 /**
  * Create a new controller instance.
  *
  * @return void
  */
 public function __construct()
 {
     $this->middleware(['auth', 'verified']);
 }

 /**
  * Handles the file upload
  *
  * @param Request $request
  *
  * @return JsonResponse
  *
  * @throws UploadMissingFileException
  * @throws UploadFailedException
  */
 public function upload(Request $request) {  //from web route
   // create the file receiver
   $receiver = new FileReceiver("file", $request, HandlerFactory::classFromRequest($request));

   // check if the upload is success, throw exception or return response you need
   if ($receiver->isUploaded() === false) {
     throw new UploadMissingFileException();
   }

   // receive the file
   $save = $receiver->receive();

   // check if the upload has finished (in chunk mode it will send smaller files)
   if ($save->isFinished()) {
     // save the file and return any response you need, current example uses `move` function. If you are
     // not using move, you need to manually delete the file by unlink($save->getFile()->getPathname())
     return $this->saveFile($save->getFile(), $request);
   }

   // we are in chunk mode, lets send the current progress
   /** @var AbstractHandler $handler */
   $handler = $save->handler();

   return response()->json([
     "done" => $handler->getPercentageDone(),
     'status' => true
   ]);
 }

 /**
  * Saves the file
  *
  * @param UploadedFile $file
  *
  * @return JsonResponse
  */
  protected function saveFile(UploadedFile $file, Request $request) {
    $user_obj = auth()->user();
    $fileName = $this->createFilename($file);

    // Get file mime type
    $mime_original = $file->getMimeType();
    $mime = str_replace('/', '-', $mime_original);

    $folderDATE = $request->dataDATE;
    $booking_id = $request->booking_id;

    $folder  = $folderDATE;
    $filePath = "public/upload/medialibrary/{$user_obj->id}/{$folder}/";
    $finalPath = storage_path("app/".$filePath);

    $fileSize = $file->getSize();
    // move the file name
    $file->move($finalPath, $fileName);

    $url_base = 'storage/upload/medialibrary/'.$user_obj->id."/{$folderDATE}/".$fileName;
    // create txt file
    $myfile = fopen("newfile.txt", "w") or die("Unable to open file!");
    $txt = json_encode($request)."\n";
    $txt .= 'file path:'.($filePath)."\n";
    $txt .= 'booking_id:'.($booking_id)."\n";
    //$txt .= 'file name:'.($fileName)."\n";
    //$txt .= 'file mime_type:'.($mime_type)."\n";
    fwrite($myfile, $txt);
    $txt .= 'file name:'.($fileName)."\n";
    //$txt .= 'file mime_type:'.($mime_type)."\n";
    fwrite($myfile, $txt);
    fclose($myfile);

    return response()->json([
     'path' => $filePath,
     'name' => $fileName,
     'mime_type' => $mime
    ]);
 }

 /**
  * Create unique filename for uploaded file
  * @param UploadedFile $file
  * @return string
  */
  protected function createFilename(UploadedFile $file) {
    $extension = $file->getClientOriginalExtension();
    $filename = str_replace(".".$extension, "", $file->getClientOriginalName()); // Filename without extension

    //delete timestamp from file name
    $temp_arr = explode('_', $filename);
    if ( isset($temp_arr[0]) ) unset($temp_arr[0]);
    $filename = implode('_', $temp_arr);

    //here you can manipulate with file name e.g. HASHED
    return $filename.".".$extension;
  }

 /**
  * Delete uploaded file WEB ROUTE
  * @param Request request
  * @return JsonResponse
  */
  public function delete (Request $request){

    $user_obj = auth()->user();

    $file = $request->filename;

    //delete timestamp from filename
    $temp_arr = explode('_', $file);
    if ( isset($temp_arr[0]) ) unset($temp_arr[0]);
    $file = implode('_', $temp_arr);

    $dir = $request->date;

    $filePath = "public/upload/medialibrary/{$user_obj->id}/{$dir}/";
    $finalPath = storage_path("app/".$filePath);

    if ( unlink($finalPath.$file) ){
      return response()->json([
        'status' => 'ok'
      ], 200);
    }
    else{
      return response()->json([
        'status' => 'error'
      ], 403);
    }
  }

}
