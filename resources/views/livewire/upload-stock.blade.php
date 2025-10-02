<div>
    @if (session()->has('status'))
        <div style="padding:10px;background:#e6ffed;border:1px solid #b7f0c6;margin-bottom:10px;">
            {{ session('status') }}
        </div>
    @endif

    <form wire:submit.prevent="submit">
        <div>
            <label>Company name</label>
            <input type="text" wire:model.defer="company_name" />
            @error('company_name') <span style="color:red">{{ $message }}</span> @enderror
        </div>

        <div style="margin-top:10px;">
            <label>Excel file (xlsx, xls, csv)</label>
            <input type="file" wire:model="excel_file" />
            @error('excel_file') <span style="color:red">{{ $message }}</span> @enderror

            <div wire:loading wire:target="excel_file">Uploading...</div>
        </div>

        <button type="submit" style="margin-top:10px;">Upload</button>
    </form>
</div>
