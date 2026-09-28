<?php

namespace App\Http\Controllers;

use App\Models\CompanyDocumentTemplate;
use App\Models\NotificationTemplate;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CompanyTemplateController extends Controller
{
    public function index()
    {
        return view('company.templates.index', [
            'templates' => CompanyDocumentTemplate::where('branch_id', $this->companyId())->latest()->get(),
            'types' => NotificationTemplate::templateTypes(),
            'tags' => NotificationTemplate::templateTags(),
            'companyTags' => NotificationTemplate::companyTagValues(auth()->user()->company),
        ]);
    }

    public function create()
    {
        return view('company.templates.form', $this->formData(new CompanyDocumentTemplate(['type' => 'invoice', 'is_active' => true])));
    }

    public function store(Request $request)
    {
        $template = CompanyDocumentTemplate::create($this->validated($request) + ['branch_id' => $this->companyId()]);
        $this->syncDefault($template);

        return redirect()->route('company.templates.index')->with('success', 'Template created.');
    }

    public function edit(CompanyDocumentTemplate $template)
    {
        $this->authorizeTemplate($template);
        return view('company.templates.form', $this->formData($template));
    }

    public function update(Request $request, CompanyDocumentTemplate $template)
    {
        $this->authorizeTemplate($template);
        $template->update($this->validated($request));
        $this->syncDefault($template);

        return redirect()->route('company.templates.index')->with('success', 'Template updated.');
    }

    public function destroy(CompanyDocumentTemplate $template)
    {
        $this->authorizeTemplate($template);
        $template->delete();

        return redirect()->route('company.templates.index')->with('success', 'Template deleted.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'type' => ['required', Rule::in(array_values(NotificationTemplate::templateTypes()))],
            'name' => ['required', 'max:150'],
            'body' => ['required'],
            'is_default' => ['boolean'],
            'is_active' => ['boolean'],
        ]) + ['is_default' => $request->boolean('is_default'), 'is_active' => $request->boolean('is_active')];
    }

    private function formData(CompanyDocumentTemplate $template): array
    {
        return [
            'template' => $template,
            'types' => NotificationTemplate::templateTypes(),
            'tags' => NotificationTemplate::templateTags(),
            'companyTags' => NotificationTemplate::companyTagValues(auth()->user()->company),
        ];
    }

    private function syncDefault(CompanyDocumentTemplate $template): void
    {
        if (! $template->is_default) return;

        CompanyDocumentTemplate::where('branch_id', $template->branch_id)
            ->where('type', $template->type)
            ->whereKeyNot($template->id)
            ->update(['is_default' => false]);
    }

    private function companyId(): int
    {
        abort_unless(auth()->user()->branch_id, 403);
        return auth()->user()->branch_id;
    }

    private function authorizeTemplate(CompanyDocumentTemplate $template): void
    {
        abort_unless($template->branch_id === $this->companyId(), 403);
    }
}
