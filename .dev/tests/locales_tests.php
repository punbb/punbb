<?php
	define('FORUM_QUIET_VISIT', 1);
	define('FORUM_ROOT', './../../');

	error_reporting(E_ALL);
	@set_time_limit(0);

	require FORUM_ROOT.'include/essentials.php';
	require FORUM_ROOT . '.dev/tests/lang/langTest.php';

	if (!defined('FORUM_DEBUG')) {
		define('FORUM_DEBUG', 1);
	}

	forum_remove_bad_characters();
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<title>PunBB for developers - Languages test</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link href="../bootstrap/css/bootstrap.min.css" rel="stylesheet">
        <link href="../css/style.css" rel="stylesheet">
	</head>
  	<body>
		<div class="navbar navbar-fixed-top">
			<div class="navbar-inner">
				<div class="container">
					<a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</a>
					<a class="brand" href="#">PunBB <small>1.4.3</small></a>
					<div class="nav-collapse">
						<ul class="nav">
							<li><a href="../">Home</a></li>
							<li class="active"><a href="">Tests</a></li>
						</ul>
					</div>
				</div>
			</div>
		</div>
		<div class="container">
			<ul class="breadcrumb">
                <li><a href="../">Home</a> <span class="divider">/</span></li>
                <li><a href="index.html">Tests</a> <span class="divider">/</span></li>
                <li class="active"><a href="">Languages test</a></li>
            </ul>

			<header>
				<h1>Languages test</h1>
			</header>

			<?php
				$langTest = new LangTest();
				$langTest->run();
			?>

      		<footer>
      		    <hr>
        		<p>&copy; PunBB 2012</p>
      		</footer>
    	</div>
   	<script src="../bootstrap/js/jquery.min.js"></script>
	<script src="../bootstrap/js/bootstrap.min.js"></script>
	</body>
</html>
