<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\RequestService;
use Illuminate\Http\Request;

class RequestController extends Controller
{
    protected $requestService;

    public function __construct(RequestService $requestService)
    {
        $this->requestService = $requestService;
    }

    public function index(Request $request)
    {
        $filters = [
            'status' => $request->query('status'),
            'created_date_from' => $request->query('created_date_from'),
            'created_date_to' => $request->query('created_date_to'),
        ];
        $requests = $this->requestService->getAll($filters);
        return view('pages.requests.index', compact('requests'));
    }

    public function show($id)
    {
        $request = $this->requestService->getRequestDetails($id);
        return view('pages.requests.edit', compact('request'));
    }
}
