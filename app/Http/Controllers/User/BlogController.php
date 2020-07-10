<?php

namespace App\Http\Controllers\User;

use App\Http\Models\Blog;
use App\Http\Models\Package;
use App\Http\Services\BlogService;
use App\Exports\BlogExport;
use App\Imports\BlogImport;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;


class BlogController extends Controller
{
    protected $blogService;
    public $blog;

    /**
     * BlogController constructor.
     */
    public function __construct() {
        $this->blogService = new BlogService();
        $this->blog = Blog::all();
    }

    /**
     * @return Factory|View
     */
    public function showCreateBlog () {

        return view("blog.create");
    }

    /**
     * @return JsonResponse
     */
    public function getBlogList(){
        $allBlog = $this->blogService->allBlog();

        return response()->json([
            'success' =>true,
            'allBlog' =>$allBlog
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse|RedirectResponse
     */
    public function createBlog (Request $request) {
        $rules = [
          'title'=>'required',
          'description'=>'required',
          'tags'=>'required'
        ];
        $validator = Validator::make($request->all(),$rules);
        if ($validator->fails()){

            return response()->json(['success'=>false, 'message'=> $validator->errors()->first()]);
        }
        $createBlogResponse = $this->blogService->create(
            $request->title,
            $request->description,
            $request->tags
        );
        if (!$createBlogResponse['success']) {
            return redirect()->back()->with('error',$createBlogResponse['message']);
        }

        return response()->json([
            'success'=> $createBlogResponse['success'],
            'message'=> $createBlogResponse['message'],
            'data'=> $createBlogResponse['data']
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function deleteBlog(Request $request) {
        $rules = ['id' => 'integer'];
        $validator =Validator::make($request->all(),$rules);
        if ($validator->fails()){

            return response()->json(['success' => false, 'message' => $validator->errors()->first()]);
        }
        $deleteBlogResponse = $this->blogService->delete($request->id);
        if (!$deleteBlogResponse['success']) {

            return response()->json(['error' => $deleteBlogResponse['message'],]);
        }

        return response()->json([
            'success' => $deleteBlogResponse['success'],
            'message' => $deleteBlogResponse['message']
        ]);
    }


    /**
     * @return BinaryFileResponse
     */
    public function export () {
        $blog =Blog::all();
        return Excel::download(new BlogExport($blog), 'blog.xlsx');
    }


    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function import(Request $request){
        //dd(request()->file('file'));
        Excel::import(new BlogImport,request()->file('file'));

        return back();
    }
    public function importExportView () {

        return view('blog.import');
    }

    /**
     * @return JsonResponse|mixed
     * @throws \Exception
     */
    public function getAllBlog() {
        $allBlog = Blog::query();

        return datatables($allBlog)
            ->addColumn('title', function ($item){
                return $item->title;
            })
            ->addColumn('description', function ($item){
                return $item->description;
            })
            ->addColumn('tags', function ($item){
                return $item->tags;
            })
            ->addColumn('created_at', function ($item){
                return $item->created_at;
            })
            ->addColumn('updated_at', function ($item){
                return $item->updated_at;
            })
            ->addColumn('action', function ($item){
                $html = '<button type="button" class="btn btn-danger">Button</button>';
                return $html;
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    /**
     * @return Application|Factory|View
     */
    public function viewImage() {
        $data['status'] = $this->blogService->checkPaymentStatus();
        $data['packages'] = Package::all();

        return view('blog.image',compact('data'));
    }
}
