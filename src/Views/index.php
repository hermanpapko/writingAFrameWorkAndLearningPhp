<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP Framework Dashboard</title>
    <style>
        :root { --primary: #2563eb; --bg: #f3f4f6; --text: #1f2937; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: var(--bg); color: var(--text); line-height: 1.5; margin: 0; padding: 20px; }
        .container { max-width: 900px; margin: 0 auto; }
        .header { text-align: center; margin-bottom: 30px; }
        .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .card { background: white; padding: 24px; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
        .full-width { grid-column: span 2; }
        h2 { margin-top: 0; font-size: 1.25rem; color: var(--primary); border-bottom: 2px solid #eff6ff; padding-bottom: 10px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; font-weight: 600; margin-bottom: 5px; font-size: 0.9rem; }
        input[type="file"], input[type="number"] { width: 100%; padding: 8px; border: 1px solid #d1d5db; border-radius: 6px; box-sizing: border-box; }
        button { background: var(--primary); color: white; border: none; padding: 10px 20px; border-radius: 6px; cursor: pointer; font-weight: 600; transition: background 0.2s; width: 100%; }
        button:hover { background: #1d4ed8; }
        .alert { padding: 12px; border-radius: 6px; margin-bottom: 20px; font-weight: 500; display: none; }
        .alert-success { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
        .alert-error { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
        ul { list-style: none; padding: 0; display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; }
        li { background: #f9fafb; padding: 8px; border-radius: 4px; border: 1px solid #f3f4f6; font-size: 0.85rem; text-align: center; }
    </style>
</head>
<body>

<div class="container">
    <div id="alert-box" class="alert"></div>

    <div class="grid">
        <div class="card">
            <h2>Import Users (CSV)</h2>
            <p style="font-size: 0.8rem; color: #6b7280;">Max file size: 5 MB. Automatic parsing enabled.</p>
            <form action="/users/import" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Select CSV File</label>
                    <input type="file" name="user_csv" accept=".csv" required>
                </div>
                <button type="submit">Upload & Parse</button>
            </form>
        </div>

        <div class="card">
            <h2>Generate Mock Data</h2>
            <p style="font-size: 0.8rem; color: #6b7280;">Uses Faker library to create dummy records.</p>
            <form action="/users/generate" method="POST">
                <div class="form-group">
                    <label>Record Quantity</label>
                    <input type="number" name="quantity" value="50" min="1" max="10000">
                </div>
                <button type="submit">Generate var/data.txt</button>
            </form>
        </div>

        <div class="card full-width">
            <h2>Database Analysis: User Cities</h2>
            <?php if (!empty($cities)): ?>
                <ul>
                    <?php foreach ($cities as $city): ?>
                        <li><?= htmlspecialchars($city) ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p style="text-align: center; color: #9ca3af; padding: 20px;">No data found. Please import a CSV file first.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    const alertBox = document.getElementById('alert-box');

    function showAlert(message, isError = false) {
        alertBox.textContent = message;
        alertBox.className = 'alert ' + (isError ? 'alert-error' : 'alert-success');
        alertBox.style.display = 'block';
    }

    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(form);
            const action = form.getAttribute('action');
            
            try {
                const response = await fetch(action, {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (response.ok) {
                    if (result.count !== undefined) {
                        showAlert(`✓ Success! ${result.count} records processed.`);
                    } else if (result.written_lines !== undefined) {
                        showAlert(`✓ Success! ${result.written_lines} lines generated in ${result.file}.`);
                    } else {
                        showAlert('✓ Success!');
                    }
                    // Если это импорт, перезагрузим список городов через 1.5 сек
                    if (action === '/users/import') {
                        setTimeout(() => window.location.reload(), 1500);
                    }
                } else {
                    showAlert(`⚠ Error: ${result.error || 'Unknown error'}`, true);
                }
            } catch (error) {
                showAlert(`⚠ Request failed: ${error.message}`, true);
            }
        });
    });
</script>

</body>
</html>