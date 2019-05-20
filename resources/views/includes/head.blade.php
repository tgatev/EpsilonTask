<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<meta http-equiv="X-UA-Compatible" content="IE=edge">

<title>Epsilon</title>
<!-- Bootstrap CSS -->
<link href="//netdna.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="//cdn.datatables.net/1.10.7/css/jquery.dataTables.min.css">
<!--[if lt IE 9]>
<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
<script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
<![endif]-->

<!-- Styles -->
<style>
    html, body {
        background-color: #fff;
        color: #636b6f;
        font-family: 'Nunito', sans-serif;
        font-weight: 200;
        height: 100vh;
        margin: 0;
    }

    .full-height {
        height: 100vh;
    }

    .flex-center {
        align-items: center;
        display: flex;
        justify-content: center;
    }

    .position-ref {
        position: relative;
    }

    .top-right {
        position: absolute;
        right: 10px;
        top: 18px;
    }

    .content {
        text-align: right;
    }

    .title {
        font-size: 24px;
    }

    .links > a {
        color: #636b6f;
        padding: 0 25px;
        font-size: 13px;
        font-weight: 600;
        letter-spacing: .1rem;
        text-decoration: none;
        text-transform: uppercase;
    }

    .m-b-md {
        margin-bottom: 30px;
    }
    /** details view */
    .td-details {
        padding: 5px;
        border: 2px solid lightseagreen;
    }

    /** Code for hover info **/

    dfn {
        background: rgba(0,0,0,0.2);
        border-bottom: dashed 1px rgba(0,0,0,0.8);
        padding: 0 0.4em;
        cursor: help;
        font-style: normal;
        position: relative;

    }
    dfn::after {
        content: attr(data-info);
        display: inline;
        position: absolute;
        top: 22px; left: 0;
        opacity: 0;
        width: 300px;
        font-size: 13px;
        font-weight: 700;
        line-height: 1.5em;
        padding: 0.5em 0.8em;
        background: rgba(0,0,0,0.8);
        color: #fff;
        pointer-events: none; /* This prevents the box from apearing when hovered. */
        transition: opacity 250ms, top 250ms;
    }
    dfn::before {
        content: '';
        display: block;
        position: absolute;
        top: 12px; left: 20px;
        opacity: 0;
        width: 0; height: 0;
        border: solid transparent 5px;
        border-bottom-color: rgba(0,0,0,0.8);
        transition: opacity 250ms, top 250ms;
    }
    dfn:hover {z-index: 2;} /* Keeps the info boxes on top of other elements */
    dfn:hover::after,
    dfn:hover::before {opacity: 1;}
    dfn:hover::after {top: 30px;}
    dfn:hover::before {top: 20px;}
</style>