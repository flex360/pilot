<?php

namespace Flex360\Pilot\Http\Controllers\Admin;

use Input;
use Flex360\Pilot\Pilot\Tag;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Flex360\Pilot\Http\Controllers\Controller;

class TagController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $tags = Tag::orderBy('name', 'asc')
            ->get();

        return $tags;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store()
    {
        $validator = Validator::make(request()->all(), [
            'name' => 'required|unique:tags',
        ]);

        if ($validator->fails()) {
            return response('Tag not created because no name given.', 500);
        }

        return Tag::create(request()->only('name'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update($id)
    {
        $validator = Validator::make(request()->all(), [
            'name' => 'required|unique:tags',
        ]);

        if ($validator->fails()) {
            return response('Tag not created because no name given.', 500);
        }

        $tag = Tag::find($id);

        $data = request()->only('name');

        $tag->fill($data);

        $tag->save();

        return $tag;
    }

    /**
     * Remove the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        $tag = Tag::find($id);

        $tag->delete();

        // set success message
        session()->flash('alert-success', 'Tag deleted successfully!');

        return redirect()->route('admin.tag.index');
    }

    public function mergeTags()
    {
        mimic('Merge Tags');

        return view('pilot::admin.mergeTags');
    }

    public function mergeTagsExecute()
    {
        $badTag = Tag::find(request()->badTag);
        $goodTag = Tag::find(request()->goodTag);

        foreach ($badTag->posts()->get() as $post) {
            DB::table('post_tag')->insert(
                ['tag_id' => $goodTag->id, 'post_id' => $post->id,]
            );
        }

        $badTag->delete();

        return redirect()->route('admin.merge.tags')->with(
            'status',
            'Tag "' . $badTag->name . '" merged into "' . $goodTag->name . '"!'
        );
    }
}
