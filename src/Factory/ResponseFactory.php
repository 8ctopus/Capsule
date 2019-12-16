<?php declare(strict_types=1);

namespace Capsule\Factory;

use Capsule\Response;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;

class ResponseFactory implements ResponseFactoryInterface
{
	/**
	 * @inheritDoc
	 */
	public function createResponse(int $code = 200, string $reasonPhrase = ''): ResponseInterface
	{
		return (new Response($code))->withStatus($code, $reasonPhrase);
	}
}