<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use function PHPSTORM_META\map;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Show the application registration form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showRegistrationForm()
    {
        $lang = collect(Storage::disk('lang')->files())->map(function ($val) {
            return Str::before($val, '.');
        });

        return view('auth.register', compact('lang'));
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'lang' => ['required', 'string'],
            'name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param array $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        $metrics = isset($data['utm_metrics']) ? $this->prepareMetrics($data['utm_metrics']) : null;

        $user = User::create([
            'balance' => 0,
            'name' => $data['name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'lang' => $data['lang'],
            'password' => Hash::make($data['password']),
            'telegram_token' => str_shuffle(Str::random(50) . Carbon::now()),
            'metrics' => $metrics,
        ]);

        $user->assignRole('Free');
        $user->assignRole('user');

        return $user;
    }

    protected function prepareMetrics($metrics)
    {
        try {
            $metrics = str_replace('=', ':', $metrics);
            $metrics = str_replace('?', '', $metrics);
            $metrics = explode('&', $metrics);

            $utmMetrics = [];

            foreach ($metrics as $metric) {
                $array = explode(':', $metric);
                if ($array[0] === 'utm_source' || $array[0] === 'utm_campaign' || $array[0] === 'utm_medium' || $array[0] === 'utm_content') {
                    $utmMetrics[$array[0]] = $array[1];
                } else if ($array[0] === 'utm_term') {
                    $term = explode('_', $array[1]);
                    $utmMetrics['utm_term_keyword'] = $term[0];
                    $utmMetrics['utm_term_source'] = $term[1];
                }
            }

            $utmMetrics = json_encode($utmMetrics);
        } catch (\Throwable $e) {
            Log::debug('Произошёл сбой подготовки данных метрики', [$metrics]);
            $utmMetrics = $metrics;
        }

        return $utmMetrics;
    }
}
