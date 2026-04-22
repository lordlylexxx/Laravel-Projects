<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update package not available</title>
    <style>
        body { font-family: system-ui, sans-serif; max-width: 640px; margin: 48px auto; padding: 0 20px; color: #1f2937; line-height: 1.5; }
        h1 { font-size: 1.25rem; color: #991b1b; }
        code { background: #f3f4f6; padding: 2px 6px; border-radius: 4px; font-size: 0.9em; word-break: break-all; }
        .box { background: #fef3c7; border: 1px solid #fcd34d; border-radius: 8px; padding: 14px 16px; margin: 16px 0; }
        a { color: #166534; }
    </style>
</head>
<body>
    <h1>Update package not available</h1>
    <p>This endpoint first tries to send you to the <strong>latest GitHub release</strong> (when <code>CENTRAL_GITHUB_REPO</code> is set). If GitHub is not configured or the API call fails, it falls back to a file on this server.</p>
    <div class="box">
        <p><strong>GitHub:</strong> set <code>CENTRAL_GITHUB_REPO=owner/repo</code> (and optional <code>CENTRAL_GITHUB_TOKEN</code> for private repos / rate limits). Optional: <code>CENTRAL_GITHUB_RELEASE_ASSET</code> to pick a specific release attachment.</p>
        <p><strong>Local fallback path:</strong></p>
        <p><code>{{ $path }}</code></p>
        <p><strong>Local filename (env):</strong> <code>CENTRAL_UPDATE_PACKAGE_FILENAME</code> → <code>{{ $filename }}</code></p>
    </div>
    <p>If you use the local fallback only, place your zip here:</p>
    <ol>
        <li>Create the folder <code>storage/app/public/updates/</code> if it does not exist.</li>
        <li>Copy your package as <code>{{ $filename }}</code> into that folder.</li>
    </ol>
    <p><a href="{{ url('/') }}">Back to home</a></p>
</body>
</html>
