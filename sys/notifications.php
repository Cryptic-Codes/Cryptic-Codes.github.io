<?php ?>
<html>

<head>
    <style>
        #loader {
            height: 100%;
            width: 100%;
            position: fixed;
            top: 0;
            left: 0;
            background: #fff;
            z-index: 10000;
        }
        #loaderContent {
            position: absolute;
            top: 75%;
            left: 75%;
            transform: translate(-50%, -50%);
            animation: fadein 2s;
            -webkit-animation: fadein 2s;
            -moz-animation: fadein 2s;
            -ms-animation: fadein 2s;
            -o-animation: fadein 2s;
        }
        #loaderImage {
            height: 160px;
            width: 160px;
        }
        @keyframes fadein {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }
        @-moz-keyframes fadein {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }
        @-webkit-keyframes fadein {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }
        @-ms-keyframes fadein {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }
        @-o-keyframes fadein {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }
    </style>
</head>

<body>
    <div id="loader">
        <div id="loaderContent">
            <svg id="loaderImage" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 200" preserveAspectRatio="xMidYMid" class="lds-dual-ring" style="background: none;">
                <circle cx="50" cy="50" ng-attr-r="{{config.radius}}" ng-attr-stroke-width="{{config.width}}" ng-attr-stroke="{{config.c1}}" ng-attr-stroke-dasharray="{{config.dasharray}}" fill="none" stroke-linecap="round" r="30" stroke-width="3" stroke="#f44336" stroke-dasharray="47.12388980384689 47.12388980384689"
                transform="rotate(24 50 50)">
                    <animateTransform attributeName="transform" type="rotate" calcMode="linear" values="0 50 50;360 50 50" keyTimes="0;1" dur="1s" begin="0s" repeatCount="indefinite"></animateTransform>
                </circle>
                <circle cx="50" cy="50" ng-attr-r="{{config.radius2}}" ng-attr-stroke-width="{{config.width}}" ng-attr-stroke="{{config.c2}}" ng-attr-stroke-dasharray="{{config.dasharray2}}" ng-attr-stroke-dashoffset="{{config.dashoffset2}}" fill="none" stroke-linecap="round"
                r="26" stroke-width="3" stroke="#ff9800" stroke-dasharray="40.840704496667314 40.840704496667314" stroke-dashoffset="40.840704496667314" transform="rotate(-24 50 50)">
                    <animateTransform attributeName="transform" type="rotate" calcMode="linear" values="0 50 50;-360 50 50" keyTimes="0;1" dur="1s" begin="0s" repeatCount="indefinite"></animateTransform>
                </circle>
            </svg>
        </div>
    </div>
    <hr/>
    
    <hr/>
    
    <!-- JS -->
	<script src="/assets/js/jquery-3.2.1.min.js" type="text/javascript"></script>
	<script src="/assets/js/jquery.cookie-1.4.1.min.js" type="text/javascript"></script>
	<script src="/assets/js/bootstrap.min.js" type="text/javascript"></script>
	<script src="/assets/js/material.min.js" type="text/javascript"></script>
</body>

</html>