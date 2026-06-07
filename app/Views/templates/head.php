<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <meta name="description" content="Presensi Siswa Sekolah SMKN 71 JAKARTA">
   <meta name="theme-color" content="#9c27b0">
   <?= csrf_meta(); ?>

   <link rel="apple-touch-icon" sizes="76x76" href="<?= base_url('assets/img/logosekolah.jpg'); ?>">
   <link rel="icon" type="image/png" href="<?= base_url('assets/img/logosekolah.jpg'); ?>">

   <?= $this->include('templates/css'); ?>

   <title><?= (string) $title ?></title>
</head>
