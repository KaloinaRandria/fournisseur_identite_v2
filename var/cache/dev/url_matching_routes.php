<?php

/**
 * This file has been auto-generated
 * by the Symfony Routing Component.
 */

return [
    false, // $matchHost
    [ // $staticRoutes
        '/_profiler' => [[['_route' => '_profiler_home', '_controller' => 'web_profiler.controller.profiler::homeAction'], null, null, null, true, false, null]],
        '/_profiler/search' => [[['_route' => '_profiler_search', '_controller' => 'web_profiler.controller.profiler::searchAction'], null, null, null, false, false, null]],
        '/_profiler/search_bar' => [[['_route' => '_profiler_search_bar', '_controller' => 'web_profiler.controller.profiler::searchBarAction'], null, null, null, false, false, null]],
        '/_profiler/phpinfo' => [[['_route' => '_profiler_phpinfo', '_controller' => 'web_profiler.controller.profiler::phpinfoAction'], null, null, null, false, false, null]],
        '/_profiler/xdebug' => [[['_route' => '_profiler_xdebug', '_controller' => 'web_profiler.controller.profiler::xdebugAction'], null, null, null, false, false, null]],
        '/_profiler/open' => [[['_route' => '_profiler_open_file', '_controller' => 'web_profiler.controller.profiler::openAction'], null, null, null, false, false, null]],
        '/checkAuth' => [[['_route' => 'checkAuth', '_controller' => 'App\\Controller\\AuthController::estAuth'], null, ['GET' => 0], null, false, false, null]],
        '/authentification' => [[['_route' => 'authentification', '_controller' => 'App\\Controller\\AuthController::authentification'], null, ['POST' => 0], null, false, false, null]],
        '/confirmPin' => [[['_route' => 'confirmPin', '_controller' => 'App\\Controller\\AuthController::confirmPin'], null, ['POST' => 0], null, false, false, null]],
        '/authMobile2' => [[['_route' => 'authMobile2', '_controller' => 'App\\Controller\\AuthMobileController::authMobile2'], null, ['POST' => 0], null, false, false, null]],
        '/authMobile' => [[['_route' => 'authMobile', '_controller' => 'App\\Controller\\AuthMobileController::authMobile'], null, ['POST' => 0], null, false, false, null]],
        '/deconnexion' => [[['_route' => 'deconnexion', '_controller' => 'App\\Controller\\DeconnexionController::deconnexion'], null, ['POST' => 0], null, false, false, null]],
        '/inscription' => [[['_route' => 'inscription', '_controller' => 'App\\Controller\\InscriptionController::inscription'], null, ['POST' => 0], null, false, false, null]],
        '/student' => [[['_route' => 'app_student', '_controller' => 'App\\Controller\\StudentController::index'], null, null, null, false, false, null]],
        '/utilisateur/modifier-complet' => [[['_route' => 'modifier_complet', '_controller' => 'App\\Controller\\UtilisateurController::modifierComplet'], null, ['POST' => 0], null, false, false, null]],
        '/utilisateurToken/modifier-nom' => [[['_route' => 'modifier_nom', '_controller' => 'App\\Controller\\UtilisateurTokenController::modifierNom'], null, ['POST' => 0], null, false, false, null]],
        '/utilisateurToken/modifier-mdp' => [[['_route' => 'modifier_mdp', '_controller' => 'App\\Controller\\UtilisateurTokenController::modifierMdp'], null, ['POST' => 0], null, false, false, null]],
        '/utilisateurToken/modifier-date-naissance' => [[['_route' => 'modifier_date_naissance', '_controller' => 'App\\Controller\\UtilisateurTokenController::modifierDateNaissance'], null, ['POST' => 0], null, false, false, null]],
        '/utilisateurToken/modifier-data-user' => [[['_route' => 'modifier-data-user', '_controller' => 'App\\Controller\\UtilisateurTokenController::modifierDataUser'], null, ['POST' => 0], null, false, false, null]],
    ],
    [ // $regexpList
        0 => '{^(?'
                .'|/_(?'
                    .'|error/(\\d+)(?:\\.([^/]++))?(*:38)'
                    .'|wdt/([^/]++)(*:57)'
                    .'|profiler/(?'
                        .'|font/([^/\\.]++)\\.woff2(*:98)'
                        .'|([^/]++)(?'
                            .'|/(?'
                                .'|search/results(*:134)'
                                .'|router(*:148)'
                                .'|exception(?'
                                    .'|(*:168)'
                                    .'|\\.css(*:181)'
                                .')'
                            .')'
                            .'|(*:191)'
                        .')'
                    .')'
                .')'
                .'|/sendNewPin/([^/]++)(*:222)'
                .'|/reinitialiser/([^/]++)(*:253)'
                .'|/confirm/([^/]++)(*:278)'
            .')/?$}sDu',
    ],
    [ // $dynamicRoutes
        38 => [[['_route' => '_preview_error', '_controller' => 'error_controller::preview', '_format' => 'html'], ['code', '_format'], null, null, false, true, null]],
        57 => [[['_route' => '_wdt', '_controller' => 'web_profiler.controller.profiler::toolbarAction'], ['token'], null, null, false, true, null]],
        98 => [[['_route' => '_profiler_font', '_controller' => 'web_profiler.controller.profiler::fontAction'], ['fontName'], null, null, false, false, null]],
        134 => [[['_route' => '_profiler_search_results', '_controller' => 'web_profiler.controller.profiler::searchResultsAction'], ['token'], null, null, false, false, null]],
        148 => [[['_route' => '_profiler_router', '_controller' => 'web_profiler.controller.router::panelAction'], ['token'], null, null, false, false, null]],
        168 => [[['_route' => '_profiler_exception', '_controller' => 'web_profiler.controller.exception_panel::body'], ['token'], null, null, false, false, null]],
        181 => [[['_route' => '_profiler_exception_css', '_controller' => 'web_profiler.controller.exception_panel::stylesheet'], ['token'], null, null, false, false, null]],
        191 => [[['_route' => '_profiler', '_controller' => 'web_profiler.controller.profiler::panelAction'], ['token'], null, null, false, true, null]],
        222 => [[['_route' => 'sendNewPin', '_controller' => 'App\\Controller\\AuthController::sendNewPin'], ['id_utilisateur'], ['GET' => 0], null, false, true, null]],
        253 => [[['_route' => 'reinitialiser', '_controller' => 'App\\Controller\\AuthController::reinitialiser'], ['id_tentative'], ['GET' => 0], null, false, true, null]],
        278 => [
            [['_route' => 'confirm', '_controller' => 'App\\Controller\\InscriptionController::confirmInscription'], ['jeton'], ['GET' => 0], null, false, true, null],
            [null, null, null, null, false, false, 0],
        ],
    ],
    null, // $checkCondition
];
