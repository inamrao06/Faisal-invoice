<!doctype html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <title>@yield('title', 'Document')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="{{ asset('paces/assets/css/vendors.min.css') }}" rel="stylesheet">
    <link href="{{ asset('paces/assets/css/app.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/invoice-print.css') }}?v=2" rel="stylesheet">
    <style>
        :root{--bs-body-bg:#ffffff;--bs-body-color:#26313d;--bs-border-color:#e2e8f0;--bs-tertiary-bg:#f8fafc}
        body{margin:0;background:#e8edf4;font-family:"Inter",system-ui,-apple-system,"Segoe UI",Roboto,sans-serif}
        .print-toolbar{position:sticky;top:0;z-index:30;display:flex;align-items:center;justify-content:space-between;gap:14px;
            flex-wrap:wrap;padding:14px 20px;background:#0f172a;color:#e2e8f0;box-shadow:0 2px 10px rgba(15,23,42,.25)}
        .print-toolbar h1{margin:0;font-size:16px;font-weight:800;color:#fff;letter-spacing:-.01em}
        .print-toolbar p{margin:3px 0 0;font-size:12px;color:#94a3b8}
        .print-toolbar-actions{display:flex;gap:8px;flex-wrap:wrap}
        .print-wrapper{max-width:900px;margin:26px auto 48px;padding:0 16px}
        .print-wrapper .doc{box-shadow:0 16px 40px rgba(15,23,42,.14)}
        .print-hint{text-align:center;margin:-10px 0 18px;font-size:11.5px;color:#94a3b8}
        @media print{
            @page{size:A4;margin:10mm}
            body{background:#ffffff}
            .print-toolbar,.print-hint,.print-no-print{display:none !important}
            .print-wrapper{max-width:none;margin:0;padding:0}
            a[href]:after{content:"" !important}
            *{-webkit-print-color-adjust:exact;print-color-adjust:exact}
        }
    </style>
    @stack('styles')
</head>
<body>
    <div class="print-toolbar print-no-print">
        <div>
            <h1>@yield('toolbar_title', 'Print Preview')</h1>
            <p>@yield('toolbar_subtitle', 'Review the document then print or save it as a PDF.')</p>
        </div>
        <div class="print-toolbar-actions">
            @hasSection('toolbar_back')
                <a class="btn btn-light btn-sm" href="@yield('toolbar_back')"><i class="ti ti-arrow-left me-1"></i>Back</a>
            @else
                <button type="button" class="btn btn-light btn-sm" onclick="window.close()"><i class="ti ti-x me-1"></i>Close</button>
            @endif
            <button type="button" class="btn btn-primary btn-sm" onclick="window.print()"><i class="ti ti-printer me-1"></i>Print / Save PDF</button>
        </div>
    </div>
    <div class="print-wrapper">
        @yield('content')
        <p class="print-hint print-no-print">A4 · margins 10mm · background graphics are included automatically</p>
    </div>
</body>
</html>
