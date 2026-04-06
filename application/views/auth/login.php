<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: sans-serif; background: #f0f2f5; display: flex;
               align-items: center; justify-content: center; min-height: 100vh; }
        .card { background: #fff; padding: 40px; border-radius: 10px;
                box-shadow: 0 2px 16px rgba(0,0,0,0.1); width: 100%; max-width: 400px; }
        h2 { margin-bottom: 24px; color: #333; }
        .form-group { margin-bottom: 16px; }
        label { display: block; margin-bottom: 6px; font-size: 14px; color: #555; }
        input { width: 100%; padding: 10px 14px; border: 1px solid #ddd;
                border-radius: 6px; font-size: 15px; }
        input:focus { outline: none; border-color: #4f46e5; }
        button { width: 100%; padding: 12px; background: #4f46e5; color: #fff;
                 border: none; border-radius: 6px; font-size: 16px; cursor: pointer; margin-top: 8px; }
        button:hover { background: #4338ca; }
        .error { background: #fee2e2; color: #b91c1c; padding: 10px 14px;
                 border-radius: 6px; margin-bottom: 16px; font-size: 14px; }
    </style>
</head>
<body>
    <div class="card">
        <h2>Sign In</h2>

        <?php if (!empty($error)): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php echo form_open('auth/login'); ?>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" value="<?= set_value('email') ?>" placeholder="you@example.com" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="••••••••" required>
            </div>
            <button type="submit">Login</button>
        <?php echo form_close(); ?>
    </div>
</body>
</html>