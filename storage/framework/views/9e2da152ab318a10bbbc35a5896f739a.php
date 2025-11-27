<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>" class="js">

<head>
    <base href="/">
    <meta charset="utf-8">
    <meta name="author" content="Rhymes Platform">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Rhymes Author Platform - Submit your books to Rovingheights for stocking consideration">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    
    <!-- Fav Icon -->
    <link rel="shortcut icon" href="./images/favicon.png">
    
    <!-- Page Title -->
    <title><?php echo $__env->yieldContent('title', 'Rhymes Author Platform'); ?></title>
    
    <!-- StyleSheets -->
    <link rel="stylesheet" href="<?php echo e(asset('/assets/css/dashlite.css')); ?>">
    <link id="skin-default" rel="stylesheet" href="<?php echo e(asset('/assets/css/theme.css')); ?>">
    
    <?php echo $__env->yieldPushContent('styles'); ?>
</head>

<body class="nk-body ui-rounder npc-general pg-auth">
    <div class="nk-app-root">
        <!-- main @s -->
        <div class="nk-main">
            <!-- wrap @s -->
            <div class="nk-wrap nk-wrap-nosidebar">
                <!-- content @s -->
                <div class="nk-content">
                    <div class="nk-block nk-block-middle nk-auth-body wide-xs">
                        <!-- Brand Logo -->
                        <div class="brand-logo pb-4 text-center">
                            <a href="<?php echo e(route('dashboard')); ?>" class="logo-link">
                                <img class="logo-light logo-img logo-img-lg" src="./images/logo.png" srcset="./images/logo2x.png 2x" alt="Rhymes Platform">
                                <img class="logo-dark logo-img logo-img-lg" src="./images/logo-dark.png" srcset="./images/logo-dark2x.png 2x" alt="Rhymes Platform">
                            </a>
                        </div>

                        <!-- Auth Card -->
                        <div class="card">
                            <div class="card-inner card-inner-lg">
                                <!-- Page Header -->
                                <div class="nk-block-head">
                                    <div class="nk-block-head-content">
                                        <h4 class="nk-block-title"><?php echo $__env->yieldContent('page-title'); ?></h4>
                                        <div class="nk-block-des">
                                            <p><?php echo $__env->yieldContent('page-description'); ?></p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Flash Messages -->
                                <?php if(session('success')): ?>
                                    <div class="alert alert-success alert-dismissible">
                                        <button class="close" data-bs-dismiss="alert"></button>
                                        <?php echo e(session('success')); ?>

                                    </div>
                                <?php endif; ?>

                                <?php if(session('error')): ?>
                                    <div class="alert alert-danger alert-dismissible">
                                        <button class="close" data-bs-dismiss="alert"></button>
                                        <?php echo e(session('error')); ?>

                                    </div>
                                <?php endif; ?>

                                <?php if($errors->any()): ?>
                                    <div class="alert alert-danger">
                                        <ul class="mb-0">
                                            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <li><?php echo e($error); ?></li>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </ul>
                                    </div>
                                <?php endif; ?>

                                <!-- Main Content -->
                                <?php echo $__env->yieldContent('content'); ?>

                                <!-- Auth Links -->
                                <?php if (! empty(trim($__env->yieldContent('auth-links')))): ?>
                                    <div class="form-note-s2 text-center pt-4">
                                        <?php echo $__env->yieldContent('auth-links'); ?>
                                    </div>
                                <?php endif; ?>

                                <!-- Social Login (Optional) -->
                                <?php if (! empty(trim($__env->yieldContent('social-login')))): ?>
                                    <div class="text-center pt-4 pb-3">
                                        <h6 class="overline-title overline-title-sap"><span>OR</span></h6>
                                    </div>
                                    <ul class="nav justify-center gx-8">
                                        <?php echo $__env->yieldContent('social-login'); ?>
                                    </ul>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="nk-footer nk-auth-footer-full">
                        <div class="container wide-lg">
                            <div class="row g-3">
                                <div class="col-lg-6">
                                    <div class="nk-block-content text-center text-lg-left">
                                        <p class="text-soft">&copy; <?php echo e(date('Y')); ?> Rhymes Platform. All Rights Reserved.</p>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="nk-block-content text-center text-lg-right">
                                        <ul class="nk-footer-links">
                                            <li><a href="#">Privacy Policy</a></li>
                                            <li><a href="#">Terms of Service</a></li>
                                            <li><a href="#">Help Center</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- content @e -->
            </div>
            <!-- wrap @e -->
        </div>
        <!-- main @e -->
    </div>
    <!-- app-root @e -->

    <!-- JavaScript -->
    <script src="<?php echo e(asset('/assets/js/bundle.js')); ?>"></script>
    <script src="<?php echo e(asset('/assets/js/scripts.js')); ?>"></script>
    
    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>

</html>
<?php /**PATH C:\xampp\htdocs\rhyme_app\resources\views/layouts/auth.blade.php ENDPATH**/ ?>