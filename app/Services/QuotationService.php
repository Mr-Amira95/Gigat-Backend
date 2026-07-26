<?php

namespace App\Services;

use App\Models\Quotation;
use App\Repositories\Interfaces\QuotationRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class QuotationService
{
    protected $quotationRepository;

    public function __construct(QuotationRepositoryInterface $quotationRepository)
    {
        $this->quotationRepository = $quotationRepository;
    }
    public function index($params = [])
    {
        return $this->quotationRepository->getAll($params);
    }
    public function store(array $data)
    {
        return $this->quotationRepository->store($data);
    }

    public function getAllQuotations($perPage = null)
    {
        return $this->quotationRepository->findAll($perPage);
    }
    public function getByUserId($perPage = null)
    {
        return $this->quotationRepository->getByUserId($perPage);
    }

    public function getQuotationById(int $id)
    {
        return $this->quotationRepository->findById($id);
    }

    // Create a new quotation comment
    public function createQuotationComment(array $data)
    {
        return $this->quotationRepository->createQuotationComment($data);
    }

    // Get all comments for a specific quotation
    public function getCommentsByQuotationId(int $quotationId)
    {
        return $this->quotationRepository->getCommentsByQuotationId($quotationId);
    }
    public function getQuotationDetails($id)
    {
        return $this->quotationRepository->getQuotationDetails($id);
    }

    public function getQuotationsForFreelancer($perPage = 10)
    {
        $user = Auth::user();
        $query = Quotation::with('user.profession.translation');

        $categoryIds = $user->categories1->pluck('id')->toArray();
        $query->whereHas('subCategory', function ($q) use ($categoryIds) {
            $q->whereIn('category_id', $categoryIds);
        });
            // ->whereDoesntHave('quotationComments', function ($q) use ($user) {
            //     $q->where('user_id', $user->id); // or 'user_id' if you're using that
            // });

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function getQuotationsForFreelancerWithComment($perPage = 10)
    {
        $user = Auth::user();

        $query = Quotation::with([
            'user',
            'quotationComments.user',  // عشان يحمّل الـ user مع كل comment
            'user.profession.translation'
        ]);

        $categoryIds = $user->categories1->pluck('id')->toArray();

        $query->whereHas('subCategory', function ($q) use ($categoryIds) {
            $q->whereIn('category_id', $categoryIds);
        });

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }
    public function delete(int $id): bool
    {
        return $this->quotationRepository->delete($id);
    }
}
