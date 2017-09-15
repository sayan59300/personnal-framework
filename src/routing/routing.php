<?php

namespace Itval\src\routing;

/* routes */
return [
    /* base */
    "/" => "base@index",
    "/contact" => "base@contact",
    "/mentions" => "base@mentions",
    /* authentification */
    "/auth" => "auth@index",
    "/registration" => "auth@register",
    "/confirmation" => "auth@confirmation",
    "/confirmed" => "auth@confirmed",
    "/profil" => "auth@profil",
    "/logout" => "auth@logout"
];
