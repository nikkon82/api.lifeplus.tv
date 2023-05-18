<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateProspectRequest;
use App\Http\Requests\UpdateProspectRequest;
use App\Models\Prospect;

class ProspectController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $columns = ['id', 'name', 'phone', 'phone_whatsapp', 'phone_viber', 'user_id', 'instrument', 'action_bot', 'step', 'result', 'created_at', 'deleted_at'];
        $prospects = Prospect::withTrashed()->select($columns)->orderBy('created_at', 'desc')->get();

        return response()->json($prospects, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  CreateProspectRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CreateProspectRequest $request)
    {
        $params = $request->all();

        $prospect = Prospect::create($params);

        if ($prospect->exists) {
            return response()->json([
                'prospect' => $prospect,
                'message' => 'Заявка успешно добавлена',
                'success' => true,
                'status_code' => 200
            ], 200);
        } else {
            return response()->json([
                'message' => 'Ошибка создания, попробуйте еще раз',
                'success' => false,
                'status_code' => 500
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $prospect = Prospect::find($id);

        return response()->json($prospect, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  UpdateProspectRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateProspectRequest $request, $id)
    {
        $params = $request->all();

        $prospect = Prospect::find($id);

        if ($prospect->update($params)) {
            return response()->json([
                'prospect' => $prospect,
                'message' => 'Заявка успешно обновлена',
                'success' => true,
                'status_code' => 200
            ], 200);
        } else {
            return response()->json([
                'message' => 'Ошибка обновления, попробуйте снова',
                'success' => false,
                'status_code' => 500
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $prospect = Prospect::find($id);

        if ($prospect->delete()) {
            return response()->json([
                'success' => true,
                'prospect' => $prospect,
                'message' => 'Заявка успешно удалена',
                'status_code' => 200
            ], 200);
        } else {
            return response()->json([
                'message' => 'Ошибка удаления, попробуйте снова',
                'success' => false,
                'status_code' => 500
            ], 500);
        }
    }

    /**
     * Restore deleting prospect
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function restore($id)
    {
        $prospect = Prospect::withTrashed()->where('id', $id)->first();

        if ($prospect->restore()) {
            return response()->json([
                'success' => true,
                'prospect' => $prospect,
                'message' => 'Заявка успешно восстановлена',
                'status_code' => 200
            ], 200);
        } else {
            return response()->json([
                'message' => 'Ошибка восстановления, попробуйте снова',
                'status_code' => 500
            ], 500);
        }
    }
}
