<?php

namespace Modules\Role\App\Http\Controllers\Api;

use App\Helpers\JSend;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Modules\Role\App\Models\Role;
use Modules\Role\App\Repositories\RoleRepository;
use Nwidart\Modules\Exceptions\ModuleNotFoundException;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('role::index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create() {}

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) {}

    /**
     * Show the specified resource.
     */
    public function show(mixed $id)
    {
        try {
            $role = (new RoleRepository)->find($id);

            return JSend::success(['role' => $role]);
        } catch (ModelNotFoundException $e) {
            return JSend::fail(['message' => 'Role not found']);
        } catch (\Exception $e) {
            return JSend::error('Server error');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id) {}

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id) {}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role) {
        try{

            $role->delete();

            return JSend::success(['message' => 'Record deleted successfully']);
        } catch(ModuleNotFoundException){
            return JSend::fail(['message' => "Record not Found"]);
        }catch(\Throwable $e){
            return JSend::error('Server Error: Fail to delete role');
        }
    }
}
