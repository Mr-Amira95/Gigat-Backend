<?php

namespace App\Repositories\Eloquents;

use App\Models\Quotation;
use App\Models\QuotationComment;
use App\Repositories\Interfaces\QuotationRepositoryInterface;
use App\Traits\PaginateTrait;
use App\Utilities\GoogleTranslator;
use Illuminate\Support\Facades\Auth;

class QuotationRepository implements QuotationRepositoryInterface
{
    use PaginateTrait;
    protected $model;
    protected $quotationComment;
    protected $googleTranslator;


    public function __construct(Quotation $quotation, QuotationComment $quotationComment, GoogleTranslator $googleTranslator)
    {
        $this->model = $quotation;
        $this->quotationComment = $quotationComment;
        $this->googleTranslator = $googleTranslator;
    }
    public function getAll($params = [])
    {
        $query = $this->model->with('user.profession')->orderBy('id', 'desc');

        if (!empty($params['created_date_from'])) {
            $query->whereDate('created_at', '>=', $params['created_date_from']);
        }

        if (!empty($params['created_date_to'])) {
            $query->whereDate('created_at', '<=', $params['created_date_to']);
        }

        return $query->get();
    }
    public function store(array $data)
    {
        // return $this->model->create($data);

        $quotation = $this->model->create([
            'sub_category_id' => $data['sub_category_id'],
            'price'           => $data['price'],
            'delivery_day'    => $data['delivery_day'],
            'revisions'       => $data['revisions'],
            'source_file'     => $data['source_file'] ?? null,
            'user_id'         => $data['user_id'],
            'status'          => $data['status'] ?? 'open',
        ]);

        // Handle translations
        $titleTranslations = $this->googleTranslator->translateForStorage($data['title']);
        $descTranslations  = !empty($data['description'])
            ? $this->googleTranslator->translateForStorage($data['description'])
            : ['en' => null, 'ar' => null];

        foreach (['en', 'ar'] as $lang) {
            $quotation->translations()->create([
                'language'    => $lang,
                'title'       => $titleTranslations[$lang],
                'description' => $descTranslations[$lang],
            ]);
        }
        return $quotation->load('translation');
    }

    public function findAll($perPage = null)
    {
        $query = $this->model->with('user.profession');
        return $this->paginate($query, $perPage);
    }

    public function getByUserId($perPage = null)
    {
        $query = $this->model->with('user.profession')->where('user_id', Auth::id());
        return $this->paginate($query, $perPage);
    }

    public function findById(int $id)
    {
        return $this->model->with('attachments')->find($id);
    }

    public function createQuotationComment(array $data)
    {
        // return $this->quotationComment->create($data);
        $comment = $this->quotationComment->create([
            'user_id'      => $data['user_id'],
            'quotation_id' => $data['quotation_id'],
        ]);

        $translations = $this->googleTranslator->translateForStorage($data['comment']);

        foreach (['en', 'ar'] as $lang) {
            $comment->translations()->create([
                'language' => $lang,
                'comment'  => $translations[$lang],
            ]);
        }

        return $comment->load('translation');
    }

    public function getCommentsByQuotationId(int $quotationId)
    {
        return $this->quotationComment->where('quotation_id', $quotationId)->with(['quotation', 'user.profession'])->get();
    }
    public function getQuotationDetails($id)
    {
        return $this->model->with(['quotationComments', 'user.profession', 'quotationComments.user', 'attachments'])->findOrFail($id);
    }
    public function delete($id)
    {
        return $this->model->where('id', $id)->delete();
    }
}
