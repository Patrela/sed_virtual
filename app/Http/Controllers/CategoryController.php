<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Provider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Category::all();
    }

    public function loadPageData($products)
    {

        // Get common data from CategoryController
        //$maingroup = $this->MainDepartment();
        if (count($products) > 0) {
            $maingroupName = $products->first()->department;
            $department = $this->MainDepartment($maingroupName);
            $maingroup = $department->id;
        } else {
            $department = $this->MainDepartment();
            $maingroup = $department->id;
            $maingroupName = $department->name;
        }

        $brands = $this->brands();
        $segments =  $this->segments();
        $departments = CategoryController::departments(0);
        // foreach ($departments as &$department) {
        //     $department['categories'] = CategoryController::childGroups($department['id']);
        // }

        // // Cache and retrieve total products
        // $totalproducts = Cache::remember('products', now()->addMinutes(30), function () {
        //   return Product::all();
        // });

        // Get pagination data from ProductController
        // $perPage = app(ProductController::class)->PerPage();
        // $page = app(ProductController::class)->CurrentPage();

        return [
            'products' => $products,
            'maingroup' => $maingroup,
            'maingroupName' => $maingroupName,
            'departments' => $departments,
            'brands' => $brands,
            'segments' => $segments,
            'categories' => CategoryController::childGroups('categoria',$maingroup),
            //   'perPage' => $perPage,
            //   'page' => $page,
            //   'total' => count($totalproducts),
        ];
    }
    private function MainDepartment(string $name = 'Computadores')
    {
        //return 6; //$department = Computadores
        $department = Category::select('id', 'name')
            ->where('group_name', "departamento")
            ->when($name !== '', function ($query) use ($name) {
                $query->where('name', "{$name}");
            })
            ->first();
        Log::error("category dep. " . $department);
        return $department;
    }

    /**
     * @return the main category = department
     */
    public static function departments(int $idgroup = 0)
    {
        // return Category::where('group_name', "departamento")->orderBy('id')->get();

        $departments = Category::select('id', 'name', 'slug', 'group_name', 'parent_id')
            ->where('group_name', "departamento")
            ->when($idgroup !== 0, function ($query) use ($idgroup) {
                $query->where('id', "{$idgroup}");
            })
            ->orderBy('id', 'ASC')
            ->get();

        return $departments;
    }

    /**
     * @return the category = brands
     */
    private function brands(int $brand = 0)
    {
        $brands = Category::where('group_name', "marca")
            ->when($brand !== 0, function ($query) use ($brand) {
                $query->where('id', "{$brand}");
            })
            ->orderBy('id', 'ASC')
            ->get();
        return $brands;
    }

    /**
     * @return the category = segments
     */
    private function segments(int $segment = 0)
    {
        //return Category::where('group_name', "segmento")->orderBy('id')->get();
        $segments = Category::where('group_name', "segmento")
            ->when($segment !== 0, function ($query) use ($segment) {
                $query->where('id', "{$segment}");
            })
            ->orderBy('id', 'ASC')
            ->get();
        return $segments;
    }

    /**
     * @return Categories from a parent group. The default parent group is Computadores = 6
     * @param int $parentid the parent group identifier
     */
    public function childGroups(string $groupname = 'categoria', int $parentid = 0)
    {
        $group = Category::where('group_name', "{$groupname}")
            ->when($parentid !== 0, function ($query) use ($parentid) {
                $query->where('parent_id', $parentid);
            })
            ->orderBy('id', 'ASC')
            ->get();
        return $group;
    }
}
