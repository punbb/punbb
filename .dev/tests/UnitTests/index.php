<?php
	require_once 'TestHelper.php';
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<title>PunBB for developers - Languages test</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link href="<?php echo $base_url ?>/.dev/bootstrap/css/bootstrap.min.css" rel="stylesheet">
        <link href="<?php echo $base_url ?>/.dev/css/style.css" rel="stylesheet">
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
					<a class="brand" href="<?php echo $base_url ?>/.dev/">PunBB <small>1.4.3</small></a>
					<div class="nav-collapse">
						<ul class="nav">
                            <li><a href="<?php echo $base_url ?>/.dev/">Home</a></li>
                            <li class="dropdown active">
                                <a href="<?php echo $base_url ?>/.dev/tests/"
                                    class="dropdown-toggle" data-toggle="dropdown">Tests
                                    <b class="caret"></b>
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a href="<?php echo $base_url ?>/.dev/tests/LanguageTest/">Language test</a></li>
                                    <li class="active"><a href="<?php echo $base_url ?>/.dev/tests/UnitTests/">Unit tests</a></li>
                                </ul>
                            </li>
                        </ul>
					</div>
				</div>
			</div>
		</div>
		<div class="container">
			<header>
				<h1>Unit tests</h1>
			</header>

			<pre>
			<?php
				define('PHPUnit_MAIN_METHOD', 'AllTests::runAndReturnResultAsJSON');
				require_once 'AllTests.php';

				AllTests::runAndReturnResultAsJSON();
			?>
			</pre>

			<footer>
      		    <hr>
        		<p>&copy; PunBB 2012</p>
      		</footer>
    	</div>
   	<script src="<?php echo $base_url ?>/.dev/bootstrap/js/jquery.min.js"></script>
	<script src="<?php echo $base_url ?>/.dev/bootstrap/js/bootstrap.min.js"></script>
	</body>
</html>
