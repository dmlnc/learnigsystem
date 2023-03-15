<?php

namespace App\Http\Controllers\Api;
use App\Models\Academy;
use App\Models\Faculty;
use App\Models\Post;
use App\Http\Controllers\Controller;
use App\Http\Resources\PostResourсe;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Http\Requests\PostRequest;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\PostFullResourсe;
use App\Http\Resources\PostStudyResourсe;
use Illuminate\Http\Response;

class PostApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();

        return PostResourсe::collection(Post::where('company_id', $user->company_id)->orderByDesc('created_at')->get());
    }

    public function indexStudy()
    {
        $user = auth()->user();

        $posts = Post::where(function ($query) use ($user) {
                $query->whereHas('company', function ($query) use ($user) {
                    return $query->where('id', $user->company_id);
                })
                ->orWhereHas('faculty', function ($query) use ($user) {
                    return $query->whereIn('id', $user->faculties->pluck('id')->toArray());
                })
                ->orWhereHas('academy', function ($query) use ($user) {
                    return $query->whereIn('id', $user->faculties()->with('academy')->get()->pluck('academy.id'));
                });
                // ->orWhereHasMorph('postable', [Faculty::class], function ($query, $type) use ($user) {
                    
                // })
                // ->orWhereHasMorph('postable', [Academy::class], function ($query, $type) use ($user) {
                //     $query->whereIn('id', $user->faculties()->with('academy')->get()->pluck('academy.id'));
                // });
            })
        ->orderByDesc('created_at')
        ->paginate(10);




        return PostStudyResourсe::collection($posts);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $company_id = auth()->user()->company_id;
        if($request['postable_type'] == 'App\Models\Company'){
            $request['postable_id'] = $company_id;
        }
        $validatedData = $request->validate([
            'postable_type' => 'required',
            'postable_id' => 'required',
            'title' => 'required|string|max:255',
            'text' => 'string',
        ]);
        $validatedData['company_id'] = $company_id;
        $validatedData['user_id'] = auth()->user()->id;

        $post = Post::create($validatedData);

        (new MediaController)->syncMedia($request->input('images', []), $post->id);

        return new PostFullResourсe($post);
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
        return new PostFullResourсe($post);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Post $post)
    {
        //
        $company_id = auth()->user()->company_id;
        if($request['postable_type'] == 'App\Models\Company'){
            $request['postable_id'] = $company_id;
        }
        $validated = $request->validate([
            'postable_id'=>'required',
            'postable_type'=>'required',
            'title' => 'required|string|max:255',
            'text' => 'string',
        ]);
        $validated['text'] = (new MediaController)->syncImagesFromHtml($post, $validated['text']);

        $post->update($validated);
        (new MediaController)->syncMedia($request->input('images', []), $post->id);

        return new PostFullResourсe($post);
    }



    public function create()
    {
        $user = auth()->user();
        $academies = Academy::where('company_id', $user->company_id);
        return response([
            'meta' => [
                'academies' => $academies->get(['id', 'name']),
                'faculties' => Faculty::whereIn('academy_id', $academies->pluck('id')->toArray())->get(['id', 'name'])
            ],
        ]);
    }

    public function destroy(Post $post)
    {
        $post->delete();

        return response()->json(null, 204);
    }
    public function storeMedia(Request $request)
    {
        $model = new Post();
        return  (new MediaController)->storeMedia($request, $model, 'post_thumbnail');

        return response()->json($media, Response::HTTP_CREATED);
    }
}
