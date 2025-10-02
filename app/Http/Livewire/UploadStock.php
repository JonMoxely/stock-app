<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use App\Jobs\ProcessStockExcel;

class UploadStock extends Component
{
    use WithFileUploads;

    public $excel_file;
    public $company_name;

    protected $rules = [
        'excel_file' => 'required|file|mimes:xlsx,xls,csv|max:51200', // 50MB
        'company_name' => 'required|string|max:255',
    ];

    public function updatedExcelFile()
    {
        $this->validateOnly('excel_file');
    }

    public function submit()
    {
        $this->validate();

        // store temporarily via Livewire (temporaryUploadedFile)
        $path = $this->excel_file->store('uploads');

        ProcessStockExcel::dispatch($path, $this->company_name);

        session()->flash('status', 'File uploaded and queued for processing.');
        $this->reset(['excel_file']);
    }

    public function render()
    {
        return view('livewire.upload-stock');
    }
}
