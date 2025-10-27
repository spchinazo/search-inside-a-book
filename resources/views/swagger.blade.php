<!DOCTYPE html>
<html>
<head>
    <title>API Documentation - Search Inside a Book</title>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700,400italic|Material+Icons">
    <link href="{{ asset('vendor/swagger-ui/swagger-ui.css') }}" rel="stylesheet">
    <style>
        html {
            box-sizing: border-box;
            overflow: -moz-scrollbars-vertical;
            overflow-y: scroll;
        }
        * {
            box-sizing: inherit;
        }
        body {
            margin: 0;
            background: #fafafa;
        }
    </style>
</head>
<body>
<div id="swagger-ui"></div>
<script src="{{ asset('vendor/swagger-ui/swagger-ui-bundle.js') }}"></script>
<script src="{{ asset('vendor/swagger-ui/swagger-ui-standalone-preset.js') }}"></script>
<script>
    window.onload = function() {
        const ui = SwaggerUIBundle({
            url: "{{ asset('storage/api-docs/api-docs.json') }}",
            dom_id: '#swagger-ui',
            deepLinking: true,
            presets: [
                SwaggerUIBundle.presets.apis,
                SwaggerUIStandalonePreset
            ],
            plugins: [
                SwaggerUIBundle.plugins.DownloadUrl
            ],
            layout: "StandaloneLayout",
            defaultModelsExpandDepth: 1,
        })
        window.ui = ui
    }
</script>
</body>
</html>
