<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Category;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Category::all();
    }

    public function loadPageData($products, $maingroupName="Computadores", $searchText = "")
    {
        // Get common data from CategoryController

        if($maingroupName != ""){
            $department = $this->mainDepartment($maingroupName);
            $maingroup = $department->id;
        }
        else {
            $maingroup = 0;
        }

        $brands = $this->childGroups('marca-dpto',$maingroup);
       // $segments =  $this->segments(); //revoked
        $departments = CategoryController::departments(0);

        // Get pagination data from ProductController
        // $perPage = app(ProductController::class)->PerPage();
        // $page = app(ProductController::class)->CurrentPage();



        $data = [
            'products' => $products,
            'maingroup' => $maingroup,
            'maingroupName' => $maingroupName,
            'departments' => $departments,
            'brands' => $brands,
            'categories' => CategoryController::childGroups('categoria', $maingroup),
            'searchText' => $searchText,

            //'segments' => $segments, //revoked
            //   'perPage' => $perPage,
            //   'page' => $page,
            //   'total' => count($totalproducts),
        ];

        // Check if the user is authenticated
        if (Auth::check()) {
            if(Auth::user()->role_type == User::ALLROLES["Administrator"])
            $data['administrator'] = Auth::user()->role_type;
        }
        //Log::info("Auth Type condition " . Auth::user()->role_type);
        //Log::info($data);
        return $data;

    }
    private function mainDepartment(string $name = 'Computadores')
    {
        //return 6; //$department = Computadores
        $department = Category::select('id', 'name')
            ->where('group_name', "departamento")
            ->when($name !== '', function ($query) use ($name) {
                $query->where('name', "{$name}");
            })
            ->first();
        //Log::error("category dep. " . $department);
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
