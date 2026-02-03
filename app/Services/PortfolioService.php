<?php

namespace App\Services;

use App\Models\Portfolio;
use App\Repositories\Interfaces\PortfolioRepositoryInterface;

class PortfolioService
{
    protected $portfolioRepository;
    public function __construct(PortfolioRepositoryInterface $portfolioRepository)
    {
        $this->portfolioRepository = $portfolioRepository;
    }
    public function index(){
        return $this->portfolioRepository->index();
    }
    public function getUserPortfolio(){
        return $this->portfolioRepository->getUserPortfolio();
    }
    public function getPortfolioByUserId($userId, $perPage = null)
    {
        return $this->portfolioRepository->getPortfolioByUserId($userId, $perPage);
    }
    public function getPortfolioByService($serviceId)
    {
        return $this->portfolioRepository->getPortfolioByService($serviceId);
    }

    public function getFeaturedPortfolios($perPage = null)
    {
        return $this->portfolioRepository->getFeaturedPortfolios($perPage);
    }
    public function getPortfolioDetails($id)
    {
        return $this->portfolioRepository->getPortfolioDetails($id);
    }
    public function find($id) {
        return $this->portfolioRepository->find($id);
    }
    public function create($data)
    {
        return $this->portfolioRepository->create($data);
    }
    public function update($data, $id)
    {
        $portfolio = $this->find($id);
        return $this->portfolioRepository->update($data, $portfolio);
    }
    public function delete($id){
        return $this->portfolioRepository->delete($id);
    }
    public function deleteMedia($id)
    {
        return $this->portfolioRepository->deleteMedia($id);
    }
}
