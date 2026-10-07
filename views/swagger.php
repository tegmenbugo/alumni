<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Alumni API - Swagger UI</title>
    <link rel="stylesheet" type="text/css" href="https://unpkg.com/swagger-ui-dist@5.11.0/swagger-ui.css" />
    <link rel="icon" type="image/png" href="https://unpkg.com/swagger-ui-dist@5.11.0/favicon-32x32.png" />
    <style>
        html { box-sizing: border-box; overflow-y: scroll; }
        *, *:before, *:after { box-sizing: inherit; }
        body { margin: 0; background: #fafafa; font-family: sans-serif; }
        .swagger-custom-header {
            background: #1f2937;
            color: white;
            padding: 14px 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .swagger-custom-header a {
            color: #60a5fa;
            text-decoration: none;
            font-size: 14px;
            border: 1px solid #374151;
            padding: 6px 12px;
            border-radius: 6px;
        }
        .swagger-custom-header a:hover { background: #374151; color: white; }
    </style>
</head>
<body>
    <div class="swagger-custom-header">
        <strong>🎓 Alumni Tracking System — MVC REST API & Swagger UI</strong>
        <div>
            <a href="/">← Ana Sayfaya Dön</a>
            <a href="/api/health" target="_blank">Health Check ↗</a>
            <a href="/api/users" target="_blank">Users JSON ↗</a>
        </div>
    </div>
    <div id="swagger-ui"></div>
    <script src="https://unpkg.com/swagger-ui-dist@5.11.0/swagger-ui-bundle.js" charset="UTF-8"></script>
    <script src="https://unpkg.com/swagger-ui-dist@5.11.0/swagger-ui-standalone-preset.js" charset="UTF-8"></script>
    <script>
        window.onload = function() {
            const openApiUrl = window.location.pathname.replace(/\/swagger\/?$/, '/openapi.json');
            window.ui = SwaggerUIBundle({
                url: openApiUrl,
                dom_id: '#swagger-ui',
                deepLinking: true,
                presets: [
                    SwaggerUIBundle.presets.apis,
                    SwaggerUIStandalonePreset
                ],
                layout: "StandaloneLayout"
            });
        };
    </script>
</body>
</html>
