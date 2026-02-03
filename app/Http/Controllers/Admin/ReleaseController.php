<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ReleaseRequest;
use App\Models\User;
use App\Services\NoticeService;
use App\Services\ReleaseService;
use Illuminate\Http\Request;
use Exception;

class ReleaseController extends Controller
{
    protected $releaseService;
    protected $noticeService;



    public function __construct(ReleaseService $releaseService, NoticeService $noticeService)
    {
        $this->releaseService = $releaseService;
        $this->noticeService    = $noticeService;
    }

    public function index()
    {
        $releases = $this->releaseService->getAllForAdmin();
        return view('pages.releases.index', compact('releases'));
    }

    public function create()
    {
        return view('pages.releases.create');
    }

    public function store(ReleaseRequest $request)
    {
        try {

            $release = $this->releaseService->create($request->validated());

            // Send notification to all users
            $userIds = User::pluck('id')->toArray(); // send to all users (admins/clients/freelancers, adjust if needed)

            $titles = [
                'en' => __('messages.new_update_released', [], 'en'),
                'ar' => __('messages.new_update_released', [], 'ar'),
            ];

            $messages = [
                'en' => __('messages.new_update_message', [], 'en'),
                'ar' => __('messages.new_update_message', [], 'ar'),
            ];

            $this->noticeService->send(
                $userIds,
                $titles,
                $messages,
                'release',
                $release->id,
                true
            );

            return redirect()->route('admin.releases.index')
                ->with('success', __('release_created_successfully'));
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }
    public function show($id)
    {
        try {
            $release = $this->releaseService->getByIdForAdmin($id);

            if (!$release) {
                return redirect()->route('admin.releases.index')
                    ->with('error', __('release_not_found'));
            }

            return view('pages.releases.show', compact('release'));
        } catch (\Exception $e) {
            return redirect()->route('admin.releases.index')
                ->with('error', $e->getMessage());
        }
    }

    public function edit($id)
    {
        $release = $this->releaseService->getByIdForAdmin($id);

        if (!$release) {
            return redirect()->route('admin.releases.index')
                ->with('error', __('release_unavailable'));
        }

        return view('pages.releases.edit', compact('release'));
    }

    public function update(ReleaseRequest $request, $id)
    {
        try {
            $validated = $request->validated();

            $this->releaseService->update($id, $validated);

            return redirect()->route('admin.releases.index')
                ->with('success', __('release_updated_successfully'));
        } catch (Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $this->releaseService->delete($id);

            return redirect()->route('admin.releases.index')
                ->with('success', __('release_deleted_successfully'));
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
    public function updateActivation($id)
    {
        try {
            $this->releaseService->updateActivation($id);

            return response()->json([
                'status' => true,
                'message' => __('release_status_updated_successfully'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
