{{--
    Municipal PDF header (logos + government block + report title).
    Used by CA admin PDFs and owner/tenant monthly PDFs.
    Logos: public/report-headers/ca-left-logo.png, ca-right-logo.png (embedded as data URIs when present).
    Re-apply edge transparency after replacing PNGs: npm run report-logos:transparency
    Expects: $pdfReportTitle (string), $pdfReportSubtitle (string, optional)
--}}
@php
    $pdfReportTitle = $pdfReportTitle ?? 'Report';
    $pdfReportSubtitle = $pdfReportSubtitle ?? '';

    $leftLogoCandidates = [
        public_path('report-headers/ca-left-logo.png'),
    ];
    $leftLogoData = null;
    foreach ($leftLogoCandidates as $candidatePath) {
        if (is_string($candidatePath) && $candidatePath !== '' && file_exists($candidatePath)) {
            $mime = function_exists('mime_content_type') ? (mime_content_type($candidatePath) ?: 'image/png') : 'image/png';
            $leftLogoData = 'data:'.$mime.';base64,'.base64_encode((string) file_get_contents($candidatePath));
            break;
        }
    }

    $rightLogoCandidates = [
        public_path('report-headers/ca-right-logo.png'),
    ];
    $rightLogoData = null;
    foreach ($rightLogoCandidates as $candidatePath) {
        if (is_string($candidatePath) && $candidatePath !== '' && file_exists($candidatePath)) {
            $mime = function_exists('mime_content_type') ? (mime_content_type($candidatePath) ?: 'image/png') : 'image/png';
            $rightLogoData = 'data:'.$mime.';base64,'.base64_encode((string) file_get_contents($candidatePath));
            break;
        }
    }
@endphp
<div class="header">
    <table class="header-table">
        <tr>
            <td class="header-left">
                @if($leftLogoData)
                    <img src="{{ $leftLogoData }}" alt="Municipality Logo" class="header-side-logo">
                @endif
            </td>
            <td class="header-center">
                <div class="header-topline">Republic of the Philippines</div>
                <div class="header-main">Municipality of Impasug-ong, Bukidnon</div>
                <div class="header-office">Tourism Management Office</div>
                <div class="header-report-line">Tulogan Monthly Report</div>
                <h1>{{ $pdfReportTitle }}</h1>
                @if($pdfReportSubtitle !== '')
                    <p>{{ $pdfReportSubtitle }}</p>
                @endif
            </td>
            <td class="header-right">
                @if($rightLogoData)
                    <img src="{{ $rightLogoData }}" alt="Impasug-ong Logo" class="header-side-logo">
                @endif
            </td>
        </tr>
    </table>
</div>
