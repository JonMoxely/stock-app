<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Jobs\ProcessStockExcel;

class UploadController extends Controller
{
    public function showForm()
    {
        return view('upload');
    }

    public function upload(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls,csv|max:51200', // 50MB
            'company_name' => 'required|string'
        ]);

        $file = $request->file('excel_file');
        $path = $file->store('uploads');

        // dispatch job to process file in background
        ProcessStockExcel::dispatch($path, $request->input('company_name'));

        return redirect()->back()->with('status', 'File uploaded and queued for processing.');
    }
}
