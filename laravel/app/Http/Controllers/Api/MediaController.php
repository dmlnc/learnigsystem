<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Http\Resources\ThumbnailResource;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaController extends Controller
{
    
    public function storeMedia(Request $request, $model, $default_collection_name)
    {
        $model_id = 0;
        if ($request->hasHeader('model_id')) {
            $model_id = $request->header('model_id');
        }
        $collection_name = $default_collection_name;
        if ($request->hasHeader('collection_name')) {
            $collection_name = $request->header('collection_name');
        }

        $model->id     = $model_id;
        $model->exists = true;
        $media         = $model->addMedia(request()->file('file'))->toMediaCollection($collection_name);

        return new ThumbnailResource($media);
    }

    public function deleteMedia(Request $request, $media_id)
    {
        $media = Media::query()->where('id', $media_id)->firstOrFail();
        Storage::deleteDirectory('public/' . $media_id);
        $media->delete();
        return response()->noContent(Response::HTTP_ACCEPTED);
    }

    public function syncImagesFromHtml($model, $html, $prefix = 'quill')
    {

        preg_match_all('/<img.*?src="(data:image\/.*?;base64,.*?)".*?>/i', $html, $base64Matches);

        // Find image URLs
        preg_match_all('/<img.*?src="([^"]+)".*?>/i', $html, $urlMatches);

        // Combine the two sets of matches
        $matches = array_merge($base64Matches[1], $urlMatches[1]);

        // Get all media items in the specified collection for the given model
        $mediaItems = $model->getMedia($prefix.'_images');

        foreach ($mediaItems as $media) {
                // Check if the media item URL is present in the HTML
            if (!in_array($media->getUrl(), $matches)) {
                // Delete the media item if it is not present in the HTML
                $model->deleteMedia($media['id']);
            }
            else{
                $index = array_search($media->getUrl(), $matches);
                if ($index !== false) {
                    unset($matches[$index]);
                }
            }
        }


        foreach ($matches as $match) {
            // If the match is a base64-encoded image
            if (strpos($match, 'data:image/') === 0) {
                $imageData = $match;
                $imageType = explode('/', explode(';', $match)[0])[1];
                $fileName = $prefix.'-' . uniqid() . '.' . $imageType;
             
                $media = $model->addMediaFromBase64($imageData)->usingFileName($fileName)->toMediaCollection($prefix.'_images');
                $html = str_replace($match, $media->getUrl(), $html); 
            }
            // If the match is an image URL
            else {
                $media = $model->getFirstMediaByUrl($match);
                // If the media item doesn't exist yet, add it to the collection
                if (!$media) {
                    $media = $model->addMediaFromUrl($match)->toMediaCollection($prefix.'_images');
                }
                $html = str_replace($match, $media->getUrl(), $html);
            }
        }

        // foreach ($matches[1] as $match) {
        //     $imageData = $match;
        //     $imageType = explode('/', explode(';', $match)[0])[1];
        //     $fileName = $prefix.'-' . uniqid() . '.' . $imageType;
         
        //     $media = $model->addMediaFromBase64($imageData)->usingFileName($fileName)->toMediaCollection($prefix.'_images');

        //     $html = str_replace($match, $media->getUrl(), $html); 
        // }



        return $html;
    }

    function syncMedia(array $media_ids, int $model_id): void
    {
        Media::whereIn('id', $media_ids)
            ->where('model_id', 0)
            ->update(['model_id' => $model_id]);
    }

}
