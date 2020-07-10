<?php

namespace App\Http\Controllers;

use App\Services\StudentService;
use App\Services\TestClassService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TestClassController extends Controller
{
    protected $studentService;
    protected $testClassService;

    /**
     * TestClassController constructor.
     * @param StudentService $studentService
     * @param TestClassService $testClassService
     */
    public function __construct(StudentService  $studentService,TestClassService $testClassService){
        $this->studentService = $studentService;
        $this->testClassService = $testClassService;

    }

    /**
     * @return JsonResponse
     */
    public function testClass() {
        $response['mainClass'] = $this->testClassService->setClassInfo('Person','This is about person which describe the person nature', True);
        $response['inheritedClass'] = $this->studentService->setClassInfo('Animal','This is about person which describe the person nature',
            True);
        $response['mainClass'] ['name'] =$this->testClassService->name = 'Putki';
        $response['stdInfo'] = $this->studentService->setStudentInfo('zim','extremely talented');
        $response['stdInfo']['comment'] = StudentService::showComment();
        dd($response);

        return response()->json($response);
    }


}

