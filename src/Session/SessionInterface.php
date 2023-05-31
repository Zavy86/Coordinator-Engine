<?php
/**
 * Session Interface
 *
 * @package Coordinator\Engine
 * @author Manuel Zavatta <manuel.zavatta@gmail.com>
 */

namespace Coordinator\Engine\Session;

interface SessionInterface{

	/**
	 * Check if session is valid
	 *
	 * @return bool
	 */
	public function isValid():bool;

	/**
	 * Invalidate the session
	 */
	public function invalidate();  // valutare se tenere o meno (fa "le veci" del session_destroy fissando valid a false)

	/**
	 * Validate the session
	 *
	 * @param string $account
	 * @param string $client
	 * @param int $duration
	 * @param array $authorizations
	 * @return bool
	 */
	public function validate(string $account,string $client,int $duration,bool $administrator,array $authorizations):bool;

	/**
	 * Debug
	 *
	 * @return array
	 */
	public function debug():array;

}
