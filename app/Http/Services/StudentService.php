<?php


namespace App\Http\Services;


class StudentService extends TestClassService
{
    public function setStudentInfo($name,$description){
        $this->name = $name;
        $this->description = $description;

        return ['name' => $this->name, 'description' => $this->description];

    }



}