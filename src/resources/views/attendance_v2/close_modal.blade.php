<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
</head>

<body>

    <script>
        if (window.parent) {

            // Fancybox閉じる
            if (window.parent.Fancybox) {
                window.parent.Fancybox.close();
            }

            // 親画面更新
            window.parent.location.reload();
        }
    </script>

</body>

</html>