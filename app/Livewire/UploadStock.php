<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Jobs\ProcessStockExcel;
use Illuminate\Support\Facades\Cache;

class UploadStock extends Component
{
    use WithFileUploads;

    public $excel_file;
    public $company_name;

    protected $rules = [
        'excel_file'   => 'required|file|mimes:xlsx,csv', 
        'company_name' => 'required|string|max:255',
    ];

    public function updatedExcelFile()
    {
        $this->validateOnly('excel_file');
    }

    public function submit()
    {
        $this->validate();

        // Read raw file contents
        $contents = file_get_contents($this->excel_file->getRealPath());

        // Encode so DB cache can store it
        $encoded = base64_encode($contents);

        // Unique cache key
        $cacheKey = 'excel_upload_' . uniqid();

        // Store in cache for 10 min
        Cache::put($cacheKey, $encoded, now()->addMinutes(10));

        // Dispatch job with cache key + company name
        ProcessStockExcel::dispatch($cacheKey, $this->company_name);

        session()->flash('status', 'File uploaded and queued for processing.');
        $this->reset(['excel_file']);
    }

    public function render()
    {
        return view('livewire.upload-stock');
    }
}
