<?php

namespace TLSoft\Barion\Model\Config\Source;

class ResultCodes
{

	/**
	 * Result code for timeouted transaction
	 */
	const RESULT_TIMEOUT = "timeout";

	/**
	 * Result code for transaction with error
	 */
	const RESULT_ERROR = "communication_error";

	/**
	 * Result code for transaction cancelled by an user
	 */
	const RESULT_USER_CANCEL = "user_cancel";

	/**
	 * Result code for timeouted transaction
	 */
	const RESULT_PENDING = "timeout";

	/**
	 * Result code for successful transaction
	 */
	const RESULT_SUCCESS = "success";

}