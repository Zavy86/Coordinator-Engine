<?php
/**
 * Operator
 *
 * @package Coordinator\Engine
 * @author Manuel Zavatta <manuel.zavatta@gmail.com>
 */

namespace Coordinator\Engine\Filter;

enum Operator:string{
	case AND = 'AND';
	case OR = 'OR';
}
