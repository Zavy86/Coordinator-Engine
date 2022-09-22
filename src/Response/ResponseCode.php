<?php
/**
 * Response Code
 *
 * @package Coordinator\Engine
 * @author Manuel Zavatta <manuel.zavatta@gmail.com>
 */

namespace Coordinator\Engine\Response;

enum ResponseCode:int{

	case OK_200=200;
	case CREATED_201=201;
	case MOVED_PERMANENTLY_301=301;
	case BAD_REQUEST_400=400;
	case UNAUTHORIZED_401=401;
	case FORBIDDEN_403=403;
	case NOT_FOUND_404=404;
	case METHOD_NOT_ALLOWED_405=405;
	case TOO_MANY_REQUESTS_429=429;
	case INTERNAL_SERVER_ERROR_500=500;
	case NOT_IMPLEMENTED_501=501;
	case SERVICE_UNAVAILABLE_503=503;

}
