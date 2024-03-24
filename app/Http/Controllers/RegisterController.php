<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Services\Contracts\UserServiceContract;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    private UserServiceContract $userService;

    /**
     * @param UserServiceContract $userService
     */
    public function __construct(UserServiceContract $userService)
    {
        $this->userService = $userService;
    }


    /**
     * Show the registration form.
     *
     * @return View
     */
    public function showRegistrationForm(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @param StoreUserRequest $request
     * @return RedirectResponse
     */
    public function register(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required','min:3','max:50'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:6', 'max:20', 'confirmed'],
            'password_confirmation' => ['required', 'string', 'min:6', 'max:20'],
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $this->userService->save($request->all());
        } catch (\Exception) {
            return redirect()->back()->with('error', 'Registration is failed.');
        }
        return redirect()->back()->with('success', 'Registration successful!');
    }
}
