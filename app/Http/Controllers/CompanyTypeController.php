<?php
namespace App\Http\Controllers;

use App\Models\BranchType;
use Illuminate\Http\Request;

class CompanyTypeController extends Controller
{
    public function index()
    {
        $types = BranchType::orderBy('name')->get();
        return view('settings.company_types', compact('types'));
    }

    public function update(Request $request)
    {
        foreach ($request->input('types', []) as $id => $row) {
            BranchType::whereKey($id)->update([
                'name' => $row['name'] ?? '',
                'is_active' => isset($row['is_active']),
            ]);
        }

        return back()->with('success', 'Company types updated.');
    }
}
