<?php

namespace RWFramework\Framework\Routing;

use RWFramework\Framework\Http\Request;

// All routers must implement this interface
interface RouterInterface {
    public function dispatch(Request $request);
}