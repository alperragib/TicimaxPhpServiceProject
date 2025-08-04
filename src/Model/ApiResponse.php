<?php

declare(strict_types=1);

namespace AlperRagib\Ticimax\Model;

/**
 * Class ApiResponse
 * Standard response class for all API responses
 */
class ApiResponse
{
    /** @var bool */
    public bool $success;

    /** @var string|null */
    public ?string $message;

    /** @var mixed */
    public $data;

    /**
     * ApiResponse constructor.
     * @param bool $success Is the operation successful?
     * @param string|null $message Message (optional)
     * @param mixed $data Data (optional)
     */
    public function __construct(bool $success, ?string $message = null, $data = null)
    {
        $this->success = $success;
        $this->message = $message;
        $this->data = $data;
    }

    /**
     * Create successful response
     * @param mixed $data
     * @param string|null $message
     * @return self
     */
    public static function success($data = null, ?string $message = null): self
    {
        return new self(true, $message, $data);
    }

    /**
     * Create error response
     * @param string $message
     * @param mixed $data
     * @return self
     */
    public static function error(string $message, $data = null): self
    {
        return new self(false, $message, $data);
    }

    /**
     * @return bool
     */
    public function isSuccess(): bool
    {
        return $this->success;
    }

    /**
     * @return string|null
     */
    public function getMessage(): ?string
    {
        return $this->message;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Convert response to array
     * @return array
     */
    public function toArray(): array
    {
        $data = $this->data;

        if (is_array($data)) {
            $data = array_map(function ($item) {
                if ($item instanceof \JsonSerializable) {
                    return $item->jsonSerialize();
                }
                return $item;
            }, $data);
        }

        return [
            'success' => $this->success,
            'message' => $this->message,
            'data' => $data,
        ];
    }

    public function __toString()
    {
        return json_encode($this->toArray());
    }
}
