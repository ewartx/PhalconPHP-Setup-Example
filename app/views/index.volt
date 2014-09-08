<!DOCTYPE html>
<html>
	<head>
		<base href="{{ baseUrl }}" />
		<meta charset="utf-8">
		{{ get_title() }}
		<link rel="shortcut icon" href="favicon.ico" sizes="256x256" />
		<!--STYLES-->
		<link rel="stylesheet" href="assets/css/vendor/bootstrap.css" />
		<link rel="stylesheet" href="assets/css/vendor/font-awesome.css" />
		<link rel="stylesheet" href="assets/css/app/style.css" />
		<!--STYLES END-->		
	</head>
	<body>
		{{ content() }}
		
		<!--SCRIPTS-->
		<script src="assets/js/vendor/jquery.min.js"></script>
		<script src="assets/js/vendor/bootstrap.min.js"></script>
		<!--SCRIPTS END-->
	</body>
</html>