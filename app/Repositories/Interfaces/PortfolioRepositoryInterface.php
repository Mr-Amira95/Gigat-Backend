<?php

namespace App\Repositories\Interfaces;

interface PortfolioRepositoryInterface
{
    public function index();
    public function getPortfolioByUserId($userId, $perPage = null);
    public function getFeaturedPortfolios($perPage = null);
    public function getPortfolioDetails($id);
    public function getPortfolioByService($serviceId);
    public function find($id);
    public function create($data);
    public function update($data,$id);
    public function delete($id);
    public function deleteMedia($id);
}   