<?php
    define('FORUM_QUIET_VISIT', 1);
    define('FORUM_ROOT', './../');

    error_reporting(E_ALL);
    @set_time_limit(0);

    require FORUM_ROOT.'include/essentials.php';
    forum_remove_bad_characters();
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<title>PunBB for developers</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link href="<?= app()->base_url ?>/.dev/bootstrap/css/bootstrap.min.css" rel="stylesheet">
		<link href="<?= app()->base_url ?>/.dev/css/style.css" rel="stylesheet">
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
					<a class="brand" href="<?= app()->base_url ?>/.dev/">PunBB <small>1.4.3</small></a>
					<div class="nav-collapse">
						<ul class="nav">
							<li class="active"><a href="<?= app()->base_url ?>/.dev/">Home</a></li>
                            <li class="dropdown">
                                <a href="<?= app()->base_url ?>/.dev/tests/" class="dropdown-toggle" data-toggle="dropdown">Tests
                                    <b class="caret"></b>
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a href="<?= app()->base_url ?>/.dev/tests/LanguageTest/">Language test</a></li>
                                    <li><a href="<?= app()->base_url ?>/.dev/tests/UnitTests/">Unit tests</a></li>
                                </ul>
                            </li>
						</ul>
					</div>
				</div>
			</div>
		</div>

		<div class="container">
			<header class="jumbotron masthead">
				<h1>PunBB for developers</h1>

      			<h2>GitHub</h2>
		  		<ul class="links">
		    		<li><a href="https://github.com/punbb/punbb/issues?state=open">Opened issues</a>.</li>
		    		<li><a href="https://github.com/punbb/punbb/">Latest commits</a>.</li>
		  		</ul>
			</header>

      		<footer>
      		    <hr>
        		<p>&copy; PunBB 2012</p>
      		</footer>
    	</div> <!-- /container -->
   	<script src="<?= app()->base_url ?>/.dev/bootstrap/js/jquery.min.js"></script>
	<script src="<?= app()->base_url ?>/.dev/bootstrap/js/bootstrap.min.js"></script>
	</body>
</html>
