<?php if (isset($is_login_page) && $is_login_page): ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title><?php echo isset($title) ? $title : 'Admin Login'; ?></title>

    <!-- CSRF Token -->
    <meta name="csrf-token" content="<?php echo Security::fetch_token(); ?>">

    <!-- Custom fonts for this template-->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template-->
    <?php echo Asset::css('sb-admin-2.min.css'); ?>
    <style>
        .btn-google {
            color: #fff;
            background-color: #ea4335;
            border-color: #ea4335;
        }
        .btn-google:hover {
            color: #fff;
            background-color: #d33b2c;
            border-color: #d33b2c;
        }
        .btn-google:focus, .btn-google.focus {
            box-shadow: 0 0 0 0.2rem rgba(234, 67, 53, 0.5);
        }
    </style>
</head>

<body class="bg-gradient-primary">
    <?php echo $content; ?>

    <!-- Bootstrap core JavaScript-->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Fallback nếu jQuery từ CDN không load được
        if (typeof jQuery === 'undefined') {
            document.write('<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"><\/script>');
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.4.1/jquery.easing.min.js"></script>
    <?php echo Asset::js('sb-admin-2.min.js'); ?>
</body>

</html>
<?php else: ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title><?php echo isset($title) ? $title : 'Admin Panel'; ?></title>

    <!-- CSRF Token -->
    <meta name="csrf-token" content="<?php echo Security::fetch_token(); ?>">

    <!-- Custom fonts for this template-->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template-->
    <?php echo Asset::css('sb-admin-2.min.css'); ?>
</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        <?php echo View::forge('layouts/partials/sidebar', array(), false); ?>

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <?php echo View::forge('layouts/partials/header', array(), false); ?>

                <!-- Begin Page Content -->
                <div class="container-fluid">
                    <?php echo $content; ?>
                </div>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            <?php echo View::forge('layouts/partials/footer', array(), false); ?>

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Bootstrap core JavaScript-->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Fallback nếu jQuery từ CDN không load được
        if (typeof jQuery === 'undefined') {
            document.write('<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"><\/script>');
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.4.1/jquery.easing.min.js"></script>
    <?php echo Asset::js('sb-admin-2.min.js'); ?>

</body>

</html>
<?php endif; ?>