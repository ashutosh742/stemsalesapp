<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Preparing Download...</title>
</head>
<body style="font-family: Arial; text-align:center; margin-top:50px;">
    <h3>Your MOM ZIP file is being downloaded...</h3>
    <p>You’ll be redirected shortly.</p>

    <script>
        const fileUrl = "<?= $zip_path ?>";
        const redirectUrl = "<?= $redirect_url ?>";

        // Start the file download
        window.location.href = fileUrl;

        // Redirect after 3 seconds
        setTimeout(() => {
            window.location.href = redirectUrl;
        }, 3000);
    </script>
</body>
</html>
