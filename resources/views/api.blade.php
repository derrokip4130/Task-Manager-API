<!DOCTYPE html>
<html>
<head>
    <title>Task Manager API</title>
    <style>
        body {
            font-family: Arial;
            margin: 40px;
            background: #f9f9f9;
        }
        h1 { color: #333; }
        code {
            background: #eee;
            padding: 4px 6px;
            border-radius: 4px;
        }
        .endpoint {
            margin-bottom: 15px;
        }
    </style>
</head>
<body>

<h1>🚀 Task Manager API</h1>
<p>Base URL: <code>/api</code></p>

<h2>Available Endpoints</h2>

<div class="endpoint">
    <strong>GET</strong> <code>/api/tasks</code> — List all tasks
</div>

<div class="endpoint">
    <strong>POST</strong> <code>/api/tasks</code> — Create a task
</div>

<div class="endpoint">
    <strong>PATCH</strong> <code>/api/tasks/{id}/status</code> — Update task status
</div>

<div class="endpoint">
    <strong>DELETE</strong> <code>/api/tasks/{id}</code> — Delete completed task
</div>

<div class="endpoint">
    <strong>GET</strong> <code>/api/tasks/report?date=YYYY-MM-DD</code> — Daily report
</div>

</body>
</html>