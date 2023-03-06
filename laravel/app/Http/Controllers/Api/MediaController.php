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


    function syncMedia(array $media_ids, int $model_id): void
    {
        Media::whereIn('id', $media_ids)
            ->where('model_id', 0)
            ->update(['model_id' => $model_id]);
    }

}
