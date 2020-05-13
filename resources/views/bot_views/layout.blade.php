<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="Laravel Telegram Chat Bot Tutorial and Live Demo. Send messages from browser to Telegram group.">

    <meta property="fb:app_id" content="356607781215457" />
    <meta property="og:locale" content="en_US" />
    <meta property="og:type" content="article" />
    <meta property="og:title" content="Laravel Telegram Chat Bot Live Demo | Codingo Tuts">
    <meta property="og:description" content="Laravel Telegram Chat Bot Tutorial and Live Demo. Send messages from browser to Telegram group.">
    <meta property="og:url" content="https://demo2.codingo.me/">
    <meta property="og:image" content="https://tuts.codingo.me/wp-content/uploads/2015/07/telegram-messenger-800x405.jpg">
    <meta property="og:site_name" content="Codingo Demo">
    <meta property="og:image:type" content="image/png">
    <meta property="article:author" content="https://www.facebook.com/ivanradunovic" />

    <link rel="shortcut icon" href="https://codingo.me/assets/img/box.png">

    <title>Laravel Telegram Chat Bot Live Demo - Codingo Tuts</title>

    <!-- Bootstrap core CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="{{ asset('css/styles.css') }}" rel="stylesheet">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>

<body>

<div class="container">
    <div class="header clearfix">
        <nav>
            <ul class="nav nav-pills pull-right">
                <li role="presentation" class="{{ request()->path() == '/' ? 'active' : '' }}"><a href="/"><i class="fa fa-home"></i>Home</a></li>
                <li role="presentation" class="{{ request()->path() == 'bot_home' ? 'active' : '' }}"><a href="/bot_home"><i class="fa fa-home"></i>Бот</a></li>
                <li role="presentation" class="{{ request()->path() == 'getMe' ? 'active' : '' }}"><a href="/getMe"><i class="fa fa-send"></i> Get Me</a></li>
                <li role="presentation" class="{{ request()->path() == 'setWebhook' ? 'active' : '' }}"><a href="/setWebhook"><i class="fa fa-send"></i> Set webhook</a>
                </li>
                <li role="presentation" class="{{ request()->path() == 'removeWebhook' ? 'active' : '' }}"><a href="/removeWebhook"><i class="fa fa-send"></i> Remove webhook</a>
                </li>
                <li role="presentation" class="{{ request()->path() == 'get-updates' ? 'active' : '' }}"><a href="/get-updates"><i class="fa fa-send"></i> Get updates</a>
            </ul>
        </nav>
        <h3 class="text-muted">Laravel & Telegram</h3>
    </div>

    @yield('content')





    <footer class="footer">
        <p>&copy; 2017 IT-Future</p>
    </footer>

</div> <!-- /container -->


<!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
<script>
    (function () {
        'use strict';

        if (navigator.userAgent.match(/IEMobile\/10\.0/)) {
            var msViewportStyle = document.createElement('style')
            msViewportStyle.appendChild(
                    document.createTextNode(
                            '@-ms-viewport{width:auto!important}'
                    )
            )
            document.querySelector('head').appendChild(msViewportStyle)
        }

    })();
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/js/bootstrap.min.js"></script>
</body>
</html>