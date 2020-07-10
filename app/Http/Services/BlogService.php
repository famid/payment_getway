<?php


namespace App\Http\Services;


use App\Http\Models\Blog;
use App\Http\Models\Order;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class BlogService
{
    protected $errorResponse;

    /**
     * BlogService constructor.
     */
    public function  __construct(){
        $this->errorResponse = [
          'success' => false,
          'message' => "something went wrong"
        ];
    }

    /**
     * @return Blog[]|Collection
     */
    public function allBlog(){
        return Blog::all();
    }

    /**
     * @param $blogId
     * @return array
     */
    public function Blog($blogId) :array{
        try{
            $blog = Blog::find($blogId);
            if (is_null($blog)){
                return ['success' => false, 'message' => 'Blog not found'];
            }
            return ['success' => true, 'data' => $blog];
        }catch (\Exception $e){
            return $this->errorResponse;
        }
    }

    /**
     * @param string $title
     * @param string $description
     * @param string $tags
     * @return array|JsonResponse
     */
    public function create(string $title, string $description, string $tags) {
        try {
            $blog = Blog::create([
                'title' => $title,
                'description' => $description,
                'tags'=> $tags
            ]);
            return [
                'success' => true,
                'message'=> 'Blog has been created successfully',
                'data' => $blog
            ];
        } catch (\Exception $e){
            return $this->errorResponse;
        }

    }

    /**
     * @param int $blogId
     * @return array
     */
    public function delete(int $blogId) {
        $blog = Blog::where('id',$blogId)->delete();
        if (!$blog){
            return $this->errorResponse;
        }
        return [
            'success' => true,
            'message'=> 'Blog has been deleted successfully'
        ];
    }

    /**
     * @return array
     */
    public function checkPaymentStatus() {
        try {
            $data = Order::where('user_id', Auth::id())->orderBy('id','desc')->first();
            if(is_null($data) || $data->status !== PAID) {

                return $this->errorResponse;
            }

            return ['success' => true, 'data' => $data->status];

        } catch (\Exception $e) {

            return $this->errorResponse;
        }
    }
}
