<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function list(Request $request)
    {
        $user = User::get();

        return ok(__('strings.success', ['name' => 'Users list get']), [
            'users'     =>  $user
        ]);
    }

    public function delete($id)
    {
        // find the user
        $user = User::findOrFail($id);

        $user->delete();

        // Return success response after account creation
        return ok(__('strings.success', ['name' => 'User delete']));
    }

    public function profile($id)
    {
        // find the user
        $user = User::findOrFail($id);

        // Return success response after account creation
        return ok(__('strings.success', ['name' => 'Get user details']), ['user' => $user]);
    }

    public function statusChange(Request $request)
    {
        $this->validate($request, [
            'id'        => 'required|exists:users',
            'status'    => 'required|string|in:A,R',
        ]);

        $user = User::findOrFail($request->id);
        $message = null;

        $user->update([
            'status' => $request->status
        ]);

        if ($request->status === "A") {
            $message = 'Status Approve';
        } else if ($request->status === "R") {
            $message = 'Status Reject';
        }

        return ok(__('strings.success', ['name' => $message]), $user);
    }
}
