<?php

namespace App\Http\Controllers\Freelancer;

use App\Models\Category;
use App\Models\SubCategory;
use App\Services\TagService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TagRequest;
use App\Http\Requests\Admin\UpdateTagRequest;
use App\Models\Tag;
use Illuminate\Support\Facades\Log;

class TagController extends Controller
{
    protected $tagService;

    public function __construct(TagService $tagService)
    {
        $this->tagService = $tagService;
    }

    public function index()
    {
        $categories = Category::get();
        $params = request()->all();
        // $tags = $this->tagService->getAllTags($params);
        // dd($categories,$params,$tags);

        if (empty($params)) {
            $tags = Tag::with(['translation', 'subCategory.translation'])->get();
        } else {
            $tags = $this->tagService->getAllTags($params);
        }

        return view('pages.tags.index', compact('tags', 'categories'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('pages.tags.create', compact('categories'));
    }

    public function store(TagRequest $request)
    {
        try {
            $this->tagService->createTag($request->validated());
            return redirect()->route('tags.index')
                ->with('success', __('tag_created_successfully'));
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }



    public function edit(int $id)
    {
        try {
            $tag = $this->tagService->getTagById($id);
            $categories = Category::all();
            $subcategories = SubCategory::all();
            return view('pages.tags.edit', compact('tag', 'categories', 'subcategories'));
        } catch (\Exception $e) {
            return redirect()->route('tags.index')
                ->with('error', __('something_went_wrong'));
        }
    }

    public function update(UpdateTagRequest $request, int $id)
    {
        try {
            $this->tagService->updateTag($id, $request->validated());
            return redirect()->route('tags.index')
                ->with('success', __('tag_updated_successfully'));
        } catch (\Exception $e) {
            return back()->with('error', __('something_went_wrong'))->withInput();
        }
    }

    public function destroy(int $id)
    {
        try {
            $this->tagService->deleteTag($id);
            return redirect()->route('tags.index')
                ->with('success', __('tag_deleted_successfully'));
        } catch (\Exception $e) {
            return back()->with('error_message', $e->getMessage());
        }
    }
}
