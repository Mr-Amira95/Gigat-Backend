<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Delivery Attachments</title>

    <style>
        body {
            font-family: 'Arial', sans-serif;
            background: #f0f2f5;
            margin: 0;
            padding: 30px;
            color: #333;
        }

        .container {
            max-width: 900px;
            margin: auto;
        }

        h1 {
            font-size: 26px;
            margin: 0;
            color: #845adf;
        }

        .header-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .subtitle {
            font-size: 15px;
            margin-bottom: 25px;
            color: #555;
        }

        .file-list {
            list-style: none;
            padding: 0;
        }

        .file-item {
            background: white;
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 12px;
            border: 1px solid #e1e1e1;
            display: flex;
            align-items: center;
            justify-content: space-between;
            transition: box-shadow 0.2s ease;
        }

        .file-item:hover {
            box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.08);
        }

        .file-info {
            display: flex;
            align-items: center;
        }

        .file-icon {
            margin-right: 12px;
            font-size: 20px;
            color: #845adf;
        }

        .file-name {
            font-weight: 600;
            font-size: 16px;
            color: #333;
        }

        .actions {
            display: flex;
            gap: 12px;
        }

        .btn {
            padding: 6px 12px;
            font-size: 14px;
            font-weight: bold;
            border-radius: 5px;
            text-decoration: none;
            cursor: pointer;
        }

        .btn-view {
            color: #845adf;
        }

        .btn-view:hover {
            text-decoration: underline;
        }

        .btn-download {
            background: #845adf;
            color: white;
        }

        .btn-download:hover {
            background: #6c47d0;
        }
    </style>

</head>

<body>
    <div class="container">

        <div class="header-row">
            <h1>Delivery Attachments</h1>

            <a class="btn btn-download" href="{{ route('download.all.delivery', $requestId) }}">
                Download All
            </a>
        </div>

        <p class="subtitle">You can view or download all the delivery files submitted for this request.</p>

        <ul class="file-list">
            @foreach ($files as $file)
                <li class="file-item">
                    <div class="file-info">
                        <span class="file-icon">📄</span>
                        <span class="file-name">{{ basename($file) }}</span>
                    </div>

                    <div class="actions">
                        <a class="btn btn-view" href="{{ $file }}" target="_blank">View</a>
                        <a class="btn btn-download" href="{{ $file }}" download>Download</a>
                    </div>
                </li>
            @endforeach
        </ul>

    </div>
</body>

</html>
