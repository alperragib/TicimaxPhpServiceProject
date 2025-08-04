<?php

namespace AlperRagib\Ticimax;

use AlperRagib\Ticimax\Service\Brand\BrandService;
use AlperRagib\Ticimax\Service\Order\OrderService;
use AlperRagib\Ticimax\Service\Product\ProductService;
use AlperRagib\Ticimax\Service\Product\FavouriteProductService;
use AlperRagib\Ticimax\Service\Supplier\SupplierService;
use AlperRagib\Ticimax\Service\Category\CategoryService;
use AlperRagib\Ticimax\Service\User\UserService;
use AlperRagib\Ticimax\Service\Location\LocationService;
use AlperRagib\Ticimax\Service\Menu\MenuService;

class Ticimax
{
	public $main_domain = null;
	public $key         = null;
	public $ticimax     = null;
	public $request     = null;

	private $categoryService              = null;
	private $productService               = null;
	private $brandService                 = null;
	private $supplierService              = null;
	private $orderService                 = null;
	private $userService                  = null;
	private $favouriteProductService      = null;
	private $locationService              = null;
	private $menuService                  = null;

	function __construct($main_domain, $key)
	{
		$this->main_domain = $main_domain;
		$this->key         = $key;
		$this->ticimax     = $this;
		$this->request     = $this->request();
	}

	function request()
	{
		return new TicimaxRequest($this->main_domain, $this->key);
	}

	function categoryService()
	{
		if ($this->categoryService === null) {
			$this->categoryService = new CategoryService($this->request);
		}
		return $this->categoryService;
	}

	function productService()
	{
		if ($this->productService === null) {
			$this->productService = new ProductService($this->request);
		}
		return $this->productService;
	}

	function brandService()
	{
		if ($this->brandService === null) {
			$this->brandService = new BrandService($this->request);
		}
		return $this->brandService;
	}

	function supplierService()
	{
		if ($this->supplierService === null) {
			$this->supplierService = new SupplierService($this->request);
		}
		return $this->supplierService;
	}

	function orderService()
	{
		if ($this->orderService === null) {
			$this->orderService = new OrderService($this->request);
		}
		return $this->orderService;
	}

	function userService()
	{
		if ($this->userService === null) {
			$this->userService = new UserService($this->request);
		}
		return $this->userService;
	}

	function favouriteProductService()
	{
		if ($this->favouriteProductService === null) {
			$this->favouriteProductService = new FavouriteProductService($this->request);
		}
		return $this->favouriteProductService;
	}

	function locationService()
	{
		if ($this->locationService === null) {
			$this->locationService = new LocationService($this->request);
		}
		return $this->locationService;
	}

	function menuService()
	{
		if ($this->menuService === null) {
			$this->menuService = new MenuService($this->request);
		}
		return $this->menuService;
	}
}
