<?php
namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Form;
use App\Models\Service;
use App\Models\UserDocuments;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ServiceController extends Controller 
{
    public function index(){
        $services = Service::get();

        return $services;
    }
}