<?php


namespace App\Http\Services;


class TestClassService
{
    public $name;
    protected $description;
    private $status;
    private $comment = "she will shine in life ";

    public function setClassInfo($name,$description,$status){
        $this->name = $name;
        $this->description = $description;
        $this->status = $status;

        return ['name' => $this->name,'description' => $this->description, 'status' => $this->status];

    }
    static function showComment(){
        return (new TestClassService())->comment;
    }

}