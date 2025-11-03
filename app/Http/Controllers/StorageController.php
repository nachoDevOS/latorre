<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManagerStatic as Image;

class StorageController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // public function store_image1($file, $folder, $size = 1200){

    //     try {
    //         Storage::makeDirectory($folder.'/'.date('F').date('Y'));
    //         $base_name = Str::random(20).'day'.date('d').date('a');

    //         // imagen normal
    //         $extension = 'avif'/* $file->getClientOriginalExtension()*/;
    //         $filename = $base_name.'.'.$extension;
    //         $path =  $folder.'/'.date('F').date('Y').'/'.$filename;
    //         $image_resize = Image::make($file->getRealPath())->orientate();
    //         $image_resize->resize($size, null, function ($constraint) {
    //             $constraint->aspectRatio();
    //         });
    //         Storage::put($path, $image_resize->encode('avif', 80));

    //         $original = Image::make($file->getRealPath())->orientate();
    //         // imagen banner
    //         $filename_banner = $base_name.'-banner.'.$extension;
    //         $image_resize = $original;
    //         $image_resize->resize(900, null, function ($constraint) {
    //             $constraint->aspectRatio();
    //         });
    //         $path_banner = "$folder/".date('F').date('Y').'/'.$filename_banner;
    //         Storage::put($path_banner, $image_resize->encode('avif', 80));

    //         // imagen medium
    //         $filename_medium = $base_name.'-medium.'.$extension;
    //         $image_resize = $original;
    //         $image_resize->resize(600, null, function ($constraint) {
    //             $constraint->aspectRatio();
    //         });
    //         $path_medium = "$folder/".date('F').date('Y').'/'.$filename_medium;
    //         Storage::put($path_medium, $image_resize->encode('avif', 80));

    //         // imagen small
    //         $filename_small = $base_name.'-small.'.$extension;
    //         $image_resize = $original;
    //         $image_resize->resize(256, null, function ($constraint) {
    //             $constraint->aspectRatio();
    //         });
    //         $path_small = "$folder/".date('F').date('Y').'/'.$filename_small;
    //         Storage::put($path_small, $image_resize->encode('avif', 80));

    //         // imagen cropped
    //         $filename_cropped = $base_name.'-cropped.'.$extension;
    //         $image_resize = $original;
    //         $image_resize->resize(null, 300, function ($constraint) {
    //             $constraint->aspectRatio();
    //         });
    //         $image_resize->resizeCanvas(300, 300);
    //         $path_cropped = "$folder/".date('F').date('Y').'/'.$filename_cropped;
    //         Storage::put($path_cropped, $image_resize->encode('avif', 80));

    //         if(env('FILESYSTEM_DRIVER') == 's3'){
    //             return env('AWS_ENDPOINT').'/'.env('AWS_BUCKET').'/'.env('AWS_ROOT').'/'.$path;    
    //         }
    //         return $path;
    //     } catch (\Throwable $th) {
    //         \Log::error('Error al guardar la imagen: ' . $th->getMessage());
    //         return null;
    //     }
    // }

    public function store_image($file, $folder, $size = 1200)
    {
        try {
            $directory = $folder.'/'.date('F').date('Y');
            Storage::disk('public')->makeDirectory($directory);
    
            $base_name = Str::random(20).'day'.date('d').date('a');
    
            // Guardar el archivo original
            // $original_extension = $file->getClientOriginalExtension();
            // $original_filename = $base_name . '-original.' . $original_extension;
            // $file->storeAs($directory, $original_filename, 'public');

            // Cargar la imagen original UNA sola vez
            $original = Image::make($file->getRealPath())->orientate();
    
            // Procesar directamente las versiones optimizadas (AVIF/WebP)
            $paths = $this->processImageVersions($original, $directory, $base_name);
    
            // Liberar memoria
            $original->destroy();
    
            if (env('FILESYSTEM_DRIVER') == 's3') {
                return env('AWS_ENDPOINT').'/'.env('AWS_BUCKET').'/'.env('AWS_ROOT').'/'.$paths['normal'];
            }
    
            // Devolvemos la ruta de la imagen principal en formato AVIF
            return $paths['normal'];
    
        } catch (\Throwable $th) {
            \Log::error('Error al guardar la imagen: ' . $th->getMessage());
            return null;
        }
    }
    
    private function processImageVersions($original, $directory, $base_name)
    {
        $versions = [
            // Versión principal y más grande en AVIF para máxima compresión.
            'normal' => [
                'suffix' => '',
                'width' => 1200,
                'height' => null,
                'format' => 'avif',
                'quality' => 75 // Reducimos un poco la calidad para acelerar la codificación
            ],
            // El banner también es importante, lo mantenemos en AVIF.
            'banner' => [
                'suffix' => '-banner',
                'width' => 900,
                'height' => null,
                'format' => 'avif',
                'quality' => 75
            ],
            // Para tamaños intermedios, WebP es una excelente alternativa: rápido y ligero.
            'medium' => [
                'suffix' => '-medium',
                'width' => 600,
                'height' => null,
                'format' => 'webp',
                'quality' => 80
            ],
            // La miniatura pequeña también en WebP.
            'small' => [
                'suffix' => '-small',
                'width' => 256,
                'height' => null,
                'format' => 'webp',
                'quality' => 80
            ],
            // La versión recortada (cropped) también en WebP por velocidad.
            'cropped' => [
                'suffix' => '-cropped',
                'width' => 300,
                'height' => 300,
                'crop' => true,
                'format' => 'webp',
                'quality' => 80
            ]
        ];
    
        $paths = [];
    
        foreach ($versions as $key => $config) {
            $filename = $base_name . $config['suffix'] . '.' . $config['format'];
            $path = $directory . '/' . $filename;
    
            $image = clone $original;
    
            if (isset($config['crop']) && $config['crop']) {
                $image->resize(null, $config['height'], function ($constraint) {
                    $constraint->aspectRatio();
                });
                $image->resizeCanvas($config['width'], $config['height']);
            } else {
                $image->resize($config['width'], $config['height'], function ($constraint) {
                    $constraint->aspectRatio();
                });
            }
    
            Storage::disk('public')->put($path, $image->encode($config['format'], $config['quality']));
            $image->destroy();
    
            $paths[$key] = $path;
        }
    
        return $paths;
    }

    

    
}
