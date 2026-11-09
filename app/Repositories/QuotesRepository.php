<?php
namespace App\Repositories;

use App\Models\Quote;
use App\Support\SafeUpload;
use Illuminate\Http\Request;

class QuotesRepository
{
    public function get(Request $request)
    {
        $rows_count = ($request->rows) ? $request->rows : 20;
        $quotes = Quote::orderBy('id', 'desc');

        if ($request->term) {
            $quotes->where('quote', 'like', '%' . $request->term . '%');
        }

        return $quotes->paginate($rows_count);
    }

    public function save(Request $request)
    {
        $quote = ($request->id) ? Quote::find($request->id) : new Quote();
        $quote->quote = $request->quote;
        $quote->link_url = $request->filled('link_url') ? $request->link_url : null;

        if ($request->hasFile('image')) {
            $dir = storage_path('app/public/uploads/quotes');
            $file = $request->file('image');
            $quote->image = SafeUpload::move($file, $dir, SafeUpload::IMAGES, 'image');
        }

        ($request->id) ? $quote->update() : $quote->save();
        return $quote;
    }

    public function find($id){

        return Quote::find($id);
    }


    public function delete($id){

        return Quote::find($id)->delete();
    }

}