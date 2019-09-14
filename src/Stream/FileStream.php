<?php declare(strict_types=1);

namespace Capsule\Stream;

use Psr\Http\Message\StreamInterface;


class FileStream implements StreamInterface
{
    /**
     * Array of file modes broken into readable and writeable.
     *
     * @var array
     */
    private $fileModes = [
        "readable" => [
            "r", "r+", "w+", "a+", "x+", "c+",
            "rb", "r+b", "w+b", "a+b", "x+b", "c+b",
            "rt", "r+t", "w+t", "a+t", "x+t", "c+t",
        ],

        "writeable" => [
            "w", "w+", "r+", "a", "a+", "x", "x+", "c", "c+",
            "wb", "w+b", "r+b", "ab", "a+b", "xb", "x+b", "cb", "c+b",
            "wt", "w+t", "r+t", "at", "a+t", "xt", "x+t", "ct", "c+t",
        ],
    ];

    /**
     * Stream resource.
     *
     * @var resource
     */
    protected $resource;

    /**
     * FileStream constructor.
     *
     * @param resource $resource
     */
    public function __construct($resource)
    {
        $this->resource = $resource;
    }

    /**
     * @inheritDoc
     */
    public function __toString()
    {
        return $this->getContents();
    }

    /**
     * @inheritDoc
     */
    public function close()
    {
        \fclose($this->resource);
    }

    /**
     * @inheritDoc
     */
    public function detach()
    {
        $this->close();
    }

    /**
     * @inheritDoc
     */
    public function getSize()
    {
        $fstat = \fstat($this->resource);
        return $fstat["size"] ?? null;
    }

    /**
     * @inheritDoc
     */
    public function tell(): int
    {
        return \ftell($this->resource);
    }

    /**
     * @inheritDoc
     */
    public function eof()
    {
        return \feof($this->resource);
    }

    /**
     * @inheritDoc
     */
    public function isSeekable()
    {
        return (bool) $this->getMetadata('seekable');
    }

    /**
     * @inheritDoc
     * @return void
     */
    public function seek($offset, $whence = SEEK_SET)
    {
        \fseek($this->resource, $offset, $whence);
    }

    /**
     * @inheritDoc
     * @return void
     */
    public function rewind()
    {
        \rewind($this->resource);
    }

    /**
     * @inheritDoc
     */
    public function isWritable()
    {
        return \in_array(
			\strtolower($this->getMetadata('mode')),
			$this->fileModes['writeable'])
		;
    }

    /**
     * @inheritDoc
     */
    public function write($string)
    {
        return \fwrite($this->resource, $string);
    }

    /**
     * @inheritDoc
     */
    public function isReadable()
    {
        return \in_array(
			\strtolower($this->getMetadata('mode')),
			$this->fileModes['readable']
		);
    }

    /**
     * @inheritDoc
     */
    public function read($length)
    {
        return \fread($this->resource, $length);
    }

    /**
     * @inheritDoc
     */
    public function getContents()
    {
        $buffer = "";

        while( !$this->eof() ){
            $buffer .= $this->read(1024);
        }

        return $buffer;
    }

    /**
     * @inheritDoc
     */
    public function getMetadata($key = null)
    {
        if( empty($key) ){
            return \stream_get_meta_data($this->resource);
        }

        $meta = \stream_get_meta_data($this->resource);

        if( \array_key_exists($key, $meta) ){
            return $meta[$key];
        }

        throw new \Exception("Unknown meta data key.");
    }
}