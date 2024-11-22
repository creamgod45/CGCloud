<?php

namespace App\Http\Controllers;

use App\Lib\Utils\EValidatorType;
use App\Lib\Utils\ValidatorBuilder;
use App\Models\Animal;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\MessageBag;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class AnimalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @throws Exception
     */
    public function store(Request $request)
    {
        //
        $init = self::baseControllerInit($request);
        $i18N = $init->getI18N();

        $vb = new ValidatorBuilder($i18N, EValidatorType::ANIMALCREATE);
        $v = $vb->validate($request->all());

        if ($v instanceof MessageBag) {
            return response()->json([
                'message' => "Failed to create animal",
                'errors' => $v->all(),
            ], ResponseAlias::HTTP_BAD_REQUEST);
        } else {
            Animal::create($request->all());
            return response()->json([
                'message' => "Animal Added",
            ], ResponseAlias::HTTP_CREATED);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Animal $animal)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Animal $animal)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Animal $animal)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        //
        // 手动查找 Animal 模型实例

        $v = Validator::make(['id' => $id], ["id" => ["required", "integer"]]);
        $validate = $v->validate();
        if (!$v->errors()->any()) {
            $animal = Animal::find($validate['id']);

            // 检查是否找到实例
            if (!$animal) {
                // 如果没有找到，返回自定义错误消息
                return response()->json(['message' => 'Animal not found with ID ' . $id],
                    ResponseAlias::HTTP_NOT_FOUND);
            }
            $animal->delete();
            return response()->json(['message' => "Animal Deleted"], ResponseAlias::HTTP_OK);
        }
        return response()->json(['message' => "id is invalid"], ResponseAlias::HTTP_OK);
    }
}
